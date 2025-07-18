<?php

namespace App\Http\Controllers\V2\Catalogs;

use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\DB;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use App\Http\Requests\AnswerQuestionRequest;
use App\Models\V2\Catalogs\AnswersQuestion;
use App\Traits\V2\ResponseApiTrait;
use App\Classes\Forms\HandlerChangeAnswers;

class AnswerQuestionController extends Controller
{
  use ResponseApiTrait;

  /**
	 * Display a listing of the resource.
	 *
	 * @return \Illuminate\Http\Response
	 */
	public function index($idForm, $idQuestion)
	{
		try {
			$data = AnswersQuestion::included()->withIndex()->filter()->customFilters($idQuestion)->customOrder()->getOrPaginate();
			return $this->successResponse($data);
		} catch(\Throwable $th) {
			throw $th;
		}
	}
	
	/**
	 * Store a newly created resource in storage.
	 *
	 * @param  App\Http\Requests\AnswerQuestionRequest $request
	 * @return \Illuminate\Http\Response
	 */
	public function store(AnswerQuestionRequest $request, $idForm, $idQuestion)
	{
		try {
			DB::beginTransaction();
			$validate = $this->validateRequest('store', $idQuestion, $request->all());
			if (!$validate['success']) {
				DB::rollback();
				return $this->errorResponse($validate['messages']);
			}
			$record = AnswersQuestion::create($validate['request']);
			$record->update(['id_track' => $record->id_answer_question]);
			DB::commit();
			return $this->successResponse($record, Response::HTTP_CREATED);
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
	public function show($idForm, $idQuestion, $idAnswerQuestion)
	{
		try {
			$data = AnswersQuestion::findOrFail($idAnswerQuestion);
			return $this->successResponse($data);
		} catch(\Throwable $th) {
			throw $th;
		}
	}

	/**
	 * Update the specified resource in storage.
	 *
	 * @param  App\Http\Requests\AnswerQuestionRequest $request
	 * @param  int  $id
	 * @return \Illuminate\Http\Response
	 */
	public function update(AnswerQuestionRequest $request, $idForm, $idQuestion, $idAnswerQuestion)
	{
		try {
			DB::beginTransaction();
			$record = AnswersQuestion::findOrFail($idAnswerQuestion);
			$validate = $this->validateRequest('update', $idQuestion, $request->all());
			if (!$validate['success']) {
				DB::rollback();
				return $this->errorResponse($validate['messages']);
			}
			$handler = new HandlerChangeAnswers();
			$destroy = $handler->canModifyAnswer($record, $validate['request'], 'update');
			if (!$destroy['success']) {
				DB::rollback();
				return $this->errorResponse($destroy['messages']);
			}
			$record->update($validate['request']);
			DB::commit();
			return $this->successResponse($record);
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
	public function destroy($idForm, $idQuestion, $idAnswerQuestion) 
	{
		try {
			DB::beginTransaction();
			$record = AnswersQuestion::findOrFail($idAnswerQuestion);
			$validate = $this->validateRequest('destroy', $idQuestion);
			if (!$validate['success']) {
				DB::rollback();
				return $this->errorResponse($validate['messages']);
			}
			$handler = new HandlerChangeAnswers();
			$destroy = $handler->canModifyAnswer($record, null, 'destroy');
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
	 * Clear request fields 
	 */
	private function validateRequest($method, $idQuestion, $request = null) 
	{
		$data['success'] = true;

		if ($method == 'store' || $method == 'update') {
			$allowed = ['order', 'description', 'id_answer_value'];
			$fields = collect($request)->only($allowed);
		}

		if ($method == 'store') {
			$fields->put('id_question', $idQuestion)->put('id_status', 1);
		}

		if ($method == 'destroy') return $data;
		
		$data['request'] = $fields->toArray();
		return $data;
	}
}
