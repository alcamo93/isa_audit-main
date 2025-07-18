<?php

namespace App\Http\Controllers\V2\Audit;

use App\Classes\Applicability\HandlerAnswerApplicability;
use App\Classes\Applicability\StateSurvey;
use App\Http\Controllers\Controller;
use App\Http\Requests\ApplicabilityEvaluateRequest;
use App\Models\V2\Audit\Aplicability;
use App\Models\V2\Audit\Audit;
use App\Models\V2\Audit\ContractAspect;
use App\Models\V2\Audit\ContractMatter;
use App\Models\V2\Audit\EvaluateApplicabilityQuestion;
use App\Models\V2\Audit\ProcessAudit;
use App\Traits\V2\ResponseApiTrait;
use Illuminate\Support\Arr;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class AplicabilityController extends Controller
{
  use ResponseApiTrait;

  /**
   * Display a listing of the resource.
   * @param int $idAuditProcess
   * @param int $idAplicabilityRegister
   * @param int $idContractMatter
   * @param int $idContractAspect
   * @return \Illuminate\Http\Response
   * 
   * Params used in Middleware route
   */
  public function index($idAuditProcess, $idAplicabilityRegister, $idContractMatter, $idContractAspect)
  {
    $process = ProcessAudit::findOrFail($idAuditProcess);
    $contractAspect = ContractAspect::with(['application_type', 'status', 'matter', 'aspect'])->findOrFail($idContractAspect);
    
    $relationships = ['question.answers', 'comment', 'applicability.answer_question'];
    $questions = EvaluateApplicabilityQuestion::with($relationships)->where('id_contract_aspect', $idContractAspect)->customOrderEvaluateQuestion()->get();
    $formIsComplete = $questions->filter(fn($item) => $item->applicability->isEmpty())->isEmpty();

    $stateSurvey = new StateSurvey($idContractAspect);
    $data['data'] = $stateSurvey->verifyQuestions($questions)->toArray();

    $data['info']['audit_process'] = $process->audit_processes;
    $data['info']['customer_name'] = $process->customer->cust_tradename;
    $data['info']['corporate_name'] = $process->corporate->corp_tradename;
    $data['info']['matter'] = $contractAspect->matter->matter;
    $data['info']['aspect'] = $contractAspect->aspect->aspect;
    $data['info']['scope'] = $process->scope->scope;
    $data['info']['application_type'] = $contractAspect->application_type->application_type ?? '---';
    $data['info']['aspect_status'] = $contractAspect->status;
    $data['info']['complete'] = $formIsComplete;

    return $this->successResponse($data);
  }

  /**
	 * Store/update answer in audit.
	 *
	 * @param App\Http\Requests\ApplicabilityEvaluateRequest $request
   * @param int $idAuditProcess
   * @param int $idAplicabilityRegister
   * @param int $idContractMatter
   * @param int $idContractAspect
   * @param int $idEvaluateQuestion
	 * @return \Illuminate\Http\Response
   * 
   * Params used in Middleware route
	 */
  public function answer(ApplicabilityEvaluateRequest $request, $idAuditProcess, $idAplicabilityRegister, $idContractMatter, $idContractAspect, $idEvaluateQuestion)
  {
    try {
      DB::beginTransaction();
      $handlerAnswerApplicability = new HandlerAnswerApplicability($idEvaluateQuestion, $request->all());
      $setAnswers = $handlerAnswerApplicability->setApplicabilityAnswer();
      if ( !$setAnswers['success'] ) {
        DB::rollback();
        return $setAnswers;
      }

      $data['values'] = $request->all();
      $data['id_evaluate_question'] = $idEvaluateQuestion;
      DB::commit();
      return $this->successResponse($data);
    } catch (\Throwable $th) {
      DB::rollback();
      throw $th;
    }
  }

  /**
	 * Store/update answer in audit.
	 *
	 * @param App\Http\Requests\ApplicabilityEvaluateRequest $request
   * @param int $idAuditProcess
   * @param int $idAplicabilityRegister
   * @param int $idContractMatter
   * @param int $idContractAspect
   * @param int $idEvaluateQuestion
	 * @return \Illuminate\Http\Response
   * 
   * Params used in Middleware route
	 */
  public function comment(ApplicabilityEvaluateRequest $request, $idAuditProcess, $idAplicabilityRegister, $idContractMatter, $idContractAspect, $idEvaluateQuestion)
  {
    try {
      $contractAspect = ContractAspect::find($idContractAspect);
      if ( $contractAspect->id_status == Aplicability::FINISHED_APLICABILITY ) {
        $info['success'] = false;
        $info['message'] = 'El aspecto ha sido finalizado, no se puede modificar';
        return $info;
      }

      $comment = $request->input('comment');
      $relationships = ['question', 'applicability', 'comment'];
      $evaluateQuestion = EvaluateApplicabilityQuestion::with($relationships)->findOrFail($idEvaluateQuestion);
      
      $commentData = ['comment' => $comment, 'id_user' => Auth::id()];

      if ( is_null($evaluateQuestion->comment) ) {
        $evaluateQuestion->comment()->create($commentData);
      } else {
        $evaluateQuestion->comment()->update($commentData);
      }

      return $this->successResponse($commentData);
    } catch (\Throwable $th) {
      throw $th;
    }
  }

  /**
   * Verify data
   */
  private function verifyRequest($method, $idContractMatter, $idAuditEvaluate = null, $data = null)
  {
    try {
      if ($method == 'index') {
        $response['success'] = true;
        return $response;
      }

      if ($method == 'show') {
        $hasAnswer = $this->hasAnswer($idAuditEvaluate);
        return $hasAnswer;
      }

      $contractMatter = ContractMatter::findOrFail($idContractMatter);
      if ($contractMatter->id_status == Audit::FINISHED_AUDIT_AUDIT) {
        $statusName = $contractMatter->status->status;
        $response['success'] = false;
        $response['message'] = "El aspecto esta en estatus {$statusName}, no es posible modificar el registro";
        return $response;
      }

      if ($method == 'finding') {
        $hasAnswer = $this->hasAnswer($idAuditEvaluate);
        if ( !$hasAnswer ) return $hasAnswer;
        $request = Arr::only($data, ['finding']);
        $response['success'] = true;
        $response['request'] = $request;
        return $response;
      }

      if ($method == 'answer') {
        $hasAnswer = $this->hasAnswer($idAuditEvaluate);
        if ( !$hasAnswer ) return $hasAnswer;
        $request = Arr::only($data, ['answer', 'recursive']);
        if ( $request['answer'] != Audit::NO_APPLY && $request['recursive'] ) {
          $request['recursive'] = false;
        }
        $response['success'] = true;
        $response['request'] = $request;
        return $response;
      }

      if ($method == 'images') {
        $hasAnswer = $this->hasAnswer($idAuditEvaluate);
        if ( !$hasAnswer ) return $hasAnswer;
        $isNegative = $hasAnswer['audit_evaluate']['audit']['answer'] == Audit::NEGATIVE;
        if ( !$isNegative ) {
          $response['success'] = false;
          $response['message'] = 'Solo se puede agregar evidencias cuando la respueta es No Cumple';
          return $response;
        }
        $response['success'] = true;
        $response['request'] = $isNegative;
        return $response;
      }
      
      $response['success'] = true;
      $response['message'] = 'Registro verificado correctamente';
      return $response;
    } catch (\Throwable $th) {
      $response['success'] = false;
      $response['message'] = 'Algo salio mal.';
      return $response;
    }
  }

  private function hasAnswer($idEvaluateQuestion)
  {
    $questionEvaluate = EvaluateApplicabilityQuestion::find($idEvaluateQuestion);
    if ( is_null($questionEvaluate->audit) ) {
      $response['success'] = false;
      $response['message'] = 'No se cuenta con respuesta, por favor primero elige una respuesta';
      return $response;
    }
    $response['success'] = true;
    $response['message'] = 'Ya cuenta con una respuesta';
    $response['audit_evaluate'] = $questionEvaluate;
    return $response;
  }
}