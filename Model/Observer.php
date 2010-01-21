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
 * @category   RicoNeitzel
 * @package    RicoNeitzel_PaymentFilter
 * @copyright  Copyright (c) 2009 Vinai Kopp http://netzarbeiter.com/
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
	 * Save the allowed_payment_methods values set in the adminhtml interface.
	 * Before Magento 1.3.1 this event needed to be used.
	 *
	 * @param Varien_Event $observer
	 */
	public function coreAbstractSaveBeforeEvent($observer)
	{
		if (version_compare(Mage::getVersion(), '1.3.1', '>=')) return;
		if (! Mage::helper('payfilter')->moduleActive()) return;
		
		$object = $observer->getObject();
		if ($object instanceof Mage_Customer_Model_Group)
		{
			$this->_setPaymentFilterOnGroup($object);
		}
	}

	/**
	 * Save the allowed_payment_methods values set in the adminhtml interface.
	 * This event is used sind Magento 1.3.1
	 *
	 * @param Varien_Event $observer
	 */
	public function customerGroupSaveBeforeEvent($observer)
	{
		if (! Mage::helper('payfilter')->moduleActive()) return;
		$this->_setPaymentFilterOnGroup($observer->getObject());
	}

	/**
	 * This method sets the allowed payment methods on the customer group
	 * 
	 * @param Mage_Customer_Model_Group $group
	 */
	protected function _setPaymentFilterOnGroup(Mage_Customer_Model_Group $group)
	{
		$allowed_payment_methods = Mage::app()->getRequest()->getParam('allowed_payment_methods');
		if (isset($allowed_payment_methods))
		{
			$group->setAllowedPaymentMethods($allowed_payment_methods);
		}
	}
}

