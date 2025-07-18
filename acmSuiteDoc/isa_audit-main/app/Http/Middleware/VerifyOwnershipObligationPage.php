<?php

namespace App\Http\Middleware;

use App\Models\V2\Audit\AplicabilityRegister;
use Closure;
use Illuminate\Http\Request;

class VerifyOwnershipObligationPage
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
		$idObligationRegister = $request->route('idObligationRegister') ?? null;
		$idObligation = $request->route('idObligation') ?? null;
		
		$exists = AplicabilityRegister::verifyOwnershipObligation($idAuditProcess, $idAplicabilityRegister, $idObligationRegister, $idObligation);
		if ( !$exists ) abort(404);

		return $next($request);
	}
}
