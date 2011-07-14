<?php

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
	public function getStoreMethods($store=null, $quote=null)
	{
		$methods = parent::getStoreMethods($store, $quote);
		if (! Mage::app()->getStore()->isAdmin())
		{
			$tmp = array();

			foreach ($methods as $method)
			{
				if (in_array($method->getCode(), Mage::helper('payfilter')->getForbiddenPaymentMethodsForCart()))
				{
					continue;
				}
				if (! in_array($method->getCode(), Mage::helper('payfilter')->getAllowedPaymentMethodsForCurrentGroup()))
				{
					continue;
				}
				$tmp[] = $method;
			}
			$methods = $tmp;
		}
		return $methods;
	}
}