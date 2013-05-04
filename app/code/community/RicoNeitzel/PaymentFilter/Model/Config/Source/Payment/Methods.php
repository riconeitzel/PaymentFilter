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

class RicoNeitzel_PaymentFilter_Model_Config_Source_Payment_Methods
    extends Mage_Eav_Model_Entity_Attribute_Source_Abstract
{
    protected $_options;

    protected $_storeCode = Mage_Core_Model_Store::ADMIN_CODE;

    public function toOptionArray()
    {
        if (!$this->_options) {
            $store = Mage::app()->getStore($this->_storeCode);
            $this->_options = Mage::helper('payfilter')->getPaymentMethodOptions($store->getId());
        }
        return $this->_options;
    }

    public function getAllOptions()
    {
        return $this->toOptionArray();
    }

    /**
     * Get a text for option value
     *
     * @param string|integer $value
     * @return string
     */
    public function getOptionText($value)
    {
        $isMultiple = false;
        if (strpos($value, ',')) {
            $isMultiple = true;
            $value = explode(',', $value);
        }

        $options = $this->getAllOptions();

        if ($isMultiple) {
            $values = array();
            foreach ($options as $item) {
                if (in_array($item['value'], $value)) {
                    $values[] = $item['label'];
                }
            }
            return $values;
        } else {
            foreach ($options as $item) {
                if ($item['value'] == $value) {
                    return $item['label'];
                }
            }
            return false;
        }
    }

    /**
     * Retrieve Column(s) for Flat Catalog
     *
     * @return array
     */
    public function getFlatColums()
    {
        $columns = array();
        $columns[$this->getAttribute()->getAttributeCode()] = array(
            'type' => 'varchar(255)',
            'unsigned' => false,
            'is_null' => true,
            'default' => null,
            'extra' => null
        );

        return $columns;
    }

    /**
     * Retrieve Indexes for Flat Catalog
     *
     * @return array
     */
    public function getFlatIndexes()
    {
        $indexes = array();

        $index = 'IDX_' . strtoupper($this->getAttribute()->getAttributeCode());
        $indexes[$index] = array(
            'type' => 'index',
            'fields' => array($this->getAttribute()->getAttributeCode())
        );

        //$sortable = $this->getAttribute()->getUsedForSortBy();

        return $indexes;
    }

    /**
     * Retrieve Select For Flat Attribute update
     *
     * @param int $store
     * @return Varien_Db_Select|null
     */
    public function getFlatUpdateSelect($store)
    {
        return Mage::getResourceModel('eav/entity_attribute_option')
            ->getFlatUpdateSelect($this->getAttribute(), $store);
    }
}