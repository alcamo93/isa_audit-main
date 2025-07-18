<?php

namespace App\Http\Controllers\V2\Catalogs;

use App\Classes\Files\StoreImage;
use App\Http\Controllers\Controller;
use App\Http\Requests\MatterRequest;
use App\Models\V2\Catalogs\Matter;
use App\Traits\V2\ResponseApiTrait;
use Illuminate\Http\Request;

class MatterController extends Controller
{
  use ResponseApiTrait;

  /**
   * Redirect to view.
   */
  public function view() 
  {
    return view('v2.catalogs.matter.main');
  }
  /**
   * Display a listing of the resource.
   *
   * @return \Illuminate\Http\Response
   */
  public function source()
  {
    $data = Matter::included()->filter()->customOrder()->getOrPaginate();
    return $this->successResponse($data);
  }

  /**
   * Display a listing of the resource.
   *
   * @return \Illuminate\Http\Response
   */
  public function index()
  {
    $data = Matter::included()->filter()->customOrder()->getOrPaginate();
    return $this->successResponse($data);
  }

  /**
   * Store a newly created resource in storage.
   *
   * @param  \App\Http\Requests\MatterRequest $request
   * @return \Illuminate\Http\Response
   */
  public function store(MatterRequest $request) 
  {
    try {
      $validate = $this->validateRequest($request->all());
			if (!$validate['success']) {
				return $this->errorResponse($validate['messages']);
			}
      $data = Matter::create($validate['request']);
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
      $data = Matter::included()->findOrFail($id);
      return $this->successResponse($data);
    } catch (\Throwable $th) {
      throw $th;
    }
  }

  /**
   * Update the specified resource in storage.
   *
   * @param  \App\Http\Requests\MatterRequest  $request
   * @param  int  $id
   * @return \Illuminate\Http\Response
   */
  public function update(MatterRequest $request, $id) 
  {
    try {
      $validate = $this->validateRequest($request->all());
			if (!$validate['success']) {
				return $this->errorResponse($validate['messages']);
			}
      $data = Matter::findOrFail($id);
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
      $data = Matter::findOrFail($id);
      $data->delete();
      return $this->successResponse($data);
    } catch(\Throwable $th) {
      throw $th;
    }
  }

  /**
   * Store a newly created resource in storage.
   *
   * @param  \App\Http\Requests\MatterRequest $request
   * @return \Illuminate\Http\Response
   */
  public function image(Request $request, $id) 
  {
    try {
      $file = $request->file('file');
      $imageableId = $id;
      $origin = 'matter';
      $store = new StoreImage($file, $imageableId, $origin);
      $image = $store->storeImage();
      if ( !$image['success'] ) {
        return $this->errorResponse($image['message']);
      }
      return $this->successResponse($image);
    } catch (\Throwable $th) {  
      throw $th;
    }
  }

  /**
	 * Clear request fields 
	 */
	private function validateRequest($request) 
	{
		$data['success'] = true;

		$allowed = ['order', 'matter'];

		$fields = collect($request)->only($allowed);
		
		$data['request'] = $fields->toArray();
		return $data;
	}
}
