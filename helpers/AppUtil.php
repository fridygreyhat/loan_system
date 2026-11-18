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
        // Never keep the client's filename/extension: see helpers/upload.php.
        require_once __DIR__ . '/upload.php';
        try {
            $file = ['name' => $fileName, 'tmp_name' => $tempLocation, 'error' => UPLOAD_ERR_OK, 'size' => @filesize($tempLocation) ?: 0];
            return '/' . safe_upload($file, 'uploads/allfiles', UPLOAD_DOCUMENT_TYPES, 10485760);
        } catch (RuntimeException $e) {
            return FALSE;
        }
    }

}
