<?php

namespace App\Traits\V2;

use App\Models\V2\Catalogs\Question;
use App\Models\V2\Catalogs\Requirement;
use App\Models\V2\Catalogs\Subrequirement;

trait OrderByAspect
{
  /**
   * For questions
   */
  public function orderQuestions($idQuestion, $questionData)
  {
    $order = $questionData['order'];
    $formId = $questionData['IdForm'];
    $questions = Question::where('form_id', $formId)
      ->orderBy('order', 'asc')->get();
    $this->verifyOrder($questions);
    $this->questionInNewOrder($questions, $idQuestion, $order);
  }

  public function questionInNewOrder($questions, $idQuestion, $newPosition) 
  {
    $currentQuestion = $questions->firstWhere('id_question', $idQuestion);
    $oldPosition = $currentQuestion->order;
    $newPosition = intval($newPosition);
    $max = $questions->max('order');
    if ($newPosition > $max) $newPosition = $max;
    if ($newPosition == $oldPosition) return;

    $toAdd = $newPosition > $oldPosition;
    $actionSearch = ( $toAdd ) ? '<=' : '>=';

    $updates = $questions->where('order', $actionSearch, $newPosition)
      ->where('id_question', '!=', $idQuestion);
    
    foreach ($updates as $update) {
      if ( $toAdd ) $update->decrement('order', 1);
			else $update->increment('order', 1);
    }
    $currentQuestion->update(['order' => $newPosition]);
    $this->verifyOrder($questions);
  }

  /**
   * For requirements
   */
  public function orderRequirement($idRequirement, $questionData)
  {
    $isEspecific = !is_null($questionData['idCustomer']) && !is_null($questionData['idCorporate']);
    $order = $questionData['order'];
    if ($isEspecific) {
      $requirements = Requirement::where('id_customer', $questionData['idCustomer'])
        ->where('id_corporate', $questionData['idCorporate'])->orderBy('order', 'asc')->get();
    } 
    else {
      $formId = $questionData['IdForm'];
      $requirements = Requirement::where('form_id', $formId)
        ->orderBy('order', 'asc')->get();
    }
    $this->verifyOrder($requirements);
    $this->requirementInNewOrder($requirements, $idRequirement, $order);
  }

  public function requirementInNewOrder($requirements, $idRequirement, $newPosition) 
  {
    $currentRequirement = $requirements->firstWhere('id_requirement', $idRequirement);
    $oldPosition = $currentRequirement->order;
    $newPosition = intval($newPosition);
    $max = $requirements->max('order');
    if ($newPosition > $max) $newPosition = $max;
    if ($newPosition == $oldPosition) return;

    $toAdd = $newPosition > $oldPosition;
    $actionSearch = ( $toAdd ) ? '<=' : '>=';

    $updates = $requirements->where('order', $actionSearch, $newPosition)
      ->where('id_requirement', '!=', $idRequirement)->sortBy('order');
    
    foreach ($updates as $update) {
      if ( $toAdd ) $update->decrement('order', 1);
			else $update->increment('order', 1);
    }

    $currentRequirement->update(['order' => $newPosition]);

    $this->verifyOrder($requirements);
  }

  /**
   * For requirements
   */
  public function orderSubrequirement($idRequirement, $idSubrequirement, $requirementData)
  {
    $order = $requirementData['order'];
    $subrequirements = Subrequirement::where('id_requirement', $idRequirement)
      ->orderBy('order', 'asc')->get();
    $this->verifyOrder($subrequirements);
    $this->subrequirementInNewOrder($subrequirements, $idSubrequirement, $order);
  }

  public function subrequirementInNewOrder($subrequirements, $idSubrequirement, $newPosition) 
  {
    $currentRequirement = $subrequirements->firstWhere('id_subrequirement', $idSubrequirement);
    $oldPosition = $currentRequirement->order;
    $newPosition = intval($newPosition);
    $max = $subrequirements->max('order');
    if ($newPosition > $max) $newPosition = $max;
    if ($newPosition == $oldPosition) return;

    $toAdd = $newPosition > $oldPosition;
    $actionSearch = ( $toAdd ) ? '<=' : '>=';

    $updates = $subrequirements->where('order', $actionSearch, $newPosition)
      ->where('id_subrequirement', '!=', $idRequirement);
    
    foreach ($updates as $update) {
      if ( $toAdd ) $update->decrement('order', 1);
			else $update->increment('order', 1);
    }
    $currentRequirement->update(['order' => $newPosition]);

    $this->verifyOrder($subrequirements);
  }

  /**
   * General
   */
  public function verifyOrder($records) {
    $currentListOrder = $records->pluck('order');
    $orderCorrect = $this->orderIsConsecutive($currentListOrder);
    $this->execOrderConsecutive($orderCorrect, $records);
  }

  public function orderIsConsecutive($collection) {
    $array = $collection->toArray();
    $min = $collection->min();
    $max = $collection->max();
    $range = range($min, $max);
    return $array == $range;
  }

  public function execOrderConsecutive($orderCorrect, $records) {
    if ( $orderCorrect ) return;
    $newOrder = 1;
    foreach ($records as $record) {
      $record->update(['order' => $newOrder]);
      $newOrder++;
    }
  }
}