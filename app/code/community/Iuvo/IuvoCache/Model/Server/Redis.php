<?php
/**
 * Iuvo Commercial Extension
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the Iuvo Commercial Extension License.
 * It is available through the world-wide-web at this URL:
 * http://www.iuvocommerce.com/license commercial-extension
 * If you did not receive a copy of the license and are unable to
 * obtain it through the world-wide-web, please send an email
 * to license@iuvocommerce.com.com so we can send you a copy immediately.
 *
 * DISCLAIMER
 *
 * Do not edit or add to this file if you wish to upgrade this package to newer
 * versions in the future. 
 *
 * @category   Iuvo
 * @package    Iuvo_IuvoCache
 * @copyright  Copyright (c) 2014 Iuvo
 * @license    http://www.iuvocommerce.com/license
 */

class Iuvo_IuvoCache_Model_Server_Redis
{
	protected $_server;
	protected $_enabled 		= false;
	protected $_useCompression	= true;
	
	public function getServer()
	{
		if(!$this->_server){
			if(($memcachedConfig = Mage::getConfig()->getNode('iuvocache/cache/servers')) && extension_loaded('memcache')){
				$this->_enabled = true;
				$this->_useCompression = (bool) $memcachedConfig->use_compression;
				$this->_server = new Memcache();
				foreach ($memcachedConfig->children() as $serverConfig) {
					$this->_server->addServer(
						 (string)$serverConfig->host
						,(string)$serverConfig->port
						,(string)$serverConfig->persistent);
				}
			}
		}
		return $this->_server;
	}
	
	public function save($key, $data, $expires=0, array $tags=array())
	{
		$server = $this->getServer();
		if($this->_enabled){
			try{
				$result = false;
				$expires = (double) $expires;
				if($server->get($key)){
					$result = $server->replace($key, $data, $this->_useCompression, $expires);
				}else{
					$result = $server->set($key, $data, $this->_useCompression, $expires);
				}
				if(count($tags)){
					foreach($tags as $tag){
						$tag = Mage::app()->prepareCacheId($tag);
						$keys = $server->get($tag);
						if(!$keys){
							$keys = array($key);
							$result = $server->set($tag, $keys, $this->_useCompression);
						}else{
							if(!in_array($key, $keys)){
								$keys[] = $key;
								$result = $server->replace($tag, $keys, $this->_useCompression);
							}
						}
					}
				}
				return $result;
			}catch(Exception $e){
				return false;
			}
		}
	}
	
	public function cleanByTag($tag)
	{
		return $this->clean(array($tag));
	}
	
	public function clean($tags=array())
	{
		$server = $this->getServer();
		try{
			if(count($tags) && !in_array('LIGHTSPEED', $tags)){
				if($this->_enabled){
					foreach($tags as $tag){
						if($keys = $server->get($tag)){
							foreach($keys as $key){
								$server->delete($key);
							}
						}
					}
				}
			}else{
				$server->flush();
			}
		}catch(Exception $e){
			return false;
		}
	}
}