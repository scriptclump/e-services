<?php

namespace App\Modules\Cpmanager\Models;

/*
  Filename : ShoppingList.php
  Author : Ebutor <info@ebutor.com>
  CreateData : 12-July-2016
  Desc : Model for list mongo table
 */

use Jenssegers\Mongodb\Eloquent\Model as Eloquent;

class ShoppingList extends Eloquent

    {
    protected $connection = 'mongo';
    protected $table = 'shoppinglist';
    protected $primaryKey = '_id';
    /*
    * Function Name: ShoppingLists
    * Description: ShoppingLists used to display shoppinglists of the particular customer
    * Author: Ebutor <info@ebutor.com>
    * Copyright: ebutor 2016
    * Version: v1.0
    * Created Date: 15 July 2016
    * Modified Date & Reason:
    */
    public function ShoppingLists($user_id)
        {
        $mongo_lsiting = $this->where('user_id', (int)$user_id)->orderBy('created_at',"desc")->get()->all();
        $mongo_list_data = json_decode(json_encode($mongo_lsiting) , true);
        return $mongo_list_data;
        }

    /*
    * Function Name: ListProducts
    * Description: ListProducts used to display products of the particular list
    * Author: Ebutor <info@ebutor.com>
    * Copyright: ebutor 2016
    * Version: v1.0
    * Created Date: 15 July 2016
    * Modified Date & Reason:
    */
    public function ListProducts($user_id, $buyer_listingid)
        {
        $mongo_lists = $this->where(['user_id' =>(int) $user_id, '_id' => (string)$buyer_listingid])->get()->all();
        $mongo_listproducts = json_decode(json_encode($mongo_lists) , true);
        return $mongo_listproducts;
        }

 /*
    * Function Name: addProduct
    * Description: addProduct used to add products of the particular list
    * Author: Ebutor <info@ebutor.com>
    * Copyright: ebutor 2016
    * Version: v1.0
    * Created Date: 15 July 2016
    * Modified Date & Reason:
    */
    public function addProduct($products,$user_id,$buyer_listingid)
        {

       $mongo_update= $this->where(['user_id' => (int)$user_id, '_id' => (string)$buyer_listingid])->push('product_id',$products);

        
        }
 /*
    * Function Name: checkListName
    * Description: checkListName used to check wether that listname with that customer is thr or not
    * Author: Ebutor <info@ebutor.com>
    * Copyright: ebutor 2016
    * Version: v1.0
    * Created Date: 15 July 2016
    * Modified Date & Reason:
    */

  public function checkListName($user_id,$listname)
    {

        $list_name = $this->where(['user_id' => (int)$user_id, 'listname' => (string)$listname])->get()->all();
        $list_name_data = json_decode(json_encode($list_name) , true);

        return $list_name_data;

    }

     /*
    * Function Name: createList
    * Description: createList used to create list
    * Author: Ebutor <info@ebutor.com>
    * Copyright: ebutor 2016
    * Version: v1.0
    * Created Date: 18 July 2016
    * Modified Date & Reason:
    */

  public function createList($user_id,$listname)
    {
        $this->user_id=(int)$user_id;
        $this->listname=(string)$listname;
        $this->created_date=date("Y-m-d-H-i-s");
        $this->product_id=[];
        $this->save();

     return true;

    }



 /*
    * Function Name: deleteProduct
    * Description: addProduct used to add products of the particular list
    * Author: Ebutor <info@ebutor.com>
    * Copyright: ebutor 2016
    * Version: v1.0
    * Created Date: 15 July 2016
    * Modified Date & Reason:
    */
    public function deleteProduct($product,$user_id,$buyer_listingid)
        {

       $mongo_update= $this->where(['user_id' => (int)$user_id, '_id' => (string)$buyer_listingid])->pull('product_id',$product);
        
        return true;
        
        }
    }