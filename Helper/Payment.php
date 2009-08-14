<?php
/**
 * Magento
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the Open Software License (OSL 3.0)
 * that is bundled with this package in the file LICENSE.txt.
 * It is also available through the world-wide-web at this URL:
 * http://opensource.org/licenses/osl-3.0.php
 * If you did not receive a copy of the license and are unable to
 * obtain it through the world-wide-web, please send an email
 * to license@magentocommerce.com so we can send you a copy immediately.
 *
 * DISCLAIMER
 *
 * Do not edit or add to this file if you wish to upgrade Magento to newer
 * versions in the future. If you wish to customize Magento for your
 * needs please refer to http://www.magentocommerce.com for more information.
 *
 * @category   RicoNeitzel
 * @package    RicoNeitzel_PaymentFilter
 * @copyright  Copyright (c) 2009 Irubin Consulting Inc. DBA Varien (http://www.varien.com)
 * @license    http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */

/**
 * Extend the payment module base helper to take the customer grup payment methods into account
 *
 * @category	RicoNeitzel
 * @package		RicoNeitzel_PaymentFilter
 * @author		Vinai Kopp <vinai@netzarbeiter.com>
 */
class RicoNeitzel_PaymentFilter_Helper_Payment extends Mage_Payment_Helper_Data
{
    /**
     * Retrieve available payment methods for store, taking conigured customer group payment methods into account.
     *
     * @param   mixed $store
     * @param   boolean $quote
     * @return  array
     * @see     Mage_Payment_Helper_Data::getStoreMethods()
     */
    public function getStoreMethods($store=null, $quote=null)
    {
    	$methods = parent::getStoreMethods($store, $quote);
    	
    	/**
    	 * @var RicoNeitzel_PaymentFilter_Helper_Data $helper
    	 */
    	$helper = Mage::helper('payfilter');
    	if ($helper->moduleActive() && ! $helper->inAdmin())
    	{
    		/**
    		 * Remove payment methods forbidden by the products in the cart
    		 */
    		$forbidden_methods = $helper->getForbiddenPaymentMethodsForCart();
    		$res = array();
	    	foreach ($methods as $method)
		    {
		    	if (! in_array($method->getCode(), $forbidden_methods)) $res[] = $method;
		    }
		    $methods = $res;
    	
    		/**
    		 * Only allow methods enabled for the current customer group
    		 */
    		$res = array();
    		$group_methods = $helper->getAllowedPaymentMethodsForCurrentGroup();
    		if (is_array($group_methods) && $group_methods)
    		{
	    		foreach ($methods as $method)
	    		{
	    			if (in_array($method->getCode(), $group_methods)) $res[] = $method;
	    		}
    		}
	    	$methods = $res;
    	}
    	
    	/**
    	 * @todo: add error message if no payment methods are available
    	 */
    	
		return $methods;
    }
}
