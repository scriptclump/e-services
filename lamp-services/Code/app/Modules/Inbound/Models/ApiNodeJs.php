<?php

namespace App\Modules\Inbound\Models;

/*
  Filename : ApiNodeJs.php
  Author : Vijaya Bhaskar Chenna
  CreateData : 03-June-2016
  Desc : Model for connecting NodeJS through CURL
 */

use Illuminate\Database\Eloquent\Model;

class ApiNodeJs extends Model {
    /*
     * Params :@$url: API URL, @$callType: GET/POST, @$params: API Data
     * Returns : ALI response as Array format
     */

    public function nodeJsApi($url, $callType, $params = '') {
        $postData = json_encode($params);
        $ch = curl_init();

        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_HTTPHEADER, array(
            'api_key: testkey',
            'api_secret: testsecret',
            'Content-Type: application/json'
        ));

        if ($callType == "POST") {
            curl_setopt($ch, CURLOPT_POST, count($postData));
            curl_setopt($ch, CURLOPT_POSTFIELDS, $postData);
        }

        $output = curl_exec($ch);
        curl_close($ch);
        return $output;
    }

}
