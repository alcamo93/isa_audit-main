<?php

namespace App\Http\Controllers\V2\Catalogs;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Http\Requests\IndustryRequest;
use App\Models\V2\Catalogs\Industry;
use App\Traits\V2\ResponseApiTrait;

class IndustryController extends Controller
{
  use ResponseApiTrait;

  /**
   * Redirect to view.
   */
  public function view() 
  {
    return view('v2.catalogs.industry.main');
  }

  /**
   * Display a listing of the resource.
   *
   * @return \Illuminate\Http\Response
   */
  public function source()
  {
    $data = Industry::included()->filter()->getOrPaginate();
    return $this->successResponse($data);
  }

  /**
   * Display a listing of the resource.
   *
   * @return \Illuminate\Http\Response
   */
  public function index()
  {
    $data = Industry::included()->filter()->getOrPaginate();
    return $this->successResponse($data);
  }

  /**
   * Store a newly created resource in storage.
   *
   * @param  \App\Http\Requests\IndustryRequest $request
   * @return \Illuminate\Http\Response
   */
  public function store(IndustryRequest $request) 
  {
    try {
      $validate = $this->validateRequest($request->all());
			if (!$validate['success']) {
				return $this->errorResponse($validate['messages']);
			}
      $data = Industry::create($validate['request']);
      return $this->successResponse($data);
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
      $data = Industry::included()->findOrFail($id);
      return $this->successResponse($data);
    } catch (\Throwable $th) {
      throw $th;
    }
  }

  /**
   * Update the specified resource in storage.
   *
   * @param  \App\Http\Requests\IndustryRequest  $request
   * @param  int  $id
   * @return \Illuminate\Http\Response
   */
  public function update(IndustryRequest $request, $id) 
  {
    try {
      $validate = $this->validateRequest($request->all());
			if (!$validate['success']) {
				return $this->errorResponse($validate['messages']);
			}
      $data = Industry::findOrFail($id);
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
      $data = Industry::findOrFail($id);
      $data->delete();
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

		$allowed = ['industry'];

		$fields = collect($request)->only($allowed)->put('id_status', 1);
		
		$data['request'] = $fields->toArray();
		return $data;
	}
}
