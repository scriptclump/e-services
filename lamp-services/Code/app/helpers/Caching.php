<?php

use Illuminate\Support\Facades\Cache;

class Caching
{
    private $expiresAt = 10;
    
    public static function getKey()
    {
        $appKeyData = explode(':', env('APP_KEY'));
        $appKey = isset($appKeyData[1]) ? $appKeyData[1] : $appKeyData;
        return $appKey;
    }
    
    public static function getElement($string, $userId = null)
    {
        try
        {
            if(!$userId)
            {
                $userId = Session::get('userId');
            }
            if($string != '')
            {
                $key = self::getKey();
                $temp = Cache::tags([$key, $userId])->get($string);
                return json_decode($temp);
            }else{
                return [];
            }
        } catch (Exception $ex) {
            Log::error($ex->getMessage());
            Log::error($ex->getTraceAsString());
            return $ex->getMessage();
        }
    }
    
    public static function setElement($string, $data, $userId = null)
    {
        try
        {
            if(!$userId)
            {
                $userId = Session::get('userId');
            }
            if($string != '')
            {
                $key = self::getKey();
                if(!Cache::tags([$key, $userId])->has($string))
                {
                    Cache::tags([$key, $userId])->add($string, json_encode($data), 10);
                }
            }else{
                return [];
            }
        } catch (Exception $ex) {
            Log::error($ex->getMessage());
            Log::error($ex->getTraceAsString());
            return $ex->getMessage();
        }
    }

    public static function getGlobalElement($string)
    {
        try
        {
//            Log::info(__METHOD__);
            if($string != '')
            {
                $key = self::getKey();
                $temp = Cache::tags([$key, $string])->get($string);
                return json_decode($temp);
            }else{
                return [];
            }
        } catch (Exception $ex) {
            Log::error($ex->getMessage());
            Log::error($ex->getTraceAsString());
            return $ex->getMessage();
        }
    }

    public static function setGlobalElement($string, $data)
    {
        try
        {
            //Log::info(__METHOD__);
            if($string != '')
            {
                $key = self::getKey();
                if(!Cache::tags([$key, $string])->has($string))
                {
                    Cache::tags([$key, $string])->add($string, json_encode($data), 1000);
                }
            }else{
                return [];
            }
        } catch (Exception $ex) {
            Log::error($ex->getMessage());
            Log::error($ex->getTraceAsString());
            return $ex->getMessage();
        }
    }
  
    public static function flushGlobalElement($string)
    {
        try
        {
            if($string != '')
            {
                $key = self::getKey();
                Cache::tags($key, $string)->flush();
            }
        } catch (Exception $ex) {
            Log::error($ex->getMessage());
            Log::error($ex->getTraceAsString());
            return $ex->getMessage();
        }
 }
    
    public static function flush($userId)
    {
        try
        {
            if($userId > 0)
            {
                $key = self::getKey();
                 Cache::tags([$key, $userId])->flush();
            }
        } catch (Exception $ex) {
            Log::error($ex->getMessage());
            Log::error($ex->getTraceAsString());
            return $ex->getMessage();
        }
    }
}