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
 * @copyright  Copyright (c) 2010 Vinai Kopp http://netzarbeiter.com/
 * @license    http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */
 
/**
 * Observer for the customer group payment methods. Save the adminhtml settings.
 *
 * @category   RicoNeitzel
 * @package    RicoNeitzel_PaymentFilter
 * @author     Vinai Kopp <vinai@netzarbeiter.com>
 */
class RicoNeitzel_PaymentFilter_Model_Observer extends Mage_Core_Model_Abstract
{
	/**
	 * Unserialize the methods array.
	 * Called in adminhtml and frontend area.
	 *
	 * @param Varien_Event_Observer $observer
	 * @return null
	 */
	public function customerGroupLoadAfter($observer)
	{
		Mage::log(__METHOD__);
		if (! Mage::helper('payfilter')->moduleActive()) return;
		$group = $observer->getEvent()->getObject();

		$val = unserialize($group->getAllowedPaymentMethods());
		$group->setAllowedPaymentMethods($val);
	}

	/**
	 * Save the allowed_payment_methods values set in the adminhtml interface.
	 * Seralize the allowed methods array.
	 * Called in adminhtml and frontend area
	 *
	 * @param Varien_Event_Observer $observer
	 * @return null
	 */
	public function customerGroupSaveBefore($observer)
	{
		Mage::log(__METHOD__);
		if (! Mage::helper('payfilter')->moduleActive()) return;
		$group = $observer->getEvent()->getObject();

		/*
		 * Update values
		 */
		if (Mage::app()->getStore()->isAdmin())
		{
			$this->_setPaymentFilterOnGroup($group);
		}

		/*
		 * Serialize array for saving
		 */
		$val = serialize($group->getAllowedPaymentMethods());
		$group->setAllowedPaymentMethods($val);
	}

	/**
	 * Set the posted allowed payment methods on the customer group model.
	 * 
	 * @param Mage_Customer_Model_Group $group
	 * @return null
	 */
	protected function _setPaymentFilterOnGroup(Mage_Customer_Model_Group $group)
	{
		$allowedPaymentMethods = Mage::app()->getRequest()->getParam('allowed_payment_methods');
		if (isset($allowedPaymentMethods))
		{
			$group->setAllowedPaymentMethods($allowedPaymentMethods);
		}
	}

	/**
	 * Check if a payment method is allowed.
	 *
	 * @param Varien_Event_Observer $observer
	 * @return null
	 */
	public function paymentMethodIsActive($observer)
	{
		Mage::log(__METHOD__);
		if (! Mage::helper('payfilter')->moduleActive()) return;

		$checkResult = $observer->getEvent()->getResult();
		$method = $observer->getEvent()->getMethodInstance();

		/*
		 * Check if the method is forbidden by products in the cart
		 */
		if ($checkResult->isAvailable)
		{
			if (in_array($method->getCode(), Mage::helper('payfilter')->getForbiddenPaymentMethodsForCart()))
			{
				$checkResult->isAvailable = false;
			}
		}
		
		/*
		 * Check if the method is forbidden for the customers group
		 */
		if ($checkResult->isAvailable)
		{
			if (! in_array($method->getCode(), $helper->getAllowedPaymentMethodsForCurrentGroup()))
			{
				$checkResult->isAvailable = false;
			}
		}
	}
}

