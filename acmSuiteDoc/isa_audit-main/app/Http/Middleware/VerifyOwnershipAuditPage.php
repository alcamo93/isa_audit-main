<?php

namespace App\Http\Middleware;

use App\Models\V2\Audit\AplicabilityRegister;
use Closure;
use Illuminate\Http\Request;

class VerifyOwnershipAuditPage
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
		$idAuditRegister = $request->route('idAuditRegister') ?? null;
		$idAuditMatter = $request->route('idAuditMatter') ?? null;
		$idAuditAspect = $request->route('idAuditAspect') ?? null;
		$idAuditEvaluate = $request->route('idAuditEvaluate') ?? null;
		
		$exists = AplicabilityRegister::verifyOwnershipAudit($idAuditProcess, $idAplicabilityRegister, $idAuditRegister, $idAuditMatter, $idAuditAspect, $idAuditEvaluate);
		if ( !$exists ) abort(404);

		return $next($request);
	}
}
