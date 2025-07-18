<?php

namespace App\Http\Middleware;

use App\Models\V2\Audit\AplicabilityRegister;
use Closure;
use Illuminate\Http\Request;

class VerifyOwnershipPolymorphPage
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
		$section = $request->route('section');
		$idSectionRegister = $request->route('idSectionRegister');
		$idActionRegister = $request->route('idActionRegister') ?? null;
		$idActionPlan = $request->route('idActionPlan') ?? null;
		$idTask = $request->route('idTask') ?? null;
		
		$exists = AplicabilityRegister::verifyOwnershipPolymorph($idAuditProcess, $idAplicabilityRegister, $section, $idSectionRegister, $idActionRegister, $idActionPlan, $idTask);
		if ( !$exists ) abort(404);

		return $next($request);
	}
}
