<?php

namespace App\Central\Repositories;
use DB;
class MongoRepo{
    protected $_mongo;
    
    public function __construct()
    {
        $this->_mongo = DB::connection('mongo');
    }
    
    public function insert($tableName, $data)
    {
        $notifications = $this->_mongo->table($tableName)->insert($data);
        return $notifications;
    }
} 