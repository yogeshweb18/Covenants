<?php

namespace Axistrustee\Covenants\Http\Controllers;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Axistrustee\ComplianceOverview\Models\ComplianceCovenant;
use Axistrustee\ComplianceOverview\Models\ComplianceInstance;
use Axistrustee\ComplianceOverview\Models\ComplianceReminder;
use Axistrustee\ComplianceOverview\Http\Controllers\ComplianceController;
use Illuminate\Support\Facades\Mail;
use DB;

class CovenantController extends Controller
{
	public function list(Request $request) {
        //print_r($request->all());die;
		$user_object = \Auth::user();
        $current_user = $user_object->id;
        $user_role = $user_object->role->role;
        //$user_role = 'Checker';
        $organization_id = $user_object->organization_id;
        $viewOnly = 0;
        $isApprover = 0;

        $query = DB::table('compliances');
        $query->join('compliances_covenants', 'compliances.id', '=', 'compliances_covenants.complianceId');
        
        if($user_role == config('global.roles.ADMIN') || $user_role == config('global.roles.AUDITOR') || $user_role == config('global.roles.CCU_MAKER')) {
            $viewOnly = 1;
            $key = 'compliances.organization_id';
            $value = $organization_id;
            $query->where($key,$value);
        }
        else if($user_role == config('global.roles.CSOG_MAKER')) {
            $key = 'compliances.userId';
            $value = $current_user;
            $query->where($key,$value);
        }
        else if($user_role == config('global.roles.CCU_CHECKER')) {
            $viewOnly = 1;
            $isApprover = 1;
            $key = 'compliances.organization_id';
            $value = $organization_id;
            $role_id = DB::table('roles')->where('role', config('global.roles.CCU_MAKER'))->first();
            $query->join('users', 'compliances.userId', '=', 'users.id');
            $query->where($key,$value);
            $query->where('users.role_id',$role_id->id);
        }
        else if($user_role == config('global.roles.CSOG_CHECKER')) {
            $viewOnly = 1;
            $isApprover = 1;
            $key = 'compliances.organization_id';
            $value = $organization_id;
            $role_id = DB::table('roles')->where('role', config('global.roles.CSOG_MAKER'))->first();
            $query->join('users', 'compliances.userId', '=', 'users.id');
            $query->where($key,$value);
            $query->where('users.role_id',$role_id->id);
        }
        else if($user_role == config('global.roles.SUPER_ADMIN')) {
            $viewOnly = 1;
        }
        
        if(isset($request->id) && $request->id != '') {
            $query->where('compliances.id',$request->id);
        }
        $query->orderBy('compliances_covenants.updated_at','desc');

        $compliance_covenants = $query->get(['compliances.docName','compliances.id as compliance_id','compliances_covenants.id as covenant_id','compliances_covenants.type','compliances_covenants.subType','compliances_covenants.description','compliances_covenants.frequency',DB::raw('DATE_FORMAT(compliances_covenants.dueDate, "%d-%m-%Y") as dueDate'),DB::raw('DATE_FORMAT(compliances_covenants.startDate, "%d-%m-%Y") as startDate'),'compliances_covenants.covenantStatus']);
        //print_r($query->toSql);die;

        /*$compliances = Compliancetool::where('userId', $current_user)
         ->pluck('clcode', 'clientReference', 'secured','inconsistencyTreatment')
         ->all();*/

        $result['viewOnly'] = $viewOnly;
        $result['isApprover'] =$isApprover;
        $result['compliance_covenants'] = $compliance_covenants;
        //print_r($result);die;
        echo json_encode($result); die;

	}

    public function approvedCovenant() {
        $user_object = \Auth::user();
        $current_user = $user_object->id;
        $user_role = $user_object->role->role;
        $organization_id = $user_object->organization_id;
        $viewOnly = 0;
        $isApprover = 0;

        $query = DB::table('compliances');
        $query->join('compliances_covenants', 'compliances.id', '=', 'compliances_covenants.complianceId');
        $query->join('users', 'compliances.userId', '=', 'users.id');

        if($user_role == config('global.roles.AUDITOR')) {
            $viewOnly = 1;

            $key = 'compliances.organization_id';
            $value = $organization_id;
            $query->where($key,$value);
            $query->where('compliances_covenants.covenantStatus','Approved');
            $query->orderBy('covenant_id','desc');

            $compliance_covenants = $query->get(['compliances.docName','compliances.id as compliance_id','compliances_covenants.id as covenant_id','compliances_covenants.type','compliances_covenants.subType','compliances_covenants.description','compliances_covenants.frequency','compliances_covenants.dueDate','compliances_covenants.startDate','compliances_covenants.covenantStatus','users.name']);
            /*echo "<pre>";
            print_r($compliance_covenants);die;*/

            /*$compliances = Compliancetool::where('userId', $current_user)
             ->pluck('clcode', 'clientReference', 'secured','inconsistencyTreatment')
             ->all();*/

            $result['viewOnly'] = $viewOnly;
            $result['isApprover'] =$isApprover;
            $result['compliance_covenants'] = $compliance_covenants;

            echo json_encode($result); die;
        }
        
        return false;

    }

    public function pendingApproval(Request $request) {
        $user_object = \Auth::user();
        $current_user = $user_object->id;
        $user_role = $user_object->role->role;
        $organization_id = $user_object->organization_id;
        $viewOnly = 0;
        $isApprover = 0;
        $compliance_covenants = [];
        $query = DB::table('compliances');
        $query->join('compliances_covenants', 'compliances.id', '=', 'compliances_covenants.complianceId');
        $query->join('users', 'compliances.userId', '=', 'users.id');
        if($user_role == config('global.roles.CCU_CHECKER') || $user_role == config('global.roles.CSOG_CHECKER')) {
            $viewOnly = 1;
            $isApprover = 1;
            $key = 'compliances.organization_id';
            $value = $organization_id;
            $query->where($key,$value);
            $query->where('compliances_covenants.covenantStatus','Pending For Approval');
            $query->orderBy('covenant_id','desc');
        }
        else if($user_role == config('global.roles.SUPER_ADMIN')) {
            $viewOnly = 1;
            $query->where('compliances_covenants.covenantStatus','Pending For Approval');
            $query->orderBy('covenant_id','desc');
        }
        else if($user_role == config('global.roles.ADMIN')) {
            $viewOnly = 1;
            $key = 'compliances.organization_id';
            $value = $organization_id;
            $query->where($key,$value);
            $query->where('compliances_covenants.covenantStatus','Pending For Approval');
            $query->orderBy('covenant_id','desc');
        }
        else if($user_role == config('global.roles.AUDITOR') || $user_role == config('global.roles.CSOG_MAKER') || $user_role == config('global.roles.CCU_MAKER')) {
            return false;
        }

        $totalFilteredRecord = $totalDataRecord = $draw_val = "";
        $columns_list = array(
            0 =>'covenant_id',
            1 =>'compliance_id',
            2 =>'type',
            3=> 'subType',
            4=> 'description',
            5=> 'frequency',
            6=> 'startDate',
            7=> 'dueDate',
            8=> 'covenantStatus',
            9=> 'name',
            10=>'actions',
        );
        if(!empty($request->input('search.value')))
        {
            $query->Where('compliances.id','LIKE','%'.$request->input('search.value').'%');
        }
        $limit_val = $request->input('length');
        $start_val = $request->input('start');
        $order_val = $columns_list[$request->input('order.0.column')];
        $dir_val = $request->input('order.0.dir');
        //$query->orderBy($order_val,$dir_val);
        $query->orderByRaw('compliances.updated_at DESC');
        $post_count = $query->count();
        $query->offset($start_val);
        $query->limit($limit_val);
//print_r($query->toSql());die;
        $compliance_covenants = $query->get(['compliances.docName','compliances.id as compliance_id','compliances_covenants.id as covenant_id','compliances_covenants.type','compliances_covenants.subType','compliances_covenants.description','compliances_covenants.frequency',DB::raw('DATE_FORMAT(compliances_covenants.dueDate, "%d-%m-%Y") as dueDate'),DB::raw('DATE_FORMAT(compliances_covenants.startDate, "%d-%m-%Y") as startDate'),'compliances_covenants.covenantStatus','users.name']);

        foreach ($compliance_covenants as $key=>$cov) {
            if(($viewOnly != 1 && $cov->covenantStatus == 'Draft') || ($isApprover == 1 && $cov->covenantStatus == 'Pending For Approval')) {
                $compliance_covenants[$key]->checkbox = '<input type="checkbox" value="'.$cov->covenant_id.'" :id="'.$cov->covenant_id.'" name="checkApproval[]"/>';
            }
            $compliance_covenants[$key]->covenant_id = $cov->covenant_id;
            $actions = '';

            $actions .= "<div class='dropdown'>";
            $actions .= "<span class='three-dots'></span>"; 
            $actions .= "<div class='dropdown-content'>"; 
            $actions .= "<ul style='width: 100%; padding-top: 8px;padding-left:0px;'>";
            $actions .= "<li><span style='width: 100%; display: flex; padding-top: 8px;'>";
            $actions .= "<a class='point view-placeholder' title='View'>View</a>";
            $actions .= "</span></li>"; 
            if($viewOnly != 1) {
                $actions .= "<li><span style='width: 100%; display: flex; padding-top: 8px;'>";
                $actions .= "<a class='point timeline-placeholder' title='View'>Timeline</a>";
                $actions .= "</span></li>";
            }
            if($isApprover == 1) {
                $actions .= "<li><span style='width: 100%; display: flex; padding-top: 8px;'>";
                $actions .= "<div class='point'>";
                if($cov->covenantStatus == 'Pending For Approval'){
                    $actions .= "<div><a class='point approve-placeholder' title='Approve'>Approve</a></div>";
                    $actions .= "<div><a class='point reject-placeholder' title='Reject'>Reject</a></div>";
                }
                $actions .= "</div>";
                $actions .= "</span></li>";
            }
            $actions .= "</ul></div></div>";

            $compliance_covenants[$key]->actions = $actions;
        }

        $totalDataRecord = $post_count;
        $totalFilteredRecord = $totalDataRecord;
        $draw_val = $request->input('draw');
        $get_json_data = array(
            "draw"            => intval($draw_val),
            "recordsTotal"    => intval($totalDataRecord),
            "recordsFiltered" => intval($totalFilteredRecord),
            "data"            => $compliance_covenants
        );
         
        echo json_encode($get_json_data);

        die;

    }

    public function pendingCovenant() {
        $user_object = \Auth::user();
        $current_user = $user_object->id;
        $user_role = $user_object->role->role;
        $organization_id = $user_object->organization_id;
        $viewOnly = 0;
        $isApprover = 0;

        $query = DB::table('compliances');
        $query->join('compliances_covenants', 'compliances.id', '=', 'compliances_covenants.complianceId');
        $query->join('users', 'compliances.userId', '=', 'users.id');

        if($user_role == config('global.roles.CCU_CHECKER') || $user_role == config('global.roles.CSOG_CHECKER')) {
            $viewOnly = 1;
            $isApprover = 1;
        }
        else if($user_role == config('global.roles.SUPER_ADMIN')) {
            $viewOnly = 1;
        }
        else if($user_role == config('global.roles.ADMIN')) {
            $viewOnly = 1;
        }
        else if($user_role == config('global.roles.AUDITOR') || $user_role == config('global.roles.CSOG_MAKER') || $user_role == config('global.roles.CCU_MAKER')) {
            return false;
        }

        return inertia('PendingApproval',[
            'viewOnly' => $viewOnly,
            'isApprover' => $isApprover
        ]);
        die;
        

    }

    public function pendingApprovalActive() {
        $comp_cov = [];
        $viewOnly = 0;
        $isApprover = 0;
        $user_object = \Auth::user();
        $current_user = $user_object->id;
        $user_role = $user_object->role->role;
        if($user_role == config('global.roles.AUDITOR') || $user_role == config('global.roles.CSOG_MAKER') || $user_role == config('global.roles.CCU_MAKER')) {
            return false;
        }
        else if($user_role == config('global.roles.CCU_CHECKER') || $user_role == config('global.roles.CSOG_CHECKER')) {
            $viewOnly = 1;
            $isApprover = 1;
        }
        else if($user_role == config('global.roles.SUPER_ADMIN') || $user_role == config('global.roles.ADMIN')) {
            $viewOnly = 1;
        }
       
        return inertia('Summary',[
            'loadPage' => 1,
            'viewOnly' => $viewOnly,
            'isApprover' => $isApprover
        ]);
        die;
    }

    public function approvedActiveList() {       
        $comp_cov = [];
        $viewOnly = 0;
        $isApprover = 0;
        $user_object = \Auth::user();
        $current_user = $user_object->id;
        $user_role = $user_object->role->role;
        if($user_role == config('global.roles.ADMIN') || $user_role == config('global.roles.CCU_CHECKER') || $user_role == config('global.roles.CSOG_CHECKER')) {
            return false;
        }
        else if($user_role == config('global.roles.CSOG_MAKER') || $user_role == config('global.roles.CCU_MAKER')) {
            return false;
        }
        else if($user_role == config('global.roles.AUDITOR')) {
            $viewOnly = 1;
        }
       
        return inertia('Summary',[
            'loadPage' => 2,
            'viewOnly' => $viewOnly,
            'isApprover' => $isApprover
        ]);
    }

    public function activeListApproved(Request $request) {
        //print_r($request->input());die;
        $totalFilteredRecord = $totalDataRecord = $draw_val = "";
        $columns_list = array(
            0 =>'instanceId',
            1 =>'clcode',
            2 =>'name',
            3=> 'type',
            4=> 'subType',
            5=> 'frequency',
            6=> 'activateDate',
            7=> 'dueDate',
            8=> 'targetValue',
            9=> 'resolution_value',
            10=> 'status',
            11=> 'targetValue',
            12=> 'username',
            13=>'actions',

        );
        $limit_val = $request->input('length');
        $start_val = $request->input('start');
        $order_val = $columns_list[$request->input('order.0.column')];
        $dir_val = $request->input('order.0.dir');
        //$search_text = $request->input('search.value');
        $comp_cov = [];
        $user_object = \Auth::user();
        $current_user = $user_object->id;
        $user_role = $user_object->role->role;
        $organization_id = $user_object->organization_id;
        $viewOnly = 0;
        $isApprover = 0;
        $today = date("Y-m-d");
        $query = DB::table('compliances_covenants_instances as ci');
        $query->join('compliances_covenants as cc', 'ci.covenantId', '=', 'cc.id');
        $query->join('compliances as c', 'ci.complianceId', '=', 'c.id');
        $query->join('clients', 'c.clientReference', '=', 'clients.id');
        $query->leftJoin('users', 'ci.resolver', '=', 'users.id');
        if($user_role == config('global.roles.ADMIN') || $user_role == config('global.roles.CCU_CHECKER') || $user_role == config('global.roles.CSOG_CHECKER')) {
            return false;
        }
        else if($user_role == config('global.roles.CSOG_MAKER') || $user_role == config('global.roles.CCU_MAKER')) {
            return false;
        }
        else if($user_role == config('global.roles.AUDITOR')) {
            $viewOnly = 1;
            $key = 'c.organization_id';
            $value = $organization_id;
            $query->where($key,$value);
        }
        
        $query->where('cc.covenantStatus','Approved');
        $query->where('ci.approvalStatus','=','Approved');
        foreach($request->input('columns') as $cols) {
            if($cols['search']['value'] != '') {
                if($cols['data'] == 'name') {
                   $query->where('clients.name','LIKE','%'.$cols['search']['value'].'%'); 
                }
                else if($cols['data'] == 'clcode') {
                   $query->where('c.clcode','LIKE','%'.$cols['search']['value'].'%'); 
                }
                else if($cols['data'] == 'type') {
                   $query->where('cc.type','LIKE','%'.$cols['search']['value'].'%'); 
                }
                else if($cols['data'] == 'subType') {
                   $query->where('cc.subType','LIKE','%'.$cols['search']['value'].'%'); 
                }
            }
        }
        if(!empty($request->input('search.value')))
        {
            $query->where('cc.frequency','LIKE','%'.$request->input('search.value').'%');
            //$query->orWhere('ci.activateDate','LIKE','%'.$request->input('search.value').'%');
        }
        $query->orderBy($order_val,$dir_val);
        $post_count = $query->count();
        $query->offset($start_val);
        $query->limit($limit_val);

        $post_data = $query->get(['ci.id','ci.complianceId','ci.covenantId','c.clcode','clients.name as cname','cc.type','cc.subType','cc.frequency',DB::raw('DATE_FORMAT(ci.activateDate, "%d-%m-%Y") as activateDate'),DB::raw('DATE_FORMAT(ci.dueDate, "%d-%m-%Y") as dueDate'),'cc.targetValue','ci.resolution_value','ci.status','cc.description','cc.comments','c.inconsistencyTreatment','c.secured','ci.approvalStatus','users.name']);
        
         
        $data_val = array();
        if(!empty($post_data))
        {
            foreach ($post_data as $cc)
            {   
                $actions = '';
                $instanceStatus= '';
                if(($viewOnly != 1 && ($cc->approvalStatus == "Not Sent" || $cc->approvalStatus == NULL) && $cc->status != 0) || ($isApprover == 1 && $cc->approvalStatus == "Pending For Approval")) {
                    $postnestedData['instanceId'] = '<input type="checkbox" value="'.$cc->id.'" :id="'.$cc->id.'" name="checkApproval[]"/>'; 
                } else {
                    $postnestedData['instanceId'] = '';
                }
                $isins = DB::table('compliances_isin')
                    ->select(DB::raw("GROUP_CONCAT(isin SEPARATOR ', ') as isin"))
                    ->groupBy('complianceId')
                    ->where('complianceId',$cc->complianceId)
                    ->get();

                if(count($isins) > 0)
                    $postnestedData['isins'] = $isins[0]->isin;

                $postnestedData['id'] = $cc->id;   
                $postnestedData['clcode'] = $cc->clcode;
                $postnestedData['name'] = $cc->cname;
                $postnestedData['type'] = $cc->type; 
                $postnestedData['subType'] = $cc->subType;
                $postnestedData['frequency'] = $cc->frequency;
                $postnestedData['activateDate'] = $cc->activateDate;
                $postnestedData['dueDate'] = $cc->dueDate;
                $postnestedData['targetValue'] = $cc->targetValue;
                $postnestedData['resolution_value'] = $cc->resolution_value;
                $instanceStatus .= $this->get_instance_status($cc->status);
                if(($cc->approvalStatus == "Not Sent")) {
                    $instanceStatus .= " <span title='Not sent for approval' class='notsent'></span>"; 
                }
                else if(($cc->approvalStatus == "Pending For Approval")) {
                    $instanceStatus .= " <span title='Pending for approval' class='pendingApproval'></span>"; 
                }
                else if(($cc->approvalStatus == "Approved")) {
                    $instanceStatus .= " <span title='Approved' class='approved'></span>"; 
                }
                else if(($cc->approvalStatus == "Rejected")) {
                    $instanceStatus .= " <span title='Rejected' class='rejected'></span>"; 
                }
                $postnestedData['status'] = $instanceStatus;
                $postnestedData['approvalStatus'] = $cc->approvalStatus;
                $postnestedData['description'] = $cc->description;
                $postnestedData['comments'] = $cc->comments;
                $postnestedData['inconsistencyTreatment'] = $cc->inconsistencyTreatment;
                $postnestedData['secured'] = $cc->secured;
                $postnestedData['username'] = $cc->name;
                if(($viewOnly != 1 && $cc->status == 0 &&  $cc->approvalStatus != "Pending For Approval" && $cc->approvalStatus != "Approved")) {
                    $actions .= "<button @click.prevent='view(".$cc->id.")' class='resolve-placeholder'> <img title='Resolution' src='/img/settings.png' /></button>"; 
                }
                if(($viewOnly != 1 && $cc->approvalStatus == "Not Sent" && $cc->status != 0)) {
                    $actions .= "<button @click.prevent='submitForApproval(".$cc->id.")' class='sfa-placeholder'><img title='Submit For Approval' src='/img/sendForApproval.png' /></button>"; 
                }
                if(($isApprover == 1 && $cc->approvalStatus == "Pending For Approval")) {
                    $actions .= "<div class='dropdown'>";
                    $actions .= "<span class='three-dots'></span>"; 
                    $actions .= "<div class='dropdown-content'>"; 
                    $actions .= "<ul style='padding: 0px;'><li><span style='width: 100%; display: flex; padding-top: 8px;'>"; 
                    $actions .= "<div v-if='isApprover == 1' class='point'>";
                    $actions .= "<a @click.prevent='mark('Approved',".$cc->id.")' title='Approve' class='approve-placeholder'> Approve</a>";
                    $actions .= "<a @click.prevent='mark('Rejected',".$cc->id.")' title='Reject' class='reject-placeholder'> Reject</a>";
                    $actions .= "</div></span></li></ul></div></div>"; 
                }
                $postnestedData['actions'] = $actions;
                //$postnestedData['actions'] = 'button';
                $data_val[] = $postnestedData;      
                
            }
        }
       
        $totalDataRecord = $post_count;
        $totalFilteredRecord = $totalDataRecord;
        $draw_val = $request->input('draw');
        $get_json_data = array(
        "draw"            => intval($draw_val),
        "recordsTotal"    => intval($totalDataRecord),
        "recordsFiltered" => intval($totalFilteredRecord),
        "data"            => $data_val
        );
         
        echo json_encode($get_json_data);
    }

    public function clone(Request $request) {

        $covenant_id = $request->input('id');
        try {
                $covenant = ComplianceCovenant::FindOrFail($covenant_id);

                $covenant_guide = DB::table('standard_covenants')
                ->where('sub_type',$covenant->subType)
                ->get(['covenant_parameters','child_covenant'])
                ->first();
                
                $covenantDetails = [];
                $covenantDetails['id'] = $covenant->id;
                $covenantDetails['complianceId'] = $covenant->complianceId;
                $covenantDetails['type'] = $covenant->type;
                $covenantDetails['subType'] = $covenant->subType;
                $covenantDetails['description'] = $covenant->description;
                $covenantDetails['frequency'] = $covenant->frequency;
                $covenantDetails['comments'] = $covenant->comments;
                $covenantDetails['startDate'] = $covenant->startDate;
                $covenantDetails['dueDate'] = $covenant->dueDate;
                $covenantDetails['applicableMonth'] = $covenant->applicableMonth;
                $covenantDetails['targetValue'] = $covenant->targetValue;
                $covenantDetails['isCustomCovenant'] = $covenant->isCustomCovenant;

                if($covenant->isCustomCovenant == 0) {
                    if(!empty($covenant_guide->covenant_parameters)) {
                        $cov_param = json_decode($covenant_guide->covenant_parameters, true);
                        $param =[];
                        foreach ($cov_param['covenant_details'] as $key => $value) {
                            $column = $value['key'];
                            $cov_param['covenant_details'][$key]['value'] = $covenant->$column;
                        }
                        $covenantDetails['covenantParameters'] = $cov_param['covenant_details'];
                    }

                    if(!empty($covenant_guide->child_covenant)) {
                        $cov_child = json_decode($covenant_guide->child_covenant, true);
                        $child =[];
                        foreach ($cov_child['child_covenant'] as $key => $value) {
                            $column = $value['key'];
                            $cov_child['child_covenant'][$key]['value'] = $covenant->$column;
                        }
                        $covenantDetails['child'] = $cov_child['child_covenant'];
                    }

                }
                else {
                    $covenantDetails['custom_parameter'] = $covenant->custom_parameter;
                    $covenantDetails['custom_value'] = $covenant->custom_value;
                    $covenantDetails['custom_child'] = $covenant->custom_child;
                    $covenantDetails['custom_child_dueDate'] = $covenant->custom_child_dueDate;
                }

        } catch (\Exception $e) {
            $covenantDetails =  response()->json(['message'=>'user not found!'], 404);
        }
        return inertia('Edit',[
            'covenant' => $covenantDetails,
            'action' => 'clone'
        ]);
    }

    public function addClone(Request $request) {

        $submittedData = $request->all();
        $covenantData = [];
        $childData = [];
        $covenantData['type'] = $submittedData['type'];
        $covenantData['complianceId'] = $submittedData['complianceId'];
        $covenantData['subType'] = $submittedData['subType'];
        $covenantData['description'] = $submittedData['description'];
        $covenantData['frequency'] = $submittedData['frequency'];
        $covenantData['targetValue'] = $submittedData['targetValue'];
        $covenantData['startDate'] = $submittedData['startDate'];
        $covenantData['applicableMonth'] = $submittedData['applicableMonth'];
        $covenantData['dueDate'] = $submittedData['dueDate'];
        $covenantData['comments'] = $submittedData['comments'];
        $covenantData['isCustomCovenant'] = $submittedData['isCustomCovenant'];
        $saveStatus = 'success';

        if($submittedData['isCustomCovenant'] == 0) {
            if(isset($submittedData['covenantParameters']) && count($submittedData['covenantParameters']) > 0) {
                foreach($submittedData['covenantParameters'] as $key=>$value) {
                    $covenantData[$value['key']] = $value['value'];
                }
            }
            if(isset($submittedData['child']) && count($submittedData['child']) > 0) {
                foreach($submittedData['child'] as $key=>$value) {
                    $covenantData[$value['key']] = $value['value'];
                }
                $childData = $submittedData['child'];
            }
        }
        else {
            $covenantData['custom_parameter'] = $submittedData['custom_parameter'];
            $covenantData['custom_value'] = $submittedData['custom_value'];
            $covenantData['custom_child'] = $submittedData['custom_child'];
            $covenantData['custom_child_dueDate'] = $submittedData['custom_child_dueDate'];
            if($covenantData['custom_child_dueDate'] != '') { 
                $childData[0]['value'] = $covenantData['custom_child_dueDate'];
                $childData[0]['label'] = $covenantData['custom_child'];
            }
 
        }
                //print_r($childData);die;
        $complianceCovenant = new ComplianceCovenant($covenantData);
        $isComplianceCovenant = $complianceCovenant->save();
        if(!$isComplianceCovenant) {
            $saveStatus = 'fail';
        }
        else {
            $covenantData['covenant_id'] = $complianceCovenant->id;
            $ComplianceController = new ComplianceController();
            $ComplianceController->saveTimelines($covenantData,$childData);
        }

        $result['status'] = $saveStatus;
        $result['complianceId'] = $submittedData['complianceId'];        

        echo json_encode($result);die;

    }

	public function edit(Request $request) {
		$covenant_id = $request->input('id');
		try {
                $covenant = ComplianceCovenant::FindOrFail($covenant_id);
                $covenant_guide = DB::table('standard_covenants')
                ->where('sub_type',$covenant->subType)
                ->get(['covenant_parameters','child_covenant'])
                ->first();


                $covenantDetails = [];
                $request->validate([
                    'description' => ['required', 'string', 'max:255', 'regex:/^[a-zA-Z0-9\s]+$/'],
                    'comments' => ['required', 'string', 'max:255', 'regex:/^[a-zA-Z0-9\s]+$/'],
                    'targetValue' => ['required', 'string', 'max:255', 'regex:/^[a-zA-Z0-9\s]+$/'],
                ]);
                
                $covenantDetails['id'] = $covenant->id;
                $covenantDetails['complianceId'] = $covenant->complianceId;
                $covenantDetails['type'] = $covenant->type;
                $covenantDetails['subType'] = $covenant->subType;
                $covenantDetails['description'] = $covenant->description;
                $covenantDetails['frequency'] = $covenant->frequency;
                $covenantDetails['comments'] = $covenant->comments;
                $covenantDetails['startDate'] = $covenant->startDate;
                $covenantDetails['dueDate'] = $covenant->dueDate;
                $covenantDetails['applicableMonth'] = $covenant->applicableMonth;
                $covenantDetails['targetValue'] = $covenant->targetValue;
                $covenantDetails['isCustomCovenant'] = $covenant->isCustomCovenant;

                if($covenant->isCustomCovenant == 0) {
	                if(!empty($covenant_guide->covenant_parameters)) {
	                    $cov_param = json_decode($covenant_guide->covenant_parameters, true);
	                    $param =[];
	                    foreach ($cov_param['covenant_details'] as $key => $value) {
	                    	$column = $value['key'];
	                    	$cov_param['covenant_details'][$key]['value'] = $covenant->$column;
	                    }
	                    $covenantDetails['covenantParameters'] = $cov_param['covenant_details'];
	                }

	                if(!empty($covenant_guide->child_covenant)) {
	                    $cov_child = json_decode($covenant_guide->child_covenant, true);
	                    $child =[];
	                    foreach ($cov_child['child_covenant'] as $key => $value) {
	                    	$column = $value['key'];
	                    	$cov_child['child_covenant'][$key]['value'] = $covenant->$column;
	                    }
	                    $covenantDetails['child'] = $cov_child['child_covenant'];
	                }
	            }
	            else {
	            	$covenantDetails['custom_parameter'] = $covenant->custom_parameter;
	            	$covenantDetails['custom_value'] = $covenant->custom_value;
	            	$covenantDetails['custom_child'] = $covenant->custom_child;
	            	$covenantDetails['custom_child_dueDate'] = $covenant->custom_child_dueDate;
                    
	            }

        } catch (\Exception $e) {
            $covenantDetails =  response()->json(['message'=>'Covenant not found!'], 404);
        }
        return inertia('Edit',[
            'covenant' => $covenantDetails,
        ]);
	}

	public function update(Request $request) {

		$submittedData = $request->all();
		$covenantData = [];
        $childData = [];
        $covenantData['description'] = $submittedData['description'];
        $covenantData['frequency'] = $submittedData['frequency'];
        $covenantData['targetValue'] = $submittedData['targetValue'];
        $covenantData['startDate'] = $submittedData['startDate'];
        $covenantData['applicableMonth'] = $submittedData['applicableMonth'];
        $covenantData['dueDate'] = $submittedData['dueDate'];
        $covenantData['comments'] = $submittedData['comments'];
        $saveStatus = 'success';

        if($submittedData['isCustomCovenant'] == 0) {
	        if(isset($submittedData['covenantParameters']) && count($submittedData['covenantParameters']) > 0) {
	        	foreach($submittedData['covenantParameters'] as $key=>$value) {
	        		$covenantData[$value['key']] = $value['value'];
	        	}
	        }
	        if(isset($submittedData['child']) && count($submittedData['child']) > 0) {
	        	foreach($submittedData['child'] as $key=>$value) {
	        		$covenantData[$value['key']] = $value['value'];
	        	}
                $childData = $submittedData['child'];
	        }
    	}
    	else {
    		$covenantData['custom_parameter'] = $submittedData['custom_parameter'];
    		$covenantData['custom_value'] = $submittedData['custom_value'];
    		$covenantData['custom_child'] = $submittedData['custom_child'];
    		$covenantData['custom_child_dueDate'] = $submittedData['custom_child_dueDate'];
            if($covenantData['custom_child_dueDate'] != '') { 
                $childData[0]['value'] = $covenantData['custom_child_dueDate'];
                $childData[0]['label'] = $covenantData['custom_child'];
            }
    	}

        $complianceCovenant = ComplianceCovenant::find($submittedData['id'])->update($covenantData);
        if(!$complianceCovenant) {
            $saveStatus = 'fail';
        }
        else {
            $covenantData['covenant_id'] = $submittedData['id'];
            $covenantData['complianceId'] = $submittedData['complianceId'];
            $covenantData['isCustomCovenant'] = $submittedData['isCustomCovenant'];
            $ComplianceController = new ComplianceController();
            DB::beginTransaction();
            try{
                $ComplianceController->deleteInstanceReminders($covenantData['covenant_id']);
                $response = $ComplianceController->saveTimelines($covenantData,$childData);
                DB::commit();
            }
            catch(\Exception $e){
                //if there is an error/exception in the above code before commit, it'll rollback
                DB::rollBack();
                return $e->getMessage();
            }
        }

        $result['status'] = $saveStatus;
        $result['complianceId'] = $submittedData['complianceId'];        

        echo json_encode($result);die;
	}

	public function view(Request $request) 
    { 
        $id = $request->input('id');
        try {
            $covenant = DB::table('compliances')
            ->join('compliances_covenants', 'compliances.id', '=', 'compliances_covenants.complianceId')
            ->where('compliances_covenants.id',$id)
            ->get(['compliances.docName','compliances.id as compliance_id','compliances_covenants.*'])
            ->first();

            $covenant_guide = DB::table('standard_covenants')
            ->where('sub_type',$covenant->subType)
            ->get(['covenant_parameters','child_covenant'])
            ->first();

            $covenantDetails = [];
            $covenantDetails['id'] = $covenant->id;
            $covenantDetails['complianceId'] = $covenant->complianceId;
            $covenantDetails['docName'] = $covenant->docName;
            $covenantDetails['type'] = $covenant->type;
            $covenantDetails['subType'] = $covenant->subType;
            $covenantDetails['description'] = $covenant->description;
            $covenantDetails['frequency'] = $covenant->frequency;
            $covenantDetails['comments'] = $covenant->comments;
            $covenantDetails['startDate'] = date("d-m-Y", strtotime($covenant->startDate));
            $covenantDetails['dueDate'] = date("d-m-Y", strtotime($covenant->dueDate));
            $covenantDetails['applicableMonth'] = $covenant->applicableMonth;
            $covenantDetails['targetValue'] = $covenant->targetValue;
            $covenantDetails['isCustomCovenant'] = $covenant->isCustomCovenant;

            if($covenant->isCustomCovenant == 0) {
                if(!empty($covenant_guide->covenant_parameters)) {
                    $cov_param = json_decode($covenant_guide->covenant_parameters, true);
                    $param =[];
                    foreach ($cov_param['covenant_details'] as $key => $value) {
                    	$column = $value['key'];
                    	$cov_param['covenant_details'][$key]['value'] = $covenant->$column;
                    }
                    $covenantDetails['covenantParameters'] = $cov_param['covenant_details'];
                }

                if(!empty($covenant_guide->child_covenant)) {
                    $cov_child = json_decode($covenant_guide->child_covenant, true);
                    $child =[];
                    foreach ($cov_child['child_covenant'] as $key => $value) {
                    	$column = $value['key'];
                    	$cov_child['child_covenant'][$key]['value'] = $covenant->$column;
                    }
                    $covenantDetails['child'] = $cov_child['child_covenant'];
                }
            }

            else {
            	$covenantDetails['custom_parameter'] = $covenant->custom_parameter;
            	$covenantDetails['custom_value'] = $covenant->custom_value;
            	$covenantDetails['custom_child'] = $covenant->custom_child;
            	$covenantDetails['custom_child_dueDate'] = $covenant->custom_child_dueDate;
            }

        } catch (\Exception $e) {
            $covenantDetails =  response()->json(['message'=>'Covenant not found!'], 404);
        }

        $result['status'] = 'success';
        $result['covenant'] = $covenantDetails;

        return json_encode($result);die;
    }

    public function timeline($id) {
        $covenant_id = $id;
        $ComplianceController = new ComplianceController();
        $covenant_data = $ComplianceController->getComplianceCovenant($covenant_id);

        return inertia('Timeline',[
            'covData' => $covenant_data
        ]);
    }

    public function saveTimeline(Request $request) {
        /*$validated = $request->validate([
            'reminder.interval' => 'required',
        ]);*/
        //print_r($errors->all());die;
        $instances = $request->input('instances');
        $child = $request->input('child') ? $request->input('child') : [];
        $covenant_id = $request->input('covenant_id');
        $reminder = $request->input('reminder') ? $request->input('reminder') : [];
        $reminderBefore = $reminder['before'] ? $reminder['before'] : 15;
        $reminderInterval = $reminder['interval'] ? $reminder['interval'] : 5;
        $ComplianceController = new ComplianceController();
        foreach ($instances as $key => $value) {
            $instanceData = [];
            $instanceData['activateDate'] = $value['activateDate'];
            $instanceData['dueDate'] = $value['dueDate'];
            $instanceData['reminderBefore'] = $reminderBefore;
            $instanceData['reminderInterval'] = $reminderInterval;
            DB::beginTransaction();
            try{
                $complianceCovenant = ComplianceInstance::find($value['id'])->update($instanceData);
                $this->deleteReminder($value['id']);
                $ComplianceController->addReminder($value['id'],$instanceData['dueDate'],$instanceData['reminderBefore'],$instanceData['reminderInterval']);
                DB::commit();

            }
            catch(\Exception $e){
                //if there is an error/exception in the above code before commit, it'll rollback
                DB::rollBack();
                return $e->getMessage();
            }   
        }
        foreach ($child as $key => $value) {
            $instanceData = [];
            $instanceData['activateDate'] = $value['activateDate'];
            $instanceData['dueDate'] = $value['dueDate'];
            $instanceData['reminderBefore'] = $value['reminderBefore'];
            $instanceData['reminderInterval'] = $value['reminderInterval'];
            DB::beginTransaction();
            try{
                $complianceCovenant = ComplianceInstance::find($value['id'])->update($instanceData);
                $this->deleteReminder($value['id']);
                $ComplianceController->addReminder($value['id'],$instanceData['dueDate'],$instanceData['reminderBefore'],$instanceData['reminderInterval']);
                DB::commit();
            }
            catch(\Exception $e){
                //if there is an error/exception in the above code before commit, it'll rollback
                DB::rollBack();
                return $e->getMessage();
            }  
        }

        $result['status'] = 'success';

        return json_encode($result);die;
    }

    public function deleteReminder($instanceId){
        if($instanceId) {
            $deletedRows = ComplianceReminder::where('instance_id', $instanceId)->delete();
        }
    }

    public function activeListPending(Request $request) {
        //print_r($request->input());die;
        $totalFilteredRecord = $totalDataRecord = $draw_val = "";
        $columns_list = array(
            0 =>'instanceId',
            1 =>'clcode',
            2 =>'name',
            3=> 'type',
            4=> 'subType',
            5=> 'frequency',
            6=> 'activateDate',
            7=> 'dueDate',
            8=> 'targetValue',
            9=> 'resolution_value',
            10=> 'status',
            11=> 'targetValue',
            12=> 'username',
            13=>'actions',

        );
        $limit_val = $request->input('length');
        $start_val = $request->input('start');
        $order_val = $columns_list[$request->input('order.0.column')];
        $dir_val = $request->input('order.0.dir');
        //$search_text = $request->input('search.value');
        $comp_cov = [];
        $user_object = \Auth::user();
        $current_user = $user_object->id;
        $user_role = $user_object->role->role;
        $organization_id = $user_object->organization_id;
        $viewOnly = 0;
        $isApprover = 0;
        $today = date("Y-m-d");
        $query = DB::table('compliances_covenants_instances as ci');
        $query->join('compliances_covenants as cc', 'ci.covenantId', '=', 'cc.id');
        $query->join('compliances as c', 'ci.complianceId', '=', 'c.id');
        $query->join('clients', 'c.clientReference', '=', 'clients.id');
        $query->leftJoin('users', 'ci.resolver', '=', 'users.id');
        if($user_role == config('global.roles.AUDITOR') || $user_role == config('global.roles.CSOG_MAKER') || $user_role == config('global.roles.CCU_MAKER')) {
            return false;
        }
        else if($user_role == config('global.roles.ADMIN')) {
            $viewOnly = 1;
            $key = 'c.organization_id';
            $value = $organization_id;
            $query->where($key,$value);
        }
        if($user_role == config('global.roles.CSOG_CHECKER') || $user_role == config('global.roles.CCU_CHECKER')) {
            $viewOnly = 1;
            $isApprover = 1;
            $key = 'c.organization_id';
            $value = $organization_id;
            $query->where($key,$value);
        }
        else if($user_role == config('global.roles.SUPER_ADMIN')) {
            $viewOnly = 1;
        }
        $query->where('cc.covenantStatus','Approved');
        $query->where('ci.approvalStatus','=','Pending For Approval');
        //$query->orderByRaw('ci.activateDate ASC');
        foreach($request->input('columns') as $cols) {
            if($cols['search']['value'] != '') {
                if($cols['data'] == 'name') {
                   $query->where('clients.name','LIKE','%'.$cols['search']['value'].'%'); 
                }
                else if($cols['data'] == 'clcode') {
                   $query->where('c.clcode','LIKE','%'.$cols['search']['value'].'%'); 
                }
                else if($cols['data'] == 'type') {
                   $query->where('cc.type','LIKE','%'.$cols['search']['value'].'%'); 
                }
                else if($cols['data'] == 'subType') {
                   $query->where('cc.subType','LIKE','%'.$cols['search']['value'].'%'); 
                }
            }
        }
        if(!empty($request->input('search.value')))
        {
            $query->where('cc.frequency','LIKE','%'.$request->input('search.value').'%');
            //$query->orWhere('ci.activateDate','LIKE','%'.$request->input('search.value').'%');
        }
        $query->orderBy($order_val,$dir_val);
        $post_count = $query->count();
        $query->offset($start_val);
        $query->limit($limit_val);

        $post_data = $query->get(['ci.id','ci.complianceId','ci.covenantId','c.clcode','clients.name as cname','cc.type','cc.subType','cc.frequency',DB::raw('DATE_FORMAT(ci.activateDate, "%d-%m-%Y") as activateDate'),DB::raw('DATE_FORMAT(ci.dueDate, "%d-%m-%Y") as dueDate'),'cc.targetValue','ci.resolution_value','ci.status','cc.description','cc.comments','c.inconsistencyTreatment','c.secured','ci.approvalStatus','users.name']);
        
         
        $data_val = array();
        if(!empty($post_data))
        {
            foreach ($post_data as $cc)
            {   
                $actions = '';
                $instanceStatus= '';
                if(($viewOnly != 1 && ($cc->approvalStatus == "Not Sent" || $cc->approvalStatus == NULL) && $cc->status != 0) || ($isApprover == 1 && $cc->approvalStatus == "Pending For Approval")) {
                    $postnestedData['instanceId'] = '<input type="checkbox" value="'.$cc->id.'" :id="'.$cc->id.'" name="checkApproval[]"/>'; 
                } else {
                    $postnestedData['instanceId'] = '';
                }
                $isins = DB::table('compliances_isin')
                    ->select(DB::raw("GROUP_CONCAT(isin SEPARATOR ', ') as isin"))
                    ->groupBy('complianceId')
                    ->where('complianceId',$cc->complianceId)
                    ->get();

                if(count($isins) > 0)
                    $postnestedData['isins'] = $isins[0]->isin;

                $postnestedData['id'] = $cc->id;   
                $postnestedData['clcode'] = $cc->clcode;
                $postnestedData['name'] = $cc->cname;
                $postnestedData['type'] = $cc->type; 
                $postnestedData['subType'] = $cc->subType;
                $postnestedData['frequency'] = $cc->frequency;
                $postnestedData['activateDate'] = $cc->activateDate;
                $postnestedData['dueDate'] = $cc->dueDate;
                $postnestedData['targetValue'] = $cc->targetValue;
                $postnestedData['resolution_value'] = $cc->resolution_value;
                $instanceStatus .= $this->get_instance_status($cc->status);
                if(($cc->approvalStatus == "Not Sent")) {
                    $instanceStatus .= " <span title='Not sent for approval' class='notsent'></span>"; 
                }
                else if(($cc->approvalStatus == "Pending For Approval")) {
                    $instanceStatus .= " <span title='Pending for approval' class='pendingApproval'></span>"; 
                }
                else if(($cc->approvalStatus == "Approved")) {
                    $instanceStatus .= " <span title='Approved' class='approved'></span>"; 
                }
                else if(($cc->approvalStatus == "Rejected")) {
                    $instanceStatus .= " <span title='Rejected' class='rejected'></span>"; 
                }
                $postnestedData['status'] = $instanceStatus;
                $postnestedData['approvalStatus'] = $cc->approvalStatus;
                $postnestedData['description'] = $cc->description;
                $postnestedData['comments'] = $cc->comments;
                $postnestedData['inconsistencyTreatment'] = $cc->inconsistencyTreatment;
                $postnestedData['secured'] = $cc->secured;
                $postnestedData['username'] = $cc->name;
                if(($viewOnly != 1 && $cc->status == 0 &&  $cc->approvalStatus != "Pending For Approval" && $cc->approvalStatus != "Approved")) {
                    $actions .= "<button @click.prevent='view(".$cc->id.")' class='resolve-placeholder'> <img title='Resolution' src='/img/settings.png' /></button>"; 
                }
                if(($viewOnly != 1 && $cc->approvalStatus == "Not Sent" && $cc->status != 0)) {
                    $actions .= "<button @click.prevent='submitForApproval(".$cc->id.")' class='sfa-placeholder'><img title='Submit For Approval' src='/img/sendForApproval.png' /></button>"; 
                }
                if(($isApprover == 1 && $cc->approvalStatus == "Pending For Approval")) {
                    $actions .= "<div class='dropdown'>";
                    $actions .= "<span class='three-dots'></span>"; 
                    $actions .= "<div class='dropdown-content'>"; 
                    $actions .= "<ul style='padding: 0px;'><li><span style='width: 100%; display: flex; padding-top: 8px;'>"; 
                    $actions .= "<div v-if='isApprover == 1' class='point'>";
                    $actions .= "<a @click.prevent='mark('Approved',".$cc->id.")' title='Approve' class='approve-placeholder'> Approve</a>";
                    $actions .= "<a @click.prevent='mark('Rejected',".$cc->id.")' title='Reject' class='reject-placeholder'> Reject</a>";
                    $actions .= "</div></span></li></ul></div></div>"; 
                }
                $postnestedData['actions'] = $actions;
                //$postnestedData['actions'] = 'button';
                $data_val[] = $postnestedData;      
                
            }
        }
       
        $totalDataRecord = $post_count;
        $totalFilteredRecord = $totalDataRecord;
        $draw_val = $request->input('draw');
        $get_json_data = array(
        "draw"            => intval($draw_val),
        "recordsTotal"    => intval($totalDataRecord),
        "recordsFiltered" => intval($totalFilteredRecord),
        "data"            => $data_val
        );
         
        echo json_encode($get_json_data);
         
    }

    public function activeList(Request $request) {
        //print_r($request->input());die;
        $totalFilteredRecord = $totalDataRecord = $draw_val = "";
        $columns_list = array(
            0 =>'instanceId',
            1 =>'clcode',
            2 =>'name',
            3=> 'type',
            4=> 'subType',
            5=> 'frequency',
            6=> 'activateDate',
            7=> 'dueDate',
            8=> 'targetValue',
            9=> 'resolution_value',
            10=> 'status',
            11=> 'targetValue',
            12=> 'username',
            13=>'actions',

        );
        $limit_val = $request->input('length');
        $start_val = $request->input('start');
        $order_val = $columns_list[$request->input('order.0.column')];
        $dir_val = $request->input('order.0.dir');
        //$search_text = $request->input('search.value');
        $comp_cov = [];
        $user_object = \Auth::user();
        $current_user = $user_object->id;
        $user_role = $user_object->role->role;
        $organization_id = $user_object->organization_id;
        $viewOnly = 0;
        $isApprover = 0;
        $today = date("Y-m-d");
        $query = DB::table('compliances_covenants_instances as ci');
        $query->join('compliances_covenants as cc', 'ci.covenantId', '=', 'cc.id');
        $query->join('compliances as c', 'ci.complianceId', '=', 'c.id');
        $query->join('clients', 'c.clientReference', '=', 'clients.id');
        $query->leftJoin('users', 'ci.resolver', '=', 'users.id');
        if($user_role == config('global.roles.ADMIN') || $user_role == config('global.roles.AUDITOR') || $user_role == config('global.roles.SUPER_ADMIN')) {
            $viewOnly = 1;
            $key = 'c.organization_id';
            $value = $organization_id;
            $query->where($key,$value);
        }
        else if($user_role == config('global.roles.CSOG_MAKER')) {
            $viewOnly = 1;
            $key = 'c.userId';
            $value = $current_user;
            $query->where($key,$value);
        }
        else if($user_role == config('global.roles.CCU_MAKER')) {
            $key = 'c.organization_id';
            $value = $organization_id;
            $query->where($key,$value);
        }
        else if($user_role == config('global.roles.CCU_CHECKER') || $user_role == config('global.roles.CSOG_CHECKER')) {
            $viewOnly = 1;
            $isApprover = 1;
            $key = 'c.organization_id';
            $value = $organization_id;
            $query->where($key,$value);
        }
        else if($user_role == config('global.roles.SUPER_ADMIN')) {
            $viewOnly = 1;
        }
        $query->where('cc.covenantStatus','Approved');
        $query->where('ci.activateDate','<=',$today);
        //$query->orderByRaw('ci.activateDate ASC');
        foreach($request->input('columns') as $cols) {
            if($cols['search']['value'] != '') {
                if($cols['data'] == 'name') {
                   $query->where('clients.name','LIKE','%'.$cols['search']['value'].'%'); 
                }
                else if($cols['data'] == 'clcode') {
                   $query->where('c.clcode','LIKE','%'.$cols['search']['value'].'%'); 
                }
                else if($cols['data'] == 'type') {
                   $query->where('cc.type','LIKE','%'.$cols['search']['value'].'%'); 
                }
                else if($cols['data'] == 'subType') {
                   $query->where('cc.subType','LIKE','%'.$cols['search']['value'].'%'); 
                }
            }
        }
        if(!empty($request->input('search.value')))
        {
            $query->where('cc.frequency','LIKE','%'.$request->input('search.value').'%');
            //$query->orWhere('ci.activateDate','LIKE','%'.$request->input('search.value').'%');
        }
        $query->orderBy($order_val,$dir_val);
        $post_count = $query->count();
        $query->offset($start_val);
        $query->limit($limit_val);

        $post_data = $query->get(['ci.id','ci.complianceId','ci.covenantId','c.clcode','clients.name as cname','cc.type','cc.subType','cc.frequency',DB::raw('DATE_FORMAT(ci.activateDate, "%d-%m-%Y") as activateDate'),DB::raw('DATE_FORMAT(ci.dueDate, "%d-%m-%Y") as dueDate'),'cc.targetValue','ci.resolution_value','ci.status','cc.description','cc.comments','c.inconsistencyTreatment','c.secured','ci.approvalStatus','users.name']);
        
         
        $data_val = array();
        if(!empty($post_data))
        {
            foreach ($post_data as $cc)
            {   
                $actions = '';
                $instanceStatus= '';
                if(($viewOnly != 1 && ($cc->approvalStatus == "Not Sent" || $cc->approvalStatus == NULL) && $cc->status != 0) || ($isApprover == 1 && $cc->approvalStatus == "Pending For Approval")) {
                    $postnestedData['instanceId'] = '<input type="checkbox" value="'.$cc->id.'" :id="'.$cc->id.'" name="checkApproval[]"/>'; 
                } else {
                    $postnestedData['instanceId'] = '';
                }
                $isins = DB::table('compliances_isin')
                    ->select(DB::raw("GROUP_CONCAT(isin SEPARATOR ', ') as isin"))
                    ->groupBy('complianceId')
                    ->where('complianceId',$cc->complianceId)
                    ->get();

                if(count($isins) > 0)
                    $postnestedData['isins'] = $isins[0]->isin;

                $postnestedData['id'] = $cc->id;   
                $postnestedData['clcode'] = $cc->clcode;
                $postnestedData['name'] = $cc->cname;
                $postnestedData['type'] = $cc->type; 
                $postnestedData['subType'] = $cc->subType;
                $postnestedData['frequency'] = $cc->frequency;
                $postnestedData['activateDate'] = $cc->activateDate;
                $postnestedData['dueDate'] = $cc->dueDate;
                $postnestedData['targetValue'] = $cc->targetValue;
                $postnestedData['resolution_value'] = $cc->resolution_value;
                $instanceStatus .= $this->get_instance_status($cc->status);
                if(($cc->approvalStatus == "Not Sent")) {
                    $instanceStatus .= " <span title='Not sent for approval' class='notsent'></span>"; 
                }
                else if(($cc->approvalStatus == "Pending For Approval")) {
                    $instanceStatus .= " <span title='Pending for approval' class='pendingApproval'></span>"; 
                }
                else if(($cc->approvalStatus == "Approved")) {
                    $instanceStatus .= " <span title='Approved' class='approved'></span>"; 
                }
                else if(($cc->approvalStatus == "Rejected")) {
                    $instanceStatus .= " <span title='Rejected' class='rejected'></span>"; 
                }
                $postnestedData['status'] = $instanceStatus;
                $postnestedData['approvalStatus'] = $cc->approvalStatus;
                $postnestedData['description'] = $cc->description;
                $postnestedData['comments'] = $cc->comments;
                $postnestedData['inconsistencyTreatment'] = $cc->inconsistencyTreatment;
                $postnestedData['secured'] = $cc->secured;
                $postnestedData['username'] = $cc->name;
                if(($viewOnly != 1 && $cc->status == 0 &&  $cc->approvalStatus != "Pending For Approval" && $cc->approvalStatus != "Approved")) {
                    $actions .= "<button @click.prevent='view(".$cc->id.")' class='resolve-placeholder'> <img title='Resolution' src='/img/settings.png' /></button>"; 
                }
                if(($viewOnly != 1 && $cc->approvalStatus == "Not Sent" && $cc->status != 0)) {
                    $actions .= "<button @click.prevent='submitForApproval(".$cc->id.")' class='sfa-placeholder'><img title='Submit For Approval' src='/img/sendForApproval.png' /></button>"; 
                }
                if(($isApprover == 1 && $cc->approvalStatus == "Pending For Approval")) {
                    $actions .= "<div class='dropdown'>";
                    $actions .= "<span class='three-dots'></span>"; 
                    $actions .= "<div class='dropdown-content'>"; 
                    $actions .= "<ul style='padding: 0px;'><li><span style='width: 100%; display: flex; padding-top: 8px;'>"; 
                    $actions .= "<div v-if='isApprover == 1' class='point'>";
                    $actions .= "<a @click.prevent='mark('Approved',".$cc->id.")' title='Approve' class='approve-placeholder'> Approve</a>";
                    $actions .= "<a @click.prevent='mark('Rejected',".$cc->id.")' title='Reject' class='reject-placeholder'> Reject</a>";
                    $actions .= "</div></span></li></ul></div></div>"; 
                }
                $postnestedData['actions'] = $actions;
                //$postnestedData['actions'] = 'button';
                $data_val[] = $postnestedData;      
                
            }
        }
       
        $totalDataRecord = $post_count;
        $totalFilteredRecord = $totalDataRecord;
        $draw_val = $request->input('draw');
        $get_json_data = array(
        "draw"            => intval($draw_val),
        "recordsTotal"    => intval($totalDataRecord),
        "recordsFiltered" => intval($totalFilteredRecord),
        "data"            => $data_val
        );
         
        echo json_encode($get_json_data);
         
    }

    public function summary(Request $request) {
        $comp_cov = [];
        $viewOnly = 0;
        $isApprover = 0;
        $user_object = \Auth::user();
        $current_user = $user_object->id;
        $user_role = $user_object->role->role;
        if($user_role == config('global.roles.ADMIN') || $user_role == config('global.roles.AUDITOR') || $user_role == config('global.roles.SUPER_ADMIN') || $user_role == config('global.roles.CSOG_MAKER')) {
            $viewOnly = 1;
        }
        else if($user_role == config('global.roles.CCU_CHECKER') || $user_role == config('global.roles.CSOG_CHECKER')) {
            $viewOnly = 1;
            $isApprover = 1;
        }
        else if($user_role == config('global.roles.SUPER_ADMIN')) {
            $viewOnly = 1;
        }
       
        return inertia('Summary',[
            'loadPage' => 0,
            'viewOnly' => $viewOnly,
            'isApprover' => $isApprover
        ]);
        die;
    }

    public function get_instance_status($status) {
        if($status == 0)
            return 'Not Started';
        else if($status == 1)
            return 'Started';
        else if($status == 2)
            return 'Passed';
        else if($status == 4)
            return 'Pending';
        else if($status == 5)
            return 'Approved';
        else if($status == 6)
            return 'Rejected';
        else if($status == 7)
            return 'Pending For Approval';        
        else
            return 'Failed';
    }

    public function resolution(Request $request) {
        $id = $request->input('id');
        try {
                $tracking_data = DB::select("
            SELECT
                ci.id, ci.covenantId, ci.status, ci.activateDate as trackingDate, ci.is_child, ci.is_fail, ci.reminderBefore, ci.reminderInterval, ci.dueDate,
                compliances_covenants.complianceId, compliances_covenants.type, compliances_covenants.subType,
                compliances.clcode, compliances.docName, compliances.startDate, compliances.endDate 
            FROM
                compliances_covenants_instances as ci 
                LEFT JOIN compliances_covenants ON ci.covenantId = compliances_covenants.id
                LEFT JOIN compliances ON compliances_covenants.complianceId = compliances.id 
                WHERE ci.id = ".$id."
            ");
                
            $comp_cov = [];

            foreach ($tracking_data as $key=>$cc) {

                $comp_cov['instanceId'] = $cc->id;
                $comp_cov['complianceId'] = $cc->complianceId;
                $comp_cov['covenantId'] = $cc->covenantId;
                $comp_cov['status'] = $cc->status;
                $comp_cov['trackingDate'] = $cc->trackingDate;
                $comp_cov['is_child'] = $cc->is_child;
                $comp_cov['is_fail'] = $cc->is_fail;
                $comp_cov['reminderBefore'] = $cc->reminderBefore;
                $comp_cov['reminderInterval'] = $cc->reminderInterval;
                $comp_cov['dueDate'] = $cc->dueDate;
                $comp_cov['type'] = $cc->type;
                $comp_cov['subType'] = $cc->subType;
                $comp_cov['clcode'] = $cc->clcode;
                $comp_cov['docName'] = $cc->docName;
            }
            //$instance = json_encode($comp_cov);
            $instance = $comp_cov;
            $result['status'] = 'success';

        } catch (\Exception $e) {
            $instance =  response()->json(['message'=>'Instance not found!'], 404);
        }


        $result['instance'] = $instance;

        return json_encode($result);die;
    }

    public function approve(Request $request) {
        //print_r($request->all());die;
        $selectedData = $request->input('selectedData');
        $comment = $request->input('comment');
        $status = $request->input('status');
        $success = false;
        if($selectedData) {
            $instance = ComplianceCovenant::whereIn('id',$selectedData);
            $update = $instance->update([
                'covenantStatus'=> $status,
                'checkerComment' => $comment
            ]);
            if($update) {
                $success = true;
            }

            $query = DB::table('compliances');
            $query->join('clients', 'compliances.clientReference', '=', 'clients.id');
            $query->join('users', 'compliances.userId', '=', 'users.id');
            $query->join('compliances_covenants', 'compliances.id', '=', 'compliances_covenants.complianceId');
            $query->whereIn('compliances_covenants.id',$selectedData);
            $rows = $query->get(['clients.name','compliances.id','users.email','compliances.clcode','compliances_covenants.type','compliances_covenants.subType','compliances_covenants.description']);
            $trs = '';
            $srno = 1;
            foreach ($rows as $key => $row) {
                $trs .= '<tr><td style="border: 1px solid black;">'.$srno.'</td><td style="border: 1px solid black;">'.$row->type.' - '.$row->subType.'</td><td style="border: 1px solid black;">'.$row->description.'</td></tr>';
                $srno ++;
            }
            $body = '<div class="container" style="background-color: #f7f9fb; padding: 80px;">';
            $body .= '<img style="display:block;width:100%;height:100%; padding-bottom: 80px;" src="img/notification-logo.png" />';
            $body .= '<div style="font-size: 24px; font-weight: 700; color: #333;">The details for following covenants have been '.$status.' in the system.</div><br>';
            $body .= '<table style="width: 100%; padding:20px; font-size: 16px; font-weight: 400; color: #333;"><tr><th style="border: 1px solid black; text-align:left;">Sr. No.</th><th style="border: 1px solid black;text-align:left;">Heading</th><th style="border: 1px solid black;text-align:left;">Description</th></tr>';
            $body .= $trs;
            $body .= '</table>';
            $body .= '<div style="font-size: 16px; color: #333; padding-top:20px;">Thanks,</div>';
            $body .= '<div style="font-size: 16px; color: #333;">Axis Trustee Services Ltd.</div><br>';
            $body .= '</div>';
            
            $mailContentIn = '<html><p>' . $body . '</p></html>';
            $sentStatusIn = $this->sendEmail('Covenant Entries '.$status.'_'.$rows[0]->name.'_'.$rows[0]->clcode, $mailContentIn, $rows[0]->email);
        }
        $result['success'] = $success;
        return json_encode($result);die;
    }

    public function approveActive(Request $request) {
        //print_r($request->all());die;
        $selectedData = $request->input('selectedData');
        $comment = $request->input('comment');
        $status = $request->input('status');
        $success = false;
        if($selectedData) {
            $instance = ComplianceInstance::whereIn('id',$selectedData);
            $update = $instance->update([
                'approvalStatus'=> $status,
                'approval_comment' => $comment
            ]);
            if($update) {
                $success = true;
            }

            /*$query = DB::table('compliances');
            $query->join('clients', 'compliances.clientReference', '=', 'clients.id');
            $query->join('users', 'compliances.userId', '=', 'users.id');
            $query->join('compliances_covenants', 'compliances.id', '=', 'compliances_covenants.complianceId');
            $query->whereIn('compliances_covenants.id',$selectedData);
            $rows = $query->get(['clients.name','compliances.id','users.email','compliances.clcode','compliances_covenants.type','compliances_covenants.subType','compliances_covenants.description']);
            $trs = '';
            $srno = 1;
            foreach ($rows as $key => $row) {
                $trs .= '<tr><td style="border: 1px solid black;">'.$srno.'</td><td style="border: 1px solid black;">'.$row->type.' - '.$row->subType.'</td><td style="border: 1px solid black;">'.$row->description.'</td></tr>';
                $srno ++;
            }
            $body = '<div class="container" style="background-color: #f7f9fb; padding: 80px;">';
            $body .= '<img style="display:block;width:100%;height:100%; padding-bottom: 80px;" src="img/notification-logo.png" />';
            $body .= '<div style="font-size: 24px; font-weight: 700; color: #333;">The details for following covenants have been '.$status.' in the system.</div><br>';
            $body .= '<table style="width: 100%; padding:20px; font-size: 16px; font-weight: 400; color: #333;"><tr><th style="border: 1px solid black; text-align:left;">Sr. No.</th><th style="border: 1px solid black;text-align:left;">Heading</th><th style="border: 1px solid black;text-align:left;">Description</th></tr>';
            $body .= $trs;
            $body .= '</table>';
            $body .= '<div style="font-size: 16px; color: #333; padding-top:20px;">Thanks,</div>';
            $body .= '<div style="font-size: 16px; color: #333;">Axis Trustee Services Ltd.</div><br>';
            $body .= '</div>';
            
            $mailContentIn = '<html><p>' . $body . '</p></html>';
            $sentStatusIn = $this->sendEmail('Covenant Entries '.$status.'_'.$rows[0]->name.'_'.$rows[0]->clcode, $mailContentIn, $rows[0]->email);*/
        }
        $result['success'] = $success;
        return json_encode($result);die;
    }

    public function submitForApproval(Request $request) {
        $success = false;
        if($request->id && !empty($request->id)) {
            $instance = ComplianceCovenant::whereIn('id',$request->id);
            $update = $instance->update([
                'covenantStatus'=> 'Pending For Approval'
            ]);
            $cmpid = $instance->get([
                'complianceId'
            ]);
            $comp_update = DB::table('compliances')
                    ->where('id',$cmpid[0]->complianceId)
                    ->update(['complianceStatus'=> 'In Progress']);
            
            if($update) {
                $success = true;
            }
            $user_object = \Auth::user();
            $current_user = $user_object->id;
            $user_role = $user_object->role->role;
            $organization_id = $user_object->organization_id;
            $checker_emails = [];
            if($user_role == config('global.roles.CSOG_MAKER')) {
                $role_id = DB::table('roles')->where('role', config('global.roles.CSOG_CHECKER'))->first();
                $userquery = DB::table('users');
                $userquery->where('role_id',$role_id->id);
                $userquery->where('organization_id',$organization_id);
                $checkers = $userquery->get(['email']);
            }
            else if($user_role == config('global.roles.CCU_MAKER')) {
                $role_id = DB::table('roles')->where('role', config('global.roles.CCU_CHECKER'))->first();
                $userquery = DB::table('users');
                $userquery->where('role_id',$role_id->id);
                $userquery->where('organization_id',$organization_id);
                $checkers = $userquery->get(['email']);
            }
            if(isset($checkers)) {
                foreach ($checkers as $key => $email) {
                    $checker_emails[] = $email->email;
                }
                $query = DB::table('compliances');
                $query->join('clients', 'compliances.clientReference', '=', 'clients.id');
                $query->join('compliances_covenants', 'compliances.id', '=', 'compliances_covenants.complianceId');
                $query->whereIn('compliances_covenants.id',$request->id);
                $rows = $query->get(['clients.name','compliances.id','compliances.clcode','compliances_covenants.type','compliances_covenants.subType','compliances_covenants.description']);
                $trs = '';
                $srno = 1;
                foreach ($rows as $key => $row) {
                    $trs .= '<tr><td style="border: 1px solid black;">'.$srno.'</td><td style="border: 1px solid black;">'.$row->type.' - '.$row->subType.'</td><td style="border: 1px solid black;">'.$row->description.'</td></tr>';
                    $srno ++;
                }
                $body = '<div class="container" style="background-color: #f7f9fb; padding: 80px;">';
                $body .= '<img style="display:block;width:100%;height:100%; padding-bottom: 80px;" src="img/notification-logo.png" />';
                $body .= '<div style="font-size: 24px; font-weight: 700; color: #333;">The details for following covenants have been entered in the system for your approval. You are requested to review and approve.</div><br>';
                $body .= '<table style="width: 100%; padding:20px; font-size: 16px; font-weight: 400; color: #333;"><tr><th style="border: 1px solid black; text-align:left;">Sr. No.</th><th style="border: 1px solid black;text-align:left;">Heading</th><th style="border: 1px solid black;text-align:left;">Description</th></tr>';
                $body .= $trs;
                $body .= '</table>';
                $body .= '<div style="font-size: 16px; color: #333; padding-top:20px;">Thanks,</div>';
                $body .= '<div style="font-size: 16px; color: #333;">Axis Trustee Services Ltd.</div><br>';
                $body .= '</div>';
                
                $mailContentIn = '<html><p>' . $body . '</p></html>';
                $sentStatusIn = $this->sendEmail('Covenant Entries for Approval_'.$rows[0]->name.'_'.$rows[0]->clcode, $mailContentIn, $checker_emails);
            }

        }
        $result['success'] = $success;
        echo json_encode($result);die;
    }

    public function submitForApprovalActive(Request $request) {
        $success = false;
        $result = [];
        if($request->id && !empty($request->id)) {
            $instance = ComplianceInstance::whereIn('id',$request->id);
            $update = $instance->update([
                'approvalStatus'=> 'Pending For Approval'
            ]);
            if($update) {
                $success = true;
            }
            
        }

        
        $result['success'] = $success;
        echo json_encode($result);die;
    }

    public function sendEmail($subject,$mailContent, $toEmail,$ccemail='')
    {        
        try {
            $mailsent = Mail::send(array(), array(), function ($message) use ($mailContent, $toEmail, $subject) {
              $message->to($toEmail)
                ->subject($subject)
                ->html($mailContent); // for HTML rich messages
            });
            return true;
        } catch (Exception $e) {

            return false;
        }
    }
}