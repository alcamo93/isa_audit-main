<?php

namespace App\Http\Controllers\V2;

use App\Http\Controllers\Controller;
use App\Http\Requests\ContractRequest;
use App\Models\V2\Admin\Contract;
use App\Models\V2\Admin\ContractHistorical;
use App\Notifications\RenewalContractNotification;
use App\Traits\V2\ResponseApiTrait;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\DB;

class ContractController extends Controller
{
  use ResponseApiTrait;

  /**
   * Redirect to view.
   */
  public function view() 
  {
    return view('v2.contract.main');
  }

  /**
   * Display a listing of the resource.
   *
   * @return \Illuminate\Http\Response
   */
  public function index()
  {
    $data = Contract::included()->withIndex()->filter()->getOrPaginate();
    return $this->successResponse($data);
  }

  /**
   * Store a newly created resource in storage.
   *
   * @param  \App\Http\Requests\ContractRequest $request
   * @return \Illuminate\Http\Response
   */
  public function store(ContractRequest $request) 
  {
    try {
      DB::beginTransaction();
      // vlidate only contract per corporate
      $idCorporate = $request->input('id_corporate');
      $contracts = Contract::where('id_corporate', $idCorporate)->where('id_status', Contract::ACTIVE)->get();
      if ($contracts->count() > 0) {
        $contractName = $contracts->first()->contract;
        $info['title'] = "El contrato '{$contractName}' esta activo";
        $info['message'] = 'Solo puede haber un contrato activo por Planta';
        return $this->successResponse([], 200, 'Ok', $info);
      }
      // create contract
      $requestData = Arr::add($request->all(), 'id_status', Contract::ACTIVE);
      $data = Contract::create($requestData);
      // historical
      $historical = [
        'sequence' => 1,
        'type' => ContractHistorical::CREATE,
        'start_date' => $data->start_date,
        'end_date' => $data->end_date,
        'num_period' => $data->license->num_period,
        'id_period' => $data->license->period_id,
        'id_status' => 1,
      ];
      $data->historicals()->create($historical);
      DB::commit();
      return $this->successResponse($data);
    } catch(\Throwable $th) {
      DB::rollback();
      throw $th;
    }
  }

  /**
   * Display the specified resource.
   *
   * @param  int  $id
   * @return \Illuminate\Http\Response
   */
  public function show($id) 
  {
    try {
      $data = Contract::included()->withIndex()->withHistoricals()->withStatus()->findOrFail($id);
      return $this->successResponse($data);
    } catch (\Throwable $th) {
      throw $th;
    }
  }

  /**
   * Update the specified resource in storage.
   *
   * @param  \App\Http\Requests\ContractRequest  $request
   * @param  int  $id
   * @return \Illuminate\Http\Response
   */
  public function update(ContractRequest $request, $id) 
  {
    try {
      DB::beginTransaction();
      // update 
      // dd( $request->all() );
      $data = Contract::findOrFail($id);
      $contractType = $this->getTypeContract($data);
      $data->update( $request->all() );
      // disable previous records
      $allHistoricals = $data->historicals->where('id_status', Contract::ACTIVE);
      if ($allHistoricals->count() > 0) {
        $allHistoricals->each->update(['id_status' => Contract::INACTIVE]);
      }
      // set historical
      $sequence = $data->historicals->pluck('sequence')->max();
      $historical = [
        'sequence' => (++$sequence),
        'type' => $contractType,
        'start_date' => $data->start_date,
        'end_date' => $data->end_date,
        'num_period' => $data->license->num_period,
        'id_period' => $data->license->period_id,
        'id_status' => Contract::ACTIVE,
        'id_contract' => $data->id_contract
      ];
      $data->historicals()->create($historical);

      // send renewal notification
      $sendNotify = $this->handlerNotifyRenewalContract( $data->refresh(), $contractType );
      if ( !$sendNotify['success'] ) {
        DB::rollBack();
        return $sendNotify;
      }

      DB::commit();
      return $this->successResponse($data);
    } catch(\Throwable $th) {
      DB::rollback();
      throw $th;
    }
  }

  /**
   * Remove the specified resource from storage.
   *
   * @param  int  $id
   * @return \Illuminate\Http\Response
   */
  public function destroy($id) 
  {
    try {
      $data = Contract::findOrFail($id);
      $data->delete();
      return $this->successResponse($data);
    } catch(\Throwable $th) {
      throw $th;
    }
  }

  /**
   * Change status the specified resource from storage.
   *
   * @param  int  $id
   * @return \Illuminate\Http\Response
   */
  public function changeStatus($id)
  {
    try {
      $data = Contract::findOrFail($id);
      // validate contracts
      $status = $data->id_status === Contract::ACTIVE ? Contract::INACTIVE : Contract::ACTIVE;
      $contracts = Contract::where('id_corporate', $data->id_corporate)->where('id_status', Contract::ACTIVE);
      if ($contracts->exist() && $status === Contract::ACTIVE) {
        $contractName = $contracts->first()->contract;
        $info['title'] = "El contrato '{$contractName}' esta activo";
        $info['message'] = 'Solo puede haber un contrato activo por Planta';
        return $this->successResponse([], 200, 'Ok', $info);
      }
      // update status
      $data->update(['id_status' => $status]);
      return $this->successResponse($data);
    } catch(\Throwable $th) {
      throw $th;
    }
  }

  /**
   * Get type update about dates o state.
   *
   * @param  App\Models\V2\Admin\Contract $contract
   * @return int $type
   */
  private static function getTypeContract($contract) 
  {
    $info = $contract->info_dates;
    $type = ContractHistorical::UPDATE;

    if ($info['date_not_yet_reached']) return ContractHistorical::UPDATE;

    if ($info['within_range_date']) return ContractHistorical::EXTENSION;

    if ($info['expired_date']) return ContractHistorical::RENEWAL;

    return $type;
  }

  /**
   * @param  App\Models\V2\Admin\Contract $contract
   * @return integer $contractType
   * @return array $info
   */
  private function handlerNotifyRenewalContract($contract, $contractType)
  {
    if( $contractType !== ContractHistorical::RENEWAL ){
      $info['success'] = true;
      $info['messages'] = '';
      return $info;
    }

    $contact = $contract->corporate->contact;
    if( is_null($contact) ){
      $info['success'] = false;
      $info['messages'] = 'No se puede enviar notificación, porque no hay un contacto registrado para este contrato';
      return $info;
    }

    $domain = Config('enviroment.domain_frontend');
    $timezone = Config('enviroment.time_zone_carbon');
    $todayDate = Carbon::now($timezone);
    $endDateContract = Carbon::parse($contract->end_date, $timezone);

    $info['full_name'] = $contract->corporate->contact->full_name;
    $info['cust_tradename'] = $contract->customer->cust_tradename;
    $info['corp_tradename'] = $contract->corporate->corp_tradename;
    $info['contract'] = $contract->contract;
    $info['license'] = $contract->license->name;
    $info['start_date'] = $contract->start_date_format;
    $info['end_date'] = $contract->end_date_format;
    $info['days'] = $endDateContract->diffInDays($todayDate);
    $info['path'] = "{$domain}v2/contract/view";
    
    $contact->notify( new RenewalContractNotification($info) );

    $info['success'] = true;
    $info['messages'] = 'Notificacioón enviada';
    return $info;
  }
}