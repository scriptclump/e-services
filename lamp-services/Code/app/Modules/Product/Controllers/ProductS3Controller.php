<?php

namespace App\Modules\Product\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Input;
use URL;
use Log;
use DB;
use Redirect;
use Session;
use App\Central\Repositories\ProductRepo;
use App\Modules\Cpmanager\Models\CategoryModel;
use App\Modules\Cpmanager\Models\AdminOrderModel;
use App\Http\Controllers\BaseController;

class ProductS3Controller extends BaseController
{

    public function __construct()
    {
        try
        {
            $this->middleware(function ($request, $next) {
                if (!Session::has('userId')) {
                         Redirect::to('/login')->send();
                }
                return $next($request);
            });
        }
        catch (\ErrorException $ex)
        {
            Log::error($ex->getMessage());
            Log::error($ex->getTraceAsString());
        }
    }

    public function uploadToS3(Request $request)
    {
        try
        {
            $categoryModel = new CategoryModel();
            $token = new AdminOrderModel();
            $data = $request->all();
            $module_id = (isset($data['module_id']) && $data['module_id'] != '') ? $data['module_id'] : 0;

            if ($module_id == 1)
            {
                $valToken = $token->checkLpToken($data['_token']);
            }
            else
            {
                $valToken = $categoryModel->checkCustomerToken($data['_token']);
            }
            if ($valToken > 0)
            {
                $imgObj = $data['image'];
                $EntityType = $data['entity'];
				$mimeType = isset($data['mime'])?$data['mime']:'';
                $type = 1;
                $proRepo = new ProductRepo();
                $url = $proRepo->uploadToS3($imgObj, $EntityType, $type,$mimeType);
                return json_encode(Array('status' => 'success', 'message' => 'Product uploaded successfully', 'data' => ['url' => $url]));
            }
            else
            {
                return json_encode(Array('status' => 'fail', 'message' => 'You have already logged into the Ebutor System', 'data' => []));
            }
        }
        catch (exception $ex)
        {
            Log::info($ex);
        }
    }

}
