<?php
namespace App\Modules\Cpmanager\Models;
use \DB;
class ShoppingModel extends \Eloquent

  {
  /*
  * Function Name: checkCustomerToken()
  * Description: checkCustomerToken function is used to check if the customer_token passed when the customer is logged in is valid.
  * Author: Ebutor <info@ebutor.com>
  * Copyright: ebutor 2016
  * Version: v1.0
  * Created Date: 18 July 2016
  * Modified Date & Reason:
  */
  public function checkCustomerToken($customer_token)
    {
    $query = DB::table("users as u")->select(DB::raw("count(u.password_token) as count,user_id"))
    ->where("u.password_token", "=", $customer_token)->get()->all();
    return $query;
    }
  }