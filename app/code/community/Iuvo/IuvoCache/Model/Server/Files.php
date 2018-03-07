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

class Iuvo_IuvoCache_Model_Server_Files
{
	protected $_server;
	
	public function getServer()
	{
		if (!isset($this->_server)){
			//verify that the file system exists and that we have rights to it.	
			$folder = Mage::getConfig()->getNode('iuvocache/cache/path');
			if (!isset($folder) || $folder == "")
				$folder = "var/iuvocache";
			rtrim($folder, "/");
			if (!is_dir($folder)){
				mkdir($folder, 0777);
			}
			$this->_server = $folder . "/";
		}
		return $this->_server;
	}
	
	public function save($key, $data, $expires=0, array $tags=array())
	{
		//open file for overwrite
		$filename =  MD5($key);
		$file = fopen($this->getServer() . $filename, 'w');
		fwrite($file, serialize($data));
		fclose($file);
		
		//fill in tag files
		foreach($tags as $tag){
			$tagfile = $this->getServer() . MD5($tag);
			$file = fopen($tagfile, 'a');
			fwrite($file, $filename . "\r\n");
			fclose($file);
		}
	}
	
	public function cleanByTag($tag)
	{
		return $this->clean(array($tag));
	}
	
	public function clean($tags=array())
	{
		try{
			if(count($tags) && !in_array('IUVOCACHE', $tags)){
				foreach($tags as $tag){
					$tagfile = $this->getServer() . MD5($tag);
					if (is_file($tagfile)){
						$file = fopen($tagfile, 'r');
						while($line = fgets($file)){
							//delete file with name of $line :)
							$filename = preg_replace("/\r|\n/", "", $this->getServer() . $line);
							if (is_file($filename)){
								unlink($filename);
							}
						}
						//delete the tag file
						unlink($tagfile);
					}
				}
			}else{
				//delete the tags folder
				$files = glob($this->getServer() . "*");
				foreach($files as $file) unlink($file);
			}
		}catch(Exception $e){
			return false;
		}
	}
}