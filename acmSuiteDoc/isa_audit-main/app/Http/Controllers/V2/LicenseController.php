<?php

namespace App\Http\Controllers\V2;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Http\Requests\LicenseRequest;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\DB;
use App\Models\V2\Admin\License;
use App\Traits\V2\ResponseApiTrait;

class LicenseController extends Controller
{
  use ResponseApiTrait;

  /**
   * Redirect to view.
   */
  public function view() 
  {
    return view('v2.catalogs.license.main');
  }

  /**
   * Display a listing of the resource.
   *
   * @return \Illuminate\Http\Response
   */
  public function source()
  {
    $data = License::included()->withScopes()->filter()->getOrPaginate();
    return $this->successResponse($data);
  }

  /**
   * Display a listing of the resource.
   *
   * @return \Illuminate\Http\Response
   */
  public function index()
  {
    $data = License::included()->withIndex()->filter()->getOrPaginate();
    return $this->successResponse($data);
  }

  /**
   * Store a newly created resource in storage.
   *
   * @param  \App\Http\Requests\LicenseRequest $request
   * @return \Illuminate\Http\Response
   */
  public function store(LicenseRequest $request) 
  {
    try {
      $fileds = collect($request->all());
      $licenseRequest = $fileds->except('quantity')->toArray();
      $quantityRequest = $fileds->only('quantity');
      // build details
      $details = [];
      foreach ($quantityRequest['quantity'] as $itemRequest) {
        $index = intval($itemRequest['id_profile_type']);
        $quantity = intval($itemRequest['quantity']);
        $details[$index] = ['quantity' => $quantity];
      }
      // set data
      DB::beginTransaction();
      $license = License::create($licenseRequest);
      $license->quantity()->attach($details);
      DB::commit();
      return $this->successResponse($license);
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
      $data = License::included()->withIndex()->findOrFail($id);
      return $this->successResponse($data);
    } catch (\Throwable $th) {
      throw $th;
    }
  }

  /**
   * Update the specified resource in storage.
   *
   * @param  \App\Http\Requests\LicenseRequest  $request
   * @param  int  $id
   * @return \Illuminate\Http\Response
   */
  public function update(LicenseRequest $request, $id) 
  {
    try {
      $fileds = collect($request->all());
      $licenseRequest = $fileds->except('quantity')->toArray();
      $quantityRequest = $fileds->only('quantity');
      $license = License::findOrFail($id);
      $quantityDB = $license->quantity;
      // check appropriate license
      foreach ($quantityRequest['quantity'] as $itemRequest) {
        $itemDB = $quantityDB->firstWhere('id_profile_type', $itemRequest['id_profile_type']);
        if ( is_null($itemDB) ) continue;
        $countDB = $itemDB->pivot->quantity;
        $countRequest = $itemRequest['quantity'];
        $isAllow = $countRequest >= $countDB;
        if ($isAllow) continue;
        $info['title'] = "No es posible dismunuir el número de usuarios tipo {$itemDB->type}, porque la licencia esta en uso";
        $info['message'] = "Actualmente esta licencia permite {$countDB} usuario(s) y se quiere establecer en {$countRequest} usuario(s)";
        return $this->successResponse([], Response::HTTP_OK, 'Ok', $info);
      }
      // build details
      $details = [];
      foreach ($quantityRequest['quantity'] as $itemRequest) {
        $index = intval($itemRequest['id_profile_type']);
        $quantity = intval($itemRequest['quantity']);
        $details[$index] = ['quantity' => $quantity];
      }
      // set data
      DB::beginTransaction();
      $license->update($licenseRequest);
      $license->quantity()->sync($details);
      DB::commit();
      return $this->successResponse($license);
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
      $data = License::findOrFail($id);
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
      $data = License::findOrFail($id);
      $status = $data->status_id === 1 ? 2 : 1;
      $data->update(['status_id' => $status]);
      return $this->successResponse($data);
    } catch(\Throwable $th) {
      throw $th;
    }
  }
}