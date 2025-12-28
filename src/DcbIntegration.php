<?php

namespace Poagas\DcbIntegration;

use Carbon\Carbon;
use DateTime;
use GuzzleHttp\Client;
use GuzzleHttp\Psr7\Request;
use Log;

class DcbIntegration
{


  protected $base_url;
  protected $client_id;
  protected $client_secret;
  protected $request_date;



  public function __construct($base_url, $client_id, $client_secret)
  {
    $dt = new DateTime();
    $this->request_date=$dt->format('Y-m-d H:i:s');
    $this->base_url = $base_url;
    $this->client_id = $client_id;
    $this->client_secret = $client_secret;
  }

  public function create_token()
  {


    $client = new Client;
    $headers = [
      'Content-Type' => 'application/json'
    ];
    $body = '{
  "clientId": "' . $this->client_id . '",
  "clientSecret": "' . $this->client_secret . '",
  "datetime":"'.$this->request_date.'"

}';
    $request = new Request('POST', $this->base_url . '/koinetPay/client/v3/authentication/token', $headers, $body);
    $res = $client->sendAsync($request)->wait();

    return $res->getBody()->getContents();
  }


  public function query_fsp($token)
  {


    $client = new Client();
    $headers = [
     "datetime"=>"'.$this->request_date.'",
      'Content-Type' => 'application/json',
      'Authorization' => 'Bearer ' . $token,
      
    ];
    $request = new Request('GET', $this->base_url . '/koinetPay/aggregator/v3/queryTipsFsps', $headers);
    $res = $client->sendAsync($request)->wait();
    return $res->getBody()->getContents();
  }

  public function account_lookup($token, $accountNo, $institutionCode)
  {

    $client = new Client();
    $headers = [
      'Content-Type' => 'application/json',
      'Authorization' => 'Bearer ' . $token,
      "datetime"=>"'.$this->request_date.'"
    ];
    $body = '{
  "accountNo": "' . $accountNo . '",
  "institutionCode": "' . $institutionCode . '"
}';
    $request = new Request('POST', $this->base_url . '/koinetPay/aggregator/v3/accountLookup', $headers, $body);
    $res = $client->sendAsync($request)->wait();
    return $res->getBody()->getContents();
  }



  public function merchant_lookup($token, $tillNumber, $merchantCode)
  {

    $client = new Client();
    $headers = [
      'Content-Type' => 'application/json',
      'Authorization' => 'Bearer ' . $token,
      "datetime"=>"'.$this->request_date.'"
    ];
    $body = '{
  "tillNumber": "' . $tillNumber . '",
  "merchantCode": "' . $merchantCode . '"
}';

Log::info("body ::: ".$body);

    $request = new Request('POST', $this->base_url . '/koinetPay/tips/v3/merchantLookup', $headers, $body);
    $res = $client->sendAsync($request)->wait();
    return $res->getBody()->getContents();
  }


  public function wallet_transfer($token, $payload)
  {
    $client = new Client();
    $headers = [
      'Content-Type' => 'application/json',
      'Authorization' => 'Bearer ' . $token,
      "datetime"=>"'.$this->request_date.'"
    ];
    $body = '{
  "sourceAccount": "' . $payload['sourceAccount'] . '",
  "senderName": "' . $payload['senderName'] . '",
  "msisdn": "' . $payload['msisdn'] . '",
  "destinationAccount": "' . $payload['destinationAccount'] . '",
  "amount": "' . $payload['amount'] . '",
  "purpose": "' . $payload['purpose'] . '",
  "beneficiaryName": "' . $payload['beneficiaryName'] . '",
  "institutionCode": "' . $payload['institutionCode'] . '",
  "reference": "' . $payload['reference'] . '",
  "callbackUrl": "' . $payload['callbackUrl'] . '"
}';
    $request = new Request('POST', $this->base_url . '/koinetPay/aggregator/v3/transfers', $headers, $body);
    $res = $client->sendAsync($request)->wait();
    return $res->getBody()->getContents();
  }


  public function send_auth_token($token, $trans_token, $reference)
  {
    $client = new Client();
    $headers = [
      'Content-Type' => 'application/json',
      'Authorization' => 'Bearer ' . $token,
      "datetime"=>"'.$this->request_date.'"
    ];
    $body = '{
  "token": "' . $trans_token . '",
  "reference": "' . $reference . '"
}';
    $request = new Request('POST', $this->base_url . '/koinetPay/aggregator/v3/paymentAuthorization', $headers, $body);
    $res = $client->sendAsync($request)->wait();
    return $res->getBody()->getContents();
  }


  public function merchant_payment($token, $payload)
  {

    $client = new Client();
    $headers = [
      'Content-Type' => 'application/json',
      'Authorization' => 'Bearer ' . $token,
      "datetime"=>"'.$this->request_date.'"
    ];

    $body = '{
  "sourceAccount": "' . $payload["sourceAccount"] . '",
  "senderName": "' . $payload["senderName"] . '",
  "customerPhone": "' . $payload["customerPhone"] . '",
  "tillNo": "' . $payload["tillNo"] . '",
  "amount": "' . $payload["amount"] . '",
  "purpose": "' . $payload["purpose"] . '",
  "beneficiaryName": "' . $payload["beneficiaryName"] . '",
  "merchantCode": "' . $payload["merchantCode"] . '",
  "reference": "' . $payload["reference"] . '",
  "callbackUrl": "' . $payload["callbackUrl"] . '"
}';
    $request = new Request('POST', $this->base_url . '/koinetPay/aggregator/v3/merchantPayments', $headers, $body);
    $res = $client->sendAsync($request)->wait();

    return $res->getBody()->getContents();
  }


  public function bill_payments($token, $payload)
  {
    $client = new Client();
    $headers = [
      'Content-Type' => 'application/json',
      'Authorization' => 'Bearer ' . $token,
      "datetime"=>"'.$this->request_date.'"
    ];
    $body = '{
  "sourceAccount": "' . $payload['sourceAccount'] . '",
  "customerNo": "' . $payload['customerNo'] . '",
  "senderName": "' . $payload['senderName'] . '",
  "msisdn": "' . $payload['msisdn'] . '",
  "smartCard": "' . $payload['smartCard'] . '",
  "amount": "' . $payload['amount'] . '",
  "purpose": "' . $payload['purpose'] . '",
  "beneficiaryName": "' . $payload['beneficiaryName'] . '",
  "billerCode": "' . $payload['billerCode'] . '",
  "description": "' . $payload['description'] . '",
  "reference": "' . $payload['reference'] . '",
  "callbackUrl": "' . $payload['callbackUrl'] . '"
}';
    $request = new Request('POST', $this->base_url . '/koinetPay/aggregator/v3/billerPayment', $headers, $body);
    $res = $client->sendAsync($request)->wait();


    return $res->getBody()->getContents();
  }

  public function bill_lookup($token,$smartCardNumber,$reference,$billerCode){
        $client = new Client();
    $headers = [
      'Content-Type' => 'application/json',
      'Authorization' => 'Bearer ' . $token,
      "datetime"=>"'.$this->request_date.'"
    ];

$body = '{
  "smartCardNumber": "'.$smartCardNumber.'",
  "reference": "'.$reference.'",
  "billerCode": "'.$billerCode.'"
}';
$request = new Request('POST', $this->base_url.'/koinetPay/aggregator/v3/billersLookup', $headers, $body);
      $res = $client->sendAsync($request)->wait();


    return $res->getBody()->getContents();
  }

  public function gepg_lookup($token,$controlNo,$amount,$currency){

     $client = new Client();
    $headers = [
      'Content-Type' => 'application/json',
      'Authorization' => 'Bearer ' . $token,
      "datetime"=>"'.$this->request_date.'"
    ];

    $body = '{
  "controlNo": "'.$controlNo.'",
  "amount": "'.$amount.'",
  "currency": "'.$currency.'"
}';
$request = new Request('POST', $this->base_url.'/koinetPay/aggregator/v3/gepgLookup', $headers, $body);
$res = $client->sendAsync($request)->wait();

    return $res->getBody()->getContents();
  }
  public function biller_codes(){
    return '{{
    "id": 1,
    "Biller Code": "DSTV",
    "Biller Description": "DSTV subscriptions"
  },
  {
    "id": 2,
    "Biller Code": "DSTVBO",
    "Biller Description": "DSTV Box office subscriptions"
  },
  {
    "id": 3,
    "Biller Code": "AZAMTV",
    "Biller Description": "AZAMTV subscriptions"
  },
  {
    "id": 4,
    "Biller Code": "STARTIMES",
    "Biller Description": "STARTIMES subscriptions"
  },
  {
    "id": 5,
    "Biller Code": "ZUKU",
    "Biller Description": "ZUKU subscriptions"
  },
  {
    "id": 6,
    "Biller Code": "SMILE",
    "Biller Description": "SMILE 4G internet"
  },
  {
    "id": 7,
    "Biller Code": "ZUKUFIBER",
    "Biller Description": "ZUKU fiber internet"
  },
  {
    "id": 8,
    "Biller Code": "TTCL",
    "Biller Description": "TTCL Prepaid and broadband"
  },
  {
    "id": 10,
    "Biller Code": "GePG",
    "Biller Description": "Government payments"
  },
  {
    "id": 11,
    "Biller Code": "PW",
    "Biller Description": "Precision air payments"
  },
  {
    "id": 12,
    "Biller Code": "LUKU",
    "Biller Description": "Prepaid electricity"
  },
  {
    "id": 13,
    "Biller Code": "TOP",
    "Biller Description": "Prepaid airtimes"
  }

  }';
}

}
