<?php

namespace App\Http\Middleware;

use App\Models\V2\Catalogs\Guideline;
use App\Traits\V2\ResponseApiTrait;
use Closure;
use Illuminate\Http\Request;
use Illuminate\Http\Response;

class VerifyOwnershipArticleGuidelineApi
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
		$idGuideline = $request->route('idGuideline');
		$idArticle = $request->route('idArticle') ?? null;

		$exists = Guideline::verifyOwnershipArticleGuideline($idGuideline, $idArticle);
		if ( !$exists ) return $this->errorResponse('No se encuentra el registro que estas buscando, verifica información', Response::HTTP_NOT_FOUND);

		return $next($request);
	}
}
