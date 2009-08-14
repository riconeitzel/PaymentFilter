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
class RicoNeitzel_PaymentFilter_Model_Config_Source_Payment_Methods
	extends Mage_Eav_Model_Entity_Attribute_Source_Abstract
{
    protected $_options;
    
    protected $_scope = Mage_Catalog_Model_Resource_Eav_Attribute::SCOPE_GLOBAL;
    

    public function toOptionArray()
    {
        if (!$this->_options) {
        	$this->_options = Mage::helper('payfilter')->getPaymentMethodOptions($this->_scope);
        }
        return $this->_options;
    }
    
    public function getAllOptions()
    {
    	return $this->toOptionArray();
    }
}