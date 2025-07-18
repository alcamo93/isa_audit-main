<?php

namespace App\Http\Controllers\Dashboard;

use App\Http\Controllers\Controller;
use App\Models\News\NewsModel;
use App\Providers\RouteServiceProvider;
use Illuminate\Foundation\Auth\AuthenticatesUsers;
use Illuminate\Support\Facades\Auth;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Session;
use App\Models\Admin\CustomersModel;
use App\Models\Admin\ProcessesModel;
use App\Models\Audit\ActionRegistersModel;
use App\Models\Audit\AplicabilityRegistersModel;
use App\Models\Audit\AuditRegistersModel;
use App\Models\Audit\AuditMattersModel;
use App\Models\Audit\ObligationsModel;
use App\Models\Audit\ActionPlansModel;
use App\Models\Audit\AuditModel;
use App\Models\Audit\AuditAspectsModel;
use App\Models\Catalogues\BasisModel;
use App\Models\Catalogues\AspectsModel;
use App\Models\Risk\RiskTotalsModel;
use App\Models\Risk\RiskCategoriesModel;
use App\Models\Risk\RiskInterpretationsModel;
use App\Classes\StatusConstants;
use App\Classes\ProfilesConstants;
use App\Http\Controllers\Audit\AuditController;
use App\Models\Admin\CorporatesModel;
use App\User;
use App\Models\Catalogues\StatusModel;

class DashboardController extends Controller
{
    /**
     * Where to redirect users after login.
     *
     * @var string
     */
    protected $redirectTo = '/home';
    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware('auth');
    }

    public function index(){
        $customer = Session::get('customer')['id_customer'];
        $month_start = strtotime('first day of this month', time());
        $month_end = strtotime('last day of this month', time());
        $date['init'] = date('Y-m-d', $month_start);
        $date['finish'] = date('Y-m-d', $month_end);
        $today = date("Y-m-d"). ' 00:00:00';
        $data['dataObligations'] = ObligationsModel::with('users')
            ->whereHas('users', function ($filter) {
                $filter->where('id_user', Session::get('user')['id_user']);
            })->get()->toArray();
        $data['dataLegal'] = BasisModel::getUpdatesBasis($date);
        
        switch (Session::get('profile')['id_profile_type']) {
            case ProfilesConstants::ADMIN_GLOBAL: 
            case ProfilesConstants::ADMIN_OPERATIVE:
                $data['customers'] = CustomersModel::GetCustomers(); 
                $infoAudits = AplicabilityRegistersModel::where('id_status', StatusConstants::FINISHED)->count();
                $evaluateCalc = true;
                $idCustomer = null;
                $idCorporate = null;
                $idUser = null;
                $view = ($infoAudits > 0) ? 'dashboard.owner.owner_view' : 'errors.Contenido';
                $data['dataNew'] = NewsModel::whereRaw('("'.$today.'" >= start_date OR "'.$today.'" <= clear_date)')
                    ->get()->sortByDesc("id_new");
                break;
            case ProfilesConstants::CORPORATE:
                $corporates = CorporatesModel::where('id_customer', Session::get('customer')['id_customer'])->pluck('id_corporate')->toArray();
                $infoAudits = AplicabilityRegistersModel::where('id_status', StatusConstants::FINISHED)
                    ->whereIn('id_corporate', $corporates)
                    ->count();
                $evaluateCalc = true;
                $idCustomer = Session::get('customer')['id_customer'];
                $idCorporate = null;
                $idUser = null;
                $view = ($infoAudits > 0) ? 'dashboard.corporate.corporate_view' : 'errors.Contenido';
                $data['dataNew'] = NewsModel::where('id_customer', $idCustomer)
                    ->whereRaw('("'.$today.'" >= start_date OR "'.$today.'" <= clear_date)')
                    ->get()->sortByDesc("id_new");
                break;
            case ProfilesConstants::COORDINATOR:
                $infoAudits = AplicabilityRegistersModel::where('id_status', StatusConstants::FINISHED)
                    ->where('id_corporate', Session::get('corporate')['id_corporate'])
                    ->count();
                $evaluateCalc = false;
                $idCustomer = Session::get('customer')['id_customer'];
                $idCorporate = Session::get('corporate')['id_corporate'];
                $idUser = null;
                $view = ($infoAudits > 0) ? 'dashboard.cordinator.cordinator_view' : 'errors.Contenido';
                $data['dataNew'] = NewsModel::where([
                    ['id_customer', $idCustomer],
                    ['id_corporate', $idCorporate]
                ])
                ->whereRaw('("'.$today.'" >= start_date OR "'.$today.'" <= clear_date)')
                ->get()->sortByDesc("id_new");
                break;
            case ProfilesConstants::OPERATIVE:
                $infoAudits = AplicabilityRegistersModel::where('id_status', StatusConstants::FINISHED)
                    ->where('id_corporate', Session::get('corporate')['id_corporate'])
                    ->count();
                $evaluateCalc = false;
                $idCustomer = Session::get('customer')['id_customer'];
                $idCorporate = Session::get('corporate')['id_corporate'];
                $idUser = Session::get('user')['id_user'];;
                $view = 'dashboard.operative.operative_view';
                $data['dataNew'] = NewsModel::where([
                    ['id_customer', $idCustomer],
                    ['id_corporate', $idCorporate]
                ])
                ->whereRaw('("'.$today.'" >= start_date OR "'.$today.'" <= clear_date)')
                ->get()->sortByDesc("id_new");
                break;
        }
        $data['corporateProcess'] = ProcessesModel::GetProcessByCustomerDashboard($idCustomer, $idCorporate, $idUser);        
        foreach($data['corporateProcess'] as $key => $ar ) {
            $data['corporateProcess'][$key]['total'] = $this->calculateTotalAPGlobal($ar);
        }
        return view($view, $data);
    }
    /**
     * get customer corporations
     */
    public function getCorporates(Request $request) {
        $idCustomer = $request->input('idCustomer');
        $data['corporateProcess'] = ProcessesModel::GetProcessByCustomerDashboard($idCustomer);
        foreach($data['corporateProcess'] as $key => $ar ) {
            $data['corporateProcess'][$key]['total'] = $this->calculateTotalAPGlobal($ar);
        }
        if( sizeof($data['corporateProcess']) != 0 ) {
            return view('dashboard.section_cards', $data);
        }
        else {
            return view('dashboard.no_audit_card');
        }
    }
    /**
     * get contract info
     */
    public function getContractInfo(Request $request) {
        $idAuditProcess = $request->input('idAuditProcess');
        $idActionRegister = $request->input('idActionRegister');
        /**
         * Global data
         */
        $dataProcess = ProcessesModel::find($idAuditProcess)->toArray();
        $dataAudit = AuditRegistersModel::where('id_audit_processes', $idAuditProcess)->first()->toArray();
        $data['global']['auditProcess'] = $dataProcess;
        $data['global']['auditRegister'] = $dataAudit;
        $data['global']['total'] = $dataAudit['total'];
        /**
         * Count requirements in audit
         */
        $whereInAspect = ActionPlansModel::select('id_aspect')
            ->where('id_action_register', $idActionRegister)
            ->distinct()->get()->pluck('id_aspect');
        $data['findings']['aspects'] = AspectsModel::select('id_aspect', 'aspect')
            ->whereIn('id_aspect', $whereInAspect)->get();
        $requirementsEvaluating = [];
        foreach ($data['findings']['aspects'] as $i => $e) {
            $auditAspect = AuditAspectsModel::where([
                    ['id_audit_processes', $idAuditProcess], 
                    ['id_aspect', $e['id_aspect']]
                ])->first()->toArray();
            $data['findings']['aspects'][$i]['total'] = isset($auditAspect['total']) ? $auditAspect['total'] : 0;
            $actions = ActionPlansModel::GetAspectsForDashboard($idAuditProcess, $e['id_aspect']);
            $tmpCritical = [];
            $tmpOperative = [];
            foreach ($actions as $k => $r) {
                $temp = [];
                $temp['id_action_plan'] = $r['id_action_plan'];
                $temp['num'] = $r['no_requirement'];
                $temp['description'] = $r['requirement'];
                $temp['status'] = $r['status'];
                $temp['total_tasks'] = $r['total_tasks'];
                $temp['done_tasks'] = $r['done_tasks'];
                $temp['id_status'] = $r['id_status'];
                $temp['id_condition'] = $r['id_condition'];
                $temp['id_aspect'] = $r['id_aspect'];
                $temp['close_date'] = $r['close_date'];
                $temp['real_close_date'] = $r['real_close_date'];
                $temp['risk'] = RiskTotalsModel::GetData($r['id_audit_processes'], $r['id_requirement'], null);
                foreach ($temp['risk'] as $x => $y) {
                    $temp['risk'][$x]['interpretation'] = AuditController::textRiskLevel($y['id_risk_category'], $y['total']);
                }
                if ($r['has_subrequirement'] == 0) {
                    if ($r['id_condition'] == StatusConstants::CRITICAL) {
                        array_push($tmpCritical, $temp);
                        array_push($requirementsEvaluating, $temp);
                    }
                    if ($r['id_condition'] == StatusConstants::OPERATIVE) {
                        array_push($tmpOperative, $temp);
                        array_push($requirementsEvaluating, $temp);
                    }
                }
                else {
                    $sub = ActionPlansModel::GetSubrequirementsByIdAuditProcess($r['id_audit_processes'], $r['id_requirement']);
                    foreach ($sub as $s) {
                        $tmp = [];
                        $tmp['id_action_plan'] = $s['id_action_plan'];
                        $tmp['num'] = $r['no_requirement'].' Subrequerimiento '.$s['no_subrequirement'];
                        $tmp['description'] = $s['subrequirement'];
                        $tmp['status'] = $s['status'];
                        $tmp['total_tasks'] = $s['total_tasks'];
                        $tmp['done_tasks'] = $s['done_tasks'];
                        $tmp['id_status'] = $s['id_status'];
                        $tmp['id_condition'] = $s['id_condition'];
                        $tmp['id_aspect'] = $s['id_aspect'];
                        $tmp['close_date'] = $s['close_date'];
                        $tmp['real_close_date'] = $s['real_close_date'];
                        $tmp['risk'] = RiskTotalsModel::GetData($r['id_audit_processes'], $s['id_requirement'], $s['id_subrequirement']);
                        foreach ($tmp['risk'] as $w => $z) {
                            $tmp['risk'][$w]['interpretation'] = AuditController::textRiskLevel($z['id_risk_category'], $z['total']);
                        }
                        if ($s['id_condition'] == StatusConstants::CRITICAL) {
                            array_push($tmpCritical, $tmp);
                            array_push($requirementsEvaluating, $tmp);
                        }
                        if ($s['id_condition'] == StatusConstants::OPERATIVE) {
                            array_push($tmpOperative, $tmp);
                            array_push($requirementsEvaluating, $tmp);
                        }
                    }
                }
            }
            $data['findings']['aspects'][$i]['critical'] = $tmpCritical;
            $data['findings']['aspects'][$i]['operative'] = $tmpOperative;
        }
        $requirementsEvaluatingPool = collect($requirementsEvaluating);
        $total = $requirementsEvaluatingPool->count();
        $critical = $requirementsEvaluatingPool->where('id_condition', StatusConstants::CRITICAL)->count();
        $operative = $requirementsEvaluatingPool->where('id_condition', StatusConstants::OPERATIVE)->count();
        $criticalCal = ($critical != 0) ? (($critical * 100) / $total) : 0;
        $operativeCalc = ($operative * 100) ? (($operative * 100) / $total) : 0;
        $data['findings']['chart']['labels'] = ['Critica', 'Operativo'];
        $data['findings']['chart']['colors'] = ['#00b0f0', '#0909fb'];
        $data['findings']['chart']['values'] = [round($criticalCal, 1, PHP_ROUND_HALF_UP), round($operativeCalc, 1, PHP_ROUND_HALF_UP)];
        $data['findings']['total'] = $total;
        /**
         * Matters and aspects
         */
        $totalMattersGlobal = 0;
        $data['matters'] = AuditMattersModel::GetMattersByStatusAuditProcess($idAuditProcess, StatusConstants::FINISHED_AUDIT);
        foreach ($data['matters'] as $i => $m) {
            $data['matters'][$i]['aspects'] = AuditAspectsModel::GetAuditedAspectsByMatter($m['id_audit_matter'], StatusConstants::FINISHED_AUDIT);
            $tempMatterTotal = 0;
            $countAspects = 0;
            foreach ($data['matters'][$i]['aspects'] as $j => $a) {
                $actions = $requirementsEvaluatingPool->where('id_aspect', $a['id_aspect'])->values();
                $tempAspectTotal = $actions->count();
                $tempAspectDone = $actions->where('id_status', ActionPlansModel::COMPLETED_AP)->count();
                $totalAspectAP = $this->calculateTotalAP($a['total'], $tempAspectTotal, $tempAspectDone);
                $data['matters'][$i]['aspects'][$j]['total_action'] = $totalAspectAP;
                $data['matters'][$i]['aspects'][$j]['action'] = $actions->toArray();
                $tempMatterTotal += $totalAspectAP;
                $countAspects ++;
            }
            $totalMattersAP = $tempMatterTotal / $countAspects;
            $data['matters'][$i]['total_action'] = $totalMattersAP;
            $totalMattersGlobal += $totalMattersAP;
        }
        $setTotalMattersGlobal = ($totalMattersGlobal != 0) ? ( $totalMattersGlobal / sizeof($data['matters']) ) : 0;
        $data['global']['total_actions'] = $setTotalMattersGlobal;
        /**
         * Critical finds
         */
        $data['other_charts'] = []; 
        $criticalL = [];
        $criticalV = [];
        $criticalC = [];
        foreach ($data['matters'] as $i => $m) {
            foreach ($data['matters'][$i]['aspects'] as $j => $a) {
                // critical finds
                $aspectData =  AspectsModel::findOrFail($a['id_aspect']);
                $aCriticalCount = $requirementsEvaluatingPool->where('id_condition', StatusConstants::CRITICAL)
                    ->where('id_aspect', $a['id_aspect'])->count();
                $color = '#'.str_pad(dechex(rand(0x000000, 0xFFFFFF)), 6, 0, STR_PAD_LEFT);
                array_push($criticalL, $aspectData->aspect);
                array_push($criticalV, $aCriticalCount);
                array_push($criticalC, $color);
            }
        }
        $data['other_charts']['critical_find']['labels'] = $criticalL;
        $data['other_charts']['critical_find']['values'] = $criticalV;
        $data['other_charts']['critical_find']['colors'] = $criticalC;
        $data['other_charts']['critical_find']['max'] = max($criticalV)+1;
        /**
         * Relevant permissions by matters
         */
        $data['other_charts']['relevant_permissions'] = [];
        foreach ($data['matters'] as $i => $m) {
            $tmpBars['idMatter'] = $m['id_matter'];
            $tmpBars['label'] = $m['matter'];
            $tmpBars['labels'] = [];
            $tmpBars['values'] = [];
            $tmpBars['colors'] = [];
            // Count relevants
            foreach ($data['matters'][$i]['aspects'] as $j => $a) {
                $color = '#'.str_pad(dechex(rand(0x000000, 0xFFFFFF)), 6, 0, STR_PAD_LEFT);
                $count = $requirementsEvaluatingPool->where('id_aspect', $a['id_aspect'])->count();
                array_push($tmpBars['labels'], $a['aspect']);
                array_push($tmpBars['values'], $count);
                array_push($tmpBars['colors'], $color);
                if ( $j === array_key_last($data['matters'][$i]['aspects']) ) {
                    $tmpBars['max'] = max($tmpBars['values'])+1;
                }
            }
            array_push($data['other_charts']['relevant_permissions'], $tmpBars);
        }
        /**
         * Count No Compliance
         */
        $noCompliceL = [];
        $noCompliceV = [];
        $noCompliceC = [];

        $status = StatusModel::where('group', 7)->get();
        $aspectLabelRes = $status->where('id_status', ActionPlansModel::UNASSIGNED_AP)->first()->status;
        $aspectCountRes = $requirementsEvaluatingPool->where('id_status', ActionPlansModel::UNASSIGNED_AP)->count();
        $colorRes = '#'.str_pad(dechex(rand(0x000000, 0xFFFFFF)), 6, 0, STR_PAD_LEFT);
        $aspectLabelProgress = $status->where('id_status', ActionPlansModel::PROGRESS_AP)->first()->status;
        $aspectCountProgress = $requirementsEvaluatingPool->where('id_status', ActionPlansModel::PROGRESS_AP)->count();
        $colorProgress = '#'.str_pad(dechex(rand(0x000000, 0xFFFFFF)), 6, 0, STR_PAD_LEFT);
        $aspectLabelComplete = $status->where('id_status', ActionPlansModel::COMPLETED_AP)->first()->status;
        $aspectCountComplete = $requirementsEvaluatingPool->where('id_status', ActionPlansModel::COMPLETED_AP)->count();
        $colorComplete = '#'.str_pad(dechex(rand(0x000000, 0xFFFFFF)), 6, 0, STR_PAD_LEFT);
        $aspectLabelReview = $status->where('id_status', ActionPlansModel::REVIEW_AP)->first()->status;
        $aspectCountReview = $requirementsEvaluatingPool->where('id_status', ActionPlansModel::REVIEW_AP)->count();
        $colorReview = '#'.str_pad(dechex(rand(0x000000, 0xFFFFFF)), 6, 0, STR_PAD_LEFT);
        $aspectLabelExpired = $status->where('id_status', ActionPlansModel::EXPIRED_AP)->first()->status;
        $aspectCountExpired = $requirementsEvaluatingPool->where('id_status', ActionPlansModel::EXPIRED_AP)->count();
        $colorExpired = '#'.str_pad(dechex(rand(0x000000, 0xFFFFFF)), 6, 0, STR_PAD_LEFT);
        $aspectLabelClosed = $status->where('id_status', ActionPlansModel::CLOSED_AP)->first()->status;
        $aspectCountClosed = $requirementsEvaluatingPool->where('id_status', ActionPlansModel::CLOSED_AP)->count();
        $colorClosed = '#'.str_pad(dechex(rand(0x000000, 0xFFFFFF)), 6, 0, STR_PAD_LEFT);

        array_push($noCompliceL, $aspectLabelRes, $aspectLabelProgress, $aspectLabelComplete, $aspectLabelReview, $aspectLabelExpired, $aspectLabelClosed);
        array_push($noCompliceC, $colorRes, $colorProgress, $colorComplete, $colorReview, $colorExpired, $colorClosed);
        array_push($noCompliceV, $aspectCountRes, $aspectCountProgress, $aspectCountComplete, $aspectCountReview, $aspectCountExpired, $aspectCountClosed);

        $data['other_charts']['noCompliance']['labels'] = $noCompliceL;
        $data['other_charts']['noCompliance']['values'] = $noCompliceV;
        $data['other_charts']['noCompliance']['colors'] = $noCompliceC;
        $data['other_charts']['noCompliance']['max'] = max($noCompliceV)+1;
        /**
         * Risk level graph Radar
         */
        $totals = RiskTotalsModel::where('id_audit_processes',$idAuditProcess)->get()->toArray();
        $totalsCategories = RiskTotalsModel::select('id_risk_category')->where('id_audit_processes',$idAuditProcess)->distinct()->get()->toArray();
        // Set interpretation
        foreach ($totals as $t => $v) {
            $totals[$t]['interpretation'] = AuditController::textRiskLevel($v['id_risk_category'], $v['total']);
        }
        // Set structure
        $labels = [StatusConstants::LOW, StatusConstants::MEDIUM, StatusConstants::HIGH];
        $data['other_charts']['risk_radar_audit']['labels'] = $labels;
        $data['other_charts']['risk_radar_audit']['data'] = [];
        foreach ($totalsCategories as $k => $tc) {
            $temp = [];
            $temp['label'] = RiskCategoriesModel::findOrFail($tc['id_risk_category'])->risk_category;
            $temp['backgroundColor'] = DashboardController::rgbaString($k);
            $temp['data'][0] = 0;
            $temp['data'][1] = 0;
            $temp['data'][2] = 0;
            foreach ($totals as $t => $v) {
                if ($tc['id_risk_category'] == $v['id_risk_category']) {
                    if ($v['interpretation'] == StatusConstants::LOW) {
                        $temp['data'][0] ++;
                    }
                    if ($v['interpretation'] == StatusConstants::MEDIUM) {
                        $temp['data'][1] ++;
                    }
                    if ($v['interpretation'] == StatusConstants::HIGH) {
                        $temp['data'][2] ++;
                    }
                }
            }
            array_push($data['other_charts']['risk_radar_audit']['data'], $temp);
        }
        /**
         * Risk level in matters graph bar
         */
        $totals = RiskTotalsModel::where('id_audit_processes',$idAuditProcess)->get()->toArray();
        $totalsCategories = RiskTotalsModel::select('id_risk_category')->where('id_audit_processes',$idAuditProcess)->distinct()->get()->toArray();
        // Set interpretation
        foreach ($totals as $t => $v) {
            $totals[$t]['interpretation'] = AuditController::textRiskLevel($v['id_risk_category'], $v['total']);
        }
        // Set structure
        $labels = [StatusConstants::LOW, StatusConstants::MEDIUM, StatusConstants::HIGH];
        $colors = ['#3e95cd', '#8e5ea2','#3cba9f'];
        $data['other_charts']['risk_bars_audit'] = [];
        foreach ($totalsCategories as $k => $tc) {
            $temp = [];
            $tempLow = 0;
            $tempMedium = 0;
            $tempHigh = 0;
            foreach ($totals as $t => $v) {
                if ($tc['id_risk_category'] == $v['id_risk_category']) {
                    if ($v['interpretation'] == StatusConstants::LOW) {
                        $tempLow ++;
                    }
                    if ($v['interpretation'] == StatusConstants::MEDIUM) {
                        $tempMedium ++;
                    }
                    if ($v['interpretation'] == StatusConstants::HIGH) {
                        $tempHigh ++;
                    }
                }
            }
            $values = [$tempLow, $tempMedium, $tempHigh];
            $temp['idRiskCategory'] = $tc['id_risk_category'];
            $temp['label'] = RiskCategoriesModel::findOrFail($tc['id_risk_category'])->risk_category;
            $temp['labels'] = $labels;
            $temp['values'] = $values;
            $temp['colors'] = $colors;
            $temp['max'] = max($values)+1;
            array_push($data['other_charts']['risk_bars_audit'], $temp);
        }
        /**
         * Action Graph Pie
         */
        $totalWeekly = $requirementsEvaluatingPool->count();
        $pendingWeekly = $requirementsEvaluatingPool->where('id_status', '!=', ActionPlansModel::COMPLETED_AP)->count();
        $doneWeekly = $requirementsEvaluatingPool->where('id_status', '=', ActionPlansModel::COMPLETED_AP)->count();

        $pendingCalc = ($totalWeekly == 0) ? 0 : ($pendingWeekly * 100) / $totalWeekly;
        $doneCalc = ($totalWeekly == 0) ? 0 : ($doneWeekly * 100) / $totalWeekly;
        if ($doneCalc > 0) {
            $data['other_charts']['weekly']['labels'] = ['Completado', 'Pendiente'];
            $data['other_charts']['weekly']['colors'] = ['#87CB16', '#FB404B'];
            $data['other_charts']['weekly']['values'] = [round($doneCalc, 1, PHP_ROUND_HALF_UP), round($pendingCalc, 1, PHP_ROUND_HALF_UP)];
        }
        else {
            $data['other_charts']['weekly']['labels'] = ['Pendiente'];
            $data['other_charts']['weekly']['colors'] = ['#FB404B'];
            $data['other_charts']['weekly']['values'] = [round($pendingCalc, 1, PHP_ROUND_HALF_UP)];
        }
        /**
         * Legal Basises Updated
         */
        $month_start = strtotime('first day of this month', time());
        $month_end = strtotime('last day of this month', time());
        $dates['init'] = date('Y-m-d', $month_start);
        $dates['finish'] = date('Y-m-d', $month_end);
        $data['legal_basises'] = BasisModel::getUpdatesBasis($dates);
        return response($data);
    }
    /**
     * Calculate global total audit + advance action plan
     */
    private function calculateTotalAPGlobal($ar) {
        $totalMattersGlobal = 0;
        $matters = AuditMattersModel::GetMattersByStatusAuditProcess($ar['id_audit_processes'], StatusConstants::FINISHED_AUDIT);
        foreach ($matters as $i => $m) {
            $matters[$i]['aspects'] = AuditAspectsModel::GetAuditedAspectsByMatter($m['id_audit_matter'], StatusConstants::FINISHED_AUDIT);
            $tempMatterTotal = 0;
            $countAspects = 0;
            foreach ($matters[$i]['aspects'] as $j => $a) {
                $exclud = ActionPlansModel::join('t_requirements', 't_requirements.id_requirement', 't_action_plans.id_requirement')
                ->where([
                    ['t_action_plans.id_audit_processes', $a['id_audit_processes']],
                    ['t_action_plans.id_aspect', $a['id_aspect']],
                    ['t_requirements.has_subrequirement', 1]
                ])
                ->whereNull('t_action_plans.id_subrequirement')
                ->pluck('t_action_plans.id_action_plan')->toArray();
                $actions = ActionPlansModel::where([
                    ['id_audit_processes', $a['id_audit_processes']],
                    ['id_aspect', $a['id_aspect']]
                ])->whereNotIn('t_action_plans.id_action_plan', $exclud)->get();
                $tempAspectTotal = $actions->count();
                $tempAspectDone = $actions->where('id_status', ActionPlansModel::COMPLETED_AP)->count();
                $totalAspectAP = $this->calculateTotalAP($a['total'], $tempAspectTotal, $tempAspectDone);
                $tempMatterTotal += $totalAspectAP;
                $countAspects ++;
            }
            $totalMattersAP = $tempMatterTotal / $countAspects;
            $totalMattersGlobal += $totalMattersAP;
        }
        if ($totalMattersGlobal != 0) {
            $total = $totalMattersGlobal / sizeof($matters);
        } else $total = 0;
        
        return $total;
    }
    /**
     * Calculate aspects and matters total audit + advance action plan
     */
    public static function calculateTotalAP($totalAudit, $total, $doneTotal) {
        if ($total != 0) {
            $remainingAudit = 100 - $totalAudit;
            $actionPlanTotal = ($remainingAudit * $doneTotal) / $total;
        }
        else {
            $actionPlanTotal = 0;
        }
        $total = $actionPlanTotal + $totalAudit;
        return $total;
    }
    /**
     * Get random color rgba format
     */
    public static function rgbaString($value) {
        switch ($value) {
            case 0:
                $rgbaSring = 'rgba(255, '.rand(0, 255).', '.rand(0, 255).', 0.5)';
                break;
            case 1:
                $rgbaSring = 'rgba('.rand(0, 255).', 255, '.rand(0, 255).', 0.5)';
                break;
            case 2:
                $rgbaSring = 'rgba('.rand(0, 255).', '.rand(0, 255).', 255, 0.5)';
                break;
            default:
                $rgbaSring = 'rgba('.rand(0, 255).', '.rand(0, 255).', '.rand(0, 255).', 0.5)';
                break;
        }
        return $rgbaSring;
    }
}
