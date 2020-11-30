<?php
namespace App\Http\Controllers;

use View;
use URL;
use DB;
use Session;
use App\models\GdsOrders\GDSOrders;
use App\models\GdsChannels\OrdersCharges;
use App\models\GdsChannels\ListingCharges;

class BaseController extends Controller {
    
    public function __construct() {
        try
        {
            $this->middleware(function ($request, $next) {
                if (!Session::has('userId'))
                {
                    $request = \Request::create('login', 'GET', array());
                    echo \Route::dispatch($request)->getContent();
                    die;
                }
                return $next($request);
            });

        } catch (\ErrorException $ex) {
            \Log::error($ex->getMessage().' '.$ex->getTraceAsString());
        }
    }

    public function Breadcrumbs($breadCrumbs) {
        $count = count($breadCrumbs);
        if (!empty($breadCrumbs)) {
            $str = '';
            //$str .='<li><a href="javascript:void(0)" ><i class="glyphicon glyphicon-home"></i></a></li>';
            foreach ($breadCrumbs as $key => $breadCrumb):
                if ($breadCrumb != '#') {
                    $str .='<li><a href="' . URL::asset($breadCrumb) . '" >' . $key . '</a><i class="fa fa-angle-right" aria-hidden="true"></i>
</li>';
                } else if($key==$count) {
                    $str .='<li><span>' . $key . '</span></li>';
                } else {
                    $str .='<li><span class="bread-color">' . $key . '</span><i class="fa fa-angle-right" aria-hidden="true"></i></li>';
                }
            endforeach;
        }
        return View::share('breadCrumbs', $str);
    }

    public function Charges($type, $name, $order_id) {

        if ($type == 1) {

            $createListing = new ListingCharges;
            $listorders = $createListing->Publish($name);
        } elseif ($type == 0) {

            $generateOrders = new OrdersCharges;
            $listorders = $generateOrders->createOrders($name, $order_id);
        } else {
            $listorders = 'OtherCharges';
        }

        return $listorders;
    }

    public function Title($title) {
        $titleString = 'eSealCentral | Dashboard';
        if (!empty($title)) {
            $titleString = $title;
        }
        return View::share('title', $titleString);
    }

    public function getFeaturesByRoleId($roles) {
        if (!empty($roles)) {
            $results = DB::select(DB::raw("SELECT role_access.role_id,role_access.feature_id, features.name, features.parent_id, features.url, features.icon , features.sort_order "
                    . "FROM role_access "
                    . "left join features on role_access.feature_id = features.feature_id "
                    . "where role_access.role_id IN (" . $roles . ") and features.is_menu = 1 and is_active = 1 "
                    . "group by features.feature_id "
                    . "order by features.parent_id ASC, features.sort_order ASC"));
        } else {
            $results = array();
        }
        return $results;
    }

    protected function setupLayout() {
        if (!is_null($this->layout)) {
            $this->layout = View::make($this->layout);
        }
    }
    
    public function checkIfChildFeature($featureId)
    {
        try
        {
            $results = [];
            if($featureId > 0)
            {
                $results = DB::select(DB::raw('SELECT f1.parent_id FROM features AS f1
                            LEFT JOIN features AS f2 ON f2.parent_id = f1.feature_id
                            LEFT JOIN features AS f3 ON f3.parent_id = f2.feature_id
                            WHERE f3.feature_id = '.$featureId));
            }
            return $results;
        } catch (\ErrorException $ex) {
            \Log::info($ex->getMessage());
            \Log::info($ex->getTraceAsString());
        }
    }
}
