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

class Iuvo_IuvoCache_Model_App_Cache_Observer
{
	public function cleanIuvoCache($observer)
	{
		Mage::getSingleton('iuvocache/server')->clean($observer->getEvent()->getTags());
	}
}