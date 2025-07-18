<?php

namespace App\Http\Middleware;

use App\Models\V2\Catalogs\Form;
use Closure;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use App\Traits\V2\ResponseApiTrait;

class VerifyOwnershipFormRequirementRecomendationApi
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
		$idRequirement = $request->route('idRequirement');
		$idRecomendation = $request->route('idRecomendation') ?? null;
		
		$exists = Form::verifyOwnershipFormRequirementRecomendation($idForm, $idRequirement, $idRecomendation);
		if ( !$exists ) return $this->errorResponse('No se encuentra el registro que estas buscando, verifica información', Response::HTTP_NOT_FOUND);

		return $next($request);
	}
}
