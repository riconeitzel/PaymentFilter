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
 * @copyright  Copyright (c) 2009 Vinai Kopp http://netzarbeiter.com/
 * @license    http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */

/**
 * Customer Group Payment Methods Helper
 *
 * @category	RicoNeitzel
 * @package		RicoNeitzel_PaymentFilter
 * @author		Vinai Kopp <vinai@netzarbeiter.com>
 */
class RicoNeitzel_PaymentFilter_Helper_Data extends Mage_Core_Helper_Abstract
{	
	/**
	 * Debug log method
	 *
	 * @param mixed $var
	 */
	public function log($var)
	{
		$var = print_r($var, 1);
		if (strtoupper(substr(PHP_OS, 0, 3)) === 'WIN') $var = str_replace("\n", "\r\n", $var);
		Mage::log($var);
	}
	
	/**
	 * Fetch all configured payment methods for the given store (0 = global config scope) as an options array for select widgets
	 *
	 * @param integer $store_id
	 * @return array()
	 */
	public function getPaymentMethodOptions($store_id)
	{
		$methods = Mage::helper('payment')->getStoreMethods($store_id);
		$options = array();
		foreach ($methods as $method)
		{
			array_unshift($options, array(
				'value' => $method->getCode(),
				'label' => $method->getTitle(),
			));
		}
		return $options;
	}
	
	/**
	 * Return the forbidden payment method codes in an array for the current cart items.
	 * 
	 * @return array()
	 * @see Netzarbeiter_ProductPayments_Helper_Payment::getStoreMethods()
	 * @see Mage_Payment_Helper_Data::getStoreMethods()
	 */
	public function getForbiddenPaymentMethodsForCart()
	{
		$methods = array();
		$product_ids  = Mage::getSingleton('checkout/cart')->getQuoteProductIds();
		foreach ($product_ids as $pid)
		{
			$product = Mage::getModel('catalog/product')->setId($pid);
			$product_payment_methods = $this->getForbiddenPaymentMethodsFromProduct($product);
			
			if (! $product_payment_methods) continue;
			
			foreach ($product_payment_methods as $_method)
			{
				if (! in_array($_method, $methods)) $methods[] = $_method;
			}
		}
		
		return $methods;
	}
	
	/**
	 * Return the payment methods that are configured as forbidden for the given product
	 *
	 * @param Mage_Catalog_Model_Product $product
	 * @return array
	 */
	public function getForbiddenPaymentMethodsFromProduct(Mage_Catalog_Model_Product $product)
	{
		$product_payment_methods = $product->getProductPaymentMethods();
		if (! isset($product_payment_methods))
		{
			$product_payment_methods = $product->load($product->getId(), array('product_payment_methods'))
				->getProductPaymentMethods();
		}
		if (! isset($product_payment_methods)) {
			$product_payment_methods = array();
		}
		if (is_string($product_payment_methods)) $product_payment_methods = explode(',', $product_payment_methods);
		return $product_payment_methods;
	}
	
	/**
	 * Return the allowed payment method codes for the current customer group.
	 * If the customer isn't logged in, this method uses the NOT LOGGED IN customer
	 * group, and not the default customer grouop set in system > config > customer
	 * This is the reason this method should be used and not the current customer
	 * group from the customer/session.
	 * 
	 *
	 * @return array()
	 */
	public function getAllowedPaymentMethodsForCurrentGroup()
	{
		return $this->getCurrentCustomerGroup()->getAllowedPaymentMethods();
	}
	
	/**
	 * Return the current customer group. This will differ from the customer/session group
	 * returned if the customer is logged out, because here the NOT LOGGED IN group is returned
	 * instead of the default customer group from system > config > customer
	 *
	 * @return Mage_Customer_Model_Group
	 */
	public function getCurrentCustomerGroup()
	{
		$session = Mage::getSingleton('customer/session');
		if (! $session->isLoggedIn()) $customerGroupId = Mage_Customer_Model_Group::NOT_LOGGED_IN_ID;
		else $customerGroupId = $session->getCustomerGroupId();
		return Mage::getModel('customer/group')->load($customerGroupId);
	}
	
	/**
	 * Return the config value for the passed key (current store)
	 * 
	 * @param string $key
	 * @return string
	 */
	public function getConfig($key)
	{
		$path = 'checkout/payfilter/' . $key;
		return Mage::getStoreConfig($path, Mage::app()->getStore());
	}

	/**
	 * Check if the extension has been disabled in the system configuration
	 * 
	 * @return boolean
	 */
	public function moduleActive()
	{
		return ! (bool) $this->getConfig('disable_ext');
	}
	
	/**
	 * Return true if the method is called in the adminhtml interface
	 *
	 * @return boolean
	 */
	public function inAdmin()
	{
		return Mage::app()->getStore()->isAdmin();
	}
}
