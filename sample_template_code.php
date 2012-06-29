<p><strong>CHANGE shipping_plugin parameter to one of the following wherever it appears: shipping_fedex, shipping_ups</strong></p>

<p>CartThrob live rates plugins require customer input. As such, it's usually best to capture customer location information BEFORE the checkout page.  This way, on the checkout page all shipping costs are reflective of customer's set shipping location. Without requiring customer input, live rates are never captured, and shipping costs will not be set. The sample code below is basic code for getting live rates returned. The live rates plugins will, by default, use any information it can find to gather and prepoulate shipping rates, but this information may not be accurate to the customer's location. Because of this, the customer needs to review their information and submit it for a quote. At this point they can accept one of the quoted rates, which wil set the shipping for the cart. </p>

<p>Any live rates plugin that is set up as the default shipping plugin for your site, will zero out the set shipping amount every time the cart is updated or added to. This gives you the ability to force the customer to update their shipping information before they attempt to check out. </p>


 	{exp:cartthrob:enforce_shipping}
		{if no_results}
			You must update your shipping selection
		{/if}
		
		{exp:cartthrob:customer_info}
			{exp:cartthrob:get_live_rates_form shipping_plugin="shipping_ups" return="" } 
			{!-- activate_plugin="yes"  use this parameter to set this as the primary plugin to override other shipping plugins--}
				<label for="shipping_zip">Shipping Postal Code
					<input value="{customer_shipping_zip}" type="text" id="shipping_zip" name="shipping_zip" />
				</label>
				<br />
				<label for="shipping_state">Shipping State
					{exp:cartthrob:state_select name="shipping_state" id="shipping_state" add_blank="yes" selected="{customer_shipping_state}"}
				</label>
				<br />
				<label for="shipping_country_code">Shipping Country 
					{exp:cartthrob:country_select name="shipping_country_code" id="shipping_country_code" add_blank="yes" selected="{customer_shipping_country_code}"}
				</label>
				<br />

				<label for="shipping_zip">Shipping Option
																							{!-- adding a prefix to reduce possible variable name clashes --}
						{exp:cartthrob:get_shipping_options shipping_plugin="shipping_ups" variable_prefix="liverates_"}
							{!-- without the liverates_ variable prefix, this would just be {count} --}
							{if liverates_count == 1}
								<select name="shipping_option">
							{/if}
				 				<option value="{liverates_rate_short_name}" {liverates_selected}>{liverates_rate_title} - {liverates_price}</option>
							{if liverates_count == liverates_total_results}
								</select>
							{/if}
							{if error_message}{error_message}{/if}
							{if no_results}
								Live rates are currently unavailable
							{/if}
						{/exp:cartthrob:get_shipping_options}
				</label>

				<br />	
				<input type="submit" />

			{/exp:cartthrob:get_live_rates_form}
		{/exp:cartthrob:customer_info}
		
	{/exp:cartthrob:enforce_shipping}



	Shipping: {exp:cartthrob:cart_shipping}<br />
	Cart Total: {exp:cartthrob:cart_total}
	{exp:cartthrob:debug_info}