<?php

namespace App\Http\Controllers\V2;

use App\Http\Controllers\Controller;
use App\Http\Requests\ContactRequest;
use App\Models\V2\Admin\Contact;
use App\Traits\V2\ResponseApiTrait;
use Illuminate\Http\Request;
use Illuminate\Support\Arr;

class ContactController extends Controller
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
   * 
   * Params used in Middleware route
   */
  public function index($idCustomer, $idCorporate)
  {
    $data = Contact::included()->filter()->getOrPaginate();
    return $this->successResponse($data);
  }

  /**
   * Store a newly created resource in storage.
   *
   * @param  \App\Http\Requests\ContactRequest $request
   * @return \Illuminate\Http\Response
   * 
   * Params used in Middleware route
   */
  public function store(ContactRequest $request, $idCustomer, $idCorporate)
  {
    try {
      $validate = $this->verifyData($idCorporate, $request->all());
      if ( !$validate['success'] ) return $this->errorResponse($validate['messages']);

      $contact = Contact::create($validate['request']);
      return $this->successResponse($contact);
    } catch (\Throwable $th) {
      throw $th;
    }
  }

  /**
   * Display the specified resource.
   *
   * @param  int  $id
   * @return \Illuminate\Http\Response
   * 
   * Params used in Middleware route
   */
  public function show($idCustomer, $idCorporate, $id)
  {
    try {
      $data = Contact::included()->findOrFail($id);
      return $this->successResponse($data);
    } catch (\Throwable $th) {
      throw $th;
    }
  }

  /**
   * Update the specified resource in storage.
   *
   * @param  \App\Http\Requests\ContactRequest  $request
   * @param  int  $id
   * @return \Illuminate\Http\Response
   * 
   * Params used in Middleware route
   */
  public function update(ContactRequest $request, $idCustomer, $idCorporate, $id)
  {
    try {
      $contact = Contact::findOrFail($id);
      
      $validate = $this->verifyData($idCorporate, $request->all());
      if ( !$validate['success'] ) return $this->errorResponse($validate['messages']);
			
      $contact->update($validate['request']);
      return $this->successResponse($contact);
    } catch (\Throwable $th) {
      throw $th;
    }
  }

  /**
   * Remove the specified resource from storage.
   *
   * @param  int  $id
   * @return \Illuminate\Http\Response
   * 
   * Params used in Middleware route
   */
  public function destroy($idCustomer, $idCorporate, $id)
  {
    try {
      $contact = Contact::findOrFail($id);
      $contact->delete();
      return $this->successResponse($contact);
    } catch (\Throwable $th) {
      throw $th;
    }
  }

  /**
   * verify data for update and store method
   */
  private function verifyData($idCorporate, $request)
  {
    try {
      $allowed = ['ct_email', 'ct_phone_office', 'ct_ext', 'ct_cell', 'ct_first_name', 'ct_second_name', 'ct_last_name', 'degree'];
    
      $fields = Arr::only($request, $allowed);
      $fields = Arr::add($fields, 'id_corporate', $idCorporate);

      $info['request'] = $fields;
      $info['success'] = true;
      $info['message'] = 'Información normalizada';
      return $info;
    } catch (\Throwable $th) {
      $info['success'] = false;
      $info['message'] = 'Algo salio mal al normalizar la información';
    }
  }
}
