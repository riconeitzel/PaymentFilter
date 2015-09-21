<?php
/* @var $this Mage_Eav_Model_Entity_Setup */
$this->startSetup();

$this->addAttribute(
    'customer',
    'allowed_payment_methods',
    array(
        'position' => 160,
        'input' => 'multiselect',
        'type' => 'varchar',
        'label' => 'Allow explicitly payment methods for this customer',
        'source' => 'payfilter/config_source_payment_methods',
        'backend' => 'payfilter/entity_backend_payment_methods',
        'global' => Mage_Catalog_Model_Resource_Eav_Attribute::SCOPE_WEBSITE,
        'default' => '',
        'user_defined' => 1,
        'required' => 0,
    )
);
$attribute = Mage::getSingleton("eav/config")->getAttribute("customer", "allowed_payment_methods");

$attribute->setData("used_in_forms", array('adminhtml_customer'))->setData("is_system", 0);
$attribute->save();


$this->endSetup();
