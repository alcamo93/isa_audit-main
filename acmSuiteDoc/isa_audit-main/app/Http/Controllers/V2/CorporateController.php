<?php

namespace App\Http\Controllers\V2;

use App\Classes\Files\StoreImage;
use App\Http\Controllers\Controller;
use App\Http\Requests\CorporateRequest;
use App\Http\Requests\ImageRequest;
use App\Models\V2\Admin\Customer;
use App\Models\V2\Admin\Corporate;
use App\Models\V2\Catalogs\Industry;
use App\Traits\V2\ResponseApiTrait;
use Illuminate\Support\Arr;
use Illuminate\Support\Str;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class CorporateController extends Controller
{
  use ResponseApiTrait;

  /**
   * Redirect to view.
   */
  public function view($idCustomer) 
  {
    $data['id_customer'] = $idCustomer;
    return view('v2.customer.corporate.main', ['data' => $data]);
  }

  /**
   * Display a listing of the resource.
   *
   * @return \Illuminate\Http\Response
   */
  public function source()
  {
    $data = Corporate::included()->filter()->withScopes()->filterUserAuth()->customFilters()->getOrPaginate();
    return $this->successResponse($data);
  }

  /**
   * Display a listing of the resource.
   *
   * @return \Illuminate\Http\Response
   */
  public function index($idCustomer)
  {
    $data = Corporate::included()->filter()->withIndex()->customFilters($idCustomer)->getOrPaginate();
    return $this->successResponse($data);
  }

  /**
   * Store a newly created resource in storage.
   *
   * @param  \App\Http\Requests\CorporateRequest $request
   * @return \Illuminate\Http\Response
   */
  public function store(CorporateRequest $request, $idCustomer) 
  {
    try {
      $validate = $this->verifyData('store', $idCustomer, $request->all());
			if (!$validate['success']) {
				return $this->errorResponse($validate['messages']);
			}
      DB::beginTransaction();
      $corporate = Corporate::create($validate['request']);
      DB::commit();
      return $this->successResponse($corporate);
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
  public function show($idCustomer, $idCrporate) 
  {
    try {
      $data = Corporate::included()->withScopes()->findOrFail($idCrporate);
      return $this->successResponse($data);
    } catch (\Throwable $th) {
      throw $th;
    }
  }

  /**
   * Update the specified resource in storage.
   *
   * @param  \App\Http\Requests\CorporateRequest  $request
   * @param  int  $id
   * @return \Illuminate\Http\Response
   */
  public function update(CorporateRequest $request, $idCustomer, $idCorporate) 
  {
    try {
      $corporate = Corporate::findOrFail($idCorporate);
			$validate = $this->verifyData('update', $idCustomer, $request->all(), $corporate);
			if (!$validate['success']) {
				return $this->errorResponse($validate['messages']);
			}
      DB::beginTransaction();
      $corporate->update($validate['request']);
      DB::commit();
      return $this->successResponse($corporate);
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
  public function destroy($idCustomer, $idCorporate) 
  {
    try {
      $corporate = Corporate::findOrFail($idCorporate);
      $validate = $this->verifyData('destroy', $idCustomer, null, $corporate);
			if (!$validate['success']) {
				return $this->errorResponse($validate['messages']);
			}
      DB::beginTransaction();
      $corporate->delete();
      DB::commit();
      return $this->successResponse($corporate);
    } catch(\Throwable $th) {
      DB::rollback();
      throw $th;
    }
  }

  /**
   * Store a newly created resource in storage.
   *
   * @param  \App\Http\Requests\ImageRequest $request
   * @return \Illuminate\Http\Response
   */
  public function image(ImageRequest $request, $idCustomer, $idCorporate) 
  {
    try {
      $file = $request->file('file');
      $imageableId = $idCorporate;
      $origin = 'corporate';
      $store = new StoreImage($file, $imageableId, $origin);
      $image = $store->storeImage();
      if ( !$image['success'] ) {
        return $this->errorResponse($image['message']);
      }
      return $this->successResponse($image);
    } catch (\Throwable $th) {  
      throw $th;
    }
  }

  /**
	 * Clear request fields 
   * @param  int  $method
   * @param  int  $idCustomer
   * @param  array  $request
   * @param  null|App\Models\V2\Admin\Corporate  $record
	 */
	private function verifyData($method, $idCustomer, $request, $record = null) 
	{
		$data['success'] = true;

    $allowed = ['corp_tradename', 'corp_trademark', 'rfc', 'id_industry', 'id_status', 'type', 'new_industry', 'add_new'];
    $fields = $method == 'store' || $method == 'update' ? Arr::only($request, $allowed) : [];
    $customer = Customer::findOrFail($idCustomer);

    if ($method == 'update' || $method == 'destroy') {
			$sameCustomer = $customer->id_customer == $record->id_customer;
			if (!$sameCustomer) {
				$data['success'] = false;
				$data['messages'] = 'La planta no pertenece al Cliente que especifica';
				return $data;
			}
			if ($method == 'destroy') return $data;
		}

		if ($method == 'store' || $method == 'update') {
      $addNew = $request['add_new'];
      $newIndustry = $request['new_industry'];
      $fields = Arr::except($request, ['add_new', 'new_industry']);

      if ( $addNew && !is_null($newIndustry) ) {
        $newIndustryNormalized = Str::ucfirst($newIndustry);
        $newIndustryNormalized = Str::of($newIndustryNormalized)->ltrim();
        $industryData = ['industry' => $newIndustryNormalized, 'id_status' => 1];
        $industry = Industry::firstOrCreate($industryData, $industryData);
        $fields['id_industry'] = $industry->id_industry;
      }
      
      $fields = Arr::add($fields, 'id_customer', $idCustomer);
      
      $data['success'] = true;
      $data['request'] = $fields;
      return $data;
    }
	}
}
