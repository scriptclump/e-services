<?php

namespace App\models\Mongo;

use Jenssegers\Mongodb\Eloquent\Model as Eloquent;
/**
 * 	Class mongo using eloquent and 
 * 	consuming the mongo classes using eloquent module
 */
class MongoMailModel extends Eloquent{

	protected $connection = 'mongo';
    protected $primaryKey = '_id';
    protected $table ="emailTemplates";


    /**
     * [getMailTemplateByName description]
     * @param  [text] $templateName [text]
     * @return [mixed]               [template of the mail name we will be requiring]
     */
    public function getMailTemplateByName($templateName){
        $this->table = 'emailTemplates';
    	$template = $this->where('templateName',$templateName)->get()->all();
        $template_data = json_decode(json_encode($template, true));
        return $template_data;
    }

    public function insertMailTemplate($templateName,$template,$status=1){

        $this->table = 'emailTemplates';
        $this->templateName =  $templateName;
        $this->template = $template;
        $this->active = $status;
        $this->save();
    }
    
}
