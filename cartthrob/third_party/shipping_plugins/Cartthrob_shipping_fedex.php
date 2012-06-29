<?php if ( ! defined('CARTTHROB_PATH')) Cartthrob_core::core_error('No direct script access allowed');

class Cartthrob_shipping_fedex extends CartThrob_shipping
{
	public $title = "fedex_title"; 
	public $overview = 'fedex_overview'; 
	public $settings = array(
		array(
			'name' => 'fedex_api_key',
			'short_name' => 'access_key',
			'type' => 'text',
			'default'	=> ''
		),
		array(
			'name' => 'fedex_account_number',
			'short_name' => 'account_number',
			'type' => 'text',
			'default'	=> ''
		),
		array(
			'name' => 'fedex_meter_number',
			'short_name' => 'meter_number',
			'type' => 'text',
			'default'	=> ''
		),
		array(
			'name' => 'password',
			'short_name' => 'password',
			'type' => 'text',
			'default'	=> ''
		),
		
		array(
			'name' => 'fedex_mode',
			'short_name' => 'mode',
			'type' => 'radio',
			'default' => 'dev',
			'options'	=> array(
					'dev' => "dev",
					'live' => "live"
			),
		),
		
		
 		/// DEFAULTS FOR SHIPPING OPTIONS
		array(
			'name' => 'fedex_length_code',
			'short_name' => 'length_code',
			'type' => 'radio',
			'default' => 'IN',
			'options'	=> array(
					'IN' => "Inches",
					'CM' => "Centimeters"
			),
		),
		array(
			'name' => 'fedex_weight_code',
			'short_name' => 'weight_code',
			'type' => 'radio',
			'default' => 'LB',
			'options'	=> array(
					'LB' => "pounds",
					'KG' => "kilograms"
			),
		),
		array(
			'name' => 'fedex_rate_chart',
			'short_name' => 'rate_chart',
			'type' => 'select',
			'default' => 'REGULAR_PICKUP', 
			'options' => array(
				'REGULAR_PICKUP'	=> "fedex_regular_pickup",
				'REQUEST_COURIER' 	=> "fedex_request_courier",
				'BUSINESS_SERVICE_CENTER' => 'fedex_business_service_center',
				'STATION'	=> 'fedex_station'
			),
		),
 		array(
			'name' => 'fedex_origination_address',
			'short_name' => 'origination_address',
			'type' => 'text'
 		),
		array(
			'name' => 'fedex_origination_address2',
			'short_name' => 'origination_address2',
			'type' => 'text'
 		),
		array(
			'name' => 'fedex_origination_city',
			'short_name' => 'origination_city',
			'type' => 'text'
		),
		array(
			'name' => 'fedex_origination_state',
			'short_name' => 'origination_state',
			'type'			=> 'select',
			'attributes'		=> array(
				'class'	=> 'states_blank',
			),
			
		),

		array(
			'name' => 'fedex_origination_zip',
			'short_name' => 'origination_zip',
			'type' => 'text'
		),
		array(	
			'name'			=> 'fedex_origination_country_code', 
			'short_name'	=> 'orig_country_code',
			'type'			=> 'select',
			'default'		=> 'USA',
			'attributes'		=> array(
				'class'	=> 'countries_blank',
			),
		),
		array(
			'name' => 'fedex_product_id',
			'short_name' => 'product_id',
			'type' => 'select',
			'default' => 'GROUND_HOME_DELIVERY', 
			'options' => array(
				''						=> 'fedex_valid_domestic_values', 
				'FEDEX_GROUND'			=> 'fedex_ground',
				'PRIORITY_OVERNIGHT' 	=> "fedex_priority_overnight",
				'STANDARD_OVERNIGHT' 	=> "fedex_standard_overnight",
				'FEDEX_2_DAY'			=> 'fedex_2_day',
				'FEDEX_EXPRESS_SAVER' 	=> 'fedex_express_saver',
				'FIRST_OVERNIGHT'		=> 'fedex_first_overnight',
				'GROUND_HOME_DELIVERY' 	=> 'fedex_ground_home_delivery',
				'SMART_POST'			=> 'fedex_smart_post', 
  				''						=> 'fedex_valid_international_values', 
				'INTERNATIONAL_ECONOMY'	=> 'fedex_international_economy',
				'INTERNATIONAL_FIRST'	=> 'fedex_international_first',
				'INTERNATIONAL_GROUND'	=> 'fedex_international_ground',
				'INTERNATIONAL_PRIORITY'	=> 'fedex_international_priority',
				'EUROPE_FIRST_INTERNATIONAL_PRIORITY' => 'fedex_europe_first_international_priority',
				
			),
		),
		array(
			'name' => 'fedex_container',
			'short_name' => 'container',
			'type' => 'select',
			'default' => 'YOUR_PACKAGING',
			'options' => array(
				'YOUR_PACKAGING' => 'fedex_package',
				'FEDEX_BOX' => 'fedex_box',
				'FEDEX_TUBE' => 'fedex_tube',
				'FEDEX_PAK' => 'fedex_pak', 
				'FEDEX_25KG_BOX' => 'fedex_25kg_box',
				'FEDEX_10KG_BOX' => 'fedex_10kg_box',
				'FEDEX_ENVELOPE' => 'fedex_envelope',
				
 			),
		),
		array(
			'name' => 'fedex_insurance_default',
			'short_name' => 'insurance_default',
			'type'	=> 'text',
			'default'	=> 100
		),
		array(
			'name' => 'fedex_insurance_currency',
			'short_name' => 'insurance_currency',
			'type'	=> 'text',
			'default'	=> "USD"
		),
		// BUSINESS RATES ARE CHEAPER
		array(
			'name' =>  "fedex_origination_res_com", 
			'short_name' => 'origination_res_com',
			'type' => 'radio',
			'default' => "RES",
			'options' => array(
				"RES" => "fedex_res",
				"COM" => "fedex_com",
			),
		),
		array(
			'name' => "fedex_destination_res_com" ,
			'short_name' => 'destination_res_com',
			'type' => 'radio',
			'default' => "RES",
			'options' => array(
				"RES" => "fedex_res",
				"COM" => "fedex_com",
			),
		),
		array(
			'name' =>  'fedex_def_length',
			'short_name' => 'def_length',
			'type' => 'text',
			'default' => '15'
		),
		array(
			'name' =>  'fedex_def_width',
			'short_name' => 'def_width',
			'type' => 'text',
			'default' => '15'
		),
		array(
			'name' =>  'fedex_def_height',
			'short_name' => 'def_height',
			'type' => 'text',
			'default' => '15'
		),
		// SMART POST 
		array(
			'name' => 'fedex_smart_post_header',
			'short_name' => 'smart_post_header',
			'type' => 'header',
		),
		array(
			'name' =>  'fedex_hubid',
			'short_name' => 'hubid',
			'type' => 'text',
		),
		array(
			'name' => "fedex_sp_ancillary_services" ,
			'short_name' => 'sp_ancillary_services',
			'type' => 'select',
 			'options' => array(
			 	''	=> "--",
				'ADDRESS_CORRECTION' 	=> 'fedex_address_correction',
				'CARRIER_LEAVE_IF_NO_RESPONSE' => 'fedex_carrier_leave_if_no_response', 
				'CHANGE_SERVICE'	=> 'fedex_change_service',
				'FORWARDING_SERVICE' => 'fedex_forwarding_service',
				'RETURN_SERVICE' => 'fedex_return_service'
				
			),
		),
 		array(
			'name' => "fedex_sp_indicia" ,
			'short_name' => 'sp_indicia',
			'type' => 'select',
 			'options' => array(
			 	''	=> "--",
				'MEDIA_MAIL' 	=> 'Media Mail',
				'PARCEL_RETURN' => 'Parcel Return', 
				'PARCEL_SELECT'	=> 'Parcel Select',
				'PRESORTED_BOUND_PRINTED_MATTER' => 'Presorted Bound Printed Matter',
				'PRESORTED_STANDARD' => 'Presorted Standard'
				
			),
		),
		
		// CUSTOMER CHOICES
		array(
			'name' => 'fedex_selectable_rates',
			'short_name' => 'selectable_rates',
			'type' => 'header',
		),
		array(
			'name' => 'fedex_ground',
			'short_name' => 'FEDEX_GROUND',
			'type' => 'radio',
			'default' => 'y',
			'options' => array(
				'n' => 'no',
				'y' => 'yes',
				)
		),
		array(
			'name' => 'fedex_priority_overnight',
			'short_name' => 'PRIORITY_OVERNIGHT',
			'type' => 'radio',
			'default' => 'n',
			'options' => array(
				'n' => 'no',
				'y' => 'yes',
				)
		),
		array(
			'name' => 'fedex_standard_overnight',
			'short_name' => 'STANDARD_OVERNIGHT',
			'type' => 'radio',
			'default' => 'y',
			'options' => array(
				'n' => 'no',
				'y' => 'yes',
				)
		),
		array(
			'name' => 'fedex_2_day',
			'short_name' => 'FEDEX_2_DAY',
			'type' => 'radio',
			'default' => 'y',
			'options' => array(
				'n' => 'no',
				'y' => 'yes',
				)
		),
		array(
			'name' => 'fedex_express_saver',
			'short_name' => 'FEDEX_EXPRESS_SAVER',
			'type' => 'radio',
			'default' => 'n',
			'options' => array(
				'n' => 'no',
				'y' => 'yes',
				)
		),
		array(
			'name' => 'fedex_first_overnight',
			'short_name' => 'FIRST_OVERNIGHT',
			'type' => 'radio',
			'default' => 'n',
			'options' => array(
				'n' => 'no',
				'y' => 'yes',
				)
		),
		array(
			'name' => 'fedex_ground_home_delivery',
			'short_name' => 'GROUND_HOME_DELIVERY',
			'type' => 'radio',
			'default' => 'y',
			'options' => array(
				'n' => 'no',
				'y' => 'yes',
				)
		),
		array(
			'name' => 'fedex_smart_post',
			'short_name' => 'SMART_POST',
			'type' => 'radio',
			'default' => 'n',
			'options' => array(
				'n' => 'no',
				'y' => 'yes',
				)
		),
		array(
			'name' => 'fedex_international_economy',
			'short_name' => 'INTERNATIONAL_ECONOMY',
			'type' => 'radio',
			'default' => 'n',
			'options' => array(
				'n' => 'no',
				'y' => 'yes',
				)
		),
		array(
			'name' => 'fedex_international_first',
			'short_name' => 'INTERNATIONAL_FIRST',
			'type' => 'radio',
			'default' => 'n',
			'options' => array(
				'n' => 'no',
				'y' => 'yes',
				)
		),
		array(
			'name' => 'fedex_international_ground',
			'short_name' => 'INTERNATIONAL_GROUND',
			'type' => 'radio',
			'default' => 'n',
			'options' => array(
				'n' => 'no',
				'y' => 'yes',
				)
		),
		array(
			'name' => 'fedex_international_priority',
			'short_name' => 'INTERNATIONAL_PRIORITY',
			'type' => 'radio',
			'default' => 'n',
			'options' => array(
				'n' => 'no',
				'y' => 'yes',
				)
		),
		array(
			'name' => 'fedex_europe_first_international_priority',
			'short_name' => 'EUROPE_FIRST_INTERNATIONAL_PRIORITY',
			'type' => 'radio',
			'default' => 'n',
			'options' => array(
				'n' => 'no',
				'y' => 'yes',
				)
		),
		
	);
	public $required_fields = array(); 
	public $shipping_methods = array(
			'FEDEX_GROUND'	=> 'Ground',
			'PRIORITY_OVERNIGHT'	=> 'Priority Overnight',
			'STANDARD_OVERNIGHT'	=> 'Standard Overnight',
			'FEDEX_2_DAY'			=> '2 Day',
			'FEDEX_EXPRESS_SAVER'	=> 'Express Saver',
			'FIRST_OVERNIGHT'		=> 'First Overnight',
			'GROUND_HOME_DELIVERY'	=> 'Ground Home Delivery',
			'SMART_POST'			=> 'Smart Post',
			'INTERNATIONAL_ECONOMY'	=> 'International Economy',
			'INTERNATIONAL_FIRST'	=> 'International First',
			'INTERNATIONAL_GROUND'	=> 'International Ground',
			'INTERNATIONAL_PRIORITY'	=> 'International Priority',
			'EUROPE_FIRST_INTERNATIONAL_PRIORITY'	=> 'Europe First International Priority'
		);
		
	public function initialize()
	{
 		require_once PATH_THIRD.'cartthrob/third_party/lib/fedex/fedex-common.php5';
		
		if(is_callable('ini_set'))
		{
			ini_set("soap.wsdl_cache_enabled", "0");
		}	
	}
 	function get_shipping()
	{
		$cart_hash = $this->core->cart->custom_data('cart_hash'); 
 		
 		if ($this->core->cart->count() <= 0 || $this->core->cart->shippable_subtotal() <= 0)
		{
			return 0;
		}
		
 		if ($cart_hash != $this->cart_hash())
		{
			$this->core->cart->set_custom_data('shipping_requires_update', $this->title ); 
			$this->core->cart->save(); 
		}
		else
		{
			$this->core->cart->set_custom_data('shipping_requires_update', NULL ); 
			$this->core->cart->save(); 
		}
		$shipping_data =$this->core->cart->custom_data(ucfirst(get_class($this)));

	 	if(!$this->core->cart->shipping_info('shipping_option'))
		{
			$temp_key = FALSE; 
			// if no option has been set, we'll get the cheapest option, and set that as the customer's shipping option. 
			if (!empty($shipping_data['price']))
			{
				// this looks weird, but we're trying to get the key. we have to find the min value, then pull the key from that. 
				$temp_key = array_search( min($shipping_data['price']), $shipping_data['price']); 
			}
			if ($temp_key !== FALSE && !empty($shipping_data['option_value'][$temp_key]))
			{
				$this->shipping_option =  $shipping_data['option_value'][$temp_key]; 
				$this->core->cart->set_shipping_info("shipping_option",  $shipping_data['option_value'][$temp_key] ); 
			}
			else
			{
				$this->shipping_option =  $this->plugin_settings('product_id'); 
				$this->core->cart->set_shipping_info("shipping_option", $this->plugin_settings('product_id')); 
				
			}
		}
		else
		{
			$this->shipping_option = $this->core->cart->shipping_info('shipping_option');
		}
		
		
		if (!empty($shipping_data['option_value']) && !empty($shipping_data['price']))
		{
			if ($this->shipping_option && in_array($this->shipping_option, $shipping_data['option_value']))
			{
				$key =array_pop(array_keys($shipping_data['option_value'], $this->shipping_option)); 
				if (!empty($shipping_data['price'][$key]))
				{                          
					return $shipping_data['price'][$key]; 
				}
			}
			elseif ( ! $this->shipping_option)
			{
				return 0;
			}
			else
			{
				return min($shipping_data['price']);
			}
		}
		return 0;
	}
 
	function get_live_rates($option_value="ALL")
	{
 		// manually set the weight at 1lb as a minimum
		
		$this->EE =& get_instance(); 
		$this->EE->load->library('cartthrob_shipping_plugins');
		$this->core->cart->set_custom_data("shipping_error", ""); 
		
 		if ($this->plugin_settings('mode') == "dev")
		{
			$wsdl_path = PATH_THIRD."cartthrob/third_party/lib/fedex/wsdl/RateService_v9_dev_version.wsdl";
			
		}
		else
		{
			$wsdl_path = PATH_THIRD."cartthrob/third_party/lib/fedex/wsdl/RateService_v9.wsdl";
			
		}
		$client = new SoapClient($wsdl_path, array('trace' => 1)); // Refer to http://us3.php.net/manual/en/ref.soap.php for more information
 
		$shipping = array(
				'error_message'	=> NULL,
				'price'			=> array(),
				'option_value'		=> array(),
				'option_name'		=> array(),
			); 
		if (!$this->plugin_settings('access_key') || !$this->plugin_settings('account_number') || ! $this->plugin_settings('password') || !$this->plugin_settings('meter_number'))
		{
			$shipping['error_message'] = $this->EE->lang->line('shipping_settings_not_configured');
			$this->core->cart->set_custom_data("shipping_error", $shipping['error_message']); 
			$this->core->cart->save(); 
			return $shipping; 
		}
		/******************************************* ASSEMBLE REQUEST & SEND *********************************/
		
		$request['WebAuthenticationDetail'] = array(
			'UserCredential' => array(
				'Key' 		=> $this->plugin_settings('access_key'), 
				'Password' 	=> $this->plugin_settings('password')
			)
		); 
					
		$request['ClientDetail'] = array(
			'AccountNumber' 	=> $this->plugin_settings('account_number'), 
			'MeterNumber' 		=> $this->plugin_settings('meter_number')
		);

 
		$request['TransactionDetail'] = array(
			'CustomerTransactionId' => ' *** Rate Available Services Request v9 using PHP ***'
		);
 
		$request['Version'] = array(
			'ServiceId' => 'crs', 
			'Major' => '9', 
			'Intermediate' => '0', 
			'Minor' => '0'
		);
		
		$request['ReturnTransitAndCommit'] = true;
		
		$request['RequestedShipment']['DropoffType'] = $this->plugin_settings('rate_chart'); // valid values REGULAR_PICKUP, REQUEST_COURIER, ...
		
		$request['RequestedShipment']['ShipTimestamp'] = date('c');
		
 
		
 		$orig_state = 	($this->plugin_settings('origination_state'))? $this->plugin_settings('origination_state') : $this->EE->cartthrob_shipping_plugins->customer_location_defaults('state') ; //@TODO add state 
		$orig_zip = 	($this->plugin_settings('origination_zip'))? $this->plugin_settings('origination_zip') : $this->EE->cartthrob_shipping_plugins->customer_location_defaults("zip");  // @TODO add zip
		$orig_country_code = ($this->plugin_settings('orig_country_code'))? $this->plugin_settings('orig_country_code') : $this->EE->cartthrob_shipping_plugins->customer_location_defaults("country_code"); //@TODO add country
  		$orig_res_com = ($this->plugin_settings('origination_res_com') == "RES")? 1: 0; 
		$destination_res_com = ($this->plugin_settings('destination_res_com') == "RES")? 1: 0; 
		
		$request['RequestedShipment']['TotalInsuredValue']=array(
			'Amount'=>$this->plugin_settings('insurance_default'),
			'Currency'=>$this->plugin_settings('insurance_currency')
		);
		
		$request['RequestedShipment']['Shipper'] = array(
			'Address' => array(
					'StreetLines' => array(
						$this->plugin_settings('origination_address'), $this->plugin_settings('origination_address2')
					),
		            'City' => $this->plugin_settings('origination_city'),
		            'StateOrProvinceCode' => $orig_state,
		            'PostalCode' =>  $orig_zip,
		            'CountryCode' => $this->EE->cartthrob_shipping_plugins->alpha2_country_code($orig_country_code),
		            'Residential' => $orig_res_com
				)
			);
		
		if ($option_value == "SMART_POST")
		{
			$request['RequestedShipment']['SmartPostDetail'] = array( 'Indicia' => $this->plugin_settings('sp_indicia'),
			                                                          'AncillaryEndorsement' => $this->plugin_settings('sp_ancillary_services'),
			                                                          'HubId' => $this->plugin_settings('hubid'),
			                                                          'CustomerManifestId' => date("md0B"));
		}
		$request['RequestedShipment']['Recipient'] = array(
			'Address' => array(
					'StreetLines' => array(
					 	$this->EE->cartthrob_shipping_plugins->customer_location_defaults("address"), $this->EE->cartthrob_shipping_plugins->customer_location_defaults('address2')
					),
		            'City' => $this->EE->cartthrob_shipping_plugins->customer_location_defaults("city", NULL),
		            'StateOrProvinceCode' => $this->EE->cartthrob_shipping_plugins->customer_location_defaults("state", NULL),
		            'PostalCode' => $this->EE->cartthrob_shipping_plugins->customer_location_defaults("zip", NULL),
		            'CountryCode' => $this->EE->cartthrob_shipping_plugins->alpha2_country_code($this->EE->cartthrob_shipping_plugins->customer_location_defaults("country_code")),
		            'Residential' => $destination_res_com
					)
				);
				
		$request['RequestedShipment']['ShippingChargesPayment'] = array(
			'PaymentType' => 'SENDER',
			'Payor' => array(
				'AccountNumber' => $this->plugin_settings('account_number'),
				'CountryCode' => $this->EE->cartthrob_shipping_plugins->alpha2_country_code($orig_country_code)
				)
		);
		$request['RequestedShipment']['RateRequestTypes'] = 'ACCOUNT'; 
	
		$request['RequestedShipment']['RateRequestTypes'] = 'LIST'; 
	
		$request['RequestedShipment']['PackageCount'] = $this->core->cart->count_all();
	
		$request['RequestedShipment']['PackageDetail'] = 'INDIVIDUAL_PACKAGES';  //  Or PACKAGE_SUMMARY
		
		$count = 0; 
		foreach ($this->core->cart->items() as $key => $item)
		{
			$request['RequestedShipment']['RequestedPackageLineItems'][$count] = 
					array(
					'Weight' => array(
						'Value' 	=>( ($item->weight() > 0 ? $item->weight() : 1) * $item->quantity()),
						'Units' 	=> $this->plugin_settings('weight_code')
						),
					'Dimensions' => array(
						'Length' 	=> ($item->item_options('length'))? $item->item_options('length'): $this->plugin_settings('def_length'),
						'Width' 	=> ($item->item_options('width'))? $item->item_options('width'): $this->plugin_settings('def_width'),
						'Height' 	=> ($item->item_options('height'))? $item->item_options('height'): $this->plugin_settings('def_height'),
						'Units' 	=> $this->plugin_settings('length_code')
						)
					); 
			$count ++; 
		}

 		try 
		{
			if(setEndpoint('changeEndpoint'))
			{
				$newLocation = $client->__setLocation(setEndpoint('endpoint'));
			}

			$response = $client->getRates($request);
			
 		    if ($response->HighestSeverity != 'FAILURE' && $response->HighestSeverity != 'ERROR' && !empty($response->RateReplyDetails))
		    {
			 
 		        foreach ($response->RateReplyDetails as $rateReply)
		        {
  					// if only ONE rate is returned.... then $rateReply is not an array, and we need to go up to the $response
					if (is_string($rateReply))
					{
 						if (isset($response->RateReplyDetails->RatedShipmentDetails[0]->ShipmentRateDetail->TotalNetCharge->Amount))
						{
							$shipping['error_message']	= NULL; 
							$shipping['price'][]	=	number_format($response->RateReplyDetails->RatedShipmentDetails[0]->ShipmentRateDetail->TotalNetCharge->Amount,2,".",",");
							$shipping['option_value'][]	= $response->RateReplyDetails->ServiceType;
							$shipping['option_name'][]  = $this->shipping_methods($response->RateReplyDetails->ServiceType); 

						}
						// breaking, because any additional iteration will continue to return the result above. 
						break; 
 					}
					else
					{
						if (isset($rateReply->RatedShipmentDetails[0]->ShipmentRateDetail->TotalNetCharge->Amount))
						{
							$shipping['error_message']	= NULL; 
							$shipping['price'][]	=	number_format($rateReply->RatedShipmentDetails[0]->ShipmentRateDetail->TotalNetCharge->Amount,2,".",",");
							$shipping['option_value'][]	= $rateReply->ServiceType;
							$shipping['option_name'][]  = $this->shipping_methods($rateReply->ServiceType); 

						}
					}

				}
 		    }
		    else
		    {
				$shipping = array(
						'error_message'	=> $this->build_errors($response->Notifications),
						'price'			=> array(),
						'option_value'	=> array(),
						'option_name'	=> array(),
					);
		    } 
		} 
		catch (SoapFault $exception) 
		{
			$shipping = array(
					'error_message'	=> $exception->faultcode ." " . $exception->faultstring. " ". $this->build_faults($exception),
					'price'			=> array(),
					'option_value'	=> array(),
					'option_name'	=> array(),
				);
		}
  		
		// CHECKING THE PRESELECTED OPTIONS THAT ARE AVAILABLE
		$available_shipping =array(); 
		foreach ($shipping['option_value'] as $key => $value)
		{
			// REMOVE THE ONES THAT ARE NOT OPTIONS
			if ( $this->plugin_settings($value) !="n" )
			{
				$available_shipping['price'][$key] 				= $shipping['price'][$key]; 
				$available_shipping['option_value'][$key]		= $shipping['option_value'][$key]; 
				$available_shipping['option_name'][$key]		= $shipping['option_name'][$key]; 
			}
		}
		
		if ($shipping['error_message'])
		{
			$available_shipping['error_message'] = $shipping['error_message']; 
			$this->core->cart->set_custom_data("shipping_error", $shipping['error_message']); 
		}
		// update cart shipping hash
		$this->cart_hash($available_shipping); 

 		// if there's no errors, but we removed all of the shipping options, it's because none of the values were configured on the backend. We need to warn.
 		if (empty($available_shipping['error_message']) && empty($available_shipping['price']) && !empty($available_shipping))
		{
			$available_shipping['error_message'] = "Shipping options compatible with your location: (".$shipping_address ." ". $shipping_address2 ." ". $shipping_city." ". ($shipping_state?",".$shipping_state: "")." ". $shipping_zip ." ". $dest_country_code.") have not been configured in the cart settings. Please contact the webmaster"; 
			if ($dest_country_code != $orig_country_code)
			{
				$available_shipping['error_message'] .= " International shipping options may need to be added. "; 
			}
			$this->core->cart->set_custom_data("shipping_error", $shipping['error_message']); 
			
		}
		$this->core->cart->save(); 
		
		return $available_shipping;
	}
	// END
	function build_faults($exception)
	{
		$errors = ""; 
		if (is_object($exception->detail->fault->details->ValidationFailureDetail->message) || is_array($exception->detail->fault->details->ValidationFailureDetail->message))
		{
			foreach($exception->detail->fault->details->ValidationFailureDetail->message as $key=> $value)
			{
				if(is_string($value))
				{    
		            $errors = $exception->detail->fault->details->ValidationFailureDetail->xmlLocation[$key] . ': ' . $value . " ";
		        }
		        else
				{
		        	$errors = $this->build_errors($value);
		        }
			}
		}
		else
		{
			return $exception->detail->fault->details->ValidationFailureDetail->message; 
		}

		return $errors; 
	}
	// END
	function build_errors($notes)
	{
		$errors = ""; 
		// handles single errors
		if (!empty($notes->Severity))
		{
			if ($notes->Severity=="ERROR" || $notes->Severity=="FAILURE" || $notes->Severity=="WARNING")
			{
				if (empty($notes->LocalizedMessage))
				{
					$errors .= (!empty($notes->Message) ? $notes->Message : $notes->Severity);

				}
				else
				{
					$errors .= $notes->LocalizedMessage;

				}
			}
 
		}
		// handles multiple errors
		else
		{
			foreach($notes as $noteKey => $note)
			{
				$errors .= $this->build_errors($note); 
			}
		}
		return $errors; 
	}
	// END
 	function shipping_methods($number = NULL, $prefix = NULL)
	{
		if (isset($this->prefix))
		{
			$prefix = $this->prefix; 
		}
 		if ($number)
		{
			if (array_key_exists($number, $this->shipping_methods))
			{
				return $this->shipping_methods[$number]; 
			}
			else
			{
				return "--"; 
			}
		}
		foreach ($this->shipping_methods as $key => $method)
		{
 			if ($this->plugin_settings($prefix.$key) =="y")
			{
				$available_options[$key] = $method; 
			}
 
		}
		return $available_options; 
	}
	// END
	public function plugin_shipping_options()
	{
		$options = array(); 
 		// GETTING THE RATES FROM SESSION
		$shipping_data =$this->core->cart->custom_data(ucfirst(get_class($this)));
		$this->core->cart->save(); 
		
		/*
 		if (!$shipping_data)
		{
			// IF NONE ARE IN SESSION, WE WILL *TRY* TO GET RATES BASED ON CURRENT CART CONTENTS
			$shipping_data = $this->get_live_rates(); 
  		}
		*/
 		$shipping_data = $this->get_live_rates(); 
		
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
		
		return $options;
	}
	function cart_hash($shipping = NULL )
	{
		// hashing the cart data, so we can check later if the cart has been updated      
		$cart_hash = md5(serialize($this->core->cart->items_array())); 
 		if ($shipping)
		{
			$this->core->cart->set_custom_data('cart_hash', $cart_hash); 
			$this->core->cart->set_custom_data(ucfirst(get_class($this)), $shipping);
		}  
		$this->core->cart->save(); 
		
		return $cart_hash; 
	}
}
