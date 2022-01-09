<?php

require_once __DIR__ . '/vendor/autoload.php';

const CARTTHROB_SHIPPING_FEDEX_NAME = 'CartThrob FedEx Shipping';
const CARTTHROB_SHIPPING_FEDEX_VERSION = '1.0.0';
const CARTTHROB_SHIPPING_FEDEX_DESC = 'FedEx Shipping Integration for CartThrob';

return [
    'author' => 'Foster Made',
    'author_url' => 'https://fostermade.co/',
    'name' => CARTTHROB_SHIPPING_FEDEX_NAME,
    'description' => CARTTHROB_SHIPPING_FEDEX_DESC,
    'version' => CARTTHROB_SHIPPING_FEDEX_VERSION,
    'namespace' => 'CartThrob\ShippingFedex',
    'settings_exist' => false,
];