<?php 
include('config.php'); 
include('create-token.php'); 
?>
<html>
<head>

  <meta charset="utf-8"/>
  <meta name="viewport" content="width=device-width, initial-scale=1"> <!-- Optimal rendering on mobile devices. -->
  <meta http-equiv="X-UA-Compatible" content="IE=edge" /> <!-- Optimal Internet Explorer compatibility -->
  <script src="https://code.jquery.com/jquery-1.11.1.min.js"></script>
  <link rel="stylesheet" type="text/css" href="cardfields.css"/>

</head>
<body>

<!-- JavaScript SDK -->
 <script src="https://www.paypal.com/sdk/js?components=hosted-fields,buttons&client-id=<?php echo $clientId; ?>" data-client-token="<?php echo $client_token; ?>"></script>

   <!-- Buttons container -->
   <table border="0" align="center" valign="top" bgcolor="#FFFFFF" style="width:39%">
     <tr>
       <td colspan="2">
         <div id="paypal-button-container"></div>
       </td>
     </tr>
     <tr><td colspan="2">&nbsp;</td></tr>
   </table>

   <div align="center"> or </div>

   <!-- Advanced credit and debit card payments form -->
   <div class='card_container'>
     <form id='my-sample-form'>
		<div id="payments-sdk__contingency-lightbox"></div>
       <label for='card-number'>Card Number</label><div id='card-number' class='card_field'></div>
       <div>
         <label for='expiration-date'>Expiration Date</label><div id='expiration-date' class='card_field'></div>
       </div>
       <div>
         <label for='cvv'>CVV</label><div id='cvv' class='card_field'></div>
       </div>
       <label for='card-holder-name'>Name on Card</label><input type='text' id='card-holder-name' name='card-holder-name' autocomplete='off' placeholder='card holder name'/>
       <div>
         <label for='card-billing-address-street'>Billing Address</label><input type='text' id='card-billing-address-street' name='card-billing-address-street' autocomplete='off' placeholder='street address'/>
       </div>
       <div>
         <label for='card-billing-address-unit'>&nbsp;</label><input type='text' id='card-billing-address-unit' name='card-billing-address-unit' autocomplete='off' placeholder='unit'/>
       </div>
       <div>
         <input type='text' id='card-billing-address-city' name='card-billing-address-city' autocomplete='off' placeholder='city'/>
       </div>
       <div>
         <input type='text' id='card-billing-address-state' name='card-billing-address-state' autocomplete='off' placeholder='state'/>
       </div>
       <div>
         <input type='text' id='card-billing-address-zip' name='card-billing-address-zip' autocomplete='off' placeholder='zip / postal code'/>
       </div>
       <div>
         <input type='text' id='card-billing-address-country' name='card-billing-address-country' autocomplete='off' placeholder='country code' />
       </div>
       <br><br>
       <button value='submit' id='submit' class='btn'>Pay</button>
     </form>
   </div>

   <!-- Implementation -->
   <script>
     //Displays PayPal buttons
     paypal.Buttons({
       commit: false,
          createOrder: function(data, actions) {
           // This function sets up the details of the transaction, including the amount and line item details
           return actions.order.create({
             purchase_units: [{
               amount: {
                 value: '2'
               }
             }]
           });
         },
         onCancel: function (data) {
             // Show a cancel page, or return to cart
          },
         onApprove: function(data, actions) {
           // This function captures the funds from the transaction
           return actions.order.capture().then(function(details) {
             // This function shows a transaction success message to your buyer
             alert('Thanks for your purchase!');
           });
         }
     }).render('#paypal-button-container');
     // Eligibility check for advanced credit and debit card payments
     if (paypal.HostedFields.isEligible()) {
       paypal.HostedFields.render({
         createOrder: function () {return "<?php echo $order_id; ?>";}, // replace order-ID with the order ID
         styles: {
           'input': {
             'font-size': '17px',
             'font-family': 'helvetica, tahoma, calibri, sans-serif',
             'color': '#3a3a3a'
           },
           ':focus': {
             'color': 'black'
           }
         },
         fields: {
           number: {
             selector: '#card-number',
             placeholder: 'card number'
           },
           cvv: {
             selector: '#cvv',
             placeholder: 'card security number'
           },
           expirationDate: {
             selector: '#expiration-date',
             placeholder: 'mm/yy'
           }
         }
       }).then(function (hf) {
         $('#my-sample-form').submit(function (event) {
           event.preventDefault();
           hf.submit({
			 // Trigger 3D Secure authentication
             contingencies: ['3D_SECURE'],
             // Cardholder Name
             cardholderName: document.getElementById('card-holder-name').value,
             // Billing Address
             billingAddress: {
               streetAddress: document.getElementById('card-billing-address-street').value,      // address_line_1 - street
               extendedAddress: document.getElementById('card-billing-address-unit').value,       // address_line_2 - unit
               region: document.getElementById('card-billing-address-state').value,           // admin_area_1 - state
               locality: document.getElementById('card-billing-address-city').value,          // admin_area_2 - town / city
               postalCode: document.getElementById('card-billing-address-zip').value,           // postal_code - postal_code
               countryCodeAlpha2: document.getElementById('card-billing-address-country').value   // country_code - country
             }
           // redirect after successful order approval
           }).then(function (payload) {
			   console.log('data: ', JSON.stringify(payload));
			   if ((payload.liabilityShifted == true) && (payload.liabilityShift === 'POSSIBLE') && (payload.authenticationStatus === 'YES')&& (payload.authenticationReason === 'SUCCESSFUL') // please refer this link for more info https://developer.paypal.com/docs/business/checkout/add-capabilities/3d-secure/#response-parameters
                /*
				Based on the results of EnrollmentStatus and AuthenticationResult, a LiabilityShift response is returned. The LiabilityShift response determines how you might proceed with authentication.
				Note: If you integrated 3D Secure prior to June 2020, the liabilityShifted, authenticationStatus, and AuthenticationReason parameters continue to work on the server, but are no longer supported.
                */
                {
                    alert('liabilityShifted = true and liabilityShift = POSSIBLE and authenticationStatus and YES and authenticationReason = SUCCESSFUL');
					window.location.replace('review.php?order_id=<?php echo $order_id; ?>');
                }

                if (payload.liabilityShift === possible) {
                     // Handle no 3D Secure contingency passed scenario
                }

                if (payload.liabilityShift) {
                     // Handle buyer confirmed 3D Secure successfully
                }
        
      }).catch(function (err) {
        console.log('error: ', JSON.stringify(err));
        document.getElementById("consoleLog").innerHTML = JSON.stringify(err);
           });
         });
       });
     }
     else {
       $('#my-sample-form').hide();  // hides the advanced credit and debit card payments fields if merchant isn't eligible
     }
   </script>

   </body>
   </html>
