<?php

class RicoNeitzel_PaymentFilter_Model_Payment_Method_Free extends Mage_Payment_Model_Method_Free
{
	/**
	 * Temporary fix
	 *
	 * @param Mage_Sales_Model_Quote $quote
	 * @return bool
	 */
	public function isAvailable($quote = null)
	{
		if (is_null($quote))
		{
			$quote = Mage::getModel('sales/quote')->setGrandTotal(0);
		}
		return parent::isAvailable($quote);
	}

}
