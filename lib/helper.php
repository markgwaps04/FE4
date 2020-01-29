<?php

/**
 * @author Mark Anthony Libres
 */


//include_once APPPATH."helpers/_helper.php";


abstract class design_model {}


class Page {


    /**
     * @var string $site_name
     *
     * The name of the site under which the current script is executing
     */

    public $site_name;

    /**
     * @var string $name The class name given to a module directive in the controller
     */

    public $name;

    /**
     * @var string $protocol The type of request made by a script
     */

    public $protocol;


    /**
     * @var string $server_name
     *
     * The name of the server host under which the current script is executing.
     * If the script is running on a virtual host,
     * this will be the value defined for that virtual host.
     */

    public $server_name;

    /**
     * @var string $controller a module to be execute
     */

    private $controller;




    public function __construct()
    {

        $this->controller = get_class($this);

        $this->site_name = strtoupper(getenv('SITENAME'));

        $this->name = ucwords($this->controller);

        $this->server_name =  $_SERVER['SERVER_NAME'];

        $this->protocol = isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] != 'off' ? 'https' : 'http';

    }



    public function title() : String
    {

        $separator_string = "|";

        if(!$this->name)

            $separator_string = "";


        return $this->site_name .  $separator_string. $this->name;

    }


    public function base_url()
    {

        return $this->protocol."://".$this->server_name;

    }

}


abstract class _modules extends Page {


    /**
     * @var String $Path parent directory of a target file
     *
     * e.g controller/file.php
     */

    public $path = null;


    /**
     * includes and evaluates the specified file
     * within the modules folder
     *
     * when a file is included, the code it contains
     * inherits the variable scope of the line on which the include occurs.
     *
     * @param String $folder
     * @param String $Path
     * @return String (return the full path of a target path)
     */

    public static function include_once(String $folder,String $Path)  : String
    {

        $path = APPPATH."modules/" . ($folder ."/". $Path);

        include_once $path;

        return $path;

    }





}



class _view extends design_model {

    private $type = "views";

    private $data = [];

    public $parentFolder = "modules";



    public function __construct(Array $data = [])
    {
        $this->data = $data;
    }



    /**
     * used to display output on the browser.
     *
     * @param String $module
     * @param String $fileName (optional)
     * @return String (return the full path of a target path)
     */

    public function render(String $filePath = EMPTY_STRING, _modules $module = null) : String
    {



        $fileName_byClass = null;

        $isValidModule = $module instanceof _modules;


        if($isValidModule)

            $fileName_byClass = get_class($module) .".php";



        if($fileName_byClass)

            $fileName_byClass = $fileName_byClass . "/";



        if($this->type)

            $this->type = $this->type . "/";



        $modulePath =  ($fileName_byClass . $this->type . $filePath);

        $path = (APPPATH. $this->parentFolder) . $modulePath;


        /**
         * Import variables into the current symbol table from an array
         * @link https://php.net/manual/en/function.extract.php
         * @param array $var_array
         * Note that prefix is only required if
         * extract_type is EXTR_PREFIX_SAME,
         * EXTR_PREFIX_ALL, EXTR_PREFIX_INVALID
         * or EXTR_PREFIX_IF_EXISTS. If
         * the prefixed result is not a valid variable name, it is not
         * imported into the symbol table. Prefixes are automatically separated from
         * the array key by an underscore character.
         * @param int $extract_type [optional]
         * The way invalid/numeric keys and collisions are treated is determined
         * by the extract_type. It can be one of the
         * following values:
         * EXTR_OVERWRITE
         * If there is a collision, overwrite the existing variable.
         * @param string $prefix [optional] Only overwrite the variable if it already exists in the
         * current symbol table, otherwise do nothing. This is useful
         * for defining a list of valid variables and then extracting
         * only those variables you have defined out of
         * $_REQUEST, for example.
         * @return int the number of variables successfully imported into the symbol
         * table.
         * @since 4.0
         * @since 5.0
         */

        extract($this->data,EXTR_PREFIX_SAME,"wddx");


        include_once $path;

        return $path;


    }





    /**
     * used to display a whole page on the browser.
     * @return void (nothing will return)
     */


    public function template() : void
    {

        $this->render(INCLUDED_TEMPLATE . INC_HEADER);

//        $this->render(INCLUDED_TEMPLATE . INC_SIDEBAR);
//
//        $this->render(INCLUDED_TEMPLATE . INC_MAIN);
//
//        $this->render(INCLUDED_TEMPLATE . INC_FOOTER);

    }


}


class Date {


    public $date_time;

    private static $api = "YU0BJ1PDIFA2";


    public function __construct(String $date ,String $time)
    {

        $this->date_time = $this->toDateTimeFormat($date,$time);

    }


    private static function byServer() {


        $curl = curl_init();

        $get = array(
            "key=".self::$api,
            "format=json",
            "by=zone",
            "zone=Asia/Singapore",
        );

        curl_setopt_array($curl, array(
            CURLOPT_URL => "http://api.timezonedb.com/v2.1/get-time-zone?".join("&",$get),
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_ENCODING => "",
            CURLOPT_MAXREDIRS => 10,
            CURLOPT_TIMEOUT => 30,
            CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
            CURLOPT_CUSTOMREQUEST => "GET",
        ));

        curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, false);

        $response = curl_exec($curl);
        $err = curl_error($curl);

        return (Array) json_decode($response);

    }


    public static function getTimeofDay() {

        $hour = date('H');

        $dayTerm = ($hour > 17) ? "Evening" : ($hour > 12) ? "Afternoon" : "Morning";

        return $dayTerm;


    }


    private static function date_and_time($format,$strict){

        self::zone();

        $fromServer = self::byServer();


        if($strict && !$fromServer)

            return new DateTime(date($format));



        if($strict && $fromServer)
        {
            $dateTime = $fromServer['formatted'];

            return new DateTime($dateTime);

        }


        if($fromServer)
        {
            $dateTime = $fromServer['formatted'];

            $create = date_create($dateTime);

            return date_format($create,$format);

        }



        return date($format);

    }


    private static function zone(){

        return date_default_timezone_set('Asia/Singapore');

    }


    public static function get( bool $date_time = false, String $format = 'Y-m-d') {

        return self::date_and_time($format,$date_time);

    }


    public static function Time(bool $date_time = false, String $format = 'H:i:s'){

        return self::date_and_time($format,$date_time);

    }


    public static function Now( bool $date_time = false,String $format = 'Y-m-d H:i:s'){

        return self::date_and_time($format,$date_time);

    }


    public function toDateTimeFormat(String $date,String $time)
    {


        $string_date = date('Y-m-d H:i:s', strtotime("{$date} {$time}"));

        return self::date_and_time($string_date,true);

    }


    public function getFormattedDate()
    {

        return ((Array) $this->date_time)['date'];

    }


    public static function format($date,$format) {

        $create = date_create($date);

        return date_format($create,$format);

    }


    public function isInverted()
    {

        $diff = (new DateTime(self::Now()))->diff($this->date_time);

        return $diff->invert;

    }


    public static function getInstance()
    {

        return new self('','');

    }


    /**
     * @param  String  $target
     * @param  String  $base start time,defaults to time()
     * @param  $format use date('Y') or strftime('%Y') format string
     * @return string
     */

    public static function relative_time( $strEnd , $strStart = null , $format = 'Y-m-d H:i:s')
    {

        if(!$strStart)

            $strStart = self::Now();

        $dateStart = new DateTime($strStart);

        $dateEnd = new DateTime($strEnd);

        return new DateTimeInterval($dateStart->diff($dateEnd));

    }



}



abstract class constraint {

    public static function urlencode(Array $array) : String { return urlencode(serialize($array)); }

    public static function urldecode(String $url_encode)  {  return @unserialize(@urldecode($url_encode)); }

    public static function htmlspecialchars(&$Arr) : Array
    {

        if(is_string($Arr))

            $Arr = [$Arr];


        if(!$Arr) return $Arr;


        foreach ($Arr as &$value){

            if(is_array($value))

                self::htmlspecialchars($value);

            if(is_string($value))

                $value = @htmlspecialchars($value,ENT_DISALLOWED);

        }

        return $Arr;

    }

    public static function convert_to_array(&$value)
    {

        if(!is_array($value))

            return [$value];



        $value =  array_map(function($every)
        {

            if(!is_array($every))

                return [$every];


            return $every;


        },$value);

        return $value;

    }

    public static function checkAllArrayHasKeysExists(Array $arr,Array $keys) {

        foreach ($arr as $every => $value) {

            $difference = array_diff(array_keys($value),$keys);

            if(count($difference) !== 0)  return false;

        }

        return true;

    }

    public static function toArray(Array $arr) : Array
    {

        if(!$arr) return $arr;

        return array_map(function($value){

            if(is_object($value))
            {

                $arrTemp = (Array) $value;

                return self::toArray($arrTemp);

            }

            return $value;

        },$arr);


    }


    public static function toValidJSON($value)
    {

        if(!is_array($value)) return;

        $result = array_map(function($value) {

            $value = json_decode($value);

            return array_map(function ($val) {

                if(is_string($val)) $parse =  json_decode($val);

                if(!$parse) return $val;

                return $parse;

            },$value);

        },$value);



        return $result;

    }


    public static function hasMinWords(Array $arr,int $num = 2) : bool {

        $limit_arr = array_filter($arr,function ($value,$key) use ($num) {

            if(!is_string($key)) return;

            return str_word_count($key) > $num;

        },ARRAY_FILTER_USE_BOTH);

        return count($limit_arr) > 0;


    }

    /**
     *
     * check an array if there's empty value of a key
     *
     * @access public
     *
     * @param array|string $check an array to check
     *
     * @param array $except check every value except a specified keys (Optional)
     *
     * @return bool return true if has false if not
     */


    public static function isThereEmpty($check,Array $except = []) : bool
    {

        if(!$check) return false;

        if(!is_array($check))

            $check = [$check];


        foreach ($check as $key => &$value)
        {


            if(in_array($key,$except))

                break;


            if(is_array($value))
            {

                if(!self::isThereEmpty($value,$except))

                    return true;

            }


            if(is_string($value))
            {

                if(!trim($value))

                    return true;

            }

        }

        return false;

    }



    /**
     * @access public
     *
     * @param array $array
     *
     * @return array
     */


    public static function removeEmpty(Array &$array) : Array
    {

        foreach ($array as $per => $every)
        {
            if(empty(trim($every))) unset($array[$per]);
        }

        return $array;

    }


    public static function filter_keys(Array &$array,Array $store) : Array
    {



        foreach ($array as $key => &$values)
        {

            if(is_array($values))


                self::filter_keys($values,$store);


            if(!in_array($key,$store))

                unset($array[$key]);


        }

        return $array;



    }


    public static function ArrayEncodeBase64(Array $arr) {

        return base64_encode(json_encode($arr));

    }


    public static function dashToPointKey(Array &$array) {

        $val = [];

        foreach ($array as $every => &$value) {

            $every = str_replace("__",".",$every);

            $val[$every] = $value;

        }

        $array = $val;

        return $val;

    }


    public static function remove_keys(Array &$array,Array $remove)
    {

        foreach ($array as $key => &$values)
        {

            if(is_array($values))


                self::remove_keys($values,$remove);


            if(in_array($key,$remove) && is_string($key))

                unset($array[$key]);


        }

    }


    public static function unMaskPhoneNumber(String $phone) {

        $phone_format = preg_replace("/[\W\s]/m","",$phone);

        return $phone_format;

    }


    public static function strict(Array &$array ,Array $filter_keys  = [],$removeEmpty = true) : bool
    {

        self::htmlspecialchars($array);

        self::filter_keys($array,$filter_keys);

        self::dashToPointKey($array);

        if($removeEmpty) self::removeEmpty($array);

        return self::keys_exist($array,$filter_keys);

    }


    public static function strip_request(Array &$array,Array $filter_keys) {

        self::htmlspecialchars($array);

        self::removeEmpty($array);

        return self::keys_exist($array,$filter_keys);

    }


    public static function mask_request(Array &$array) {

        self::htmlspecialchars($array);

        self::removeEmpty($array);

        $arr = array();

        foreach ($array as $key => $value) {

            $arr[preg_replace("(\W)","",$key)] = $value;

        }


        $array = $arr;

        return self::hasMinWords($array);

    }


    /**
     * convert array to sql IN statement values
     * @param array $arr
     * @return String
     */


    public static function sql_IN_statement_array(Array $arr) : String
    {

        return "'".implode("','",array_map('strval',$arr))."'";

    }



    public static function encrypt_result(Array &$array)
    {

        self::remove_keys($array,(new SCOA())->confidential_attributes);

        return $array;


    }



    public static function implode_has(Array $arr,String $has) {

        return "{$has}".implode("{$has},{$has}",$arr)."{$has}";

    }


    public static function covertAsStringRep(Array $arr) {

        $json = json_encode($arr);

        $json = preg_replace("(:)","=",$json);

        $json = preg_replace("([\{\}])","",$json);

        echo $json;

    }


    /**
     * @access public
     *
     * @param array $source an array to check
     *
     * @param string|array|int $check a keys to check
     *
     * @return bool return true if has false if not
     */

    public static function keys_exist(Array $source,$check) : bool
    {

        if(!is_array($check))

            $check = [$check];


        foreach ($check as $key)
        {

            if(!array_key_exists($key,$source))

                return false;

        }

        return true;

    }

    /**
     *
     * check if a valid request is sent to ajax
     * and not from url or form request
     *
     * @access public
     *
     * @deprecated unsafe using header can easily spoofed
     *
     * @return void
     *
     */

    public static function isAjaxRequest() : bool
    {

        $headers = apache_request_headers();

        return  (@$headers['X-Requested-With'] and @$headers['X-Requested-With'] === 'XMLHttpRequest');

    }


    public static function callAsync(Array $arrayOfFunctions) : bool
    {

        foreach ($arrayOfFunctions as $every => $function) {

            $call = $function();

            if(!$call) return false;

        }

        return true;

    }

    public static function changeKeys(Array $keysWithNew,Array $arrayToChanged) : Array
    {

        $array = array();

        foreach ($keysWithNew as $every => $value)
        {

            $is_valid = array_key_exists($every,$arrayToChanged);

            if(!$is_valid) continue;

            $array[$value] = $arrayToChanged[$every];

        }


        return $array;


    }





}













