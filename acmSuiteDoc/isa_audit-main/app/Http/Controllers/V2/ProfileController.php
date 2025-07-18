<?php

namespace App\Http\Controllers\V2;

use App\Http\Controllers\Controller;
use App\Models\V2\Admin\Customer;
use App\Models\V2\Admin\Profile;
use App\Models\V2\Catalogs\ProfileType;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Session;
use App\Traits\V2\ResponseApiTrait;

class ProfileController extends Controller
{
  use ResponseApiTrait;

  public function whatProfile() 
  {
    $idProfile = Session::get('profile')['id_profile'];
    $data = Profile::withIndex()->findOrFail($idProfile);
    return $this->successResponse($data);
  }

  /**
   * Redirect to view.
   */
  public function view() 
  {
    return view('v2.profile.main');
  }

  /**
   * Display a listing of the resource.
   *
   * @return \Illuminate\Http\Response
   */
  public function source()
  {
    try {
      $data = Profile::included()->withScopes()->filter()->getOrPaginate();
      return $this->successResponse($data);
    } catch (\Throwable $th) {
      throw $th;
    }
  }

  /**
   * Display a listing of the resource.
   *
   * @return \Illuminate\Http\Response
   */
  public function index()
  {
    try {
      $data = Profile::included()->withIndex()->filter()->getOrPaginate();
      return $this->successResponse($data);
    } catch (\Throwable $th) {
      throw $th;
    }
  }

  /**
   * Store a newly created resource in storage.
   *
   * @param  \App\Http\Requests\ProfileRequest $request
   * @return \Illuminate\Http\Response
   */
  public function store(Request $request) 
  {
    try {
      $validate = $this->validateData($request->all());
      if ( !$validate['success'] ) {
        return $this->successResponse([], 200, 'Ok', $validate);
      }
      $profile = Profile::create($request->all());
      return $this->successResponse($profile);
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
      $data = Profile::included()->findOrFail($id);
      return $this->successResponse($data);
    } catch (\Throwable $th) {
      throw $th;
    }
  }

  /**
   * Update the specified resource in storage.
   *
   * @param  \App\Http\Requests\ProfileRequest  $request
   * @param  int  $id
   * @return \Illuminate\Http\Response
   */
  public function update(Request $request, $id) 
  {
    try {
      $profile = Profile::findOrFail($id);
      $profile->update($request->all());
      return $this->successResponse($profile);
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
      $profile = Profile::findOrFail($id);
      $profile->delete();
      return $this->successResponse($profile);
    } catch(\Throwable $th) {
      throw $th;
    }
  }

  /**
   * Validate data
   */
  private function validateData($data)
  {
    try {
      $customer = Customer::findOrFail($data['id_customer']);
      $profileType = ProfileType::findOrFail($data['id_profile_type']);
      $validate = $customer->id_customer == 1 || ($customer->owner == $profileType->owner);
      if ( !$validate ) {
        $info['success'] = false;
        $info['title'] = "El tipo de perfil '{$profileType->type}' no esta permitido para este usuario";
        $info['message'] = "Los usuarios de clientes solo pueden tener perfiles tipo Corporativo, Coordinador y Operativo";
        return $info;
      }
      $info['success'] = true;
      return $info;
    } catch (\Throwable $th) {
      $info['success'] = false;
      $info['title'] = "Error al validar datos de Perfil";
      $info['message'] = "Sucedio un error al buscar el tipo de perfil dentro del cliente";
      return $info;
    }
  }
}