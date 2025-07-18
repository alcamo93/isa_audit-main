<?php

namespace App\Classes\Forms;

use App\Models\V2\Audit\ProcessAudit;
use App\Models\V2\Catalogs\Question;
use App\Classes\Forms\HandlerChangeArticles;
use App\Classes\Forms\HandlerChangeAnswers;

class HandlerChangeQuestions
{
  /**
   * constructor
   */
  public function __construct()
  {

  }

  /**
	 * Verify if can update question
	 *
	 * @param \App\Models\V2\Catalogs\Question $question
	 * @param array $changes
	 * @return array
	 */
	public function canModifyQuestion($question, $oldData = [], $method = null) 
	{
    if ( !is_null($method) && $method == 'update' ) {
			$oldIdQuestionType = $oldData['id_question_type'];
			$oldIdState = $oldData['id_state'];
      $oldIdCity = $oldData['id_city'];
			$newIdQuestionType = $question->id_question_type;
			$newIdState = $question->id_state;
      $newIdCity = $question->id_city;
			if ($oldIdQuestionType == $newIdQuestionType && $oldIdState == $newIdState && $oldIdCity == $newIdCity) {
				$data['success'] = true;
				$data['messages'] = 'No se debe actualizar datos de Preguntas o Requerimientos';
				return $data;
			}
		}

    if ( !is_null($method) && $method == 'update' ) {
      $verify = $this->verifyInProcess($question, $method);
      if ( !$verify['success'] ) {
        $data['success'] = false;
        $data['messages'] = $verify['messages'];
        return $data;
      }
			$update = $this->updateQuestionType($question->id_question);
			$data['success'] = $update['success'];
			$data['messages'] = $update['messages'];
			return $data;
		}

    if ( !is_null($method) && $method == 'destroy' ) {
      $verify = $this->verifyInProcess($question, $method);
      if ( !$verify['success'] ) {
        $data['success'] = false;
        $data['messages'] = $verify['messages'];
        return $data;
      }
			$destroy = $this->destroyContentQuestions([$question->id_question], 'legals.dependencies.answers');
			$data['success'] = $destroy['success'];
			$data['messages'] = $destroy['messages'];
			return $data;
		}
  }

  /**
	 * Verify if can update question
	 *
	 * @param \App\Models\V2\Catalogs\Question $question
	 * @param string $method
	 * @return array
	 */
  private function verifyInProcess($question, $method)
  {
    $action = $method == 'update' ? ' actualizar ' : ' eliminar ';
    $fields = $method == 'update' ? ' algunos de los campos: "Tipo de pregunta/Estado/Ciudad" ' : '';
		$relationships = 'aplicability_register.contract_matters.contract_aspects.evaluate_question';
		$condition = fn($query) => $query->where('id_question', $question->id_question);
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
   * @param int $idQuestion
   */
  public function updateQuestionType($idQuestion) 
  {
    try {
      $question = Question::findOrFail($idQuestion);

      $handlerArticles = new HandlerChangeArticles();
      $destroy = $handlerArticles->destroyRelationArticles('question', $question);
      if ( !$destroy['success'] ) return $destroy;

      $answers = $question->answers;
      $handlerAnswers = new HandlerChangeAnswers();
      $destroyAnswer = $handlerAnswers->destroyContentAnswers( $answers, 'legals.requirements.subrequirements' );
      if ( !$destroyAnswer['success'] ) return $destroyAnswer;

    } catch (\Throwable $th) {
      $data['success'] = false;
      $data['messages'] = "No se puedo actualizar esta Pregunta";
      return $data; 
    } 
  }

  /**
   * @param array $questionsIds
   * @param string $levelString
   * example: all|legals|relationReq|questions|dependencies|answers|requirements
   */
  public function destroyContentQuestions($questionsIds, $levelString = 'all')
  {
    try {
      $level = collect( explode('.', $levelString) );
    
      $questions = Question::with('articles')->whereIn('id_question', $questionsIds)->get();
      // destroy legals
      if ( $level->contains('all') || $level->contains('questions') || $level->contains('legals') ) {
        foreach ($questions as $question) {
          $handlerArticles = new HandlerChangeArticles();
          $destroy = $handlerArticles->destroyRelationArticles('question', $question);
          if ( !$destroy['success'] ) return $destroy;
        }
      }
      // destroy answers and content
      if ( $level->contains('all') || $level->contains('questions') || $level->contains('answers') || $level->contains('requirements') || $level->contains('dependencies') ) {
        foreach ($questions as $question) {
          $answers = $question->answers;
          $handlerAnswers = new HandlerChangeAnswers();
          $destroyAnswer = $handlerAnswers->destroyContentAnswers( $answers );
          if ( !$destroyAnswer['success'] ) return $destroyAnswer;
        }
      }
      // destroy question
      if ( $level->contains('all') ) {
        Question::whereIn('id_question', $questionsIds)->delete();
      }

      $data['success'] = true;
      $data['messages'] = 'Eliminación exitosa';
      return $data;
    } catch (\Throwable $th) {
      $data['success'] = false;
      $data['messages'] = "No se pueden eliminar el recurso (questions)";
      return $data; 
    }
  }
}