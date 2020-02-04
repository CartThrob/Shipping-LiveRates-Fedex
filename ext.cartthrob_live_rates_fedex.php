<?php

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

if (!defined('CARTTHROB_PATH')) {
    exit('No direct script access allowed');
}

require PATH_THIRD . 'cartthrob/vendor/autoload.php';

class Cartthrob_live_rates_fedex_ext extends ShippingPlugin
{
    // ExpressionEngine Properties
    public $title = 'CartThrob - Shipping - FedEx Live Rates';
    public $version = '2.0.0';
    public $description = 'Provide calculated shipping rates for FedEx';
    public $settings_exist = 'y';
    public $settings = [];

    // CartThrob Properties
    public $short_title = 'fedex_short_title';
    public $overview = 'fedex_overview';

    // Internal Properties
    private $shipping_methods = [
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

    public function __construct($settings = '')
    {
        ee()->load->add_package_path(PATH_THIRD . 'cartthrob/');

        parent::__construct($settings);
    }

    public function __destruct()
    {
        ee()->load->remove_package_path(PATH_THIRD . 'cartthrob/');
    }

    /**
     * @return array
     */
    public function settings(): array
    {
        ee()->load->library('locales');

        $settings = [];

        $settings['fedex_api_key'] = ['i', '', ''];
        $settings['fedex_account_number'] = ['i', '', ''];
        $settings['fedex_meter_number'] = ['i', '', ''];
        $settings['fedex_password'] = ['i', '', ''];
        $settings['fedex_mode'] = ['r', ['dev' => 'dev', 'live' => 'live'], 'dev'];
        $settings['fedex_length_code'] = ['r', ['IN' => 'Inches', 'CM' => 'Centimeters'], 'IN'];
        $settings['fedex_weight_code'] = ['r', ['LB' => 'Pounds', 'KG' => 'Kilograms'], 'LB'];
        $settings['fedex_rate_chart'] = [
            'r',
            [
                'REGULAR_PICKUP' => 'fedex_regular_pickup',
                'REQUEST_COURIER' => 'fedex_request_courier',
                'BUSINESS_SERVICE_CENTER' => 'fedex_business_service_center',
                'STATION' => 'fedex_station',
            ],
            'REGULAR_PICKUP',
        ];
        $settings['fedex_origination_address'] = ['i', '', ''];
        $settings['fedex_origination_address2'] = ['i', '', ''];
        $settings['fedex_origination_city'] = ['i', '', ''];
        $settings['fedex_origination_state'] = ['s', ee()->locales->states($this->getSetting('fedex_origination_country_code')), ''];
        $settings['fedex_origination_zip'] = ['i', '', ''];
        $settings['fedex_origination_country_code'] = ['s', ['CAN' => 'Canada', 'USA' => 'United States'], 'USA'];
        $settings['fedex_product_id'] = [
            's',
            [
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
            'GROUND_HOME_DELIVERY',
        ];
        $settings['fedex_container'] = [
            's',
            [
                'YOUR_PACKAGING' => 'fedex_package',
                'FEDEX_BOX' => 'fedex_box',
                'FEDEX_TUBE' => 'fedex_tube',
                'FEDEX_PAK' => 'fedex_pak',
                'FEDEX_25KG_BOX' => 'fedex_25kg_box',
                'FEDEX_10KG_BOX' => 'fedex_10kg_box',
                'FEDEX_ENVELOPE' => 'fedex_envelope',
            ],
            'YOUR_PACKAGING',
        ];
        $settings['fedex_insurance_default'] = ['i', '', '100'];
        $settings['fedex_insurance_currency'] = ['i', '', 'USD'];
        $settings['fedex_origination_res_com'] = ['r', ['RES' => 'fedex_res', 'COM' => 'fedex_com'], 'RES'];
        $settings['fedex_destination_res_com'] = ['r', ['RES' => 'fedex_res', 'COM' => 'fedex_com'], 'RES'];
        $settings['fedex_def_length'] = ['i', '', '15'];
        $settings['fedex_def_width'] = ['i', '', '15'];
        $settings['fedex_def_height'] = ['i', '', '15'];
        $settings['fedex_sp_ancillary_services'] = [
            's',
            [
                '' => 'fedex_none',
                'ADDRESS_CORRECTION' => 'fedex_address_correction',
                'CARRIER_LEAVE_IF_NO_RESPONSE' => 'fedex_carrier_leave_if_no_response',
                'CHANGE_SERVICE' => 'fedex_change_service',
                'FORWARDING_SERVICE' => 'fedex_forwarding_service',
                'RETURN_SERVICE' => 'fedex_return_service',
            ],
            '',
        ];
        $settings['fedex_sp_indicia'] = [
            's',
            [
                '' => 'fedex_none',
                'MEDIA_MAIL' => 'fedex_media_mail',
                'PARCEL_RETURN' => 'fedex_parcel_return',
                'PARCEL_SELECT' => 'fedex_parcel_select',
                'PRESORTED_BOUND_PRINTED_MATTER' => 'fedex_presorted_bound_printed_matter',
                'PRESORTED_STANDARD' => 'fedex_presorted_standard',
            ],
            '',
        ];
        $settings['fedex_ground'] = ['r', ['n' => 'No', 'y' => 'Yes'], 'y'];
        $settings['fedex_priority_overnight'] = ['r', ['n' => 'No', 'y' => 'Yes'], 'n'];
        $settings['fedex_standard_overnight'] = ['r', ['n' => 'No', 'y' => 'Yes'], 'y'];
        $settings['fedex_2_day'] = ['r', ['n' => 'No', 'y' => 'Yes'], 'y'];
        $settings['fedex_express_saver'] = ['r', ['n' => 'No', 'y' => 'Yes'], 'n'];
        $settings['fedex_first_overnight'] = ['r', ['n' => 'No', 'y' => 'Yes'], 'n'];
        $settings['fedex_ground_home_delivery'] = ['r', ['n' => 'No', 'y' => 'Yes'], 'y'];
        $settings['fedex_international_economy'] = ['r', ['n' => 'No', 'y' => 'Yes'], 'n'];
        $settings['fedex_international_first'] = ['r', ['n' => 'No', 'y' => 'Yes'], 'n'];
        $settings['fedex_international_ground'] = ['r', ['n' => 'No', 'y' => 'Yes'], 'n'];
        $settings['fedex_international_priority'] = ['r', ['n' => 'No', 'y' => 'Yes'], 'n'];
        $settings['fedex_europe_first_international_priority'] = ['r', ['n' => 'No', 'y' => 'Yes'], 'n'];

        return $settings;
    }

    /**
     * @param array $items
     * @return array
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
     * @param Cartthrob_cart $cart
     * @return Money
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

    // END

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

    /**
     * @return bool
     */
    private function configIsSetup(): bool
    {
        return $this->getSetting('fedex_api_key') && $this->getSetting('fedex_account_number')
            && $this->getSetting('fedex_password') && $this->getSetting('fedex_meter_number');
    }

    /**
     * @param array $items
     * @return RateRequest
     */
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
            $rateRequest->RequestedShipment->RequestedPackageLineItems[$count]->GroupPackageCount = $count + 1;

            $count++;
        }

        return $rateRequest;
    }

    /**
     * @param array $items
     * @return Collection
     * @throws ShippingRateException
     */
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
                $this->getSetting('fedex_mode', true) ? Request::TESTING_URL : Request::PRODUCTION_URL
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
            throw new ShippingRateException(
                sprintf('%s %s :: %s', $e->faultcode, $e->faultstring, $e->detail->desc)
            );
        }

        return $data;
    }

    /**
     * @param Cartthrob_cart $cart
     * @param array $shippingData
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

    /**
     * @param array $shippingData
     * @param $shippingOption
     * @return mixed
     */
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
}
