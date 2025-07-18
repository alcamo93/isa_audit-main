<?php

namespace App\Http\Middleware;

use App\Models\V2\Audit\AplicabilityRegister;
use Closure;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use App\Traits\V2\ResponseApiTrait;

class VerifyOwnershipAplicabilityApi
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
		$idAuditProcess = $request->route('idAuditProcess');
		$idAplicabilityRegister = $request->route('idAplicabilityRegister');
		$idContractMatter = $request->route('idContractMatter') ?? null;
		$idContractAspect = $request->route('idContractAspect') ?? null;
		$idEvaluateQuestion = $request->route('idEvaluateQuestion') ?? null;
		
		$exists = AplicabilityRegister::verifyOwnershipAplicability($idAuditProcess, $idAplicabilityRegister, $idContractMatter, $idContractAspect, $idEvaluateQuestion);
		if ( !$exists ) return $this->errorResponse('No se encuentra el registro que estas buscando, verifica información', Response::HTTP_NOT_FOUND);

		return $next($request);
	}
}
