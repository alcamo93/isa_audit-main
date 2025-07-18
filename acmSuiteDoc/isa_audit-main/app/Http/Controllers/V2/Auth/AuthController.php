<?php

namespace App\Http\Controllers\V2\Auth;

use App\Http\Controllers\Controller;
use App\Http\Requests\AuthRequest;
use App\Models\V2\Admin\Contract;
use App\Models\V2\Admin\User;
use App\Models\V2\Catalogs\ProfileType;
use App\Traits\V2\ResponseApiTrait;
use Illuminate\Http\Request;
use Illuminate\Http\Response;

class AuthController extends Controller
{
	use ResponseApiTrait;

	/** 
	 * Get authentication token with one-hour duration
	 */
	public function login(AuthRequest $request)
	{
		try {
			$user = User::where('email', $request->email)->get()->first();
			if ( $user->id_status == User::INACTIVE ) {
				$message = 'Usuario deshabilitado';
				$extra = 'Usuario deshabilitado, contacta con tu administrador';
				return $this->errorResponse($message, Response::HTTP_UNAUTHORIZED, $extra);
			}
			
			$idProfileType = $user->profile->id_profile_type;
			if( $idProfileType != ProfileType::ADMIN_GLOBAL && $idProfileType != ProfileType::ADMIN_OPERATIVE ) {
				$exist = Contract::where('id_corporate', $user->id_corporate)->where('id_status', Contract::ACTIVE)->first();
				$invalidContract = $exist->in_range_date ?? false;
				if ( !$invalidContract ) {
					$message = 'Verifica el contrato';
					$extra = 'Contacta con tu administrador y verifica el estatus del contrato';
					return $this->errorResponse($message, Response::HTTP_UNAUTHORIZED, $extra);
				}
			}
			
			$tokenRequest = Request::create('/oauth/token', 'POST', [
				'grant_type' => 'password',
				'client_id' => config('passport.password_grant_client.id'),
				'client_secret' => config('passport.password_grant_client.secret'),
				'username' => $request->email,
				'password' => $request->password,
				'scope' => '',
			]);
			
			$response = app()->handle($tokenRequest);

			$body = json_decode((string)$response->getContent(), true);
			
			$status = $response->getStatusCode();
			
			if ($status != Response::HTTP_OK) {
				$isDevelopment = env('APP_DEBUG');
				$message = $isDevelopment ? $body['error'] : 'Error en el proceso de autorizacion';
				$extra = $isDevelopment ? $body['error_description'] : 'Check Logs';
				return $this->errorResponse($message, $status, $extra);
			}

			return $this->successResponse($body);
		} catch (\Throwable $th) {
			throw $th;
		}
	}

	/** 
	 * Get authentication token with one-hour duration
	 */
	public function setRefreshToken(AuthRequest $request)
	{
		try {
			$tokenRequest = Request::create('/oauth/token', 'POST', [
				'grant_type' => 'refresh_token',
				'refresh_token' => $request->refresh_token,
				'client_id' => config('passport.password_grant_client.id'),
				'client_secret' => config('passport.password_grant_client.secret'),
				'scope' => '',
			]);

			$response = app()->handle($tokenRequest);

			$body = json_decode((string)$response->getContent(), true);
			$status = $response->getStatusCode();

			if ($status != Response::HTTP_OK) {
				$isDevelopment = env('APP_DEBUG');
				$message = $isDevelopment ? $body['error'] : 'Error in refresh token authorization process';
				$extra = $isDevelopment ? $body['error_description'] : 'Check Logs';
				return $this->errorResponse($message, $status, $extra);
			}

			return $this->successResponse($body);
		} catch (\Throwable $th) {
			throw $th;
		}
	}
}
