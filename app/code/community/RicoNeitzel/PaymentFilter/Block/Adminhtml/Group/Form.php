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
 * Adminhtml extension customer groups edit form block
 *
 * @category   RicoNeitzel
 * @package    RicoNeitzel_PaymentFilter
 * @author     Vinai Kopp <vinai@netzarbeiter.com>
 */
class RicoNeitzel_PaymentFilter_Block_Adminhtml_Group_Form extends Mage_Adminhtml_Block_Customer_Group_Edit_Form
{
    /**
     * The store id to get the active payment methods for.
     *
     * @var int
     */
    protected $_paymentMethodConfigStoreId;

    /**
     * Set the default store code
     */
    protected function _construct()
    {
        parent::_construct();

        $this->_paymentMethodConfigStoreId = Mage::app()->getStore(Mage_Core_Model_Store::ADMIN_CODE)->getId();
    }

    /**
     * Return the store id to get the active payment methods for.
     *
     * @return int
     */
    protected function _getStoreId()
    {
        return $this->_paymentMethodConfigStoreId;
    }

    /**
     * Extend form for rendering the payment method multiselect
     *
     * @return Mage_Adminhtml_Block_Customer_Group_Edit_Form
     */
    protected function _prepareLayout()
    {
        /*
         * Remember the posted form value, because parent::_prepareLayout() might
         * set them to null after assigning to form.
         */
        if (Mage::helper('payfilter')->moduleActive()) {
            if (Mage::getSingleton('adminhtml/session')->getCustomerGroupData()) {
                $values = Mage::getSingleton('adminhtml/session')->getCustomerGroupData();
            } else {
                $values = Mage::registry('current_group')->getData();
            }
            $value = isset($values['allowed_payment_methods']) ? $values['allowed_payment_methods'] : array();
        }

        /*
         * Parent setup of the form
         */
        parent::_prepareLayout();

        /*
         * Add payment method multiselect and set value
         */
        if (Mage::helper('payfilter')->moduleActive()) {
            $form = $this->getForm();
            $fieldset = $form->addFieldset('payment_fieldset', array(
                'legend' => Mage::helper('payfilter')->__('Group Payment Methods')
            ));

            $fieldset->addField('payment_methods_posted', 'hidden', array(
                'name' => 'payment_methods_posted',
                'value' => '1',
            ));

            $fieldset->addField('payment_methods', 'multiselect', array(
                'name' => 'allowed_payment_methods',
                'label' => Mage::helper('payfilter')->__('Payment Methods'),
                'title' => Mage::helper('payfilter')->__('Payment Methods'),
                'class' => '',
                'required' => false,
                'values' => Mage::helper('payfilter')->getPaymentMethodOptions($this->_getStoreId()),
                'value' => $value,
                'after_element_html' => $this->_getPaymentComment()
            ));
        }

        return $this;
    }

    /**
     * Return the explanation for the payment methods multiselect as html
     *
     * @return string
     */
    protected function _getPaymentComment()
    {
        $html = '';
        $html .= $this->__(
            'To select multiple values, hold the Control-Key<br/>while clicking on the payment method names.'
        );
        return '<div>' . $html . '</div>';
    }
}
