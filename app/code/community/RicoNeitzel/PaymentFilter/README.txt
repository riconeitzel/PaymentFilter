
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

This is the Readme for the Magento extension PaymentFilter.

This module enables you to select which payment methods are available for
every customer group. Also, Payment methods can be disabled for specific
products. A customer can only use the payment methods during checkout
available to his customer group AND not disabled for the products in the
shopping cart.

This extension supports Magento 1.3, Magento 1.4 and Magento 1.5.


It was created by Vinai Kopp http://netzarbeiter.com/ for Rico Neitzel
(http://n-punkt.de/).

After installing this extension you have to configure the payment
methods available to each customer group. You do that in the admin interface
under Customers > Customer Groups. The default is NONE, so if you don't do that NO
payment methods will be available and customers will not be able to check out.

The default for products is to allow ALL payment methods, so you only have to configure
the payment methods available to every group. Only change the product level payment method
configuration if you want to disable one or more for a specific products.

The whole extension can be disabled in "System > Config > Sales > Checkout" on a
Global or Website scope.

If you ever uninstall the extension (I don't hope so ;)) your site will be broken, because
Magento doesn't support database updates on uninstalls to remove attributes.

To fix the error, execute the following SQL:

   DELETE FROM `eav_attribute` WHERE attribute_code = 'product_payment_methods';
   DELETE FROM `core_resource` WHERE code = 'payfilter_setup';
   ALTER TABLE `customer_group` DROP `allowed_payment_methods';

IMPORTANT! Then clear the magento cache.

This module replaces the extension RicoNeitzel_CGroupPayments that only included
the option to filter by customer groups. That module is no longer continued.
Please install this module instead.

If you have ideas for improvements or find bugs, please send them to vinai@netzarbeiter.com,
with RicoNeitzel_PaymentFilter as part of the subject line.
