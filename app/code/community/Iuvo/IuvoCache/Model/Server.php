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

class Iuvo_IuvoCache_Model_Server
{
	protected $_server;
	protected $_enabled 		= false;
	protected $_useCompression	= false;
	
	public function getServer()
	{
		if(!$this->_server){
			$type = Mage::getConfig()->getNode('iuvocache/cache/type');
			if ($type == "memcached"){
				$this->_server = Mage::getModel("iuvocache/server_memcache");
			}else{
				$this->_server = Mage::getModel("iuvocache/server_files");
			}
		}
		return $this->_server;
	}
	
	public function save($key, $data, $expires=0, array $tags=array())
	{
		return $this->getServer()->save($key, $data, $expires, $tags);
	}
	
	public function cleanByTag($tag)
	{
		return $this->getServer()->cleanByTag($tag);
	}
	
	public function clean($tags=array())
    {
	    if(!is_array($tags)){
        	$tags = array($tags);
	    }
	    $newTags = array();
	    if(count($tags)){
            foreach($tags as $tag){
                    $newTags[] = strtoupper($tag);
            }
	    }
	    return $this->getServer()->clean($newTags);
    }
}