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
 * @author     Lee Saferite
 * @license    http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */
/**
 * This allows us to lookup table names using the module/name method.
 * Thanks to Lee Saferite for this code snipplet!
 */
class RicoNeitzel_PaymentFilter_Model_Entity_Setup extends Mage_Eav_Model_Entity_Setup
{
    protected $resourcePreviousState = null;

    public function startSetup()
    {
    	if (version_compare(Mage::getVersion(), '1.2', '<'))
    	{
	        $this->resourcePreviousState = Mage::registry('resource');
	        Mage::unregister('resource');
	        Mage::register('resource', true);
    	}
        return parent::startSetup();
    }

    public function endSetup()
    {
    	if (version_compare(Mage::getVersion(), '1.2', '<'))
    	{
	        Mage::unregister('resource');
	        if($this->resourcePreviousState !== null)
	        {
	            Mage::register('resource', $this->resourcePreviousState);
	        }
    	}
        return parent::endSetup();
    }

    public function getDefaultEntities()
    {
        return array();
    }
}
