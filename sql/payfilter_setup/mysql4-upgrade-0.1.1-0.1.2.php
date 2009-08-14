<?php

$this->startSetup();

$this->updateAttribute('catalog_product', 'product_payment_methods', 'is_filterable_in_search', '0');

$this->endSetup();
