<?php
require 'SimpleCSV.php';
class Helper extends SimpleCSV
{
    public function __construct()
    {
    }
    public function timeInterval($start, $stop){
        // Дата начала интервала
        $start = new DateTime($start);
// Дата окончания интервала
        $end = new DateTime($stop);
// Интервал в один день
        $step = new DateInterval('P1D');
// Итератор по дням
        $period = new DatePeriod($start, $step, $end);

// Вывод дней
        foreach($period as $datetime) {
            $array_date[] = $datetime->format("d.m.Y");
        }
        return $array_date;
    }
    public function generatePassword(){
        $token = '';
        for($i=0;$i<10;$i++){
            //48-57 ; 65-90
            do{
                $char = rand(40,100);
            }while(($char<49)||($char>90));
            if(($char>57)&&($char<65)){
                $token .=(string)$char;
            }else{
                if($char==79){
                    $token .= 'A';
                }else{
                    $token .=chr($char);
                }
            }
        }
        $pass = '';
        foreach (str_split($token) as $sym){
            if(rand(1,100)<50){
                $pass .= mb_strtolower($sym);
            }else{
                $pass .= $sym;
            }
        }
        return $pass;
    }
    public static function get_ip()
    {
        $value = '';
        if (!empty($_SERVER['HTTP_CLIENT_IP'])) {
            $value = $_SERVER['HTTP_CLIENT_IP'];
        } elseif (!empty($_SERVER['HTTP_X_FORWARDED_FOR'])) {
            $value = $_SERVER['HTTP_X_FORWARDED_FOR'];
        } elseif (!empty($_SERVER['REMOTE_ADDR'])) {
            $value = $_SERVER['REMOTE_ADDR'];
        }

        return $value;
    }
    public function ipuser(){
        $client  = @$_SERVER['HTTP_CLIENT_IP'];
        $forward = @$_SERVER['HTTP_X_FORWARDED_FOR'];
        $remote  = @$_SERVER['REMOTE_ADDR'];

        if(filter_var($client, FILTER_VALIDATE_IP)) $ip = $client;
        elseif(filter_var($forward, FILTER_VALIDATE_IP)) $ip = $forward;
        else $ip = $remote;
        $session_id = $_SERVER['HTTP_COOKIE'] ?? null;
        session_start();
        $user_id = $_COOKIE['user_id'] ?? null;
        session_write_close();
        return ['ip'=>$ip, 'port'=>$_SERVER['REMOTE_PORT'], 'session_id'=>ltrim($session_id,'PHPSESSID='), 'user_id'=>$user_id];
    }
    public function generateToken($range=20){
        $token = '';
        for($i=0;$i<$range;$i++){
            //48-57 ; 65-90
            do{
                $char = rand(40,100);
            }while(($char<48)||($char>90));
            if(($char>57)&&($char<65)){
                $token .=(string)$char;
            }else{
                if($char==79){
                    $token .=(string)$char;
                }else $token .=chr($char);
            }
        }
        return $token;
    }
    public static function out_d($val)
    {
        echo '<pre>';
        print_r($val);
        echo '</pre>';
    }

    //========================================================================
    public static function dump($val)
    {
        echo '<pre>';
        var_dump($val);
        echo '</pre>';
    }

    //========================================================================

    public function send_json($data)
    {
        header('Access-Control-Allow-Origin: *');
        header('Access-Control-Allow-Credentials: true');
        header('Access-Control-Max-Age: 1728000');
        header("Access-Control-Allow-Headers: X-Requested-With");
        header('Access-Control-Allow-Methods: POST, GET, DELETE, PUT, PATCH, OPTIONS');
        header('Access-Control-Allow-Headers: Origin, Content-Type, X-Auth-Token , Authorization');
        header('Content-Type:application/json;');
        echo $data;
        return null;
    }

    //===========================================================================
    public function send_array($data)
    {
        $result = json_encode($data);
        header('Access-Control-Allow-Origin: *');
        header('Access-Control-Allow-Credentials: true');
        header('Access-Control-Max-Age: 1728000');
        header("Access-Control-Allow-Headers: X-Requested-With");
        header('Access-Control-Allow-Methods: POST, GET, DELETE, PUT, PATCH, OPTIONS');
        header('Access-Control-Allow-Headers: Origin, Content-Type, X-Auth-Token , Authorization');
        header('Content-Type:application/json;');
        echo $result;
        return null;
    }
    //===========================================================================
    public function send_array404($data)
    {
        $result = json_encode($data);
        header($_SERVER["SERVER_PROTOCOL"]." 404 Not Found", true, 404);
        header('Access-Control-Allow-Origin: *');
        header('Access-Control-Allow-Credentials: true');
        header('Access-Control-Max-Age: 1728000');
        header("Access-Control-Allow-Headers: X-Requested-With");
        header('Access-Control-Allow-Methods: POST, GET, DELETE, PUT, PATCH, OPTIONS');
        header('Access-Control-Allow-Headers: Origin, Content-Type, X-Auth-Token , Authorization');
        header('Content-Type:application/json;');
        echo $result;
        return null;
    }
    //===========================================================================
    public function send_array400($description)
    {
        $result = json_encode(['error_description'=>$description, 'error_code'=>400]);
        header($_SERVER["SERVER_PROTOCOL"]." 400 Bad Request", true, 400);
        header('Access-Control-Allow-Origin: *');
        header('Access-Control-Allow-Credentials: true');
        header('Access-Control-Max-Age: 1728000');
        header("Access-Control-Allow-Headers: X-Requested-With");
        header('Access-Control-Allow-Methods: POST, GET, DELETE, PUT, PATCH, OPTIONS');
        header('Access-Control-Allow-Headers: Origin, Content-Type, X-Auth-Token , Authorization');
        header('Content-Type:application/json;');
        echo $result;
        return null;
    }
    //===========================================================================
    public function getRequest($address)
    {
        $host = $address;
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_HEADER, 0);
        curl_setopt($ch, CURLOPT_REFERER, 'yandex.ru');
        curl_setopt($ch, CURLOPT_USERAGENT, "Opera/9.80 (Windows NT 5.1; U; ru) Presto/2.9.168 Version/11.51");
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
        curl_setopt($ch, CURLOPT_URL, $host);
        curl_setopt($ch, CURLOPT_TIMEOUT, 10);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($ch, CURLOPT_FOLLOWLOCATION, 1);
        $result = curl_exec($ch);
        curl_close($ch);
        return $result;
    }

    //===========================================================================
    public function postRequest($host, $data)
    {
        $curl = curl_init();
        curl_setopt($curl, CURLOPT_URL, $host);
        curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($curl, CURLOPT_POST, true);
        curl_setopt($curl, CURLOPT_POSTFIELDS, $data);
        $out = curl_exec($curl);
        curl_close($curl);
        return $out;
    }
    public function getRequestToken($host="http://10.0.1.126/1c/hs/exchange/TypesOfPrices", $auth_string='Web:pzt58dgw543'){
        $token = base64_encode($auth_string);
        $ch = curl_init();
        $authorization = "Authorization: Basic " . $token;
        curl_setopt($ch, CURLOPT_URL, $host);
        curl_setopt($ch, CURLOPT_HTTPHEADER, array('Content-Type: application/json', $authorization));
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, TRUE);
        curl_setopt($ch, CURLOPT_FOLLOWLOCATION, TRUE);
        curl_setopt($ch, CURLOPT_REFERER, "https://yandex.ru");
        curl_setopt($ch, CURLOPT_USERAGENT, "Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/91.0.4472.106 Safari/537.36");
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
        curl_setopt($ch, CURLOPT_TIMEOUT, 10);
        $html = curl_exec($ch);
        echo curl_error($ch);
        curl_close($ch);
        return json_decode($html);
    }

    //===========================================================================
    public function postRequestToken($host, $token, $data)
    {
        $ch = curl_init();
        $post = json_encode($data);
        $authorization = "Authorization: Bearer " . $token;
        curl_setopt($ch, CURLOPT_URL, $host);
        curl_setopt($ch, CURLOPT_HTTPHEADER, array('Content-Type: application/json', $authorization));
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, TRUE);
        curl_setopt($ch, CURLOPT_POST, TRUE);
        curl_setopt($ch, CURLOPT_CUSTOMREQUEST, "POST");
        curl_setopt($ch, CURLOPT_POSTFIELDS, $post);
        curl_setopt($ch, CURLOPT_FOLLOWLOCATION, TRUE);
        $out = curl_exec($ch);
        curl_close($ch);
        return $out;
    }

    //=========================================================================
    public function capcha()
    {
        $img = '';
        $capcha_array = '';
        for ($i = 1; $i < 5; $i++) {
            $rand = rand(0, 31);
            $img = $img . '<img src="' . '/capcha/' . CAPCHA[$rand][1] . '" style="max-height: 50px;">';
            $capcha_array = $capcha_array . CAPCHA[$rand][0];
        }
        session_start();
        $_SESSION['capcha'] = $capcha_array;
        session_write_close();
        return $img;
    }

    //=========================================================================
    public function get_capcha()
    {
        $img = '';
        $capcha_array = '';
        for ($i = 1; $i < 5; $i++) {
            $rand = rand(0, 31);
            $img = $img . '<img src="' . '/capcha/' . CAPCHA[$rand][1] . '" style="max-height: 50px;">';
            $capcha_array = $capcha_array . CAPCHA[$rand][0];
        }
        session_start();
        $_SESSION['capcha'] = $capcha_array;
        session_write_close();
        return $img;
    }

    //=========================================================================
    public function rus_to_lat($str)
    {
        $str = preg_replace('/[^a-zA-Zа-яА-ЯёЁ0-9_\. ]/u', '', $str);
        $rus = array('А', 'Б', 'В', 'Г', 'Д', 'Е', 'Ё', 'Ж', 'З', 'И', 'Й', 'К', 'Л', 'М', 'Н', 'О', 'П', 'Р', 'С', 'Т', 'У', 'Ф', 'Х', 'Ц', 'Ч', 'Ш', 'Щ', 'Ъ', 'Ы', 'Ь', 'Э', 'Ю', 'Я', 'а', 'б', 'в', 'г', 'д', 'е', 'ё', 'ж', 'з', 'и', 'й', 'к', 'л', 'м', 'н', 'о', 'п', 'р', 'с', 'т', 'у', 'ф', 'х', 'ц', 'ч', 'ш', 'щ', 'ъ', 'ы', 'ь', 'э', 'ю', 'я', ' ', '.', '(', ')', ',', '®', '/', '\\', '|');
        $lat = array('A', 'B', 'V', 'G', 'D', 'E', 'E', 'Gh', 'Z', 'I', 'Y', 'K', 'L', 'M', 'N', 'O', 'P', 'R', 'S', 'T', 'U', 'F', 'H', 'C', 'Ch', 'Sh', 'Sch', 'Y', 'Y', 'Y', 'E', 'Yu', 'Ya', 'a', 'b', 'v', 'g', 'd', 'e', 'e', 'gh', 'z', 'i', 'y', 'k', 'l', 'm', 'n', 'o', 'p', 'r', 's', 't', 'u', 'f', 'h', 'c', 'ch', 'sh', 'sch', 'y', 'y', 'y', 'e', 'yu', 'ya', '_', '.', '_', '_', '_', '', '', '', '');
        return str_replace($rus, $lat, $str);
    }

    //=========================================================================
    public function diff_two_dimensional_array($array)
    {
        self::out_d($array);
        $array2 = $array;
        foreach ($array2 as $key2 => $arr2) {
            $array_key[] = $key2;   // Массив ключей
        }
        $diff_array[] = $array[$array_key[0]];
        for ($i = 0; $i < count($array_key); $i++) {
            for ($ii = $i + 1; $ii < count($array_key); $ii++) {
                $first_el = (array)$array2[$array_key[$i]];
                $sec_el = (array)$array[$array_key[$ii]];
                $res_diff = count(array_diff($first_el, $sec_el));
                echo $i, '==?==', $ii, '==->', $res_diff, '<br>';
                if ($res_diff == 0) {
                    unset($array[$array_key[$ii]]);
                }
            }
        }
        self::out_d($array);
    }

    //=========================================================================
    public function correrct_nulled($word = '')
    {
        $yauri = "https://speller.yandex.net/services/spellservice.json/checkText?text=" . $word;
        $json = $this->getRequest($yauri);
        $array_correct = json_decode($json);
        return $array_correct;
    }

    //=========================================================================
    public function correrct_spell($word = '')
    {
        $yauri = "https://speller.yandex.net/services/spellservice.json/checkText?lang=ru&text=" . $word;
        $json = $this->getRequest($yauri);
        $array_correct = json_decode($json);
        if (count($array_correct) == 0) return null;
        $correct = $array_correct[0]->s[0];
        return $correct;
    }

    //=========================================================================
    public function correrct_spell_v2($word)
    {
        $word = urlencode($word);
        $yauri = "https://speller.yandex.net/services/spellservice.json/checkText?lang=ru&text=" . $word;
        $json = $this->getRequest($yauri);
        $array_correct = json_decode($json);
        $strword = '';
        foreach ($array_correct as $arc) {
            $strword = $strword . $arc->s[0] . ' ';
        }
        return trim($strword);
    }

    //=========================================================================
    public function correrct_spell_all($word = '')
    {
        $count = explode(' ', $word);
        if (count($count) > 1) {
            $word_sum = '';
            foreach ($count as $mword) {
                $yauri = "https://speller.yandex.net/services/spellservice.json/checkText?lang=ru&text=" . $mword;
                $json = $this->getRequest($yauri);
                $array_correct = json_decode($json);
                if (count($array_correct) == 0) $correct = $mword;
                else $correct = $array_correct[0]->s[0];
                $word_sum = $word_sum . ' ' . $correct;
            }
            $word_sum = preg_replace('/\s/', ' ', $word_sum);
            return trim($word_sum) ?? null;
        } else {
            $yauri = "https://speller.yandex.net/services/spellservice.json/checkText?lang=ru&text=" . $word;
            $json = $this->getRequest($yauri);
            $array_correct = json_decode($json);
            if (count($array_correct) == 0) return null;
            $correct = $array_correct[0]->s[0];
            return trim($correct);
        }
    }
    //=========================================================================
    public function predictor($words)
    {
        $yauri = "https://predictor.yandex.net/api/v1/predict.json/complete?key=" . $this->API_KEY . "&lang=ru&q=" . urlencode($words);
        $res = $this->getRequest($yauri);
        $ar = json_decode($res);
        return $ar->accs;
    }

    //=========================================================================
    public function heroku($word)
    {
        $link = 'http://pyphrasy.herokuapp.com/inflect?phrase=' . urlencode($word) . '&cases=accs';
        $res = $this->getRequest($link);
        $ar = json_decode($res);
        return $ar->accs;
    }
    //=========================================================================
    public function get_token()
    {
        $auth = $_SERVER['HTTP_AUTHORIZATION'] ?? null;
        if ($auth) return explode(' ', $auth);
        else null;
    }

    //=========================================================================
    public function getWiki($phrase)
    {
        $struri = 'https://www.startpage.com/sp/search?query=wikipedia' . urlencode(' ' . trim($phrase));
        $result = $this->getRequest($struri);
        return $result;
    }

    public function get_dadata_address($arFields = ['Полтавская 18 Москва'])
    {
        $arResult = [];
        if ($oCurl = curl_init("https://cleaner.dadata.ru/api/v1/clean/address")) {
            curl_setopt($oCurl, CURLOPT_RETURNTRANSFER, 1);
            curl_setopt($oCurl, CURLOPT_HTTPHEADER, [
                'Content-Type: application/json',
                'Accept: application/json',
                'Authorization: Token 80908b35adc295125f8cab8428f5fa4d635f453f',
                'X-Secret: 5e6acebc6bb587ca1f851cce24687ccad05a70d4'
            ]);
            curl_setopt($oCurl, CURLOPT_POST, 1);
            curl_setopt($oCurl, CURLOPT_POSTFIELDS, json_encode($arFields));
            $sResult = curl_exec($oCurl);
            $arResult = json_decode($sResult, true);
            curl_close($oCurl);
        }

        return $arResult;
    }

    public function get_dadata_fastaddress($arFieldsIn)
    {
        $arFields = ['query' => $arFieldsIn, 'language' => 'ru'];
        $arResult = [];
        if ($oCurl = curl_init("https://suggestions.dadata.ru/suggestions/api/4_1/rs/suggest/address")) {
            curl_setopt($oCurl, CURLOPT_RETURNTRANSFER, 1);
            curl_setopt($oCurl, CURLOPT_HTTPHEADER, [
                'Content-Type: application/json',
                'Accept: application/json',
                'Authorization: Token 000000000000000000000000000000000',
                'X-Secret: 0000000000000000000000000000000'
            ]);
            curl_setopt($oCurl, CURLOPT_POST, 1);
            curl_setopt($oCurl, CURLOPT_POSTFIELDS, json_encode($arFields));
            $sResult = curl_exec($oCurl);
            $arResult = json_decode($sResult, true);
            curl_close($oCurl);
        }
        return $arResult;
    }

    public function get_dadata_inn($arFieldsIn)
    {
        $arFields = ['query' => $arFieldsIn, 'language' => 'ru'];
        $arResult = [];
        if ($oCurl = curl_init("https://suggestions.dadata.ru/suggestions/api/4_1/rs/findById/party")) {
            curl_setopt($oCurl, CURLOPT_RETURNTRANSFER, 1);
            curl_setopt($oCurl, CURLOPT_HTTPHEADER, [
                'Content-Type: application/json',
                'Accept: application/json',
                'Authorization: Token 000000000000000000000000000000000',
                'X-Secret: 0000000000000000000000000000000'
            ]);
            curl_setopt($oCurl, CURLOPT_POST, 1);
            curl_setopt($oCurl, CURLOPT_POSTFIELDS, json_encode($arFields));
            $sResult = curl_exec($oCurl);
            $arResult = json_decode($sResult, true);
            curl_close($oCurl);
        }
        return $arResult;
    }

    public function get_dadata_okved($arFieldsIn)
    {
        $arFields = ['query' => $arFieldsIn, 'language' => 'ru'];
        $arResult = [];
        if ($oCurl = curl_init("https://suggestions.dadata.ru/suggestions/api/4_1/rs/findById/okved2")) {
            curl_setopt($oCurl, CURLOPT_RETURNTRANSFER, 1);
            curl_setopt($oCurl, CURLOPT_HTTPHEADER, [
                'Content-Type: application/json',
                'Accept: application/json',
                'Authorization: Token 000000000000000000000000000000000',
                'X-Secret: 0000000000000000000000000000000'
            ]);
            curl_setopt($oCurl, CURLOPT_POST, 1);
            curl_setopt($oCurl, CURLOPT_POSTFIELDS, json_encode($arFields));
            $sResult = curl_exec($oCurl);
            $arResult = json_decode($sResult, true);
            curl_close($oCurl);
        }
        return $arResult;
    }

    public function webAuth($login, $password, $host)
    {
        $ch = curl_init($host);
        curl_setopt($ch, CURLOPT_USERPWD, $login . ':' . $password);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
        curl_setopt($ch, CURLOPT_HEADER, false);
        $html = curl_exec($ch);
        curl_close($ch);
    }

    public function clean_string($string)
    {
        $string = preg_replace('/[^_@a-zA-Z0-9\d]/ui', '', $string);
        return trim($string) ?? null;
    }

    public function crypt_loginpass($login = null, $password = null, $reverse = false, $inverse = false)
    {
        //self::out_d(openssl_get_cipher_methods());
        //self::out_d(hash_hmac_algos());die();
        if (empty($login)) return ['error' => 'Bad login', 'result' => $login];
        if (empty($password)) return ['error' => 'Bad password', 'result' => $password];
        else {
            if (mb_strlen($password) < 6) $password = $password . $password;
        }
        if (!$reverse) {
            $login = hash('sha256', $login);
            $password = hash('sha256', $password);
            $code = openssl_encrypt($password, 'rc4', $login, 0, '');
            if ($inverse) {
                return ['error' => 'Not real restore', 'result' => null];
            } else return ['error' => 'Hash password', 'result' => $code];
        } else {
            if ($inverse) {
                $password_e = openssl_decrypt($password, 'rc4', $login, 0, '');
                return ['error' => 'Restore password', 'result' => $password_e];
            } else {
                $code = openssl_encrypt($password, 'rc4', $login, 0, '');
                return ['error' => 'Unsafe hash', 'result' => $code];
            }
        }
    }
    public function rus_lat($text,$flag=true){      //Возвратная транслитерация true - русский в транслит, false - обратно
        $trans = array(
            "а"=>"a",  "б"=>"b",  "в"=>"v",  "г"=>"g",
            "д"=>"d",  "е"=>"e",  "ё"=>"_yo", "ж"=>"_zh",
            "з"=>"z",  "и"=>"i",  "й"=>"j",  "к"=>"k",
            "л"=>"l",  "м"=>"m",  "н"=>"n",  "о"=>"o",
            "п"=>"p",  "р"=>"r",  "с"=>"s",  "т"=>"t",
            "у"=>"u",  "ф"=>"f",  "х"=>"h",  "ц"=>"c",
            "ч"=>"_ch", "ш"=>"_sh", "щ"=>"_hh", "ы"=>"y",
            "э"=>"_je", "ю"=>"_yu", "я"=>"_ya","ь"=>"_mm",
            "ъ"=>"_tt",  "("=>"_ls", ")"=>"_rs"," "=>"~",

            "А"=>"A",  "Б"=>"B",  "В"=>"V",  "Г"=>"G",
            "Д"=>"D",  "Е"=>"E",  "Ё"=>"_YO", "Ж"=>"_ZH",
            "З"=>"Z",  "И"=>"I",  "Й"=>"J",  "К"=>"K",
            "Л"=>"L",  "М"=>"M",  "Н"=>"N",  "О"=>"O",
            "П"=>"P",  "Р"=>"R",  "С"=>"S",  "Т"=>"T",
            "У"=>"U",  "Ф"=>"F",  "Х"=>"H",  "Ц"=>"C",
            "Ч"=>"_CH", "Ш"=>"_SH", "Щ"=>"_HH", "Ы"=>"Y",
            "Э"=>"_JE", "Ю"=>"_YU", "Я"=>"_YA","Ь"=>"_MM",
            "Ъ"=>"_TT"
        );
        if($flag){
            // Прямая транслитерация
            $recipient = '';
            foreach (mb_str_split($text) as $char){
                if(isset($trans[$char])) $recipient .= $trans[$char];
                else $recipient .= $char;
            }
            return $recipient;
        }else{
            // Обратная транслитерация
            $recipient = '';
            $arr = mb_str_split($text);
            $recount = count($arr);
            $count=0;
            while($count<$recount){
                if($arr[$count]==='_'){
                    $char = '_';
                    $count++;
                    $char .= $arr[$count];
                    $count++;
                    $char .= $arr[$count];
                    $count++;
                    //self::out_d($char);
                }else{
                    $char = $arr[$count];
                    $count++;
                }
                foreach ($trans as $rus=>$eng){
                    if($eng===$char){
                        $key = $rus;
                        break;
                    }
                    else $key = false;
                }
                if($key) $recipient .= $key;
                else $recipient .= $char;
            }
            return $recipient;
        }
    }
    public function refcode(){
        $refminus = '';
        for($i=0;$i<32;$i++){
            $key = rand(0,15);
            switch ($key){
                case 10: $refminus = $refminus.'a';break;
                case 11: $refminus = $refminus.'b';break;
                case 12: $refminus = $refminus.'c';break;
                case 13: $refminus = $refminus.'d';break;
                case 14: $refminus = $refminus.'e';break;
                case 15: $refminus = $refminus.'f';break;
                default: $refminus = $refminus.$key;break;
            }
        }
        $temp = mb_substr($refminus,0,8).'-'.mb_substr($refminus,8,4).'-'.mb_substr($refminus,12,4).'-'.mb_substr($refminus,16,4).'-'.mb_substr($refminus,20);
        return $temp;
    }
    public function only_float($string){
        $text = str_replace(',','.', $string);
        $str = preg_replace('/[^0-9\.]/','',$text);
        //$str = str_replace(chr(160),'',$content);
        return (float)$str ?? 0;
    }
}
