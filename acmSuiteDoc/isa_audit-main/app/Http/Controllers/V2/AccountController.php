<?php

namespace App\Http\Controllers\V2;

use App\Classes\Files\StoreImage;
use App\Http\Controllers\Controller;
use App\Http\Requests\AccountRequest;
use App\Http\Requests\ImageRequest;
use App\Models\V2\Admin\User;
use App\Traits\V2\ResponseApiTrait;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class AccountController extends Controller
{
  use ResponseApiTrait;

  /**
   * Redirect to view.
   */
  public function view() 
  {
    $data = ['id_user' => Auth::id()];
    return view('v2.personal.account.main', ['data' => $data]);
  }

  /**
   * Display a listing of the resource.
   *
   * @return \Illuminate\Http\Response
   */
  public function index()
  {
    try {
      $data = User::included()->withIndexAccount()->findOrFail( Auth::id() );
      return $this->successResponse($data);
    } catch (\Throwable $th) {
      throw $th;
    }
  }

  /**
   * Store a newly created resource in storage.
   *
   * @param  \App\Http\Requests\AccountRequest $request
   * @return \Illuminate\Http\Response
   */
  public function store(Request $request) 
  {
    //  
  }

  /**
   * Display the specified resource.
   *
   * @param  int  $id
   * @return \Illuminate\Http\Response
   */
  public function show($id) 
  {
    // 
  }

  /**
   * Update the specified resource in storage.
   *
   * @param  \App\Http\Requests\AccountRequest  $request
   * @param  int  $id
   * @return \Illuminate\Http\Response
   */
  public function update(AccountRequest $request) 
  {
    try {
      $user = User::findOrFail( Auth::id() );
      $requestCollect = collect( $request->all() );
      // set data user
      $fieldsUser = ['email', 'secondary_email'];
      $dataUser = $requestCollect->only($fieldsUser)->toArray();
      $user->update( $dataUser );
      // set data person
      $fieldsPerson = ['first_name', 'second_name', 'last_name', 'rfc', 'gender', 'birthdate'];
      $dataPerson = $requestCollect->only($fieldsPerson)->toArray();
      $person = $user->person->update($dataPerson);

      return $this->successResponse($user);
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
    // 
  }

  /**
   * Store a newly created resource in storage.
   *
   * @param  \App\Http\Requests\ImageRequest $request
   * @return \Illuminate\Http\Response
   */
  public function image(ImageRequest $request) 
  {
    try {
      $file = $request->file('file');
      $imageableId = Auth::id();
      $origin = 'user';
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
}
