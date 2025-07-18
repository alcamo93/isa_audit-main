<?php

namespace App\Http\Controllers\V2\Catalogs;

use Illuminate\Support\Facades\DB;
use App\Http\Controllers\Controller;
use App\Http\Requests\FormDataRequest;
use App\Models\V2\Catalogs\Form;
use Illuminate\Http\Response;
use App\Traits\V2\ResponseApiTrait;
use App\Classes\Forms\HandlerChangeForms;
use App\Classes\Forms\ReplicateForm;

class FormController extends Controller
{
	use ResponseApiTrait;

	/**
	 * Redirect to view.
	 */
	public function view() 
	{
		return view('v2.catalogs.forms.form_view');
	}

	/**
   * Display a listing of the resource.
   *
   * @return \Illuminate\Http\Response
   */
  public function source()
  {
    $data = Form::where('is_current', 1)->withIndex()->included()->filter()->getOrPaginate();
    return $this->successResponse($data);
  }

	/**
	 * Display a listing of the resource.
	 *
	 * @return \Illuminate\Http\Response
	 */
	public function index()
	{
		try {
			$data = Form::included()->withIndex()->filter()->customOrder()->getOrPaginate();
			return $this->successResponse($data);
		} catch(\Throwable $th) {
			throw $th;
		}
	}
	
	/**
	 * Store a newly created resource in storage.
	 *
	 * @param  \App\Http\Requests\FormDataRequest $request
	 * @return \Illuminate\Http\Response
	 */
	public function store(FormDataRequest $request)
	{
		try {
			$data = collect($request->all());
			$lastVersionForm = $this->getLastVersion($data['aspect_id']);
			$data = $data->merge(['version' => $lastVersionForm])->toArray();
			$form = Form::create($data);
			$form->update(['id_track' => $form->id]);
			return $this->successResponse($form,  Response::HTTP_CREATED);
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
			$data = Form::withIndex()->findOrFail($id);
			return $this->successResponse($data);
		} catch(\Throwable $th) {
			throw $th;
		}
	}

	/**
	 * Update the specified resource in storage.
	 *
	 * @param  \App\Http\Requests\FormDataRequest  $request
	 * @param  int  $id
	 * @return \Illuminate\Http\Response
	 */
	public function update(FormDataRequest $request, $id)
	{
		try {
			DB::beginTransaction();
			$data = $request->all();
			$form = Form::findOrFail($id);
			$oldData = $form->toArray();
			// update forms and childs
			$form->update($data);
			$form = $form->fresh();
			$handlerForm = new HandlerChangeForms();
			$validation = $handlerForm->canModifyForm($form, $oldData, 'update');
			if ( !$validation['success'] ) {
				return $this->errorResponse($validation['messages']);	
			}
			DB::commit();
			return $this->successResponse($form);
		} catch(\Throwable $th) {
			DB::rollback();
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
			DB::beginTransaction();
			$form = Form::findOrFail($id);
			$handlerForm = new HandlerChangeForms();
			$validation = $handlerForm->canModifyForm($form, null, 'destroy');
			if ( !$validation['success'] ) {
				DB::rollback();
				return $this->errorResponse($validation['messages']);	
			}
			$form->delete();
			DB::commit();
			return $this->successResponse($form);
		} catch(\Throwable $th) {
			DB::rollback();
			throw $th;
		}
	}

	/**
	 * Change status the specified resource from storage.
	 *
	 * @param  int  $id
	 * @return \Illuminate\Http\Response
	 */
	public function changeCurrent($id)
	{
		try {
			$form = Form::findOrFail($id);
			$disabled = Form::where('aspect_id', $form->aspect_id)
					->where('is_current', 1);
			$disabled->update(['is_current' => 0]);
			$form->update(['is_current' => 1]);
			return $this->successResponse($form);
		} catch(\Throwable $th) {
			throw $th;
		}
	}

	/**
	 * Duplicate form.
	 *
	 * @param  int  $id
	 * @return \Illuminate\Http\Response
	 */
	public function duplicateForm($id)
	{
		try {
			$replicate = new ReplicateForm($id);
			$form = $replicate->replicateForm();
			return $this->successResponse($form);
		} catch(\Throwable $th) {
			throw $th;
		}
	}

	public function getLastVersion($idAspect)
	{
		$forms = Form::where('aspect_id', $idAspect)->get();
		$maxVersion = $forms->pluck('version')->max();
		$lastVersionForm = is_null($maxVersion) ? 1 : $maxVersion + 1;
		return $lastVersionForm;
	}
}
