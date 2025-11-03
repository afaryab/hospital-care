<?php

function print_array($array, $toBeClosed = 0){
    echo '<pre>'.print_r($array,true).'</pre>';
    if($toBeClosed != 0){
        die;
    }
}
function generateRandomString($length = 10) {
    $characters = '0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ';
    $charactersLength = strlen($characters);
    $randomString = '';
    for ($i = 0; $i < $length; $i++) {
        $randomString .= $characters[rand(0, $charactersLength - 1)];
    }
    return $randomString;
}

/**
 * @param $type int 1 Payment, 2 Appointment Booking, 3 Appointment Reminder, 4 Appointment Cancelled, 5 Appointment Updated
 * @param $array array
 * @return bool
 */
function sendMessage($type,$array){


    return true;
    if($type == 1){

        $CI = &get_instance();
        $CI->load->model('patients_model', 'patients');

        $patient = $CI->patients->getPatientById($array['patient_id']);
        $doctor  = $CI->aauth->get_user($array['doctor_id']);

        $dateToSend = date('d-m-Y',strtotime($array['start_date']));
        $dateToSend .= ' at ';
        $dateToSend .= date('h:i a',strtotime($array['start_date']));

        $message = 'Hi #Name#, Rahman and Rahman Dental Surgeon scheduled your appointment with #DoctorName# on #Time#, contact us at 04235947610';
        $message = str_replace('#Name#',$patient['pateint_name'],$message);
        $message = str_replace('#DoctorName#',$doctor->name,$message);
        $message = str_replace('#Time#',$dateToSend,$message);


        $contact = $patient['patient_contact_mobile'];

        $url = HOST_MSG;
        $url = str_replace('#contactNumber#',$contact,$url);
        $url = str_replace('#Message#',urlencode($message),$url);

        $ch = curl_init($url);
        curl_setopt($ch, CURLOPT_HEADER, 0);
        curl_setopt($ch, CURLOPT_POST, 1);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        $output = curl_exec($ch);
        curl_close($ch);

    }elseif($type == 2){
        $CI = &get_instance();

        $CI->load->model('patients_model', 'patients');

        $patient = $CI->patients->getPatientById($array['patient_id']);

        $amount = $array['amount_in_num'];
        $remaining = $array['remaining_balance'];
        $mode = $array['payment_type'];

        $message = 'Hi #Name#, You have just paid #Amount# PKR by using #Mode#';

        $message = str_replace('#Name#',$patient['pateint_name'],$message);
        $message = str_replace('#Amount#',$amount,$message);
        $message = str_replace('#Mode#',$mode,$message);

        if($remaining <= 0){
            $message .= '.';
        }else{
            $message .= ', your remaining balance is #Remaining# PKR.';
            $message = str_replace('#Remaining#',$remaining,$message);
        }
        $contact = $patient['patient_contact_mobile'];

        $url = HOST_MSG;
        $url = str_replace('#contactNumber#',$contact,$url);
        $url = str_replace('#Message#',urlencode($message),$url);

        $ch = curl_init($url);
        curl_setopt($ch, CURLOPT_HEADER, 0);
        curl_setopt($ch, CURLOPT_POST, 1);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        $output = curl_exec($ch);
        curl_close($ch);
    }elseif($type == 3){

        $CI = &get_instance();
        $CI->load->model('patients_model', 'patients');

        $patient = $CI->patients->getPatientById($array['patient_id']);
        $doctor  = $CI->aauth->get_user($array['doctor_id']);

        $dateToSend = date('d-m-Y',strtotime($array['start_date']));
        $dateToSend .= ' at ';
        $dateToSend .= date('h:i a',strtotime($array['start_date']));

        $message = 'Hi #Name#, Rahman and Rahman Dental Surgeon re-scheduled your appointment with #DoctorName# on #Time#, contact us at 04235947610';
        $message = str_replace('#Name#',$patient['pateint_name'],$message);
        $message = str_replace('#DoctorName#',$doctor->name,$message);
        $message = str_replace('#Time#',$dateToSend,$message);


        $contact = $patient['patient_contact_mobile'];

        $url = HOST_MSG;
        $url = str_replace('#contactNumber#',$contact,$url);
        $url = str_replace('#Message#',urlencode($message),$url);

        $ch = curl_init($url);
        curl_setopt($ch, CURLOPT_HEADER, 0);
        curl_setopt($ch, CURLOPT_POST, 1);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        $output = curl_exec($ch);
        curl_close($ch);

    }
}

function sort_array_multi_dimention_by_id($a, $b){
	return $a['id'] - $b['id'];
}

function array_orderby()
{
    $args = func_get_args();
    $data = array_shift($args);
    foreach ($args as $n => $field) {
        if (is_string($field)) {
            $tmp = array();
            foreach ($data as $key => $row)
                $tmp[$key] = $row[$field];
            $args[$n] = $tmp;
        }
    }
    $args[] = &$data;
    call_user_func_array('array_multisort', $args);
    return array_pop($args);
}

function getLastNDays($days, $format = 'd/m'){
    $m = date("m"); $de= date("d"); $y= date("Y");
    $dateArray = array();
    for($i=0; $i<=$days-1; $i++){
        $dateArray[] =  date($format, mktime(0,0,0,$m,($de-$i),$y)) ;
    }
    return array_reverse($dateArray);
}

function sortArrayByOrder( $a, $b) {
    $field = 'order';
    return $a[$field] - $b[$field];
        
}

function serverURLGenerator($perposeOfURL,$licenceKey = ''){
    switch ($perposeOfURL){
        case 'REGISTERATION':
            return 'http://localhost/services/public/Syncser/Registeration/Validate?licenceKey='.$licenceKey;
            break;
        case 'EMAIL':
            return 'http://localhost/services/public/Syncser/Email?licenceKey='.$licenceKey;
            break;
        case 'OUTBOUND_PATH':
//            echo 'http://localhost/services/public/Syncser/Inbound?licenceKey='.$licenceKey;
            return 'http://localhost/services/public/Syncser/Inbound?licenceKey='.$licenceKey;
        case 'INBOUND_PATH':
            return 'http://localhost/services/public/Syncser/Oubound?licenceKey='.$licenceKey;
        default:
            return 'http://local.services/public/?licenceKey='.$licenceKey;
            break;
    }
}

function doCurl( $url, $type = 1, $data = []){

    $response = false;

    require_once __DIR__.'/../libraries/curl.php';
    require_once __DIR__.'/../libraries/curl_response.php';


    $curl = new Curl;


    if($type == 1){

        $response = $curl->get($url, array('Accept' => 'application/json'));

    }elseif($type == 2){
        $headers = array('Content-Type' => 'application/json');
        $response = $curl->post($url, ['body' => json_encode($data)]);

    }

    return $response;
    
}

function nicetime($date)
{
    if(empty($date)) {
        return "No date provided";
    }

    $periods         = array("second", "minute", "hour", "day", "week", "month", "year", "decade");
    $lengths         = array("60","60","24","7","4.35","12","10");
    $newDateTime = new DateTime("now");
    date_timezone_set($newDateTime, timezone_open(DATETIMEZONE));
    $LaterDateTime = new DateTime(date('d-m-Y h:i:s',strtotime($date)));

    $now             = strtotime($newDateTime->format('d-m-Y h:i:s'));
    $unix_date         = strtotime($LaterDateTime->format('d-m-Y h:i:s'));

    // check validity of date
    if(empty($unix_date)) {
        return "--:--";
    }

    // is it future date or past date
    if($now > $unix_date) {
        $difference     = $now - $unix_date;
        $tense         = "ago";

    } else {
        $difference     = $unix_date - $now;
        $tense         = "from now";
    }


    for($j = 0; $difference >= $lengths[$j] && $j < count($lengths)-1; $j++) {
        $difference /= $lengths[$j];
    }

    $difference = round($difference);

    if($difference != 1) {
        $periods[$j].= "s";
    }

    return "$difference $periods[$j] {$tense}";
}

if (!function_exists('money_format')) {
    /**
     * PHP Native replacement for money_format
     *
     * @param $format
     * @param $number
     *
     * @return mixed
     */
    function money_format($format, $number)
    {
        $regex = '/%((?:[\^!\-]|\+|\(|\=.)*)([0-9]+)?' .
            '(?:#([0-9]+))?(?:\.([0-9]+))?([in%])/';
        if (setlocale(LC_MONETARY, 0) == 'C') {
            setlocale(LC_MONETARY, '');
        }
        $locale = localeconv();
        preg_match_all($regex, $format, $matches, PREG_SET_ORDER);
        foreach ($matches as $fmatch) {
            $value = floatval($number);
            $flags = array(
                'fillchar'  => preg_match('/\=(.)/', $fmatch[1], $match) ?
                    $match[1] : ' ',
                'nogroup'   => preg_match('/\^/', $fmatch[1]) > 0,
                'usesignal' => preg_match('/\+|\(/', $fmatch[1], $match) ?
                    $match[0] : '+',
                'nosimbol'  => preg_match('/\!/', $fmatch[1]) > 0,
                'isleft'    => preg_match('/\-/', $fmatch[1]) > 0
            );
            $width = trim($fmatch[2]) ? (int)$fmatch[2] : 0;
            $left = trim($fmatch[3]) ? (int)$fmatch[3] : 0;
            $right = trim($fmatch[4]) ? (int)$fmatch[4] : $locale['int_frac_digits'];
            $conversion = $fmatch[5];
            $positive = true;
            if ($value < 0) {
                $positive = false;
                $value *= -1;
            }
            $letter = $positive ? 'p' : 'n';
            $prefix = $suffix = $cprefix = $csuffix = $signal = '';
            $signal = $positive ? $locale['positive_sign'] : $locale['negative_sign'];
            switch (true) {
                case $locale["{$letter}_sign_posn"] == 1 && $flags['usesignal'] == '+':
                    $prefix = $signal;
                    break;
                case $locale["{$letter}_sign_posn"] == 2 && $flags['usesignal'] == '+':
                    $suffix = $signal;
                    break;
                case $locale["{$letter}_sign_posn"] == 3 && $flags['usesignal'] == '+':
                    $cprefix = $signal;
                    break;
                case $locale["{$letter}_sign_posn"] == 4 && $flags['usesignal'] == '+':
                    $csuffix = $signal;
                    break;
                case $flags['usesignal'] == '(':
                case $locale["{$letter}_sign_posn"] == 0:
                    $prefix = '(';
                    $suffix = ')';
                    break;
            }
            if (!$flags['nosimbol']) {
                $currency = $cprefix .
                    ($conversion == 'i' ? $locale['int_curr_symbol'] : $locale['currency_symbol']) .
                    $csuffix;
            } else {
                $currency = '';
            }
            $space = $locale["{$letter}_sep_by_space"] ? ' ' : '';
            $value = number_format($value, $right, $locale['mon_decimal_point'],
                $flags['nogroup'] ? '' : $locale['mon_thousands_sep']);
            $value = @explode($locale['mon_decimal_point'], $value);
            $n = strlen($prefix) + strlen($currency) + strlen($value[0]);
            if ($left > 0 && $left > $n) {
                $value[0] = str_repeat($flags['fillchar'], $left - $n) . $value[0];
            }
            $value = implode($locale['mon_decimal_point'], $value);
            if ($locale["{$letter}_cs_precedes"]) {
                $value = $prefix . $currency . $space . $value . $suffix;
            } else {
                $value = $prefix . $value . $space . $currency . $suffix;
            }
            if ($width > 0) {
                $value = str_pad($value, $width, $flags['fillchar'], $flags['isleft'] ?
                    STR_PAD_RIGHT : STR_PAD_LEFT);
            }
            $format = str_replace($fmatch[0], $value, $format);
        }
        return $format;
    }
}