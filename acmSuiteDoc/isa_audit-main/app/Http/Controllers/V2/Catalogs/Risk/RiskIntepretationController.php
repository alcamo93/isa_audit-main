<?php

namespace App\Http\Controllers\V2\Catalogs\Risk;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Http\Requests\RiskInterpretationRequest;
use App\Models\V2\Catalogs\RiskInterpretation;
use App\Traits\V2\ResponseApiTrait;

class RiskIntepretationController extends Controller
{
  use ResponseApiTrait;

  /**
   * Display a listing of the resource.
   *
   * @return \Illuminate\Http\Response
   */
  public function index($idRiskCategory)
  {
    try {
      $data = RiskInterpretation::included()->filterRiskCategory($idRiskCategory)->filter()->getOrPaginate();
      return $this->successResponse($data);
    } catch (\Throwable $th) {
      throw $th;
    }
  }

    /**
   * Store a newly created resource in storage.
   *
   * @param  \App\Http\Requests\RiskInterpretationRequest $request
   * @return \Illuminate\Http\Response
   */
  public function store(RiskInterpretationRequest $request, $idRiskCategory) 
  {
    try {
      $validate = $this->validateRequest($request->all(), $idRiskCategory);

			if (!$validate['success']) {
				return $this->errorResponse($validate['messages']);
			}
      
      DB::beginTransaction();
      $data = $validate['request']->map(function($item) {
        $existItem = [ 'interpretation' => $item['interpretation'], 'id_risk_category' => $item['id_risk_category'] ];
        return RiskInterpretation::updateOrCreate($existItem, $item->toArray());
      });

      DB::commit();
      return $this->successResponse($data);
    } catch(\Throwable $th) {
      DB::rollback();
      throw $th;
    }
  }
  
  /**
	 * Clear request fields 
	 */
	private function validateRequest($request, $idRiskCategory) 
	{
		$data['success'] = true;
		$allowed = ['interpretation', 'interpretation_min', 'interpretation_max'];

		$interpretations = collect($request['interpretations']);
    $isRepeat = $interpretations->duplicates('interpretation');

    if ($isRepeat->isNotEmpty()) {
      $data['success'] = false;
      $data['message'] = 'Error, no puedes repetir el campo interpretation';
      return $data;
    }

    $total = $interpretations->reduce(function ($carry, $item) {
      return $carry + ($item['interpretation_max'] - $item['interpretation_min'] + 1);
    }, 0);

    if ($total !== 100) {
      $data['success'] = false;
      $data['message'] = 'Error, los rangos deben sumar un total de 100';
      return $data;
    }

    $fields = $interpretations->map(function($item) use ($allowed, $idRiskCategory) {
      $interpretation = collect($item)->only($allowed);
      $interpretation = $interpretation->put('id_risk_category', $idRiskCategory);
      return $interpretation;
    });
		
		$data['request'] = $fields;
		return $data;
	}
}
