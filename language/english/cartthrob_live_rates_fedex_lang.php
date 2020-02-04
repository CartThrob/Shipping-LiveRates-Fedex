<?php

$lang = [
        'fedex_short_title' => 'FedEx Live Rates',
        'fedex_overview' => '
            <p>This shipping plugin requires  CartThrob 5+, PHP 7.1+, and the Soap module enabled. Settings for this add-on are managed with the plugins <a href="/admin.php?/cp/addons/settings/cartthrob_live_rates_fedex">Settings Page</a>.</p>

            <h3 style="display:block">Use of This Plugin</h3>

            <p>LiveRates shipping costs are not updated automatically when the cart contents are modified. LiveRates shipping plugins requires that you manually request a shipping quote at some point during your checkout process. If you do not use the <a href="https://cartthrob.com/docs/tags_detail/get_shipping_options/index.html">{exp:cartthrob:get_shipping_options}</a> tag, your shipping costs may not be set for checkout.</p> 
                
                <p>For more details about this and other live rates plugins see: <a href="https://cartthrob.com/docs/tags_detail/get_live_rates_form/">http://cartthrob.com/docs/tags_detail/get_live_rates_form/</a></p>

            <h3 style="display:block">Estimate Accuracy Warning</h3>
            <p>If your actual packing and shipping methods differ from the information you use to request the cost estimate, your shipping costs may vary from the estimated value. By default the entire cart weight is used to calculate shipping costs, along with default length, width, and height values set below.</p>

            <p>Each time items in the cart are added, updated, or removed, shipping costs are reset to zero when using a LiveRates shipping plugin. It is recommended that you check to see that shipping costs are set before allowing a customer to check out. For example: {if "{exp:cartthrob:cart_shipping prefix=""}" = "0.00"}show live rates{if:else}show checkout{/if}</p>

            <h3 style="display:block">Dimensional Weight Warning</h3><p>Length + Width + Height values are <strong>required</strong> for shipping quotes using LiveRates plugins. Some shipping methods such as overnight, next-day, and two-day shipping methods may only calculate costs by box dimensions rather than standard weights. Please consult documentation provided by your shipping company, or your shipping representative for more information.</p>
        ',
        'fedex_2_day' => 'Domestic - 2 Day',
        'fedex_api_key' => 'API Key',
        'fedex_account_number' => 'Account Number',
        'fedex_address_correction' => 'Address Correction',
        'fedex_business_service_center' => 'Business Service Center',
        'fedex_carrier_leave_if_no_response' => 'Carrier Leave if No Response',
        'fedex_change_service' => 'Change Service',
        'fedex_com' => 'Commercial',
        'fedex_container' => 'Packaging Type Default',
        'fedex_def_height' => 'Default Height',
        'fedex_def_length' => 'Default Length',
        'fedex_def_width' => 'Default Width',
        'fedex_destination_res_com' => 'Delivery Type Default',
        'fedex_dev' => 'Development',
        'fedex_europe_first_international_priority' => 'Europe First International Priority',
        'fedex_10kg_box' => 'FedEx 10KG Box',
        'fedex_25kg_box' => 'FedEx 25KG Box',
        'fedex_package' => 'FedEx Package',
        'fedex_box' => 'FedEx Box',
        'fedex_envelope' => 'FedEx Envelope',
        'fedex_express_saver' => 'Domestic - Express Saver',
        'fedex_pak' => 'FedEx Pak',
        'fedex_tube' => 'FedEx Tube',
        'fedex_first_overnight' => 'Domestic - First Overnight',
        'fedex_forwarding_service' => 'Forwarding Service',
        'fedex_ground_home_delivery' => 'Domestic - Ground Home Delivery',
        'fedex_ground' => 'Domestic - Ground',
        'fedex_insurance_currency' => 'Insurance Currency (use 3 char currency code)',
        'fedex_insurance_default' => 'Insurance Amount Default',
        'fedex_international_economy' => 'International Economy',
        'fedex_international_first' => 'International First',
        'fedex_international_ground' => 'International Ground',
        'fedex_international_priority' => 'International Priority',
        'fedex_length_code' => 'Unit of Length Measurement',
        'fedex_live' => 'Live',
        'fedex_media_mail' => 'Media Mail',
        'fedex_meter_number' => 'Meter Number',
        'fedex_mode' => 'Are the above access keys developer or production credentials?',
        'fedex_none' => 'None',
        'fedex_origination_address' => 'Origination Address',
        'fedex_origination_address2' => 'Origination Address 2',
        'fedex_origination_city' => 'Origination City',
        'fedex_origination_res_com' => 'Origination Type Default',
        'fedex_origination_state' => 'Origination State',
        'fedex_origination_zip' => 'Origination Zip',
        'fedex_origination_country_code' => 'Origination Country Code',
        'fedex_parcel_return' => 'Parcel Return',
        'fedex_parcel_select' => 'Parcel Select',
        'fedex_password' => 'Password',
        'fedex_presorted_bound_printed_matter' => 'Presorted Bound Printed Matter',
        'fedex_presorted_standard' => 'Presorted Standard',
        'fedex_priority_overnight' => 'Domestic - Priority Overnight',
        'fedex_product_id' => 'Service Default',
        'fedex_rate_chart' => 'Pickup Type Default',
        'fedex_regular_pickup' => 'Regular Pickup',
        'fedex_request_courier' => 'Request Courier',
        'fedex_res' => 'Residential',
        'fedex_return_service' => 'Return Service',
        'fedex_selectable_rates' => 'Customer Selectable Rate Options',
        'fedex_standard_overnight' => 'Domestic - Standard Overnight',
        'fedex_station' => 'Station',
        'fedex_weight_code' => 'Unit of Weight Measurement',
        'fedex_your_packaging' => 'Your Packaging',

        // Errors
        'fedex_not_configured' => 'Shipping options compatible with your location have not been configured in the cart settings.',

        // General
        'dev' => 'Dev',
        'pounds' => 'Pounds',
        'kilograms' => 'Kilograms',
];
