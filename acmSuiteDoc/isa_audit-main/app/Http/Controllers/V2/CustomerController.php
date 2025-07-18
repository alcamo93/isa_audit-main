<?php

namespace App\Http\Controllers\V2;

use App\Classes\Files\StoreImage;
use App\Http\Controllers\Controller;
use App\Http\Requests\CustomerRequest;
use App\Http\Requests\ImageRequest;
use App\Models\V2\Admin\Customer;
use App\Traits\V2\ResponseApiTrait;
use Illuminate\Http\Request;

class CustomerController extends Controller
{
  use ResponseApiTrait;

  /**
   * Redirect to view.
   */
  public function view() 
  {
    return view('v2.customer.main');
  }

  /**
   * Display a listing of the resource.
   *
   * @return \Illuminate\Http\Response
   */
  public function source()
  {
    $data = Customer::included()->filter()->withScopes()->filterUserAuth()->getOrPaginate();
    return $this->successResponse($data);
  }

  /**
   * Display a listing of the resource.
   *
   * @return \Illuminate\Http\Response
   */
  public function index()
  {
    $data = Customer::included()->filter()->withImages()->customFilters()->getOrPaginate();
    return $this->successResponse($data);
  }

  /**
   * Store a newly created resource in storage.
   *
   * @param  \App\Http\Requests\CustomerRequest $request
   * @return \Illuminate\Http\Response
   */
  public function store(CustomerRequest $request) 
  {
    try {
      $customer = Customer::create($request->all());
      return $this->successResponse($customer);
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
  public function show($id) 
  {
    try {
      $data = Customer::included()->findOrFail($id);
      return $this->successResponse($data);
    } catch (\Throwable $th) {
      throw $th;
    }
  }

  /**
   * Update the specified resource in storage.
   *
   * @param  \App\Http\Requests\CustomerRequest  $request
   * @param  int  $id
   * @return \Illuminate\Http\Response
   */
  public function update(CustomerRequest $request, $id) 
  {
    try {
      $customer = Customer::findOrFail($id);
      $customer->update($request->all());
      return $this->successResponse($customer);
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
  public function destroy($id) 
  {
    try {
      $customer = Customer::findOrFail($id);
      $customer->delete();
      return $this->successResponse($customer);
    } catch(\Throwable $th) {
      throw $th;
    }
  }

  /**
   * Store a newly created resource in storage.
   *
   * @param  \App\Http\Requests\ImageRequest $request
   * @return \Illuminate\Http\Response
   */
  public function image(ImageRequest $request, $idCustomer, $usage) 
  {
    try {
      $file = $request->file('file');
      $imageableId = $idCustomer;
      $origin = 'customer';
      $store = new StoreImage($file, $imageableId, $origin, $usage);
      $image = $store->storeImage();
      if ( !$image['success'] ) {
        return $this->errorResponse($image['message']);
      }
      return $this->successResponse($image);
    } catch (\Throwable $th) {  
      throw $th;
    }
  }
}
