<?php

namespace App\Http\Middleware;

use App\Models\V2\Audit\AplicabilityRegister;
use Closure;
use Illuminate\Http\Request;

class VerifyOwnershipAplicabilityPage
{
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
		$idAplicabilityEvaluate = $request->route('idAplicabilityEvaluate') ?? null;
		
		$exists = AplicabilityRegister::verifyOwnershipAplicability($idAuditProcess, $idAplicabilityRegister, $idContractMatter, $idContractAspect, $idAplicabilityEvaluate);
		if ( !$exists ) abort(404);

		return $next($request);
	}
}
