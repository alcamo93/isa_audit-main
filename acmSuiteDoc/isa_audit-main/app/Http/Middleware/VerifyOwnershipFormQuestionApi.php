<?php

namespace App\Http\Middleware;

use App\Models\V2\Catalogs\Form;
use Closure;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use App\Traits\V2\ResponseApiTrait;

class VerifyOwnershipFormQuestionApi
{
	use ResponseApiTrait;
	/**
	 * Handle an incoming request.
	 *
	 * @param  \Illuminate\Http\Request  $request
	 * @param  \Closure(\Illuminate\Http\Request): (\Illuminate\Http\Response|\Illuminate\Http\RedirectResponse)  $next
	 * @return \Illuminate\Http\Response|\Illuminate\Http\RedirectResponse
	 */
	public function handle(Request $request, Closure $next)
	{
		$idForm = $request->route('idForm');
		$idQuestion = $request->route('idQuestion');
		$idAnswerQuestion = $request->route('idAnswerQuestion') ?? null;
		
		$exists = Form::verifyOwnershipFormQuestion($idForm, $idQuestion, $idAnswerQuestion);
		if ( !$exists ) return $this->errorResponse('No se encuentra el registro que estas buscando, verifica información', Response::HTTP_NOT_FOUND);

		return $next($request);
	}
}
