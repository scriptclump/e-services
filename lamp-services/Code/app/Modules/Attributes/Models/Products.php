<?php
namespace App\Modules\Attributes\Models;
use App\Central\Repositories\CustomerRepo;
use App\Central\Repositories\RoleRepo;

use \DB;
use Log;
class Products extends \Eloquent {

    protected $table = 'products'; // table name
    protected $primaryKey = 'product_id';
    public $timestamps = false;
    private $customerRepo;
    private $roleRepo;
    
    public function __construct()
    {
        $this->customerRepo = new CustomerRepo;
        $this->roleRepo = new RoleRepo;
    }
    // model function to store product data to database

  
    
    public function saveProductAttributesets($data)
    {
        try
        {
            if(!empty($data) && isset($data['product_attribute_sets']))
            {
                $productAttributeDetails = isset($data['product_attribute_sets']['attribute_details']) ? $data['product_attribute_sets']['attribute_details'] : array();
                if(!empty($productAttributeDetails))
                {
                    $productId = isset($data['product_attribute_sets']['product_id']) ? $data['product_attribute_sets']['product_id'] : 0;
                    //DB::table('product_attributesets')->where('product_id', $productId)->delete();
                    $attributeSetIds = DB::table('product_attributesets')->where('product_id', $productId)->pluck(DB::raw('group_concat(id)'))->all();
                    $attributeSetIdsArray = array();                    
                    if(!empty($attributeSetIds))
                    {
                        $attributeSetIdsArray = explode(',' ,$attributeSetIds);
                    }                    
                    $tempIds = array();
                    foreach($productAttributeDetails as $productAttr)
                    {
                        $tempArray = (array) json_decode($productAttr);
                        if(!isset($tempArray['id']))
                        {
                            $tempArray['product_id'] = $productId;
                            $groupId = isset($data['product']['group_id']) ? $data['product']['group_id'] : 0;
                            $tempArray['product_group_id'] = $groupId;
                            DB::table('product_attributesets')->insert($tempArray);   
                        }else if(isset($tempArray['id'])){
                            $tempIds[] = $tempArray['id'];
                        }
                    }
                    $deleteIds = array();
                    $tempDiffArray = array_diff($attributeSetIdsArray, $tempIds);
                    if(!empty($tempDiffArray))
                    {
                        $deleteIds = $tempDiffArray;
                    }else{
                        $tempDiffArray = array_diff($tempIds, $attributeSetIdsArray);
                        if(!empty($tempDiffArray))
                        {
                            $deleteIds = $tempDiffArray;
                        }
                    }
                    foreach($deleteIds as $id)
                    {
                        DB::table('product_attributesets')->where('id', $id)->delete();
                    }
                }
            }
        } catch (\ErrorException $ex) {
            return $ex;
        }
    }
    
    
    
}