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
 * Customer Group Payment Methods Helper
 *
 * @category    RicoNeitzel
 * @package        RicoNeitzel_PaymentFilter
 * @author        Vinai Kopp <vinai@netzarbeiter.com>
 */
class RicoNeitzel_PaymentFilter_Helper_Data extends Mage_Core_Helper_Abstract
{
    const EXPLANATION_URL = 'https://github.com/riconeitzel/PaymentFilter/issues/19';

    /**
     * @var string[]
     */
    protected $_forbiddenPaymentMethodsForCart;

    /**
     * @var Mage_Customer_Model_Group
     */
    private $_customerGroup;

    /**
     * @var Mage_Customer_Model_Customer
     */
    private $_customer;

    /** @var  Mage_Sales_Model_Quote */
    protected $_quote;

    /**
     * Fetch all configured payment methods for the given store (0 = global
     * config scope) as an options array for select widgets.
     *
     *
     * @param integer $storeId
     * @param Mage_Sales_Model_Quote $quote
     * @return array
     */
    public function getPaymentMethodOptions($storeId, $quote = null)
    {
        return Mage::helper('payment')->getPaymentMethodList(true, true, true);
    }

    /**
     * Return the forbidden payment method codes in an array for the current cart items.
     *
     * @param Mage_Sales_Model_Quote|null $quote The current quote
     *
     * @return array
     * @see Netzarbeiter_ProductPayments_Helper_Payment::getStoreMethods()
     * @see Mage_Payment_Helper_Data::getStoreMethods()
     */
    public function getForbiddenPaymentMethodsForCart(Mage_Sales_Model_Quote $quote=null)
    {
        if( is_null($quote) ) {
            $quote = $this->getCurrentQuote();
        }
        if (null === $this->_forbiddenPaymentMethodsForCart) {
            $methods = array();
            $items = $quote->getAllItems();
            foreach ($items as $item) {
                $productPaymentMethds = $this->getForbiddenPaymentMethodsFromProduct($item->getProduct(), $quote);

                if (!$productPaymentMethds) {
                    continue;
                }

                foreach ($productPaymentMethds as $method) {
                    if (!in_array($method, $methods)) {
                        $methods[] = $method;
                    }
                }
            }
            $this->_forbiddenPaymentMethodsForCart = $methods;
        }

        return $this->_forbiddenPaymentMethodsForCart;
    }

    /**
     * Return the payment methods that are configured as forbidden for the given product
     *
     * @param Mage_Catalog_Model_Product $product
     * @return array
     */
    public function getForbiddenPaymentMethodsFromProduct(Mage_Catalog_Model_Product $product)
    {
        $productPaymentMethds = $product->getProductPaymentMethods();

        if (!is_array($productPaymentMethds)) {
            $productPaymentMethds = explode(',', (string)$productPaymentMethds);
        }

        return $productPaymentMethds;
    }

    /**
     * Return the allowed payment method codes for the current customer group.
     *
     * @return array
     */
    public function getAllowedPaymentMethodsForCurrentGroup()
    {
        return (array)$this->getCurrentCustomerGroup()->getAllowedPaymentMethods();
    }

    /**
     * Return the allowed payment method codes for the current customer
     *
     * @return array
     */
    public function getAllowedPaymentMethodsForCustomer()
    {
        return (array)$this->getCurrentCustomer()->getAllowedPaymentMethods();
    }

    /**
     * Return the current customer group. If the customer is not logged in, the NOT LOGGED IN group is returned.
     * This is different from the default group configured in system > config > customer.
     *
     * @return Mage_Customer_Model_Group
     */
    public function getCurrentCustomerGroup()
    {
        if (!isset($this->_customerGroup)) {
            $groupId = Mage::getSingleton('customer/session')->getCustomerGroupId();
            $this->_customerGroup = Mage::getModel('customer/group')->load($groupId);
        }

        return $this->_customerGroup;
    }

    /**
     * Return the current customer, if the customer is logged in
     *
     * @return Mage_Customer_Model_Customer
     */
    public function getCurrentCustomer()
    {
        if (!isset($this->_customer)) {
            $this->_customer = Mage::getSingleton('customer/session')->getCustomer();
        }

        return $this->_customer;
    }

    /**
     * Return the current quote based on the customer session and log a
     * self-explanatory warning.
     *
     * @return Mage_Sales_Model_Quote
     */
    public function getCurrentQuote()
    {
        Mage::log(
            sprintf(
                '%s: Loading quote from session. If this line floods the logs
                 we are in _afterLoad of a cart being loaded. See: %s',
                __CLASS__, self::EXPLANATION_URL
            ), null, Zend_Log::NOTICE, true
        );
        if( !isset($this->_quote) )
            $this->_quote = Mage::getSingleton('checkout/cart')->getQuote();

        return $this->_quote;
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
        return !(bool)$this->getConfig('disable_ext');
    }

}
