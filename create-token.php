<?php
include('config.php');
$ch_1 = curl_init();
curl_setopt($ch_1, CURLOPT_URL, $apiEndPoint."/v1/oauth2/token");
curl_setopt($ch_1, CURLOPT_HEADER, false);
curl_setopt($ch_1, CURLOPT_SSL_VERIFYPEER, false);
curl_setopt($ch_1, CURLOPT_POST, true);
curl_setopt($ch_1, CURLOPT_RETURNTRANSFER, true); 
curl_setopt($ch_1, CURLOPT_USERPWD, $clientId.":".$secret);
curl_setopt($ch_1, CURLOPT_POSTFIELDS, "grant_type=client_credentials");

$result_1 = curl_exec($ch_1);

if(empty($result_1))die("Error: No response.");
else
{
    $json_1 = json_decode($result_1);
	$access_token = $json_1->access_token;
    //print_r($json_1->access_token);
}

curl_close($ch_1);


$data_2 = '{
}';

$ch_2 = curl_init();
curl_setopt($ch_2, CURLOPT_URL, $apiEndPoint."/v1/identity/generate-token");
curl_setopt($ch_2, CURLOPT_POST, true);
curl_setopt($ch_2, CURLOPT_POSTFIELDS, $data_2); 
curl_setopt($ch_2, CURLOPT_HTTPHEADER, array(
  "Content-Type: application/json",
  "Authorization: Bearer ".$access_token)
);

curl_setopt($ch_2, CURLOPT_CUSTOMREQUEST, "POST");
curl_setopt($ch_2, CURLOPT_RETURNTRANSFER, TRUE);

$result_2 = curl_exec($ch_2); 

if(empty($result_2))die("Error: No response.");
else
{

	$json_2 = json_decode($result_2);
	$client_token = $json_2->client_token;
}
curl_close($ch_2);

$data_3 = '{
  "intent": "CAPTURE",
  "application_context": {
    "brand_name": "MY BRAND NAME",
    "shipping_preference": "SET_PROVIDED_ADDRESS",
    "locale": "en-IN", 
    "user_action": "PAY_NOW",
    "return_url": "http://your_URL.com/success",
    "cancel_url": "http://your_URL.com/cancel",
    "payment_method": {
      "payer_selected": "PAYPAL"
    }
  },
  "payer": {
    "name": {
      "given_name": "first name",
      "surname": "last name"
    },
    "email_address": "buyer-test-us@paypal.com",
    "phone": {
      "phone_number": {
        "national_number": "9874563210"
      }
    },
    "address": {
      "address_line_1": "4th Floor",
      "address_line_2": "Unit #34",
      "admin_area_2": "chennai",
      "admin_area_1": "Tamil Nadu",
      "postal_code": "600096",
      "country_code": "IN"
    }
  },
  "purchase_units": [
    {
      "amount": {
        "currency_code": "'.$currency_code.'",
        "value": "220.00",
        "breakdown": {
          "item_total": {
            "currency_code": "'.$currency_code.'",
            "value": "180.00"
          },
          "discount": {
            "currency_code": "'.$currency_code.'",
            "value": "20.00"
          },
          "insurance": {
            "currency_code": "'.$currency_code.'",
            "value": "20.00"
          },
          "shipping": {
            "currency_code": "'.$currency_code.'",
            "value": "20.00"
          },
          "handling": {
            "currency_code": "'.$currency_code.'",
            "value": "10.00"
          },
          "tax_total": {
            "currency_code": "'.$currency_code.'",
            "value": "20.00"
          },
          "shipping_discount": {
            "currency_code": "'.$currency_code.'",
            "value": "10.00"
          }
        }
      },
      "items": [
        {
          "name": "T-Shirt",
          "description": "Green XL",
          "sku": "sku01",
          "unit_amount": {
            "currency_code": "'.$currency_code.'",
            "value": "90.00"
          },
          "tax": {
            "currency_code": "'.$currency_code.'",
            "value": "10.00"
          },
          "quantity": "1",
          "category": "PHYSICAL_GOODS"
        },
        {
          "name": "Shoes",
          "description": "Running, Size 10.5",
          "sku": "sku02",
          "unit_amount": {
            "currency_code": "'.$currency_code.'",
            "value": "45.00"
          },
          "tax": {
            "currency_code": "'.$currency_code.'",
            "value": "5.00"
          },
          "quantity": "2",
          "category": "PHYSICAL_GOODS"
        }
      ],
       "shipping": {
        "name": {
          "full_name": "Shipping to buyer info"
        },
        "address": {
          "address_line_1": "shipp to 4th Floor",
          "address_line_2": "shipp to Unit #34",
          "admin_area_2": "chennai",
          "admin_area_1": "Tamil Nadu",
          "postal_code": "600096",
          "country_code": "IN"
        }
      },
      "description": "test transaction",
      "custom_id": "CUST-ID-'.rand().'",
      "invoice_id": "INV-ID-'.rand().'",
      "soft_descriptor": "some soft description"
    }
  ]
}';

$ch_3 = curl_init();
curl_setopt($ch_3, CURLOPT_URL, "https://api.sandbox.paypal.com/v2/checkout/orders");
curl_setopt($ch_3, CURLOPT_POST, true);
curl_setopt($ch_3, CURLOPT_POSTFIELDS, $data_3); 
curl_setopt($ch_3, CURLOPT_HTTPHEADER, array(
  "Content-Type: application/json",
  "Authorization: Bearer ".$access_token, 
  "Content-length: ".strlen($data_3))
);

curl_setopt($ch_3, CURLOPT_CUSTOMREQUEST, "POST");
curl_setopt($ch_3, CURLOPT_RETURNTRANSFER, TRUE);

$result_3 = curl_exec($ch_3);

if(empty($result_3))die("Error: No response.");
else
{
	$json_3 = json_decode($result_3);
	$order_id = $json_3->id;
}
curl_close($ch_3);
?>