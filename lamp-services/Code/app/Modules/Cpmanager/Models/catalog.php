<?php
  namespace App\Modules\Cpmanager\Models;
  
  
  use \DB;
  
    use Config;
    use App\Modules\Cpmanager\Models\Review;
  class catalog extends \Eloquent {
    public function __construct() {
      
      $this->Review = new Review();
      
    }  
    /*
      * Function Name: getProducts()
      * Description: getProducts function is used to get the product multivariant array where
      $api=1 is for fetching category based Products
      $api=2 is for fetching product Detail API
      $api=3 for top Rated
      $api=6 for brand products
      $api=4 for getOfferProducts Api
      * Author: Ebutor <info@ebutor.com>
      * Copyright: ebutor 2016
      * Version: v1.0
      * Created Date: 18 July 2016
      * Modified Date & Reason:
    */
    public function getProducts($category_id,$offset,$offset_limit,$sort_id,$customer_token='',$api,$prodData,$pincode,$segment_id){
      
      if(empty($pincode)){
        $pincode = -1;
      }
      
      
      if(!empty($api) && $api==1){  // $api=1 for getCategories
       $t = '@total';
        //print_r(DB::raw("CALL getCpProducts($category_id,$le_wh_id,$sort_id,$offset_limit,$offset,$t)"));exit;
        $productDatas = DB::select("CALL getCpProducts($category_id,$pincode,$sort_id,$offset_limit,$offset,$t)"); 
        //print_r($productDatas);exit;
        $catTotal = DB::select("SELECT @total");
        
        $catTotal = $catTotal[0]->$t;
        $product['TotalProducts'] = $catTotal;
        $product['data'] = $productDatas;



        return $product;
        
        }elseif($api==2){ //$api=2 for productDetails
            $productDatas= array();
            $productDatas[0]['product_id'] = $category_id;
            
            $product_name= DB::table('products as prod')
            ->select(DB::raw("prod.product_title as product_name, pc.description,prod.primary_image,prod.mrp"))
            
            ->leftJoin('product_content as pc','pc.product_id','=','prod.product_id')
            ->where('prod.product_id','=',$category_id)
            ->get()->all();
            $productDatas[0]['product_title'] = $product_name[0]->product_name;
            $productDatas[0]['description'] = $product_name[0]->description;
            $productDatas[0]['mrp'] = $product_name[0]->mrp;
            $productDatas[0]['rating'] = $this->Review->Review($category_id);
            $productDatas[0]['reviews'] = $this->Review->getReviews($category_id);
            $productDatas[0]['related_products'] = [];
            $productDatas[0]['image'] = $product_name[0]->primary_image;
            $productDatas[0]['images'] = $this->getMedia($category_id);
        
        //  print_r($productDatas);exit;
        
        }elseif (($api==3) || ($api==5) || ($api==6)) { //$api=3 for toprated, $api=6 for brand products
        //   print_r($prodData);exit;
        
        foreach ($prodData as $prod) {
          
          if(isset($prod->product_id)){
            $prod = $prod->product_id;
          }
          $product_name= DB::table('products as prod')
          ->select(DB::raw("prod.product_id,prod.product_title, pc.description,prod.primary_image,prod.mrp"))
          
          ->leftJoin('product_content as pc','pc.product_id','=','prod.product_id')
          ->where('prod.product_id','=',$prod)
          ->get()->all();
          //print_r($product_name);
          if(!empty($product_name)){
            //print_r($product_name[0]);
            $productDatas[] = $product_name[0]; 
            }else{
            $productDatas[] = [];
          }
          
        }
        
        // print_r($productDatas);exit;
        
        
        }elseif($api==4  || $api==7){ //margin and fast moving products
        $productDatas = $prodData;
        //print_r($prodData);exit;
      }
      
      $products = json_encode($productDatas);
      $products  = json_decode($products,true);
      
      //print_r($products);exit;       
            $productDatas= sizeof($productDatas);
      
      
            $array = array();
            $i=0;
      
      
      
      //Multivariant Loop    
      foreach ($products as $product) {
        if(!empty($product)){
          $inventory =0;  
          // if($product['product_id']==4812){    
          $childProducts = $this->getChildProduct($product['product_id']);
          $array[$i]['product_id'] = $product['product_id'];
          if(isset($product['product_title'])){
            $array[$i]['product_name'] = $product['product_title']; 
            }else{
            $array[$i]['product_name'] = $product['product_name'];
          }
          
          $array[$i]['mrp'] = $product['mrp'];
          
          //print_r($childProducts);exit;
          if($api==2){
            $array[$i]['description'] = $product['description'];
            if(isset($product['primary_image']) ){
              $array[$i]['image'] = $product['primary_image']; 
              $array[$i]['images']= $this->getMedia($product['product_id']);
              }else{
              $array[$i]['image'] = $product['image']; 
              $array[$i]['images'] = $this->getMedia($product['product_id']); 
            }
            $array[$i]['reviews'] = $this->Review->getReviews($product['product_id']);
          }
          
          
          $array[$i]['rating'] = $this->Review->Review($product['product_id']);
          
          $array[$i]['related_products'] = [];
          //  print_r($childProducts[0]->primary_image);exit;
          
          if(isset($product['primary_image'])  && $api !=2){
            $array[$i]['image'] = $product['primary_image']; 
            //$array[$i]['images'][]['image'] = $product['primary_image'];
            }else{
            $array[$i]['image'] = $product['image']; 
            //  $array[$i]['images'][]['image'] = $product['image']; 
          }
          
          $inventory = $this->getInventory($product['product_id'],$pincode,$segment_id);
          /*         if (isset($customer_token) && !empty($customer_token)) {
            //DB::enableQuerylog();
            
            $inventory = $this->getInventory($customer_token,$product['product_id']);
            //print_r($inventory);exit;
            if(!empty($inventory)){
            $inventory = $inventory[0]->inventory; 
            }else{
            $inventory =0;
            }
            
            // print_r($inventory);exit;
                        //echo "hi";
            //print_r(DB::getQuerylog());            
            }else{
            if(isset($product['product_inventory']) && !empty($product['product_inventory'])){
            $inventory = $product['product_inventory'];
            }else{
            $inventory = $this->getInventory($customer_token='',$product['product_id']);
            if(!empty($inventory)){
            $inventory = $inventory[0]->inventory; 
            }else{
            $inventory =0;
            }  
            }
            
          }*/
          $array[$i]['quantity'] = $inventory;
          
          $x=0;
          $l=0;
          $j=0;
          
          if(!empty($childProducts)){
            
            foreach ($childProducts as $childProduct) {
              $childProduct = json_encode($childProduct);
              $childProduct = json_decode($childProduct,true);
              
              $inventory = $this->getInventory($childProduct['product_id'],$pincode,$segment_id);
              /*            if (isset($customer_token) && !empty($customer_token)) {
                //DB::enableQuerylog();
                
                $inventory = $this->getInventory($customer_token,$product['product_id']);
                if(!empty($inventory)){
                $inventory = $inventory[0]->inventory; 
                }else{
                $inventory =0;
                }                        
                // print_r($inventory);exit;
                //echo "hi";
                //print_r(DB::getQuerylog());            
                }else{
                if(isset($product['product_inventory']) && !empty($childProduct['product_inventory'])){
                $inventory = $product['product_inventory'];
                }else{
                $inventory = $this->getInventory($customer_token='',$childProduct['product_id']);
                if(!empty($inventory)){
                $inventory = $inventory[0]->inventory; 
                }else{
                $inventory =0;
                } 
                }
                
              }*/
              
              $variants1 = array();
              $variants3 = array();
              $variants2 = array();
              //$inventory = 0;
              $j=0;
              if(!empty($childProduct['variant_value1'])){
                
                $variants1[$j]['product_name'] = $childProduct['product_title'];
                $variants1[$j]['product_id'] = $childProduct['product_id'];
                if($api==2){
                  $variants1[$j]['description'] = $childProduct['description'];
                  $spec = $this->getProductSpecifications($childProduct['product_id']);
                  $variants1[$j]['specifications'] = $spec;
                  $Reviews = $this->Review->getReviews($childProduct['product_id']);
                  $variants1[$j]['reviews'] = $Reviews;
                  $variants1[$j]['related_products'] = [];
                  $variants1[$j]['images'] = $this->getMedia($childProduct['product_id']);
                }
                
                
                
                $variants1[$j]['mrp'] = $childProduct['mrp'];
                $variants1[$j]['quantity'] = $inventory;
                $variants1[$j]['rating'] = $this->Review->Review($childProduct['product_id']);
                
                
                $variants1[$j]['image'] = $childProduct['primary_image'];
                
                
                $variants1[$j]['variant_name'] = $childProduct['variant_value1'];
                //print_r($variants1);exit;
                //$variants1[$j]['variant_id'] = $product['variant_id1'];
                $l=0;
                if(!empty($childProduct['variant_value2'])){
                  $variants1[$j]['has_inner_varients'] = 1;
                  $variants2[$l]['product_name'] = $childProduct['product_title'];
                  $variants2[$l]['product_id'] = $childProduct['product_id'];
                  $variants2[$l]['mrp'] = $childProduct['mrp'];
                  
                  $variants2[$l]['image'] = $childProducts[0]->primary_image;
                  if($api==2){
                    $variants2[$l]['images'] = $this->getMedia($childProduct['product_id']); 
                    $variants2[$l]['description'] = $childProduct['description'];
                    //$spec = 
                    if(isset($spec)){
                      $variants2[$l]['specifications'] = $spec; 
                      }else{
                      $variants2[$l]['specifications'] = $this->getProductSpecifications($childProduct['product_id']);
                    }
                    
                    if(isset($Reviews)){
                      $variants2[$l]['reviews'] = $Reviews;
                      }else{
                      $variants2[$l]['reviews'] = $this->Review->getReviews($childProduct['product_id']);
                    }
                    
                    
                  }
                  
                  $variants2[$l]['quantity'] = $inventory;
                  $variants2[$l]['variant_name'] = $childProduct['variant_value2'];
                  $k=0;
                  if(!empty($childProduct['variant_value3'])){
                    $variants2[$l]['has_inner_varients'] = 1;
                    $variants3[$k]['variant_name'] = $childProduct['variant_value3'];
                    $variants3[$k]['has_inner_varients'] = 0;
                    $variants3[$k]['quantity'] = $inventory;
                    $variants3[$k]['product_name'] = $childProduct['product_title'];
                    $variants3[$k]['product_id'] = $childProduct['product_id'];
                    $variants3[$k]['mrp'] = $childProduct['mrp'];
                    
                    $variants3[$k]['image'] = $childProduct['primary_image'];
                    if($api==2){
                      $variants3[$k]['images'] = $this->getMedia($childProduct['product_id']);
                      
                      if(isset($Reviews)){
                        $variants3[$k]['reviews'] = $Reviews;  
                        }else{
                        $variants3[$k]['reviews'] = $this->Review->getReviews($childProduct['product_id']);
                      }
                      if(isset($spec)){
                        $variants3[$k]['specifications'] = $spec; 
                        }else{
                        $variants3[$k]['specifications'] = $this->getProductSpecifications($childProduct['product_id']); 
                      }
                      
                    }
                    
                    $variants3[$k]['packs'] = $this->getPackData($childProduct['product_id'],$customer_token,$pincode);
                    $variants2[$l]['variants'] = $variants3;
                    
                    $k++;
                    
                    }else{
                    $variants2[$l]['has_inner_varients'] = 0;
                    $variants2[$l]['quantity'] = $inventory;
                    $variants2[$l]['product_name'] = $childProduct['product_title'];
                    $variants2[$l]['product_id'] = $childProduct['product_id'];
                    $variants2[$l]['mrp'] = $childProduct['mrp'];
                    $variants2[$l]['image'] = $childProduct['primary_image'];
                    if($api==2){
                      $variants2[$l]['images'] =$this->getMedia($childProduct['product_id']);
                      if(isset($Reviews)){
                        $variants2[$l]['reviews'] = $Reviews;  
                        }else{
                        $variants2[$l]['reviews'] = $this->Review->getReviews($childProduct['product_id']);
                      }
                      if(isset($spec)){
                        $variants2[$l]['specifications'] = $spec; 
                        }else{
                        $variants2[$l]['specifications'] = $this->getProductSpecifications($childProduct['product_id']); 
                      }
                    }
                    
                    $variants2[$l]['packs'] = $this->getPackData($childProduct['product_id'],$customer_token,$pincode);  
                  }
                  //$variants2[$l]['variant_id'] = $product['variant_id2'];
                  $variants1[$j]['variants'] = $variants2;
                  $l++;
                  
                  }else{
                  $variants1[$j]['has_inner_varients'] = 0;
                  $variants1[$j]['quantity'] = $inventory;  
                  $variants1[$j]['packs'] = $this->getPackData($childProduct['product_id'],$customer_token,$pincode);
                }
                
                $j++;
                //print_r($variants1);exit;
                $array[$i]['variants'][$x] = $variants1[0];
                
              }
              
              
              $x++; 
            }
            
            
            
          } 
          if(!isset($array[$i]['variants'])){
            //$values = $test;
            unset($array[$i]); 
            $i = $i-1;
          }
          //$array[$i]['variants']
          
          
          $i++;
          
          //}
          
        }//productData null check
      }
      $variants1 = array();
      //print_r(json_encode($array));exit;
      if($api==5  || $api=7){
        $variants1['TotalProducts']=$productDatas; 
      }
      if($api==1){
        $variants1['TotalProducts']= $catTotal;
      }
      
      $variants1['data'] = $array;
      // print_r($variants1);exit;
      return $variants1;
      
      
      
    }
    
    /*
      * Function Name: getChildProduct()
      * Description: getChildProduct function is used to get the child product of the product_id passed
      
      * Author: Ebutor <info@ebutor.com>
      * Copyright: ebutor 2016
      * Version: v1.0
      * Created Date: 18 July 2016
      * Modified Date & Reason:
    */
    
    
    
    
    
    
    public function getChildProduct($product_id){
      //print_r($product_id);
      $query =  DB::table('vw_cp_products as cp')
      ->select(DB::raw(" cp.*, pc.description "))
      ->leftJoin('product_content as pc','pc.product_id','=','cp.product_id')
      
      ->where('cp.parent_id','=',$product_id)
      
      
      ->get()->all();
      
      return $query;   
      
      
      
      
    }
    /*
      * Function Name: getPackData()
      * Description: getPackData function is used to get slabs for the product_id passed based on customer_token and pincode.
      
      * Author: Ebutor <info@ebutor.com>
      * Copyright: ebutor 2016
      * Version: v1.0
      * Created Date: 18 July 2016
      * Modified Date & Reason:
    */
    public function getPackData($product_id,$customer_token,$pincode){
      
      if(!empty($customer_token)){
        $user = DB::table('users as u')
        ->select("u.user_id")
        ->where('u.password_token','=',$customer_token)
        ->get()->all();
        
        if(!empty($user)){
          $user_id = $user[0]->user_id;
          }else{
          $user_id = 0;
        }
        
        //print_r($user_id);exit;
        
        
        }else{
        $user_id = 0;
      }
      
      //user_id=0 when customer is not logged in.
      
      $query = DB::select("CALL getProductSlabs($product_id,$pincode,$user_id)"); 
      //print_r($query);
      
      
      
      
      //print_r($query);exit;
      
      /*
        DB::enableQuerylog();
        
        if(!empty($customer_token)){$query =  DB::table('products_slab_rates as psr')
        ->select("psr.product_slab_id","psr.product_id","psr.end_range as pack_size","psr.margin","psr.price as unit_price","psr.is_markup")
        ->where('psr.product_id','=',$product_id)
        ->get()->all();
        }else{
        $query =  DB::table('products_slab_rates as psr')
        ->select("psr.product_slab_id","psr.product_id","psr.end_range as pack_size","psr.is_markup")
        ->where('psr.product_id','=',$product_id)
        ->get()->all();
        }
      print_r(DB::getQuerylog());exit;    */
      
      return json_decode(json_encode($query),true);   
      
    }
    
        /*
      * Function Name: getInventory()
      * Description: getInventory function is used to fetch the inventory based on the pincode, product_id and segment_id
      
      * Author: Ebutor <info@ebutor.com>
      * Copyright: ebutor 2016
      * Version: v1.0
      * Created Date: 18 July 2016
      * Modified Date & Reason:
    */
    
    public function getInventory($product_id,$pincode,$segment_id){
      //DB::enableQuerylog();
      $inventory = DB::select("SELECT GetCPInventoryStatus($product_id,$pincode, $segment_id,4) as inventory"); 
      $inventory = $inventory[0]->inventory;
      /*      if(!empty($customer_token)){
        $inventory = DB::table("users as u")
        ->select(DB::raw("(inv.soh)+(inv.atp)-(inv.order_qty) as inventory"))
        ->leftJoin("legalentity_warehouses as lew","lew.legal_entity_id",'=','u.legal_entity_id')
        ->leftJoin('wh_serviceables as whs','whs.pincode','=','lew.pincode')
        ->leftJoin('inventory as inv','inv.le_wh_id','=','whs.le_wh_id')
        ->where("password_token","=",$customer_token)
        ->where("product_id",'=',$product_id)
        ->groupBY('whs.le_wh_id')
        ->get()->all();
        
        }else{
        $inventory = DB::table("inventory as inv")
        ->select(DB::raw("Sum((inv.soh)+(inv.atp)-(inv.order_qty)) as inventory"))
        ->where("inv.product_id",'=',$product_id)
        ->groupBY('inv.le_wh_id')
        ->get()->all();
      }*/
      // print_r(DB::getQuerylog());exit;              
      return $inventory;
    }
    
    
    /*
      * Function Name: getProducts()
      * Description: getProducts function is used to get the product multivariant array where
      $api=8 is for editCart API.
      
      * Author: Ebutor <info@ebutor.com>
      * Copyright: ebutor 2016
      * Version: v1.0
      * Created Date: 18 July 2016
      * Modified Date & Reason:
    */
    public function getViewProducts($children='',$customer_token='',$quantity='', $api='',$pincode,$segment_id){
      
      //$customer_token = '';
      /*$children = DB::table("vw_cp_products")
        ->select('product_id')
        ->skip(0)
        ->take(10)
      ->get()->all();*/
      if($api!=8){
        $children = json_decode(json_encode($children),true);
        
        $child = array_values(array_unique(array_column($children,'product_id')));
        }else{
        $child = $children;
        //print_r($child);exit;
      }
      $products = DB::table("vw_cp_products as vw")
            ->select(DB::raw('distinct(vw.parent_id) as product_id,vw.product_id as pid, p.product_title, p.mrp, p.primary_image,p.primary_image as image'))
            ->Join('products as p','p.product_id','=','vw.product_id')
            ->whereIn("vw.product_id", $child)
      // / ->groupBy('vw.parent_id')
      //  ->skip(0)
      // ->take(10)
            ->get()->all();
      $products = json_decode(json_encode($products),true);   
      //print_r($products);exit;
      $childs =array();
      foreach ($products as $prd) {
        $child['parent_id'] = $prd['product_id'];
        if( !(in_array($prd['product_id'], array_column($childs,'parent_id')))){
          //if()
          $child['product_id']= $prd['pid'];
          $child['parent_id'] = $prd['product_id'];
        }
        $childs[] = $child;
      }    // print_r($child);exit;
      $prodVals = array_unique(array_column($childs,'product_id'));
      //print_r($prodVals);exit;
      
      //print_r($childs);exit;
      
      $i=0;
      foreach ($products as $product) {
        //print_r($product);exit;
        if(!empty($product)){
          //if(isset($quantity)){
          $inventory =0;
          $ordered_quantity = 0;
          //}
          
          // if($product['product_id']==4812){    
          $childProducts = $this->getChildProduct($product['product_id']);
          $array[$i]['product_id'] = $product['product_id'];
          if(isset($product['product_title'])){
            $array[$i]['product_name'] = $product['product_title']; 
            }else{
            $array[$i]['product_name'] = $product['product_name'];
          }
          
          $array[$i]['mrp'] = $product['mrp'];
          
          
          
          $array[$i]['rating'] = $this->Review->Review($product['product_id']);
          
          $array[$i]['related_products'] = [];
          //  print_r($childProducts[0]->primary_image);exit;
          
          if(isset($product['primary_image']) ){
            $array[$i]['image'] = $product['primary_image']; 
            //$array[$i]['images'][]['image'] = $product['primary_image'];
            }else{
            $array[$i]['image'] = $product['image']; 
            //  $array[$i]['images'][]['image'] = $product['image']; 
          }
          
          
          
          if(isset($api) && $api==8){
            //print_r($product['product_id']);exit;
            if($product['product_id'] == $childs[0]['product_id']){
              //$inventory = $this->getInventory($customer_token,$product['product_id']);
              $inventory = $this->getInventory($product['product_id'],$pincode,$segment_id);
              //$inventory = $inventory[0]->inventory; 
              $ordered_quantity = $quantity;
              //print_r($inventory);exit;
              }else{
              if (isset($customer_token) && !empty($customer_token)) {
                //DB::enableQuerylog();
                
                $inventory = $this->getInventory($product['product_id'],$pincode,$segment_id);
                $ordered_quantity = 0;
                /*
                  //print_r($inventory);exit;
                  if(!empty($inventory)){
                  $inventory = $inventory[0]->inventory; 
                  $ordered_quantity = 0;
                  }else{
                  $inventory =0;
                  $ordered_quantity = 0;
                }*/
                
                // print_r($inventory);exit;
                //echo "hi";
                //print_r(DB::getQuerylog());            
                }else{
                if(isset($product['product_inventory']) && !empty($product['product_inventory'])){
                  $inventory = $product['product_inventory'];
                  $ordered_quantity = 0;
                  }else{
                  // $inventory = $this->getInventory($customer_token='',$product['product_id']);
                  $inventory = $this->getInventory($product['product_id'],$pincode,$segment_id);
                  $ordered_quantity = 0;
                  
                  /* if(!empty($inventory)){
                    $inventory = $inventory[0]->inventory; 
                    $ordered_quantity = 0;
                    }else{
                    $inventory =0;
                    $ordered_quantity = 0;
                  } */ 
                }
                
              }
            }
          }
          if(isset($api) && $api !=8){
            
            
            if (isset($customer_token) && !empty($customer_token)) {
              //DB::enableQuerylog();
              $inventory = $this->getInventory($product['product_id'],$pincode,$segment_id);
              /*$inventory = $this->getInventory($customer_token,$product['product_id']);
                //print_r($inventory);exit;
                if(!empty($inventory)){
                $inventory = $inventory[0]->inventory; 
                }else{
                $inventory =0;
              }*/
              
              
              }else{
              if(isset($product['product_inventory']) && !empty($product['product_inventory'])){
                $inventory = $product['product_inventory'];
                }else{
                /* $inventory = $this->getInventory($customer_token='',$product['product_id']);
                  if(!empty($inventory)){
                  $inventory = $inventory[0]->inventory; 
                  }else{
                  $inventory =0;
                } */ 
                $inventory = $this->getInventory($product['product_id'],$pincode,$segment_id);
              }
              
            }
          }
          
          
          
          $array[$i]['quantity'] = $inventory;
          $array[$i]['ordered_quantity'] = $ordered_quantity;
          
          
          //print_r($array[$i]['quantity']);exit;
          
          
          $x=0;
          $l=0;
          $j=0;
          
          if(!empty($childProducts)){
            
            foreach ($childProducts as $childProduct) {
              $childProduct = json_encode($childProduct);
              $childProduct = json_decode($childProduct,true);
              
              if(in_array($childProduct['product_id'], $prodVals)){
                $is_default = 1;
                }else{
                $is_default =0;
              }
              // print_r($is_default);exit;  
              
              if(isset($api) && $api==8){
                if($childProduct['product_id'] == $childs[0]['product_id']){
                  $inventory = $this->getInventory($childProduct['product_id'],$pincode,$segment_id);
                  /*$inventory = $this->getInventory($customer_token,$product['product_id']);
                    
                  $inventory = $inventory[0]->inventory; */
                  
                  $ordered_quantity= $quantity;
                  //$array[$i]['ordered_quantity'] = $quantity;
                  }else{
                  if (isset($customer_token) && !empty($customer_token)) {
                    //DB::enableQuerylog();
                    $inventory = $this->getInventory($childProduct['product_id'],$pincode,$segment_id);
                    $ordered_quantity = 0;
                    /*$inventory = $this->getInventory($customer_token,$product['product_id']);
                      //print_r($inventory);exit;
                      if(!empty($inventory)){
                      $inventory = $inventory[0]->inventory; 
                      $ordered_quantity = 0;
                      }else{
                      $inventory =0;
                      $ordered_quantity = 0;
                      }
                    */                     
                    //print_r($inventory);exit;
                    //echo "hi";
                    //print_r(DB::getQuerylog());            
                    }else{
                    if(isset($product['product_inventory']) && !empty($product['product_inventory'])){
                      $inventory = $product['product_inventory'];
                      $ordered_quantity = 0;
                      }else{
                      $inventory =$this->getInventory($childProduct['product_id'],$pincode,$segment_id);
                      $ordered_quantity = 0;
                      /*if(!empty($inventory)){
                        $inventory = $inventory[0]->inventory; 
                        $ordered_quantity = 0;
                        }else{
                        $inventory =0;
                        $ordered_quantity = 0;
                      } */ 
                    }
                    
                  }
                }
              }
              if(isset($api) && $api !=8){
                
                
                if (isset($customer_token) && !empty($customer_token)) {
                  //DB::enableQuerylog();
                  
                  //$inventory = $this->getInventory($customer_token,$product['product_id']);
                  $inventory = $this->getInventory($product['product_id'],$pincode,$segment_id);
                  //print_r($inventory);exit;
                  /*if(!empty($inventory)){
                    $inventory = $inventory[0]->inventory; 
                    }else{
                    $inventory =0;
                  }*/
                                    
                  
                  }else{
                  if(isset($product['product_inventory']) && !empty($product['product_inventory'])){
                    $inventory = $product['product_inventory'];
                    }else{
                    // $inventory = $this->getInventory($customer_token='',$product['product_id']);
                    $inventory = $this->getInventory($product['product_id'],$pincode,$segment_id);
                    /* if(!empty($inventory)){
                      $inventory = $inventory[0]->inventory; 
                      }else{
                      $inventory =0;
                    }*/  
                  }
                  
                }
              }
              
              $variants1 = array();
              $variants3 = array();
              $variants2 = array();
              //$inventory = 0;
              $j=0;
              if(!empty($childProduct['variant_value1'])){
                
                $variants1[$j]['product_name'] = $childProduct['product_title'];
                $variants1[$j]['product_id'] = $childProduct['product_id'];
                $variants1[$j]['is_default'] = $is_default;
                
                $variants1[$j]['description'] = $childProduct['description'];
                $variants1[$j]['mrp'] = $childProduct['mrp'];
                $variants1[$j]['quantity'] = $inventory;
                if($api==8){
                  $variants1[$j]['ordered_quantity'] = $ordered_quantity;
                }
                $variants1[$j]['rating'] = $this->Review->Review($childProduct['product_id']);
                $variants1[$j]['reviews'] = $this->Review->getReviews($childProduct['product_id']);
                $variants1[$j]['related_products'] = [];
                $variants1[$j]['image'] = $childProduct['primary_image'];
                //$variants1[$j]['images'][]['image'] = $childProduct['primary_image'];
                
                $variants1[$j]['variant_name'] = $childProduct['variant_value1'];
                //$variants1[$j]['variant_id'] = $product['variant_id1'];
                $l=0;
                if(!empty($childProduct['variant_value2'])){
                  $variants1[$j]['has_inner_varients'] = 1;
                  $variants2[$l]['product_name'] = $childProduct['product_title'];
                  $variants2[$l]['product_id'] = $childProduct['product_id'];
                  $variants2[$l]['is_default'] = $is_default;
                  $variants2[$l]['mrp'] = $childProduct['mrp'];
                  
                  $variants2[$l]['image'] = $childProducts[0]->primary_image;
                  // $variants2[$l]['images'][]['image'] = $childProducts[0]->primary_image;
                  $variants2[$l]['quantity'] = $inventory;
                  if($api==8){
                    $variants2[$l]['ordered_quantity'] = $ordered_quantity;  
                  }
                  $variants2[$l]['variant_name'] = $childProduct['variant_value2'];
                  $k=0;
                  if(!empty($childProduct['variant_value3'])){
                    $variants2[$l]['has_inner_varients'] = 1;
                    $variants3[$k]['variant_name'] = $childProduct['variant_value3'];
                    $variants3[$k]['has_inner_varients'] = 0;
                    $variants3[$k]['quantity'] = $inventory;
                    
                    if($api==8){
                      $variants3[$k]['ordered_quantity'] = $ordered_quantity;  
                    }
                    $variants3[$k]['product_name'] = $childProduct['product_title'];
                    $variants3[$k]['product_id'] = $childProduct['product_id'];
                    $variants3[$k]['is_default'] = $is_default;
                    $variants3[$k]['mrp'] = $childProduct['mrp'];
                    
                    $variants3[$k]['image'] = $childProduct['primary_image'];
                    //  $variants3[$k]['images'][]['image'] = $childProduct['primary_image'];
                    $variants3[$k]['packs'] = $this->getPackData($childProduct['product_id'],$customer_token,$pincode);
                    $variants2[$l]['variants'] = $variants3;
                    
                    $k++;
                    
                    }else{
                    $variants2[$l]['has_inner_varients'] = 0;
                    $variants2[$l]['quantity'] = $inventory;
                    if($api==8){
                      $variants2[$l]['ordered_quantity'] = $ordered_quantity;  
                    }
                    $variants2[$l]['product_name'] = $childProduct['product_title'];
                    $variants2[$l]['product_id'] = $childProduct['product_id'];
                    $variants2[$l]['is_default'] = $is_default;
                    $variants2[$l]['mrp'] = $childProduct['mrp'];
                    $variants2[$l]['image'] = $childProduct['primary_image'];
                    //  $variants2[$l]['images'][]['image'] =$childProduct['primary_image'];
                    $variants2[$l]['packs'] = $this->getPackData($childProduct['product_id'],$customer_token,$pincode);  
                  }
                  //$variants2[$l]['variant_id'] = $product['variant_id2'];
                  $variants1[$j]['variants'] = $variants2;
                  $l++;
                  
                  }else{
                  $variants1[$j]['has_inner_varients'] = 0;
                  $variants1[$j]['quantity'] = $inventory;  
                  if($api==8){
                    $variants1[$j]['ordered_quantity'] = $ordered_quantity;  
                  }
                  $variants1[$j]['packs'] = $this->getPackData($childProduct['product_id'],$customer_token,$pincode);
                }
                
                $j++;
                //print_r($variants1);exit;
                $array[$i]['variants'][$x] = $variants1[0];
                
              }
              $x++; 
            }
          } 
          //$array[$i]['variants']
          if(!isset($array[$i]['variants'])){
            //$values = $test;
            unset($array[$i]); 
            $i = $i-1;
          }
          
          
          $i++;
          
          //}
          
        }//productData null check
      }
      
      $variants1 = array();
      //print_r(json_encode($array));exit;
      // if($api==1 || $api==5 || $api=6 || $api=7){
      //$variants1['TotalProducts']=sizeof($products); 
      //}
      if(isset($array))  {
        
        $variants1['data'] = $array;
        }else{
        $variants1['data'] = [];
      }
      //  print_r($variants1);exit;
      return $variants1;
      
    }  
    /*
      * Function Name: getProductSpecifications()
      * Description: getProductSpecifications function is used to get the product specifiation for the product_id passed.
      
      * Author: Ebutor <info@ebutor.com>
      * Copyright: ebutor 2016
      * Version: v1.0
      * Created Date: 18 July 2016
      * Modified Date & Reason:
    */
    
    public function getProductSpecifications($product_id){
      
      $result = DB::table('product_attributes as pa')
      ->select('pa.attribute_id','pa.value', 'a.name')
      ->leftJoin('attributes as a','a.attribute_id','=','pa.attribute_id')
      ->where('pa.product_id','=',$product_id)
      ->where('pa.value','!=','')
      ->get()->all();
      //print_r($result);
      return $result;
    }
    /*
      * Function Name: getMedia()
      * Description: getMedia function is used to get the multiple product images for the product_id passed.
      
      * Author: Ebutor <info@ebutor.com>
      * Copyright: ebutor 2016
      * Version: v1.0
      * Created Date: 18 July 2016
      * Modified Date & Reason:
    */
    public function getMedia($product_id){
      $result = DB::table('product_media as pm')
      ->select('pm.url as image')
      ->where('pm.product_id','=',$product_id)
      ->where('pm.media_type','=','85003')
      ->get()->all();
      
      return $result; 
    }
    
  } 
  
  
  
