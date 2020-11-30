<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use DB;
use App\models\suppliers\SupplierModel;
use App\models\suppliers\ligalEntitesModel;
use App\models\suppliers\SupplierDetailsModel;
use App\Http\Requests;


class SupplierController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        //
        return view('suppliers.suppliers');
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {

           //return redirect('suppliers');
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        //
        $suppilerData=$request->all();
        $seller_id = 14;//Session::get('userId');
        $rs=SupplierModel::create([
        'user_name' => $suppilerData['firstname'].' '.$suppilerData['lastname'],
        'firstname' => $suppilerData['firstname'],
        'lastname' => $suppilerData['lastname'],
        'email_id' => $suppilerData['email_id'],]);
         
         $ligalEntitesResponse=ligalEntitesModel::create([
        'legal_name' => $suppilerData['legal_name'],
        'address1' => $suppilerData['address1'],
        'address2' => $suppilerData['address2'],
        'city' => $suppilerData['city'],
        'legal_entity_type_id'=>'1002', 
        'state_id' => $suppilerData['state_id'],
        'pincode' => $suppilerData['pincode'],
        'pan_number'=>$suppilerData['pan_number'],
        'business_name'=>$suppilerData['bussiness_name'],
        'website_url' => $suppilerData['website_url'],
        'parent_id' => $seller_id ]);
        
        $lastInsertedId= $ligalEntitesResponse->id;
        SupplierDetailsModel::create(['supplier_id'=>$lastInsertedId,'legal_entity_id'=>$seller_id]);

         //SupplierDetailsModel return json_encode($final);
        return view('suppliers.suppliers');
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show()
    {
        //            
        $finalData = SupplierDetailsModel::selectRaw('le.business_name as business_name, le.legal_name as legal_name')
        ->Join('legal_entities as le','le.legal_entity_id','=','supplier_details.legal_entity_id')
        ->Join('supplier_details as sd','sd.supplier_id','=','le.parent_id')
        ->get()->all();
        DB::enableQueryLog();
        print_r(DB::getquerylog());
        echo "<pre/>";
        print_r($finalData);
        die();
        return json_encode($finalData);
           
          // return view('suppliers.suppliers',compact('userData'));
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {   
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        //
    }
}
