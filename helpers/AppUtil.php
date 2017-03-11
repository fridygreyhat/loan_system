<?php

class AppUtil {

    function __construct() {
        
    }

    public static function userId() {
        return isset($_SESSION['user_id']) ? $_SESSION['user_id'] : 0;
    }

    public static function isValidEmail($string) {
        if (filter_var($string, FILTER_VALIDATE_EMAIL)) {
            //echo "This ($email_a) email address is considered valid.";
            return TRUE;
        }
        return FALSE;
    }

    public static function json_encodeWithCheck($arrayMulti) {
        for ($i = 0; $i < count($arrayMulti); $i++) {
            $array = $arrayMulti[$i];
            foreach ($array as $key => $value) {
                //print_r($value);
                $decoded = json_decode($value);
                if (json_last_error() == JSON_ERROR_NONE) {
                    $array[$key] = $decoded;
                    $arrayMulti[$i] = $array;
                }
            }
        }
        return json_encode($arrayMulti);
    }

    public static function saveFile($tempLocation, $fileName) {
        $relativePath = "/uploads/allfiles/" . time() . "_" . basename($fileName);
        $target_file = dirname(__DIR__) . $relativePath;

        if (move_uploaded_file($tempLocation, $target_file)) {
            //echo "The file " . $fileName . " has been uploaded.";
            return $relativePath;
        }
        return FALSE;
    }

}
