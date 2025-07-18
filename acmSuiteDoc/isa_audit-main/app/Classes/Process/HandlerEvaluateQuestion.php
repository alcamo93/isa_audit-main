<?php

namespace App\Classes\Process;

use App\Models\V2\Admin\Address;
use App\Models\V2\Audit\Aplicability;
use App\Models\V2\Catalogs\Question;
use App\Models\V2\Audit\EvaluateApplicabilityQuestion;

class HandlerEvaluateQuestion
{
  protected $process = null;
  protected $address = null;
  
  /**
   * 
   * @param App\Models\V2\Audit\ProcessAudit $process
   * 
   */
  public function __construct($process)
  {
    $this->process = $process->refresh();
    $this->address = $this->getAddress();
  }

  /**
   * get address for filter question
   */
  private function getAddress()
  {
    return $this->process->corporate->addresses
      ->where('type', Address::PHYSICAL)->first();
  }

  /**
   * set question per aspect in evalluate
   */
  public function handlerQuestions()
  {
    if ( is_null($this->address) ) {
      $info['success'] = false;
      $info['message'] = 'La Planta a la que se hace referencia en la evaluación no tiene una dirección física';
      return $info;
    }
    $this->process->refresh();
    $contractMatters = $this->process->aplicability_register->contract_matters;
    $contractAspects = $contractMatters->flatMap(fn($item) => $item['contract_aspects']);

    foreach ($contractAspects as $aspect) {
      $setEvaluates = $this->setEvaluateQuestion($aspect);
      if ( !$setEvaluates['success'] ) return $setEvaluates;
    }

    $info['success'] = true;
    $info['message'] = 'Preguntas de aspecto registradas exitosamente';
    return $info;
  }

  /**
   * set values question per aspect
   * 
   * @param App\Models\V2\Audit\ContractAspect $aspect
   */
  public function setEvaluateQuestion($aspect)
  {
    try {
      $this->process->refresh();
      // get question per aspect
      $questioIds = Question::where('form_id', $aspect->form_id)->where('id_status', Question::ACTIVE)->where(function ($query) {
        $query->whereNull('id_state')->whereNull('id_city')
          ->orWhere( fn($query) => $query->where('id_state', $this->address->id_state)->whereNull('id_city') )
          ->orWhere( fn($query) => $query->where('id_state', $this->address->id_state)->where('id_city', $this->address->id_city) );
      })->customOrderQuestion()->pluck('id_question');
      // build structure per question evaluate
      $buildEvaluate = $questioIds->map(function($idQuestion) use ($aspect) {
        return [
          'id_contract_aspect' => $aspect->id_contract_aspect,
          'id_question' => $idQuestion
        ];
      });
      // create or update
      $buildEvaluate->each(function($item) {
        EvaluateApplicabilityQuestion::updateOrCreate($item, $item);
      });

      $info['success'] = true;
      $info['message'] = 'Preguntas de aspecto registradas exitosamente';
      return $info;
    } catch (\Throwable $th) {
      $info['success'] = false;
      $info['message'] = "Algo salio mal al registrar preguntas a evaluar en un aspecto";
      return $info;
    }
  }

  /**
   * set values question per aspect
   * 
   * @param Array App\Models\V2\Audit\ContractAspect $contractAspectIds
   */
  public function removeAplicabilityAnswer($contractAspectIds)
  {
    try {
      $this->process->refresh();
      $itemsRemove = Aplicability::whereIn('id_contract_aspect', $contractAspectIds);
      $itemsRemove->delete();
      
      $info['success'] = true;
      $info['message'] = 'Respuestas de aplicabilidad eliminadas exitosamente';
      return $info;
    } catch (\Throwable $th) {
      $info['success'] = false;
      $info['message'] = "Algo salio mal al remover respuestas de aplicabilidad";
      return $info;
    }
  }
}