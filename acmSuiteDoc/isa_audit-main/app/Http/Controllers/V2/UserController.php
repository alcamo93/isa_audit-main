<?php

namespace App\Http\Controllers\V2;

use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\DB;
use Illuminate\Http\Request;
use App\Http\Requests\UserRequest;
use App\Models\V2\Admin\Person;
use App\Models\V2\Admin\User;
use App\Models\V2\Admin\Contract;
use App\Models\V2\Admin\Profile;
use App\Models\V2\Catalogs\ProfileType;
use App\Notifications\AccountNotifications;
use App\Traits\V2\ResponseApiTrait;
 
class UserController extends Controller
{
  use ResponseApiTrait;

  /**
   * Redirect to view.
   */
  public function view() 
  {
    return view('v2.user.main');
  }

  /**
   * Display a listing of the resource.
   *
   * @return \Illuminate\Http\Response
   */
  public function source()
  {
    try {
      $data = User::included()->withScopes()->withInfo()->filter()->customFilters()->filterUserAuth()->getOrPaginate();
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
      $data = User::included()->withIndex()->filter()->customFilters()->getOrPaginate();
      return $this->successResponse($data);
    } catch (\Throwable $th) {
      throw $th;
    }
  }

  /**
   * Store a newly created resource in storage.
   *
   * @param  \App\Http\Requests\UserRequest $request
   * @return \Illuminate\Http\Response
   */
  public function store(UserRequest $request) 
  {
    try {
      // validates contracts for customers
      $validate = $this->verifyData('store', $request->all());
      if ( !$validate['success'] ) {
        return $this->errorResponse($validate['message']);
      }
      // set data person
      DB::beginTransaction();
      $dataPerson = collect( $request->all() )->only(['first_name', 'second_name', 'last_name'])->toArray();
      $person = Person::create($dataPerson);
      // set data user
      $password = $this->randomPassword();
      $passwordEncrypt = Hash::make($password);
      $dataUser = collect( $request->all() )->put('password', $passwordEncrypt)
        ->put('id_person', $person->id_person)->toArray();
      $user = User::create($dataUser);
      // notify user
      $dataMail['info'] = [
        'title' => 'Bienvenido',
        'body' => 'Nueva Cuenta Creada',
        'description' => 'Su contraseña ha sido enviada a su correo electronico principal',
        'link' => config('enviroment.domain_frontend')
      ];
      $dataMail['mail'] = [
          'user' => $user->email,
          'name' => $user->person->full_name,
          'password' => $password
      ];
      $user->notify(new AccountNotifications($dataMail, 'mails.welcome', 'Registro'));
      DB::commit();
      return $this->successResponse($user);
    } catch (\Throwable $th) {
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
      $data = User::included()->withIndex()->findOrFail($id);
      return $this->successResponse($data);
    } catch (\Throwable $th) {
      throw $th;
    }
  }

  /**
   * Update the specified resource in storage.
   *
   * @param  \App\Http\Requests\UserRequest  $request
   * @param  int  $id
   * @return \Illuminate\Http\Response
   */
  public function update(UserRequest $request, $id) 
  {
    try {
      $user = User::findOrFail($id);
      // validates contracts for customers
      $validate = $this->verifyData('update', $request->all(), $user);
      if ( !$validate['success'] ) {
        return $this->errorResponse($validate['message']);
      }
      DB::beginTransaction();
      // set data user
      $user->update( $request->all() );
      $dataPerson = collect( $request->all() )->only(['first_name', 'second_name', 'last_name'])->toArray();
      // set data person
      $person = $user->person->update($dataPerson);
      DB::commit();
      return $this->successResponse($user);
    } catch (\Throwable $th) {
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
      DB::beginTransaction();
      $user = User::findOrFail($id);
      $user->person->delete();
      $user->delete();
      DB::commit();
      return $this->successResponse($user);
    } catch(\Throwable $th) {
      DB::rollback();
      throw $th;
    }
  }

  /**
   * Update the password resource in storage.
   *
   * @param  \App\Http\Requests\UserRequest  $request
   * @param  int  $id
   * @return \Illuminate\Http\Response
   */
  public function password(UserRequest $request, $id) 
  {
    try {
      $password = $request->input('password');
      $passwordEncrypt = Hash::make($password);
      $user = User::findOrFail($id);
      $user->update(['password' => $passwordEncrypt]);
      // Notification
      $userAuth = User::findOrFail(Auth::id());
      $dataMail['info'] = [
          'title' => 'Cambio de contraseña Manual',
          'body' => "El usuario <b>{$userAuth->person->full_name}</b> cambio tu contraseña",
          'description' => 'Su contraseña ha sido enviada a su correo electronico principal',
          'link' => config('enviroment.domain_frontend')
      ];
      $dataMail['mail'] = [
          'user' => $user->email,
          'name' => $user->person->full_name,
          'password' => $password
      ];
      $user->notify(new AccountNotifications($dataMail, 'mails.password', 'Cambio de contraseña'));

      return $this->successResponse($user);
    } catch (\Throwable $th) {
      throw $th;
    }
  }

  /**
   * get a random password
   */
  private function randomPassword() 
  {
    $length = 8;
    $numbers = '0123456789';
    $upper = 'ABCDEFGHIJKLMNOPQRSTUVWXYZ';
    $lower = 'abcdefghijklmnopqrstuvwxyz';
    $special = '!@#$%^&*()-_=+[]{};:,.<>?';

    $password = [];

    // We ensure the minimum requirements
    for ($i = 0; $i < 2; $i++) {
      $password[] = $numbers[random_int(0, strlen($numbers) - 1)];
      $password[] = $upper[random_int(0, strlen($upper) - 1)];
      $password[] = $lower[random_int(0, strlen($lower) - 1)];
    }

    // At least one special character
    $password[] = $special[random_int(0, strlen($special) - 1)];

    // Fill with random characters if length is missing
    $all = $numbers . $upper . $lower . $special;
    while (count($password) < $length) {
      $password[] = $all[random_int(0, strlen($all) - 1)];
    }

    shuffle($password);

    return implode('', $password);
  }

  /**
   * rules for users with contracts
   */
  private function verifyData($method, $data, $user = null)
  {
    try {
      if ( $data['id_customer'] != 1 ) {
        $info['success'] = true;
        $info['message'] = 'No se tiene ninguna restricción';
        return $info;
      }
      // validate has contract
      $idCorporate = $data['id_corporate'];
      $contract = Contract::where('id_corporate', $idCorporate)->where('id_status', 1)->first();
      if ( is_null($contract) ) {
        $info['success'] = false;
        $info['message'] = 'Error: Planta sin contrato, no esta vigente o no existe un contrato para esta planta';
        return $info;
      }
      
      $profileAdminTypes = collect([ ProfileType::ADMIN_GLOBAL, ProfileType::ADMIN_OPERATIVE ]);
      $profile = Profile::findOrFail($data['id_profile']);

      if ( !$profileAdminTypes->contains( $profile->id_profile_type ) ) {
        // validate number users per profile_type in contract
        $userPerCorporate = User::where('id_corporate', $idCorporate)->get();
        $countUsers = $contract->license->quantity->map(function($item) use ($userPerCorporate) {
          $profile['id_profile_type'] = $item->pivot->id_profile_type;
          $profile['max'] = $item->pivot->quantity;
          $profile['count'] = $userPerCorporate->where('profile.id_profile_type', $item->pivot->id_profile_type)->count();
          return $profile;
        });

        $type = $countUsers->firstWhere('id_profile_type', $profile->id_profile_type);
        
        $validateCount = $type['count'] >= $type['max'];
        $changeTypeProfile = is_null($user) ? false : $user->profile->id_profile_type != $profile->id_profile_type;
        $validate = $method == 'store' ? $validateCount : ($validateCount && $changeTypeProfile);

        if ( $validate ) {
          $info['success'] = false;
          $info['message'] = "Número máximo de usuario en contrato para perfil {$profile->profile_name} ({$profile->type->type}). Solo puedes agregar {$type['max']} usuarios del nivel de perfil {$profile->type->type}";
          return $info;
        }
      }

      $info['success'] = true;
      $info['message'] = 'Verificación exitosa';
      return $info;
    } catch (\Throwable $th) {
      $info['success'] = false;
      $info['message'] = 'Algo salio mal al verificar la información';
      return $info;
    }
  }
}
