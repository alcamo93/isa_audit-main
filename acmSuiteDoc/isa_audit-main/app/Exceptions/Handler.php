<?php

namespace App\Exceptions;

use Throwable;
use InvalidArgumentException;
use Illuminate\Foundation\Exceptions\Handler as ExceptionHandler;
use Symfony\Component\HttpKernel\Exception\HttpException;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Auth\Access\AuthorizationException;
use Illuminate\Auth\AuthenticationException;
use Facade\Ignition\Exceptions\ViewException;
use Illuminate\Validation\ValidationException;
use Illuminate\Http\Response;
use Illuminate\Database\QueryException;
use App\Traits\V2\ResponseApiTrait;

class Handler extends ExceptionHandler
{
	use ResponseApiTrait;
	/**
	 * A list of the exception types that are not reported.
	 *
	 * @var array<int, class-string<Throwable>>
	 */
	protected $dontReport = [
		//
	];

	/**
	 * A list of the inputs that are never flashed for validation exceptions.
	 *
	 * @var array<int, string>
	 */
	protected $dontFlash = [
		'current_password',
		'password',
		'password_confirmation',
	];

	/**
	 * Register the exception handling callbacks for the application.
	 *
	 * @return void
	 */
	public function register()
	{
		$isDevelopment = env('APP_DEBUG');
		$genericMsg = 'Check logs';

		$this->renderable(function (HttpException $e) use ($isDevelopment, $genericMsg) {
			$code = $e->getStatusCode();
			if ($code == 419) { // handler session expired (method file session)
				$message = 'El token de seguridad ha expirado por inactividad recargue página para accesar nuevamente';
				return $this->errorResponse($message, Response::HTTP_UNAUTHORIZED);
			}
			if ($code == Response::HTTP_NOT_FOUND) { // handler session expired (method file session)
				return response()->view('errors.404', [], 404);
			}
			$message = Response::$statusTexts[$code];
			$extras = $isDevelopment ? $e->getMessage() : $genericMsg;
			return $this->errorResponse($message, $code, $extras);
		});

		$this->renderable(function (ModelNotFoundException $e) use ($isDevelopment, $genericMsg) {
			$errors = 'Internal Server Error';
			$model = strtolower( class_basename( $e->getModel() ) );
			$extras = $isDevelopment ? "Does not exist any instance of {$model} with the given id" : $genericMsg;
			return $this->errorResponse($errors, Response::HTTP_NOT_FOUND, $extras);
		});

		$this->renderable(function (AuthorizationException $e) {
			return $this->errorResponse($e->getMessage(), Response::HTTP_FORBIDDEN);
		});

		$this->renderable(function (AuthenticationException $e) { //auth:api
	    return $this->errorResponse($e->getMessage(), Response::HTTP_UNAUTHORIZED);
		});

		$this->renderable(function (ValidationException $e) {
			$errors = $e->validator->errors()->all();
			return $this->errorResponse($errors, Response::HTTP_UNPROCESSABLE_ENTITY); 
		});

		$this->renderable(function (QueryException $e) use ($isDevelopment, $genericMsg) {
			$codeQuery = $e->getCode();
			if ($codeQuery === '23000' && isset($e->errorInfo[1]) && $e->errorInfo[1] === 1451) { 
				// response to indicate that the resource is being used in another table
				$info['title'] = 'No es posible eliminar Registro';
				$info['message'] = 'Este registro genera dependencia en otra sección, elimine primero la dependencia y vuelva a intentar';
				return $this->successResponse([], Response::HTTP_OK, '', $info);
			}
			if ($codeQuery === '23000' && isset($e->errorInfo[1]) && $e->errorInfo[1] === 1062) {
				// response to indicate tha some fileds is unique in DB
				$info['title'] = 'Campos únicos';
				$info['message'] = 'Revisa por favor tu información un campo de tu solicitud es único y esta en uso dentro del sistema';
				return $this->successResponse([], Response::HTTP_OK, '', $info);
			}
			$errors = 'Internal Server Error';
			$extras = $isDevelopment ? $e->getMessage() : $genericMsg;
			return $this->errorResponse($errors, Response::HTTP_INTERNAL_SERVER_ERROR, $extras);
		});

		$this->renderable(function (InvalidArgumentException $e) use ($isDevelopment, $genericMsg) {
			$errors = 'Internal Server Error';
			$extras = $isDevelopment ? $e->getMessage() : $genericMsg;
			return $this->errorResponse($errors, Response::HTTP_INTERNAL_SERVER_ERROR, $extras);
		});

		$this->renderable(function (ViewException $e) use ($isDevelopment, $genericMsg) {
			$errors = 'Internal Server Error';
			$extras = $isDevelopment ? $e->getMessage() : $genericMsg;
			return $this->errorResponse($errors, Response::HTTP_INTERNAL_SERVER_ERROR, $extras);
		});
	}
}