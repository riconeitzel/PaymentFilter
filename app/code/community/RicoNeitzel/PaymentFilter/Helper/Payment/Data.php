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

class RicoNeitzel_PaymentFilter_Helper_Payment_Data extends Mage_Payment_Helper_Data
{
    /**
     * Retrieve available payment methods for store, filtered according to the payment filter configuration.
     * This is only used in magento < 1.4. In newer versions the helper isn't rewritten.
     *
     * @param Mage_Core_Model_Store $store
     * @param Mage_Sales_Model_Quote $quote
     * @return array
     */
    public function getStoreMethods($store = null, $quote = null)
    {
        $methods = parent::getStoreMethods($store, $quote);
        if (!Mage::app()->getStore()->isAdmin()) {
            $tmp = array();

            foreach ($methods as $method) {
                if (
                    in_array(
                        $method->getCode(),
                        Mage::helper('payfilter')->getForbiddenPaymentMethodsForCart())
                ) {
                    continue;
                }
                if (
                    !in_array(
                        $method->getCode(),
                        Mage::helper('payfilter')->getAllowedPaymentMethodsForCurrentGroup())
                ) {
                    continue;
                }
                $tmp[] = $method;
            }
            $methods = $tmp;
        }
        return $methods;
    }
}