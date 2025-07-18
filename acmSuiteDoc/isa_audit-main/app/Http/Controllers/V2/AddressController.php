<?php

namespace App\Http\Controllers\V2;

use App\Http\Controllers\Controller;
use Illuminate\Support\Arr;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use App\Http\Requests\AddressRequest;
use App\Models\V2\Admin\Corporate;
use App\Models\V2\Admin\Address;
use App\Models\V2\Audit\ProcessAudit;
use App\Models\V2\Audit\AplicabilityRegister;
use App\Traits\V2\ResponseApiTrait;

class AddressController extends Controller
{
  use ResponseApiTrait;

  /**
   * Redirect to view.
   */
  public function view() 
  {
    // 
  }

  /**
   * Display a listing of the resource.
   *
   * @return \Illuminate\Http\Response
   */
  public function index($idCustomer, $idCorporate)
  {
    $data = Address::included()->filter()->customFilter($idCustomer, $idCorporate)->getOrPaginate();
    return $this->successResponse($data);
  }

  /**
   * Store a newly created resource in storage.
   *
   * @param  \App\Http\Requests\AddressRequest $request
   * @return \Illuminate\Http\Response
   */
  public function store(AddressRequest $request, $idCustomer, $idCorporate) 
  {
    try {
      $validate = $this->verifyData('store', $idCustomer, $idCorporate, $request->all());
			if (!$validate['success']) {
				return $this->errorResponse($validate['message']);
			}

      $handler = $this->handlerData('store', $idCorporate, $validate['request']);
			if (!$handler['success']) {
				return $this->successResponse([], Response::HTTP_OK, 'Ok', $handler);
			}

      $address = Address::create($validate['request']);
      return $this->successResponse($address);
    } catch(\Throwable $th) {
      throw $th;
    }
  }

  /**
   * Display the specified resource.
   *
   * @param  int  $id
   * @return \Illuminate\Http\Response
   */
  public function show($idCustomer, $idCorporate, $id) 
  {
    try {
      $data = Address::included()->findOrFail($id);
      $validate = $this->verifyData('show', $idCustomer, $idCorporate, null, $data);
			if (!$validate['success']) {
				return $this->errorResponse($validate['messages']);
			}
      return $this->successResponse($data);
    } catch (\Throwable $th) {
      throw $th;
    }
  }

  /**
   * Update the specified resource in storage.
   *
   * @param  \App\Http\Requests\AddressRequest  $request
   * @param  int  $id
   * @return \Illuminate\Http\Response
   */
  public function update(AddressRequest $request, $idCustomer, $idCorporate, $id) 
  {
    try {
      $address = Address::findOrFail($id);

      $validate = $this->verifyData('update', $idCustomer, $idCorporate, $request->all(), $address);
			if (!$validate['success']) {
				return $this->errorResponse($validate['messages']);
			}

      $handler = $this->handlerData('update', $idCorporate, $validate['request']);
			if (!$handler['success']) {
				return $this->successResponse([], Response::HTTP_OK, 'Ok', $handler);
			}

      $address->update($validate['request']);
      return $this->successResponse($address);
    } catch(\Throwable $th) {
      throw $th;
    }
  }

  /**
   * Remove the specified resource from storage.
   *
   * @param  int  $id
   * @return \Illuminate\Http\Response
   */
  public function destroy($idCustomer, $idCorporate, $id) 
  {
    try {
      $address = Address::findOrFail($id);

      $validate = $this->verifyData('destroy', $idCustomer, $idCorporate, null, $address);
			if (!$validate['success']) {
				return $this->errorResponse($validate['messages']);
			}

      $handler = $this->handlerData('destroy', $idCorporate, $address->type);
			if (!$handler['success']) {
				return $this->successResponse([], Response::HTTP_OK, 'Ok', $handler);
			}

      $address->delete();
      return $this->successResponse($address);
    } catch(\Throwable $th) {
      throw $th;
    }
  }

  /**
   * verify data
   */
  private function verifyData($method, $idCustomer, $idCorporate, $request, $record = null)
  {
    $info['success'] = true;

    $corporate = Corporate::find($idCorporate);
    $sameCustomer = $corporate->id_customer == $idCustomer;
    if ( !$sameCustomer ) {
      $info['success'] = false;
      $info['message'] = 'Parametros erroneos';
      return $info;
    }

    $allowed = ['street', 'ext_num', 'int_num', 'zip', 'suburb', 'type', 'id_country', 'id_state', 'id_city'];
    $fields = $method == 'store' || $method == 'update' ? Arr::only($request, $allowed) : [];

   if ($method == 'update' || $method == 'destroy' || $method == 'show') {
			$sameCorporate = $record->id_corporate == $record->id_corporate;
			if (!$sameCorporate) {
				$info['success'] = false;
				$info['messages'] = 'La dirección no pertenece a la información especificada';
				return $info;
			}
			if ($method == 'destroy' || $method == 'show') return $info;
		}

    if ($method == 'store' || $method == 'update') {
      $fields = Arr::add($fields, 'id_corporate', $idCorporate);
      $info['request'] = $fields;
      return $info;
    }
  }

  /**
   * Handler dependencies
   */
  private function handlerData($method, $idCorporate, $type = null)
  {
    $info['success'] = true;

    if ($method == 'store') {
      $hasAddress = Address::where('id_corporate', $idCorporate)->where('type', $type)->first();
      if ( !is_null($hasAddress) ) {
        $info['success'] = false;
        $info['title'] = "Ya existe una dirección {$hasAddress->type_text}";
        $info['message'] = 'Recarga por favor';
        return $info; 
      }
    }

    if ($method == 'update' || $method == 'destroy') {
      $filterApplicability = fn($query) => $query->where('id_status', '!=', AplicabilityRegister::NOT_CLASSIFIED_APLICABILITY);
      $hasProjects = ProcessAudit::with(['aplicability_register' => $filterApplicability])
        ->whereHas('aplicability_register', $filterApplicability)
        ->where('id_corporate', $idCorporate)->first();
      
      if ( !is_null($hasProjects) ) {
        $action = $method == 'update' ? 'actualizar' : 'eliminar';
        $info['success'] = false;
        $info['title'] = `No es posible {$action} la Dirección Física, ya que existen ejercicios en Proceso`;
        $info['message'] = 'Pueden crear un nuevo ejercicio con una nueva Dirección Física';
        return $info;
      }
    }

    return $info;
  }
}