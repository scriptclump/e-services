<?php namespace App\Http\Controllers;

use Session;
use Caching;

use Illuminate\Support\Facades\Cache;
/*
	Description : This is the Head Controller and contains the Data to Display Features based on LoggedIn User Roles
	Created Author      : Venkat Reddy Muthuru
    Created Date        : May-15-2015
*/

class HeaderController extends BaseController {

    public function compose($view)
    {
        $userId = Session::get('userId');
        $menu = Caching::getElement('menu', $userId);
        if($menu != '')
        {
            $view->with('roleFeatures', $menu);
        }else{
            $roleFeatures = $this->getFeaturesByRoleId(Session::get('roles'));
            $temp = array();
            foreach($roleFeatures as $roleFeature){
                if($roleFeature->parent_id == 0)
                {
                    $temp[$roleFeature->feature_id] = $roleFeature;
                }elseif ($roleFeature->parent_id > 0) {
                    $result = $this->checkIfChildFeature($roleFeature->feature_id);
                    if(empty($result))
                        $temp[$roleFeature->parent_id]->submenus[] = $roleFeature->name.'-'.$roleFeature->url;
                }
            }

            if(!empty($temp))
            {
                $roleFeatures = $temp;
            }
            Caching::setElement('menu', $roleFeatures, $userId);
            $view->with('roleFeatures', $roleFeatures);
        }    	
    }
}