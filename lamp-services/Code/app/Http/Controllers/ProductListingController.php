<?php

namespace App\Http\Controllers;

use Session;
use View;
use Validator;
use Illuminate\Support\Facades\Input;
use URL;
use Log;
use Redirect;

class ProductListingController extends BaseController {

    public function __construct() {   
        try
        {
            $this->middleware(function ($request, $next) {
                if (!Session::has('userId'))
                {
                    return Redirect::to('/');
                }
                return $next($request);
            });                
            parent::Title('Product Listing');
        } catch (\ErrorException $ex) {
            Log::error($ex->getMessage());
            Log::error($ex->getTraceAsString());
        }        
    }
    
    public function indexAction()
    {
        try
        {
            return View::make('productlisting/index');
        } catch (\ErrorException $ex) {
            Log::error($ex->getMessage());
            Log::error($ex->getTraceAsString());
        }
    }
    
    public function addAction()
    {
        try
        {
            return View::make('productlisting/add');
        } catch (\ErrorException $ex) {
            Log::error($ex->getMessage());
            Log::error($ex->getTraceAsString());
        }
    }    
   
    public function editAction()
    {
        try
        {
            return View::make('productlisting/edit');
        } catch (\ErrorException $ex) {
            Log::error($ex->getMessage());
            Log::error($ex->getTraceAsString());
        }
    }
    
    public function deleteAction()
    {
        try
        {
            
        } catch (\ErrorException $ex) {
            Log::error($ex->getMessage());
            Log::error($ex->getTraceAsString());
        }
    }

}
