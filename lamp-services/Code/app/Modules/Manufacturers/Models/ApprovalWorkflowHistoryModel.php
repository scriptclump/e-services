<?php

namespace App\Modules\Manufacturers\Models;

use Illuminate\Database\Eloquent\Model;

class ApprovalWorkflowHistoryModel extends Model
{
    public $timestamps = true;
    protected $fillable = ['awf_history_id', 'awf_for_type', 'awf_for_id', 'awf_comment', 'status_from_id', 'status_to_id', 'user_id', 'created_by', 'created_at', 'updated_by', 'updated_at'];
    protected $table = "appr_workflow_history";
    protected $primaryKey = 'awf_history_id';
}
