<?php

if (!defined('CARTTHROB_PATH')) {
    exit('No direct script access allowed');
}

use CartThrob\Plugins\Exceptions\ShippingRateException;
use CartThrob\Plugins\Shipping\ShippingPlugin;
use FedEx\RateService\ComplexType\Money as FedexMoney;
use FedEx\RateService\ComplexType\Payor;
use FedEx\RateService\ComplexType\RateRequest;
use FedEx\RateService\ComplexType\RequestedPackageLineItem;
use FedEx\RateService\Request;
use FedEx\RateService\SimpleType\PaymentType;
use FedEx\RateService\SimpleType\RateRequestType;
use Illuminate\Support\Collection;
use Money\Money;

class Cartthrob_shipping_fedex extends ShippingPlugin
{
    // ExpressionEngine Properties
    public $title = 'CartThrob - Shipping - FedEx Live Rates';
    public string $version = '2.0.0';
    public string $description = 'Provide calculated shipping rates for FedEx';
    public string $settings_exist = 'y';

    // CartThrob Properties
    public $short_title = 'fedex_short_title';
    public $overview = 'fedex_overview';

    public $settings = [
        [
            'name' => 'fedex_api_key',
            'short_name' => 'fedex_api_key',
            'type' => 'text',
            'default' => '',
        ],
        [
            'name' => 'fedex_account_number',
            'short_name' => 'fedex_account_number',
            'type' => 'text',
            'default' => '',
        ],
        [
            'name' => 'fedex_meter_number',
            'short_name' => 'fedex_meter_number',
            'type' => 'text',
            'default' => '',
        ],
        [
            'name' => 'fedex_password',
            'short_name' => 'fedex_password',
            'type' => 'text',
            'default' => '',
        ],
        [
            'name' => 'fedex_mode',
            'short_name' => 'fedex_mode',
            'default' => 'dev',
            'type' => 'radio',
            'options' => [
                'dev' => 'Dev',
                'live' => 'Live',
            ],
        ],
        [
            'name' => 'fedex_length_code',
            'short_name' => 'fedex_length_code',
            'default' => 'IN',
            'type' => 'radio',
            'options' => [
                'IN' => 'Inches',
                'CM' => 'Centimeters',
            ],
        ],
        [
            'name' => 'fedex_weight_code',
            'short_name' => 'fedex_weight_code',
            'default' => 'LB',
            'type' => 'radio',
            'options' => [
                'LB' => 'Pounds',
                'KG' => 'Kilograms',
            ],
        ],
        [
            'name' => 'fedex_rate_chart',
            'short_name' => 'fedex_rate_chart',
            'default' => 'REGULAR_PICKUP',
            'type' => 'radio',
            'options' => [
                'REGULAR_PICKUP' => 'fedex_regular_pickup',
                'REQUEST_COURIER' => 'fedex_request_courier',
                'BUSINESS_SERVICE_CENTER' => 'fedex_business_service_center',
                'STATION' => 'fedex_station',
            ],
        ],
        [
            'name' => 'fedex_origination_address',
            'short_name' => 'fedex_origination_address',
            'type' => 'text',
            'default' => '',
        ],
        [
            'name' => 'fedex_origination_address2',
            'short_name' => 'fedex_origination_address2',
            'type' => 'text',
            'default' => '',
        ],
        [
            'name' => 'fedex_origination_city',
            'short_name' => 'fedex_origination_city',
            'type' => 'text',
            'default' => '',
        ],
        [
            'name' => 'fedex_origination_state',
            'short_name' => 'fedex_origination_state',
            'type' => 'text',
            'default' => '',
//            'options' => [
//
//            ] // ee()->locales->states($this->getSetting('fedex_origination_country_code'))
        ],
        [
            'name' => 'fedex_origination_zip',
            'short_name' => 'fedex_origination_zip',
            'type' => 'text',
            'default' => '',
        ],
        [
            'name' => 'fedex_origination_country_code',
            'short_name' => 'fedex_origination_country_code',
            'type' => 'select',
            'default' => 'USA',
            'options' => [
                'CAN' => 'Canada',
                'USA' => 'United States',
            ],
        ],
        [
            'name' => 'fedex_product_id',
            'short_name' => 'fedex_product_id',
            'type' => 'select',
            'default' => 'GROUND_HOME_DELIVERY',
            'options' => [
                'FEDEX_GROUND' => 'fedex_ground',
                'PRIORITY_OVERNIGHT' => 'fedex_priority_overnight',
                'STANDARD_OVERNIGHT' => 'fedex_standard_overnight',
                'FEDEX_2_DAY' => 'fedex_2_day',
                'FEDEX_EXPRESS_SAVER' => 'fedex_express_saver',
                'FIRST_OVERNIGHT' => 'fedex_first_overnight',
                'GROUND_HOME_DELIVERY' => 'fedex_ground_home_delivery',
                'INTERNATIONAL_ECONOMY' => 'fedex_international_economy',
                'INTERNATIONAL_FIRST' => 'fedex_international_first',
                'INTERNATIONAL_GROUND' => 'fedex_international_ground',
                'INTERNATIONAL_PRIORITY' => 'fedex_international_priority',
                'EUROPE_FIRST_INTERNATIONAL_PRIORITY' => 'fedex_europe_first_international_priority',
            ],
        ],
        [
            'name' => 'fedex_container',
            'short_name' => 'fedex_container',
            'type' => 'select',
            'default' => 'YOUR_PACKAGING',
            'options' => [
                'YOUR_PACKAGING' => 'fedex_package',
                'FEDEX_BOX' => 'fedex_box',
                'FEDEX_TUBE' => 'fedex_tube',
                'FEDEX_PAK' => 'fedex_pak',
                'FEDEX_25KG_BOX' => 'fedex_25kg_box',
                'FEDEX_10KG_BOX' => 'fedex_10kg_box',
                'FEDEX_ENVELOPE' => 'fedex_envelope',
            ],
        ],
        [
            'name' => 'fedex_insurance_default',
            'short_name' => 'fedex_insurance_default',
            'type' => 'text',
            'default' => '100',
        ],
        [
            'name' => 'fedex_insurance_currency',
            'short_name' => 'fedex_insurance_currency',
            'type' => 'text',
            'default' => 'USD',
        ],
        [
            'name' => 'fedex_origination_res_com',
            'short_name' => 'fedex_origination_res_com',
            'type' => 'radio',
            'default' => 'RES',
            'options' => [
                'RES' => 'fedex_res',
                'COM' => 'fedex_com',
            ],
        ],
        [
            'name' => 'fedex_destination_res_com',
            'short_name' => 'fedex_destination_res_com',
            'type' => 'radio',
            'default' => 'RES',
            'options' => [
                'RES' => 'fedex_res',
                'COM' => 'fedex_com',
            ],
        ],
        [
            'name' => 'fedex_def_length',
            'short_name' => 'fedex_def_length',
            'type' => 'text',
            'default' => 15,
        ],
        [
            'name' => 'fedex_def_width',
            'short_name' => 'fedex_def_width',
            'type' => 'text',
            'default' => 15,
        ],
        [
            'name' => 'fedex_def_height',
            'short_name' => 'fedex_def_height',
            'type' => 'text',
            'default' => 15,
        ],
        [
            'name' => 'fedex_sp_ancillary_services',
            'short_name' => 'fedex_sp_ancillary_services',
            'type' => 'select',
            'default' => '',
            'options' => [
                '' => 'fedex_none',
                'ADDRESS_CORRECTION' => 'fedex_address_correction',
                'CARRIER_LEAVE_IF_NO_RESPONSE' => 'fedex_carrier_leave_if_no_response',
                'CHANGE_SERVICE' => 'fedex_change_service',
                'FORWARDING_SERVICE' => 'fedex_forwarding_service',
                'RETURN_SERVICE' => 'fedex_return_service',
            ],
        ],
        [
            'name' => 'fedex_sp_indicia',
            'short_name' => 'fedex_sp_indicia',
            'type' => 'select',
            'default' => '',
            'options' => [
                '' => 'fedex_none',
                'MEDIA_MAIL' => 'fedex_media_mail',
                'PARCEL_RETURN' => 'fedex_parcel_return',
                'PARCEL_SELECT' => 'fedex_parcel_select',
                'PRESORTED_BOUND_PRINTED_MATTER' => 'fedex_presorted_bound_printed_matter',
                'PRESORTED_STANDARD' => 'fedex_presorted_standard',
            ],
        ],
        [
            'name' => 'fedex_ground',
            'short_name' => 'fedex_ground',
            'type' => 'radio',
            'default' => 'y',
            'options' => [
                'n' => 'No',
                'y' => 'Yes',
            ],
        ],
        [
            'name' => 'fedex_priority_overnight',
            'short_name' => 'fedex_priority_overnight',
            'type' => 'radio',
            'default' => 'n',
            'options' => [
                'n' => 'No',
                'y' => 'Yes',
            ],
        ],
        [
            'name' => 'fedex_standard_overnight',
            'short_name' => 'fedex_standard_overnight',
            'type' => 'radio',
            'default' => 'y',
            'options' => [
                'n' => 'No',
                'y' => 'Yes',
            ],
        ],
        [
            'name' => 'fedex_2_day',
            'short_name' => 'fedex_2_day',
            'type' => 'radio',
            'default' => 'y',
            'options' => [
                'n' => 'No',
                'y' => 'Yes',
            ],
        ],
        [
            'name' => 'fedex_express_saver',
            'short_name' => 'fedex_express_saver',
            'type' => 'radio',
            'default' => 'n',
            'options' => [
                'n' => 'No',
                'y' => 'Yes',
            ],
        ],
        [
            'name' => 'fedex_first_overnight',
            'short_name' => 'fedex_first_overnight',
            'type' => 'radio',
            'default' => 'n',
            'options' => [
                'n' => 'No',
                'y' => 'Yes',
            ],
        ],
        [
            'name' => 'fedex_ground_home_delivery',
            'short_name' => 'fedex_ground_home_delivery',
            'type' => 'radio',
            'default' => 'y',
            'options' => [
                'n' => 'No',
                'y' => 'Yes',
            ],
        ],
        [
            'name' => 'fedex_international_economy',
            'short_name' => 'fedex_international_economy',
            'type' => 'radio',
            'default' => 'n',
            'options' => [
                'n' => 'No',
                'y' => 'Yes',
            ],
        ],
        [
            'name' => 'fedex_international_first',
            'short_name' => 'fedex_international_first',
            'type' => 'radio',
            'default' => 'n',
            'options' => [
                'n' => 'No',
                'y' => 'Yes',
            ],
        ],
        [
            'name' => 'fedex_international_ground',
            'short_name' => 'fedex_international_ground',
            'type' => 'radio',
            'default' => 'n',
            'options' => [
                'n' => 'No',
                'y' => 'Yes',
            ],
        ],
        [
            'name' => 'fedex_international_priority',
            'short_name' => 'fedex_international_priority',
            'type' => 'radio',
            'default' => 'n',
            'options' => [
                'n' => 'No',
                'y' => 'Yes',
            ],
        ],
        [
            'name' => 'fedex_europe_first_international_priority',
            'short_name' => 'fedex_europe_first_international_priority',
            'type' => 'radio',
            'default' => 'n',
            'options' => [
                'n' => 'No',
                'y' => 'Yes',
            ],
        ],
    ];

    // Internal Properties
    private array $shipping_methods = [
        'FEDEX_GROUND' => 'Ground',
        'PRIORITY_OVERNIGHT' => 'Priority Overnight',
        'STANDARD_OVERNIGHT' => 'Standard Overnight',
        'FEDEX_2_DAY' => '2 Day',
        'FEDEX_EXPRESS_SAVER' => 'Express Saver',
        'FIRST_OVERNIGHT' => 'First Overnight',
        'GROUND_HOME_DELIVERY' => 'Ground Home Delivery',
        'INTERNATIONAL_ECONOMY' => 'International Economy',
        'INTERNATIONAL_FIRST' => 'International First',
        'INTERNATIONAL_GROUND' => 'International Ground',
        'INTERNATIONAL_PRIORITY' => 'International Priority',
        'EUROPE_FIRST_INTERNATIONAL_PRIORITY' => 'Europe First International Priority',
    ];

    public function settings($key, $default = null): array
    {
        return $this->settings;
    }

    /**
     * @throws ShippingRateException
     */
    public function getLiveRates(array $items): array
    {
        if (!$this->configIsSetup()) {
            throw new ShippingRateException(ee()->lang->line('shipping_settings_not_configured'));
        }

        $shipping = [];
        $data = $this->makeRequest($items);

        foreach ($data['option_value'] as $key => $value) {
            if (bool_string($this->getSetting(strtolower('fedex_' . $value))) == false) {
                continue;
            }

            $shipping['price'][$key] = $data['price'][$key];
            $shipping['option_value'][$key] = $data['option_value'][$key];
            $shipping['option_name'][$key] = $data['option_name'][$key];
        }

        if (count($shipping) <= 0) {
            throw new ShippingRateException(lang('fedex_not_configured'));
        }

        return $shipping;
    }

    /**
     * @throws ShippingRateException
     */
    public function rate(Cartthrob_cart $cart): Money
    {
        $shippingData = $cart->custom_data($this->getBaseName());

        if ($cart->custom_data('shipping_requires_update') || empty($shippingData['option_value']) || empty($shippingData['price'])) {
            $shippingData = $this->getLiveRates($cart->items());
            $cart->set_custom_data($this->getBaseName(), $shippingData);
            $cart->hash($rehash = true);
        }

        $shippingOption = $this->setShippingOption($cart, $shippingData);

        return $this->prepareMoney($shippingData, $shippingOption);
    }

    /**
     * @param $notifications
     * @return string
     */
    public function buildErrors($notifications)
    {
        $errors = '';

        if (!empty($notifications->Severity)) {
            if (in_array($notifications->Severity, ['ERROR', 'FAILURE', 'WARNING'])) {
                if (empty($notifications->LocalizedMessage)) {
                    $errors .= $notifications->Message ?? $notifications->Severity;
                } else {
                    $errors .= $notifications->LocalizedMessage;
                }
            }
        } else {
            foreach ($notifications as $note) {
                $errors .= $this->buildErrors($note);
            }
        }

        return $errors;
    }

    /**
     * @param $number
     * @param $prefix
     * @return array|string
     */
    public function shippingMethods($number = null, $prefix = null)
    {
        $shippingOptions = [];

        if (isset($this->prefix)) {
            $prefix = $this->prefix;
        }

        if ($number) {
            if (array_key_exists($number, $this->shipping_methods)) {
                return $this->shipping_methods[$number];
            } else {
                return '--';
            }
        }

        foreach ($this->shipping_methods as $key => $method) {
            if ($this->getSetting($prefix . $key) == 'y') {
                $shippingOptions[$key] = $method;
            }
        }

        return $shippingOptions;
    }

    private function configIsSetup(): bool
    {
        return $this->getSetting('fedex_api_key') && $this->getSetting('fedex_account_number')
            && $this->getSetting('fedex_password') && $this->getSetting('fedex_meter_number');
    }

    private function prepareRequest(array $items): RateRequest
    {
        ee()->load->library('cartthrob_shipping_plugins');

        $rateRequest = new RateRequest();
        $rateRequest->WebAuthenticationDetail->UserCredential->Key = $this->getSetting('fedex_api_key');
        $rateRequest->WebAuthenticationDetail->UserCredential->Password = $this->getSetting('fedex_password');
        $rateRequest->ClientDetail->AccountNumber = $this->getSetting('fedex_account_number');
        $rateRequest->ClientDetail->MeterNumber = $this->getSetting('fedex_meter_number');

        $rateRequest->TransactionDetail->CustomerTransactionId = ' *** Rate Available Services Request v9 using PHP ***';

        $rateRequest->Version->ServiceId = 'crs';
        $rateRequest->Version->Major = 24;
        $rateRequest->Version->Minor = 0;
        $rateRequest->Version->Intermediate = 0;

        $rateRequest->ReturnTransitAndCommit = true;

        $originState = $this->getSetting('fedex_origination_state', ee()->cartthrob_shipping_plugins->customer_location_defaults('state'));
        $originZip = $this->getSetting('fedex_origination_zip', ee()->cartthrob_shipping_plugins->customer_location_defaults('zip'));
        $originCountryCode = $this->getSetting('fedex_origination_country_code', ee()->cartthrob_shipping_plugins->customer_location_defaults('country_code'));
        $originResidentialCommercial = ($this->getSetting('fedex_origination_res_com') == 'RES') ? 1 : 0;
        $destResidentialCommercial = ($this->getSetting('fedex_destination_res_com') == 'RES') ? 1 : 0;

        $rateRequest->RequestedShipment->PreferredCurrency = ee()->config->item('cartthrob:number_format_defaults_currency_code');
        $rateRequest->RequestedShipment->Shipper->Address->StreetLines = [
            $this->getSetting('fedex_origination_address'),
            $this->getSetting('fedex_origination_address2'),
        ];
        $rateRequest->RequestedShipment->Shipper->Address->City = $this->getSetting('fedex_origination_city');
        $rateRequest->RequestedShipment->Shipper->Address->StateOrProvinceCode = $originState;
        $rateRequest->RequestedShipment->Shipper->Address->PostalCode = $originZip;
        $rateRequest->RequestedShipment->Shipper->Address->CountryCode = alpha2_country_code($originCountryCode);
        $rateRequest->RequestedShipment->Shipper->Address->Residential = $originResidentialCommercial;

        $rateRequest->RequestedShipment->Recipient->Address->StreetLines = [
            ee()->cartthrob_shipping_plugins->customer_location_defaults('address'),
            ee()->cartthrob_shipping_plugins->customer_location_defaults('address2'),
        ];
        $rateRequest->RequestedShipment->Recipient->Address->City = ee()->cartthrob_shipping_plugins->customer_location_defaults('city', null);
        $rateRequest->RequestedShipment->Recipient->Address->StateOrProvinceCode = ee()->cartthrob_shipping_plugins->customer_location_defaults('state', null);
        $rateRequest->RequestedShipment->Recipient->Address->PostalCode = ee()->cartthrob_shipping_plugins->customer_location_defaults('zip', null);
        $rateRequest->RequestedShipment->Recipient->Address->CountryCode = alpha2_country_code(ee()->cartthrob_shipping_plugins->customer_location_defaults('country_code'));
        $rateRequest->RequestedShipment->Recipient->Address->Residential = $destResidentialCommercial;

        $rateRequest->RequestedShipment->DropoffType = $this->getSetting('fedex_rate_chart');
        $rateRequest->RequestedShipment->ShipTimestamp = date('c');
        $rateRequest->RequestedShipment->TotalInsuredValue = (new FedexMoney())
            ->setCurrency($this->getSetting('fedex_insurance_currency'))
            ->setAmount($this->getSetting('fedex_insurance_default'));

        $rateRequest->RequestedShipment->ShippingChargesPayment->PaymentType = PaymentType::_SENDER;
        $rateRequest->RequestedShipment->ShippingChargesPayment->setPayor(new Payor([
            'AccountNumber' => $this->getSetting('fedex_account_number'),
            'CountryCode' => alpha2_country_code($originCountryCode),
        ]));

        $rateRequest->RequestedShipment->RateRequestTypes = [RateRequestType::_LIST];
        $rateRequest->RequestedShipment->PackageCount = count($items);
        $rateRequest->RequestedShipment->RequestedPackageLineItems = array_fill(0, count($items), new RequestedPackageLineItem());
        $count = 0;
        foreach ($items as $key => $item) {
            $rateRequest->RequestedShipment->RequestedPackageLineItems[$count]->Weight->Value = ($item->weight() > 0 ? $item->weight() : 1) * $item->quantity();
            $rateRequest->RequestedShipment->RequestedPackageLineItems[$count]->Weight->Units = $this->getSetting('fedex_weight_code');
            $rateRequest->RequestedShipment->RequestedPackageLineItems[$count]->Dimensions->Length = !empty($item->item_options('length')) ? $item->item_options('length') : $this->getSetting('fedex_def_length');
            $rateRequest->RequestedShipment->RequestedPackageLineItems[$count]->Dimensions->Width = !empty($item->item_options('width')) ? $item->item_options('length') : $this->getSetting('fedex_def_width');
            $rateRequest->RequestedShipment->RequestedPackageLineItems[$count]->Dimensions->Height = !empty($item->item_options('height')) ? $item->item_options('length') : $this->getSetting('fedex_def_height');
            $rateRequest->RequestedShipment->RequestedPackageLineItems[$count]->Dimensions->Units = $this->getSetting('fedex_length_code');
            $rateRequest->RequestedShipment->RequestedPackageLineItems[$count]->GroupPackageCount = 1;

            $count++;
        }

        return $rateRequest;
    }

    private function makeRequest(array $items): Collection
    {
        $data = new Collection([
            'price' => new Collection(),
            'option_value' => new Collection(),
            'option_name' => new Collection(),
        ]);

        try {
            $rateServiceRequest = new Request();
            $rateServiceRequest->getSoapClient()->__setLocation(
                $this->getSetting('fedex_mode') == 'live' ? Request::PRODUCTION_URL : Request::TESTING_URL
            );

            $response = $rateServiceRequest->getGetRatesReply($this->prepareRequest($items));

            if (in_array($response->HighestSeverity, ['FAILURE', 'ERROR']) || empty($response->RateReplyDetails)) {
                throw new ShippingRateException($this->buildErrors($response->Notifications));
            }

            foreach ($response->RateReplyDetails as $rateReply) {
                // if only ONE rate is returned.... then $rateReply is not an array, and we need to go up to the $response
                if (is_string($rateReply)) {
                    if (isset($response->RateReplyDetails->RatedShipmentDetails[0]->ShipmentRateDetail->TotalNetCharge->Amount)) {
                        $data['price'][] = ee('cartthrob:MoneyService')->toMoney($response->RateReplyDetails->RatedShipmentDetails[0]->ShipmentRateDetail->TotalNetCharge->Amount);
                        $data['option_value'][] = $response->RateReplyDetails->ServiceType;
                        $data['option_name'][] = $this->shippingMethods($response->RateReplyDetails->ServiceType);
                    }

                    // breaking, because any additional iteration will continue to return the result above.
                    break;
                } elseif (isset($rateReply->RatedShipmentDetails[0]->ShipmentRateDetail->TotalNetCharge->Amount)) {
                    $data['price'][] = ee('cartthrob:MoneyService')->toMoney($rateReply->RatedShipmentDetails[0]->ShipmentRateDetail->TotalNetCharge->Amount);
                    $data['option_value'][] = $rateReply->ServiceType;
                    $data['option_name'][] = $this->shippingMethods($rateReply->ServiceType);
                }
            }
        } catch (SoapFault $e) {
            throw new ShippingRateException(sprintf('%s %s :: %s', $e->faultcode, $e->faultstring, $e->detail->desc));
        }

        return $data;
    }

    /**
     * @return mixed
     */
    private function setShippingOption(Cartthrob_cart $cart, array $shippingData)
    {
        if ($cart->shipping_info('shipping_option')) {
            $shippingOption = $cart->shipping_info('shipping_option');
        } else {
            $cheapestOption = false;

            // if no option has been set, we'll get the cheapest option, and set that as the customer's shipping option.
            if (!empty($shippingData['price'])) {
                $cheapestOption = array_search(min($shippingData['price']), $shippingData['price']);
            }

            if ($cheapestOption !== false && !empty($shippingData['option_value'][$cheapestOption])) {
                $shippingOption = $shippingData['option_value'][$cheapestOption];
            } else {
                $shippingOption = $this->getSetting('product_id');
            }

            $cart->set_shipping_info('shipping_option', $shippingOption);
        }

        return $shippingOption;
    }

    private function prepareMoney(array $shippingData, $shippingOption): Money
    {
        if (!$shippingOption || empty($shippingData['option_value']) || empty($shippingData['price'])) {
            return ee('cartthrob:MoneyService')->fresh();
        } elseif (in_array($shippingOption, $shippingData['option_value'])) {
            $keys = array_keys($shippingData['option_value'], $shippingOption);
            $key = array_pop($keys);

            if (!empty($shippingData['price'][$key])) {
                return $shippingData['price'][$key];
            }
        }

        $minPrice = null;
        foreach ($shippingData['price'] as $price) {
            if (!$minPrice || $price->lessThan($minPrice)) {
                $minPrice = $price;
            }
        }

        return $minPrice;
    }

    public function plugin_shipping_options(): array
    {
        //return [];
        $items = ee()->cartthrob->cart->items();
        $shipping_data = $this->getLiveRates($items);
        if (!empty($shipping_data['option_value'] ))
        {
            foreach ($shipping_data['option_value'] as $key => $value)
            {
                $options[] = array(
                    'rate_short_name' => $value,
                    'price' => $shipping_data['price'][$key],
                    'rate_price' => $shipping_data['price'][$key],
                    'rate_title' => $shipping_data['option_name'][$key],
                );
            }
        }
    }

    /**
     * @param $key
     * @param bool $default
     * @return array|bool|mixed
     */
    public function getSetting($key, $default = false)
    {
        $settings = $this->core->store->config(get_class($this) . '_settings');

        if ($key === false) {
            return ($settings) ? $settings : $default;
        }

        return (isset($settings[$key])) ? $settings[$key] : $default;
    }
}
