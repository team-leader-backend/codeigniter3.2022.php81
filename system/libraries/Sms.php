<?php
defined('BASEPATH') OR exit('No direct script access allowed');
class CI_Sms{
    function __construct() {
    }
    function sendCodeSms(string $number, int $range){
        preg_match_all('/[0-9]+/',$number, $numberPhone);
        $numberPhoneStr = implode('',$numberPhone[0]);
        if(strlen($numberPhoneStr)!=11) return ['error'=>'The length of the number is not equal to 11 numbers'];
        $xml = <<<XML
<?xml  version="1.0" encoding="utf-8"?> 
<request>  
    <security>  
        <login>Login</login>  
        <password>Password</password>  
    </security>  
    <phone>$numberPhoneStr</phone>  
    <sender>SEVvektor</sender>  
    <random_string_len>$range</random_string_len>  
    <text>Код проверки</text> 
    </request>
XML;
        $curl = curl_init();
        curl_setopt_array($curl, array(
            CURLOPT_URL => 'http://apiagent.ru/password_generation/api.php',
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_ENCODING => '',
            CURLOPT_MAXREDIRS => 10,
            CURLOPT_TIMEOUT => 0,
            CURLOPT_FOLLOWLOCATION => true,
            CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
            CURLOPT_CUSTOMREQUEST => 'GET',
            CURLOPT_POSTFIELDS =>$xml,
            CURLOPT_HTTPHEADER => array(
                'Content-Type: text/plain'
            ),
        ));
        $response = curl_exec($curl);
        curl_close($curl);
        $xml = simplexml_load_string($response);
        $result = json_decode(json_encode($xml), true);
        if(isset($result['error'])) return $result;
        elseif(isset($result['success']['@attributes'])) return $result['success']['@attributes'];
        else return ['error'=>'Unknown answer'];
    }
    function getStatus(int $status){
        $xml = <<<XML
<?xml version="1.0" encoding="utf-8"?>
<request>
    <security>
        <login value="Login"/>
        <password value="Password"/>
   </security>
   <get_state>
        <id_sms>$status</id_sms>
   </get_state>
</request>
XML;
        $curl = curl_init();
        curl_setopt_array($curl, array(
            CURLOPT_URL => 'https://sms.targetsms.ru/xml/state.php',
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_ENCODING => '',
            CURLOPT_MAXREDIRS => 10,
            CURLOPT_TIMEOUT => 0,
            CURLOPT_FOLLOWLOCATION => true,
            CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
            CURLOPT_CUSTOMREQUEST => 'GET',
            CURLOPT_POSTFIELDS =>$xml,
            CURLOPT_HTTPHEADER => array(
                'Content-Type: text/plain'
            ),
        ));
        $response = curl_exec($curl);
        curl_close($curl);
        $xml = simplexml_load_string($response);
        $result = json_decode(json_encode($xml), true);
        return $result;
    }
    public function sendTextSms(string $number, string $text){
        $text = urlencode($text);
        preg_match_all('/[0-9]+/',$number, $numberPhone);
        $numberPhoneStr = implode('',$numberPhone[0]);
        if(strlen($numberPhoneStr)!=11) return ['error'=>'The length of the number is not equal to 11 numbers'];
        $curl = curl_init();
        curl_setopt_array($curl, array(
            CURLOPT_URL => "https://sms.targetsms.ru/sendsms.php?user=myu940&pwd=2Sb643092&name_delivery=Registration&sadr=SEVvektor&dadr=$numberPhoneStr&text=$text",
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_ENCODING => '',
            CURLOPT_MAXREDIRS => 10,
            CURLOPT_TIMEOUT => 0,
            CURLOPT_FOLLOWLOCATION => true,
            CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
            CURLOPT_CUSTOMREQUEST => 'GET',
            CURLOPT_POSTFIELDS =>'',
            CURLOPT_HTTPHEADER => array(
                'Content-Type: text/plain'
            ),
        ));
        $response = curl_exec($curl);
        curl_close($curl);
        return ['id_sms'=>$response];
    }
}
