<?php

namespace App\Http\Controllers\V2\Catalogs;

use App\Classes\Files\ImageDecodeToFile;
use App\Classes\Forms\HandlerChangeQuestions;
use App\Http\Controllers\Controller;
use App\Http\Requests\QuestionRequest;
use App\Models\V2\Catalogs\Form;
use App\Models\V2\Catalogs\Question;
use App\Models\V2\Catalogs\QuestionType;
use App\Traits\V2\ResponseApiTrait;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\DB;

class QuestionController extends Controller
{
  use ResponseApiTrait;
  
	/**
	 * Redirect to view.
	 */
	public function view($idForm) 
	{
		$data = ['id_form' => $idForm];
		return view('v2.catalogs.forms.question.main', ['data' => $data]);
	}

  /**
	 * Display a listing of the resource.
	 *
	 * @return \Illuminate\Http\Response
	 */
	public function index($idForm)
	{
		try {
			$data = Question::included()->withIndex()->filter()->customFilters($idForm)->customOrderQuestion()->getOrPaginate();
			return $this->successResponse($data);
		} catch(\Throwable $th) {
			throw $th;
		}
	}
	
	/**
	 * Store a newly created resource in storage.
	 *
	 * @param  App\Http\Requests\QuestionRequest $request
	 * @return \Illuminate\Http\Response
	 */
	public function store(QuestionRequest $request, $idForm)
	{
		try {
			DB::beginTransaction();
			$validate = $this->validateRequest('store', $idForm, $request->all());
			if (!$validate['success']) {
				DB::rollback();
				return $this->errorResponse($validate['messages']);
			}
			$record = Question::create($validate['request']);
			// store disk: define in config/filesystems.php : disks []
			$storeDisk = 'questionsHelpers';
			$decodeImgObject = new ImageDecodeToFile($storeDisk, $record->id_question);
			$decodeImgObject->decodeImg64ToLink($validate['request']['help_question']);
			if (!$decodeImgObject->allCreate) {
				DB::rollback();
				return $this->errorResponse('No se ha podido crear los archivos, verifica que las imagenes no esten dañadas');
			}
			// update only help question
			$record->update([
				'help_question' => $decodeImgObject->richText,
				'id_track' => $record->id_question
			]);
			$response = $record->only('id_question', 'question', 'order', 'id_question_type');
			DB::commit();
			return $this->successResponse($response, Response::HTTP_CREATED);
		} catch (\Throwable $th) {
			DB::rollback();
			throw $th;
		}
	}

	/**
	 * Display the specified resource.
	 *
	 * @param  int  $id
	 * @return \Illuminate\Http\Response
	 */
	public function show($idForm, $id)
	{
		try {
			$data = Question::findOrFail($id);
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
	public function single($id)
	{
		try {
			$data = Question::included()->withSource()->findOrFail($id);
			return $this->successResponse($data);
		} catch(\Throwable $th) {
			throw $th;
		}
	}

	/**
	 * Update the specified resource in storage.
	 *
	 * @param  App\Http\Requests\QuestionRequest $request
	 * @param  int  $id
	 * @return \Illuminate\Http\Response
	 */
	public function update(QuestionRequest $request, $idForm, $id)
	{
		try {
			DB::beginTransaction();
			$record = Question::findOrFail($id);
			$validate = $this->validateRequest('update', $idForm, $request->all(), $record);
			if (!$validate['success']) {
				DB::rollback();
				return $this->errorResponse($validate['messages']);
			}
			$handler = new HandlerChangeQuestions();
			$destroy = $handler->canModifyQuestion($record, $validate['request'], 'update');
			if (!$destroy['success']) {
				DB::rollback();
				return $this->errorResponse($destroy['messages']);
			}
			$record->update($validate['request']);
			// store disk: define in config/filesystems.php : disks []
			$storeDisk = 'questionsHelpers';
			$decodeImgObject = new ImageDecodeToFile($storeDisk, $record->id_question);
			$decodeImgObject->decodeImg64ToLink($validate['request']['help_question']);
			if (!$decodeImgObject->allCreate) {
				DB::rollback();
				return $this->errorResponse('No se ha podido crear los archivos, verifica que las imagenes no esten dañadas');
			}
			// update only help question
			$record->update(['help_question' => $decodeImgObject->richText]);
			$response = $record->only('id_question', 'question', 'order', 'id_question_type');
			DB::commit();
			return $this->successResponse($response);
		} catch (\Throwable $th) {
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
	public function destroy($idForm, $id) 
	{
		try {
			DB::beginTransaction();
			$record = Question::findOrFail($id);
			$validate = $this->validateRequest('destroy', $idForm, null, $record);
			if (!$validate['success']) {
				DB::rollback();
				return $this->errorResponse($validate['messages']);
			}
			$handler = new HandlerChangeQuestions();
			$destroy = $handler->canModifyQuestion($record, null, 'destroy');
			if (!$destroy['success']) {
				DB::rollback();
				return $this->errorResponse($destroy['messages']);
			}
			$record->delete();
			DB::commit();
			return $this->successResponse($record);
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
	public function status($idForm, $id) 
	{
		try {
			$record = Question::findOrFail($id);
			$validate = $this->validateRequest('status', $idForm, null, $record);
			if (!$validate['success']) {
				return $this->errorResponse($validate['messages']);
			}
			$status = $record->id_status == 1 ? 2 : 1;
			$record->update(['id_status' => $status]);
			return $this->successResponse($record);
		} catch(\Throwable $th) {
			throw $th;
		}
	}

	/**
	 * Clear request fields 
	 */
	private function validateRequest($method, $idForm, $request, $record = null) 
	{
		$data['success'] = true;

		$allowed = [
      'order', 'question', 'help_question', 'allow_multiple_answers', 
      'id_question_type', 'id_state', 'id_city',
		];

		$fields = collect($request)->only($allowed);
		$form = Form::findOrFail($idForm);

		if ($method == 'store') {
			$fields->put('form_id', $form->id)
				->put('id_matter', $form->matter_id)
				->put('id_aspect', $form->aspect_id);
		}

		if ($method == 'update' || $method == 'destroy' || $method == 'status') {
			$sameForm = $form->id_form == $record->id_form;
			if (!$sameForm) {
				$data['success'] = false;
				$data['messages'] = 'La pregunta no pertenece al Formulario que especifica';
				return $data;
			}
			if ($method == 'destroy' || $method == 'status') return $data; 
		}

		$questionType = $request['id_question_type'];
		if ($questionType == QuestionType::FEDERAL) {
			$fields->put('id_state', null)->put('id_city', null);
		}
		if ($questionType == QuestionType::STATE) {
			$fields->put('id_city', null);
		}
		
		$data['request'] = $fields->toArray();
		return $data;
	}
}
