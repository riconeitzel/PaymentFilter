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
 * @copyright  Copyright (c) 2011 Vinai Kopp http://netzarbeiter.com/
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
     * Override the payment helper for magento versions < 1.4
     *
     * @param Varien_Event_Observer $observer
     */
    public function controllerFrontInitBefore($observer)
    {
        if (!Mage::helper('payfilter')->moduleActive()) {
            return;
        }

        if (version_compare(Mage::getVersion(), '1.4.0', '<')) {
            Mage::getConfig()->setNode(
                'global/helpers/payment/rewrite/data', 'RicoNeitzel_PaymentFilter_Helper_Payment_Data'
            );
        }
    }

    /**
     * Unserialize the methods array.
     * Called in adminhtml and frontend area.
     *
     * @param Varien_Event_Observer $observer
     * @return null
     */
    public function customerGroupLoadAfter($observer)
    {
        if (!Mage::helper('payfilter')->moduleActive()) {
            return;
        }

        $group = $observer->getEvent()->getObject();

        if (is_string($group->getAllowedPaymentMethods())) {
            $val = unserialize($group->getAllowedPaymentMethods());
            $group->setAllowedPaymentMethods($val);
        }
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
        if (!Mage::helper('payfilter')->moduleActive()) {
            return;
        }
        $group = $observer->getEvent()->getObject();

        /*
         * Update values
         */
        if (Mage::app()->getStore()->isAdmin()) {
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
        if (Mage::app()->getRequest()->getParam('payment_methods_posted')) {
            $allowedPaymentMethds = Mage::app()->getRequest()->getParam('allowed_payment_methods');
            $group->setAllowedPaymentMethods($allowedPaymentMethds);
        }
    }

    /**
     * Initialize the payment methods attribute value with an array if it is
     * empty.
     * If we don' do this we cannot deselect all payment methods for a product.
     *
     * @param Varien_Event_Observer $observer
     */
    public function catalogProductSaveBefore($observer)
    {
        if (!Mage::helper('payfilter')->moduleActive()) {
            return;
        }

        $product = $observer->getEvent()->getProduct();
        $params = Mage::app()->getRequest()->getParam('product');
        if (!isset($params['product_payment_methods'])) {
            $product->setProductPaymentMethods(array());
        }
    }

    /**
     * Check if a payment method is allowed.
     * Only triggered in Magento >= 1.4
     *
     * @param Varien_Event_Observer $observer
     * @return null
     */
    public function paymentMethodIsActive($observer)
    {
        if (!Mage::helper('payfilter')->moduleActive()) {
            return;
        }

        $checkResult = $observer->getEvent()->getResult();
        $method = $observer->getEvent()->getMethodInstance();
        $quote = $observer->getEvent()->getQuote();

        /*
         * Check if the method is forbidden by products in the cart
         */
        if ($checkResult->isAvailable) {
            if (in_array($method->getCode(), Mage::helper('payfilter')->getForbiddenPaymentMethodsForCart($quote))) {
                $checkResult->isAvailable = false;
            }
        }

        /*
         * Check if the method is forbidden for the customers group
         */
        if ($checkResult->isAvailable) {
            $allowedPaymentMethodsForGroup = Mage::helper('payfilter')->getAllowedPaymentMethodsForCurrentGroup();
            $allowedPaymentMethodsForCustomer = Mage::helper('payfilter')->getAllowedPaymentMethodsForCustomer();
            $allowedPaymentMethods = array_merge($allowedPaymentMethodsForCustomer, $allowedPaymentMethodsForGroup);
            if (!in_array($method->getCode(), $allowedPaymentMethods)) {
                $checkResult->isAvailable = false;
            }
        }
    }
}

