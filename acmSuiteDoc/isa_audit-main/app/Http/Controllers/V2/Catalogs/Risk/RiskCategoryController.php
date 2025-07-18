<?php

namespace App\Http\Controllers\V2\Catalogs\Risk;

use App\Http\Controllers\Controller;
use Illuminate\Http\Response;
use Illuminate\Http\Request;
use App\Http\Requests\RiskCategoryRequest;
use App\Models\V2\Catalogs\RiskCategory;
use App\Models\V2\Catalogs\RiskHelp;
use App\Traits\V2\ResponseApiTrait;

class RiskCategoryController extends Controller
{
  use ResponseApiTrait;

  /**
	 * Redirect to view.
	 */
	public function view() 
	{
		return view('v2.catalogs.risk.main');
	}

  /**
   * Display a listing of the resource.
   *
   * @return \Illuminate\Http\Response
   */
  public function source()
  {
   try {
    
    $data = RiskCategory::with('attributes')->get();
    $helps = RiskHelp::get();
    $data = $data->map(function($category) use ($helps) {
      $tmpCategory = $category;
      $tmpCategory->value_category = null;
      $tmpCategory->attributes = $tmpCategory->attributes->map(function($attribute) use ($category, $helps) {
        $tmpAttribute = $attribute;
        $tmpAttribute->value_attribute = null;
        $filter = $helps->where('id_risk_category', $category->id_risk_category)->where('id_risk_attribute', $attribute->id_risk_attribute);
        $attribute->helps = $filter->values();
        return $tmpAttribute;
      });
      return $tmpCategory;
    });

    return $this->successResponse($data);
   } catch (\Throwable $th) {
    throw $th;
   }
  }

  /**
   * Display a listing of the resource.
   *
   * @return \Illuminate\Http\Response
   */
  public function index()
  {
    try {
      $data = RiskCategory::included()->filter()->getOrPaginate();
      return $this->successResponse($data);
    } catch (\Throwable $th) {
      throw $th;
    }
  }

    /**
   * Store a newly created resource in storage.
   *
   * @param  \App\Http\Requests\RiskCategoryRequest $request
   * @return \Illuminate\Http\Response
   */
  public function store(RiskCategoryRequest $request) 
  {
    try {
      // Adding a new exchange category some rules in Audit and Obligations
      $info['title'] = 'No es posible agregar una nueva categoría';
      $info['message'] = 'Consulte con administrador las opción de agregar una nueva categoría';
      return $this->successResponse([], Response::HTTP_OK, '', $info);
    } catch(\Throwable $th) {
      throw $th;
    }
  }

  /**
   * Display the specified resource.
   *
   * @param  int  $id
   * @return \Illuminate\Http\Response
   */
  public function show($id) 
  {
    try {
      $data = RiskCategory::included()->findOrFail($id);
      return $this->successResponse($data);
    } catch (\Throwable $th) {
      throw $th;
    }
  }

  /**
   * Update the specified resource in storage.
   *
   * @param  \App\Http\Requests\RiskCategoryRequest  $request
   * @param  int  $id
   * @return \Illuminate\Http\Response
   */
  public function update(RiskCategoryRequest $request, $id) 
  {
    try {
      $validate = $this->validateRequest($request->all());
			if (!$validate['success']) {
				return $this->errorResponse($validate['messages']);
			}
      $data = RiskCategory::findOrFail($id);
      $data->update($validate['request']);
      return $this->successResponse($data);
    } catch(\Throwable $th) {
      throw $th;
    }
  }

  /**
   * Remove the specified resource from storage.
   *
   * @param  int  $id
   * @return \Illuminate\Http\Response
   */
  public function destroy($id) 
  {
    try {
      // Adding a new exchange category some rules in Audit and Obligations
      $info['title'] = 'No es posible eliminar una categoría';
      $info['message'] = 'Consulte con administrador las opción de eliminar una categoría';
      return $this->successResponse([], Response::HTTP_OK, '', $info);
      return $this->successResponse($data);
    } catch(\Throwable $th) {
      throw $th;
    }
  }

  /**
	 * Clear request fields 
	 */
	private function validateRequest($request) 
	{
		$data['success'] = true;

		$allowed = ['risk_category'];

		$fields = collect($request)->only($allowed);
		
		$data['request'] = $fields->toArray();
		return $data;
	}
}
