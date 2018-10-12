# This is the Readme for the Magento extension PaymentFilter.

This module enables you to select which payment methods are available for
every customer and customer group. Also, Payment methods can be disabled for specific
products. A customer can only use the payment methods during checkout
available to his (customer group OR himself) AND not disabled for the products in the
shopping cart.

This extension supports Magento 1.3 through 1.9. 

It was created by Vinai Kopp http://netzarbeiter.com/ for Rico Neitzel
(http://buro71a.de/).

After installing this extension you have to configure the payment
methods available to each customer group. You do that in the admin interface
under Customers > Customer Groups. The default is NONE, so if you don't do that NO
payment methods will be available and customers will not be able to check out.

The default for products is to allow ALL payment methods, so you only have to configure
the payment methods available to every group. Only change the product level payment method
configuration if you want to disable one or more for a specific products.

# Disable Extension

The whole extension can be disabled in "System > Config > Sales > Checkout" on a
Global or Website scope.

# Uninstall

If you ever uninstall the extension (I don't hope so ;)) your site will be broken, because
Magento doesn't support database updates on uninstalls to remove attributes.

To fix the error, execute the following SQL:

    DELETE FROM `eav_attribute` WHERE attribute_code = 'product_payment_methods';
    DELETE FROM `core_resource` WHERE code = 'payfilter_setup';
    ALTER TABLE `customer_group` DROP `allowed_payment_methods`;

**IMPORTANT!** Then clear the magento cache.

This module replaces the extension RicoNeitzel_CGroupPayments that only included
the option to filter by customer groups. That module is no longer continued.
Please install this module instead.

# Maintainer

If you have ideas for improvements or find bugs, please send them to info@buro71a.de,
with RicoNeitzel_PaymentFilter as part of the subject line.

# License

This module is licensed under OSL-3.0
