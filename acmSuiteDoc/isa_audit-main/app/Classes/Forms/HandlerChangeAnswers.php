<?php

namespace App\Classes\Forms;

use App\Models\V2\Audit\ProcessAudit;
use App\Models\V2\Catalogs\AnswersQuestion;

class HandlerChangeAnswers
{
  /**
   * constructor
   */
  public function __construct()
  {

  }

  /**
	 * Verify if can update answer
	 *
	 * @param \App\Models\V2\Catalogs\AnswersQuestion $answer
	 * @param array $changes
	 * @return array
	 */
	public function canModifyAnswer($answer, $oldData = [], $method = null) 
	{
    if ( !is_null($method) && $method == 'update' ) {
			$oldIdAnswerValue = $oldData['id_answer_value'];
			$newIdAnswerValue = $answer->id_answer_value;
			if ($oldIdAnswerValue == $newIdAnswerValue) {
				$data['success'] = true;
				$data['messages'] = 'No se debe actualizar datos de Respuesta';
				return $data;
			}
		}

    if ( !is_null($method) && $method == 'update' ) {
      $verify = $this->verifyInProcess($answer, $method);
      if ( !$verify['success'] ) {
        $data['success'] = false;
        $data['messages'] = $verify['messages'];
        return $data;
      }
			$update = $this->updateAnswerValue($answer->id_answer_question);
			$data['success'] = $update['success'];
			$data['messages'] = $update['messages'];
			return $data;
		}

    if ( !is_null($method) && $method == 'destroy' ) {
      $verify = $this->verifyInProcess($answer, $method);
      if ( !$verify['success'] ) {
        $data['success'] = false;
        $data['messages'] = $verify['messages'];
        return $data;
      }
			$destroy = $this->destroyContentAnswers([$answer]);
			$data['success'] = $destroy['success'];
			$data['messages'] = $destroy['messages'];
			return $data;
		}
  }

  /**
	 * Verify if can update answer
	 *
	 * @param \App\Models\V2\Catalogs\Answer $answer
	 * @param string $method
	 * @return array
	 */
	private function verifyInProcess($answer, $method)
  {
    $action = $method == 'update' ? ' actualizar ' : ' eliminar ';
    $fields = $method == 'update' ? ' el "Valor de Respuesta" ' : '';
		$relationships = 'aplicability_register.contract_matters.contract_aspects.evaluate_question.applicability';
		$condition = fn($query) => $query->where('id_answer_question', $answer->id_answer_question);
		$inProcess = ProcessAudit::whereHas($relationships, $condition);
		if ( $inProcess->exists() ) {
			$count = $inProcess->count();
			$data['success'] = false;
			$data['messages'] = "No es posible {$action}{$fields}ya que se usa en {$count} ejercicios";
			return $data;
		}
    $data['success'] = true;
    $data['messages'] = "Puede continuar";
    return $data;
  }

  /**
   * @param int $idAnswerQuestion
   */
  public function updateAnswerValue($idAnswerQuestion)
  {
    try {
      $relationships = ['requirements_assigned', 'subrequirements_assigned'];
      $answer = AnswersQuestion::with($relationships)->findOrFail($idAnswerQuestion);
      $destroyAnswer = $this->destroyContentAnswers( [$answer], 'requirements.subrequirements' );
      if ( !$destroyAnswer['success'] ) return $destroyAnswer;
    } catch (\Throwable $th) {
      $data['success'] = false;
      $data['messages'] = "No se puedo actualizar esta Respuesta";
      return $data; 
    }
    $data['success'] = true;
    $data['messages'] = "Puede continuar";
    return $data;
  }

  /**
   * Destroy answers and content
   * @param array $answers [App\Models\V2\Catalogs\AnswersQuestion, ...]
   * @param string $levelString
   * example: all|answers|requirements|subrequirements|dependencies
   */
  public function destroyContentAnswers( $answers, $levelString = 'all' )
  {
    try {
      $level = collect( explode('.', $levelString) );
      
      foreach ($answers as $answer) {
        // destroy only requirements
        if ( $level->contains('all') || $level->contains('answers') || $level->contains('requirements') || $level->contains('subrequirements') ) {
          // destroy sub per answer
          $destroySub = $this->destroyRelationRequirements('subrequirement', $answer);
          if ( !$destroySub['success'] ) return $destroySub;
          // destroy req per answer
          $destroyReq = $this->destroyRelationRequirements('requirement', $answer);
          if ( !$destroyReq['success'] ) return $destroyReq;
        }
        // destroy only dependencies
        if ( $level->contains('all') || $level->contains('answers') || $level->contains('dependencies') ) {
          $destroyDep = $this->destroyRelationDependencies($answer);
          if ( !$destroyDep['success'] ) return $destroyDep;
        }
        // destroy only answer
        if ( $level->contains('all') || $level->contains('answers') || $level->contains('answers') ) {
          $answer->delete();
        }
      }

      $data['success'] = true;
      $data['messages'] = 'Eliminación exitosa';
      return $data;
    } catch (\Throwable $th) {
      $data['success'] = false;
      $data['messages'] = "Algo salio mal al eliminar los recursos (answers)";
      return $data;
    }
  }

  /**
   * Destroy relations requirements and questions
   * @param string $type
   * @param App\Models\V2\Catalogs\AnswersQuestion $record
   */
  public function destroyRelationRequirements($type, $record)
  {
    try {
      $types = collect(['requirement', 'subrequirement']);
      if ( !$types->contains($type) ) {
        $data['success'] = false;
        $data['messages'] = "No se pueden eliminar relacion del recurso ({$type})";
        return $data; 
      }
      
      if ($type == 'requirement') {
        $detachRequirement = $record->requirements_assigned->pluck('id_requirement')->toArray();
        $record->requirements_assigned()->detach($detachRequirement);
      }

      if ($type == 'subrequirement') {
        $detachSubrequirement = $record->subrequirements_assigned->pluck('id_subrequirement')->toArray();
        $record->subrequirements_assigned()->detach($detachSubrequirement);
      }
      
      $data['success'] = true;
      $data['messages'] = 'Eliminación exitosa';
      return $data;
    } catch (\Throwable $th) {
      $data['success'] = false;
      $data['messages'] = "Algo salio mal al eliminar los recursos ({$type})";
      return $data;
    }
  }

  /**
   * Destroy relations dependencies and answers questions
   * @param App\Models\V2\Catalogs\AnswersQuestion $record
   */
  public function destroyRelationDependencies($record)
  {
    try {
      $detachQuestion = $record->dependency->pluck('id_question')->toArray();
      $record->dependency()->detach($detachQuestion);

      $data['success'] = true;
      $data['messages'] = 'Eliminación exitosa';
      return $data;
    } catch (\Throwable $th) {
      $data['success'] = false;
      $data['messages'] = "Algo salio mal al eliminar los recursos (dependency)";
      return $data;
    }
  }
}