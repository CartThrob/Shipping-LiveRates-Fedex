<p><strong>CHANGE shipping_plugin parameter to one of the following wherever it appears: shipping_fedex, shipping_ups, shipping_usps</strong></p>

<p>CartThrob live rates plugins require customer input. As such, it's usually best to capture customer location information BEFORE the checkout page.  This way, on the checkout page all shipping costs are reflective of customer's set shipping location. Without requiring customer input, live rates are never captured, and shipping costs will not be set. The sample code below is basic code for getting live rates returned. The live rates plugins will, by default, use any information it can find to gather and prepoulate shipping rates, but this information may not be accurate to the customer's location. Because of this, the customer needs to review their information and submit it for a quote. At this point they can accept one of the quoted rates, which wil set the shipping for the cart. </p>

<p>Any live rates plugin that is set up as the default shipping plugin for your site, will zero out the set shipping amount every time the cart is updated or added to. This gives you the ability to force the customer to update their shipping information before they attempt to check out. </p>

 
		{exp:cartthrob:customer_info}
			<select name="shipping_option">
			    {exp:cartthrob:get_shipping_options shipping_plugin="shipping_fedex"}
			        <option value="{rate_short_name}" {selected}>{rate_title} - {rate_price}</option>
			    {/exp:cartthrob:get_shipping_options}
			</select>
		{/exp:cartthrob:customer_info}
		
		Shipping: {exp:cartthrob:cart_shipping}<br />
		Cart Total: {exp:cartthrob:cart_total}
		{exp:cartthrob:debug_info}
		
If you use the live_rates="yes" parameter in the checkout form, the checkout form will not allow the customer to checkout if there is a shipping error, or if the shipping requires an update. 

 		{exp:cartthrob:checkout_form return="" live_rates="yes"}

		{gateway_fields}

		<input type="submit" value="Checkout" />

		{/exp:cartthrob:checkout_form}


