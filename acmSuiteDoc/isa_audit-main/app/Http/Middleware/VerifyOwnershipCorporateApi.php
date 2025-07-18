<?php

namespace App\Http\Middleware;

use App\Models\V2\Admin\Customer;
use App\Traits\V2\ResponseApiTrait;
use Closure;
use Illuminate\Http\Request;
use Illuminate\Http\Response;

class VerifyOwnershipCorporateApi
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
		$type = 'corporate';
		$idCustomer = $request->route('idCustomer');
		$idCorporate = $request->route('idCorporate');
		
		$exists = Customer::verifyOwnershipCorporateSource($type, $idCustomer, $idCorporate);
		if ( !$exists ) return $this->errorResponse('No se encuentra el registro que estas buscando, verifica información', Response::HTTP_NOT_FOUND);

		return $next($request);
	}
}
