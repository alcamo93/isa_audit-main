<?php

namespace App\Http\Controllers\V2\Catalogs;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use \App\Http\Requests\AspectRequest;
use App\Models\V2\Catalogs\Matter;
use App\Models\V2\Catalogs\Aspect;
use App\Traits\V2\ResponseApiTrait;

class AspectController extends Controller
{
  use ResponseApiTrait;

  /**
   * Display a listing of the resource.
   *
   * @return \Illuminate\Http\Response
   */
  public function source()
  {
    $data = Aspect::included()->filter()->customOrder()->getOrPaginate();
    return $this->successResponse($data);
  }

  /**
   * Display a listing of the resource.
   *
   * @return \Illuminate\Http\Response
   */
  public function index($idMatter)
  {
    try {
      $data = Aspect::included()->customFilters($idMatter)->filter()->customOrder()->getOrPaginate();
      return $this->successResponse($data);
    } catch (\Throwable $th) {
      throw $th;
    }
  }

  /**
   * Store a newly created resource in storage.
   *
   * @param  \App\Http\Requests\AspectRequest $request
   * @return \Illuminate\Http\Response
   */
  public function store(AspectRequest $request, $idMatter) 
  {
    try {
      $validate = $this->validateRequest('store', $idMatter, $request->all());
			if (!$validate['success']) {
				return $this->errorResponse($validate['messages']);
			}
      $data = Aspect::create($validate['request']);
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
  public function show($idMatter, $id) 
  {
    try {
      $data = Aspect::included()->findOrFail($id);
      return $this->successResponse($data);
    } catch (\Throwable $th) {
      throw $th;
    }
  }

  /**
   * Update the specified resource in storage.
   *
   * @param  \App\Http\Requests\AspectRequest  $request
   * @param  int  $id
   * @return \Illuminate\Http\Response
   */
  public function update(AspectRequest $request, $idMatter, $id) 
  {
    try {
      $record = Aspect::findOrFail($id);
      $validate = $this->validateRequest('update', $idMatter, $request->all(), $record);
			if (!$validate['success']) {
				return $this->errorResponse($validate['messages']);
			}
      $record->update($validate['request']);
      return $this->successResponse($record);
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
  public function destroy($idMatter, $id) 
  {
    try {
      $data = Aspect::findOrFail($id);
      $validate = $this->validateRequest('destroy', $idMatter, null, $data);
			if (!$validate['success']) {
				return $this->errorResponse($validate['messages']);
			}
      $data->delete();
      return $this->successResponse($data);
    } catch(\Throwable $th) {
      throw $th;
    }
  }

  /**
	 * Clear request fields 
	 */
	private function validateRequest($method, $idMatter, $request, $record = null) 
	{
		$data['success'] = true;

		$allowed = ['order', 'aspect'];

		$fields = collect($request)->only($allowed);
		$matter = Matter::findOrFail($idMatter);

		if ($method == 'store') {
			$fields->put('id_matter', $matter->id_matter);
		}

		if ($method == 'update' || $method == 'destroy') {
			$sameMatter = $matter->id_matter == $record->id_matter;
			if (!$sameMatter) {
				$data['success'] = false;
				$data['messages'] = 'El Aspecto no pertenece a la Materia que especifica';
				return $data;
			}
			if ($method == 'destroy') return $data; 
		}
		
		$data['request'] = $fields->toArray();
		return $data;
	}
}
