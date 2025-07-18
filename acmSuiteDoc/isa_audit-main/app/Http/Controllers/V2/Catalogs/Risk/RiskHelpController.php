<?php

namespace App\Http\Controllers\V2\Catalogs\Risk;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Arr;
use App\Http\Requests\RiskHelpRequest;
use App\Models\V2\Catalogs\RiskHelp;
use App\Traits\V2\ResponseApiTrait;

class RiskHelpController extends Controller
{
  use ResponseApiTrait;

  /**
   * Display a listing of the resource.
   *
   * @param int $idRiskCategory
   * @return \Illuminate\Http\Response
   */
  public function index($idRiskCategory)
  {
    try {
      $relationships = ['attribute'];
      $data = RiskHelp::with($relationships)->included()->filterRiskCategory($idRiskCategory)->filter()->getOrPaginate();
      return $this->successResponse($data);
    } catch (\Throwable $th) {
      throw $th;
    }
  }

  /**
   * Store a newly created resource in storage.
   *
   * @param  \App\Http\Requests\RiskHelpRequest $request
   * @param int $idRiskCategory
   * @return \Illuminate\Http\Response
   */
  public function store(RiskHelpRequest $request, $idRiskCategory) 
  {
    try {
      $validate = $this->validateRequest('store', $request->all(), $idRiskCategory);

			if (!$validate['success']) {
				return $this->errorResponse($validate['messages']);
			}
      
      $data = RiskHelp::create($validate['request']);

      return $this->successResponse($data);
    } catch(\Throwable $th) {
      throw $th;
    }
  }

  /**
   * Display the specified resource.
   *
   * @param  int $idRiskCategory
   * @param  int  $id
   * @return \Illuminate\Http\Response
   */
  public function show($idRiskCategory, $id) 
  {
    try {
      $data = RiskHelp::included()->findOrFail($id);
      return $this->successResponse($data);
    } catch (\Throwable $th) {
      throw $th;
    }
  }

  /**
   * Store a newly created resource in storage.
   *
   * @param  \App\Http\Requests\RiskHelpRequest $request
   * @param  int $idRiskCategory
   * @param  int $id
   * @return \Illuminate\Http\Response
   */
  public function update(RiskHelpRequest $request, $idRiskCategory, $id) 
  {
    try {
      $data = RiskHelp::findOrFail($id);

      $validate = $this->validateRequest('update', $request->all(), $idRiskCategory, $data);
      
			if (!$validate['success']) {
				return $this->errorResponse($validate['messages']);
			}
      
      $data->update($validate['request']);

      return $this->successResponse($data);
    } catch(\Throwable $th) {
      throw $th;
    }
  }
  
  /**
	 * Clear request fields 
   * 
   * @param string $method
   * @param array $request
   * @param int $idRiskCategory
   * @param \App\Http\Requests\RiskHelpRequest $record
	 */
	private function validateRequest($method, $request, $idRiskCategory, $record = null) 
	{
		$data['success'] = true;

		$allowed = ['risk_help', 'standard', 'value', 'id_risk_category', 'id_risk_attribute', 'id_status'];

    $request = Arr::add($request, 'id_risk_category', $idRiskCategory);
    $request = Arr::add($request, 'id_status', 1);

    if ($method == 'update') {
      // remove field to avoid exist data
      $allowed = ['risk_help', 'standard', 'value', 'id_status'];
      // evaluate values
      $sameRiskCategory = $idRiskCategory == $record->id_risk_category;
      $sameRiskAttribute = $request['id_risk_attribute'] == $record->id_risk_attribute;
      if (!$sameRiskCategory && !$sameRiskAttribute) {
        $data['success'] = false;
        $data['message'] = 'Error, la categoría o atributo no pueden ser cambiados';
        return $data;
      }
    }

    $fields = collect($request)->only($allowed)->toArray();
		$data['request'] = $fields;
		return $data;
	}
}
