<?php

namespace App\Classes\Forms;

use Illuminate\Support\Facades\DB;
use App\Models\V2\Catalogs\Form;

class ReplicateForm
{
  // forms
  protected $formId, $oldForm, $newForm;
  // questions
  protected $oldQuestions = [], $oldAnswers = [];
  // requirements
  protected $oldRequirement = [], $oldSubrequirements = [];
  
  protected $relationships = [
    // step 1 replicate: replicateForm
    // step 2 hasMany: replicateRequirements
    'requirements.articles', // step 3 pivot: replicateRequirementLegal
    'requirements.recomendations', // step 4 hasMany: replicateRequirementRecomendations
    'requirements.subrequirements', // step 5 hasMany: replicateSubrequirements
    'requirements.subrequirements.articles', // step 6 pivot: replicateSubrequirementLegal
    'requirements.subrequirements.recomendations', // step 7 hasMany: replicateSubrequirementRecomendations
    // step 8 hasMany: replicateQuestions
    'questions.articles', // step 9 pivot: replicateQuestionLegal
    'questions.answers', // step 10 hasMany: replicateAnswers 
    // step 11 replicate: replicateCustom
    'questions.answers.dependency', // step 12 pivot: replicateDependency
    'questions.answers.requirements_assigned', // step 13 pivot: replicateRequirementsAssigned
    'questions.answers.subrequirements_assigned', // step 14 pivot: replicateSubrequirementsAssigned
  ];

  public function __construct($formId)
  {
    $this->formId = $formId;
  }

  public function replicateForm() 
  {
    DB::beginTransaction();
    try {
      $this->oldForm = Form::with($this->relationships)->findOrFail($this->formId);
      $lastVersionForm = $this->getLastVersion();
      $newForm = $this->oldForm->replicate()->fill([
        'name' => "{$this->oldForm->name} - V{$lastVersionForm}",
        'is_current' => false,
        'version' => $lastVersionForm,
        'id_track' => $this->oldForm->id_track,
      ]);
      $newForm->save();
      $this->newForm = $newForm;
      $this->replicateRequirements($this->oldForm->requirements);
      $this->replicateQuestions($this->oldForm->questions);
      $this->replicateCustom();
      DB::commit();
      return $this->newForm;
    } catch (\Throwable $th) {
      DB::rollback();
      throw $th;
    }
  }

  private function replicateRequirements($requirements)
  {
    foreach ($requirements as $requirement) {
      $newRequirement = $requirement->replicate()->fill(['form_id' => $this->newForm->id]);
      $newRequirement->save();
      $relationReplicate = [
        'requirement_old_id' => $requirement->id_requirement,
        'requirement_new_id' => $newRequirement->id_requirement
      ];
      array_push($this->oldRequirement, $relationReplicate);
      $this->replicateRequirementLegal($requirement->articles, $newRequirement);
      $this->replicateRequirementRecomendations($requirement->recomendations, $newRequirement->id_requirement);
      $this->replicateSubrequirements($requirement->subrequirements, $newRequirement->id_requirement);
    }
  }

  private function replicateRequirementLegal($articles, $requirement)
  {
    $valuesAttach = $articles->pluck('id_legal_basis');
    $requirement->articles()->attach($valuesAttach);
  }

  private function replicateRequirementRecomendations($recomendations, $requirementNewId)
  {
    foreach ($recomendations as $recomendation) {
      $newRecomendation = $recomendation->replicate()->fill(['id_requirement' => $requirementNewId]);
      $newRecomendation->save();
      
    }
  }

  private function replicateSubrequirements($subrequirements, $requirementNewId)
  {
    foreach ($subrequirements as $subrequirement) {
      $newSubrequirement = $subrequirement->replicate()->fill(['id_requirement' => $requirementNewId]);
      $newSubrequirement->save();
      $relationReplicate = [
        'requirement_new_id' => $requirementNewId,
        'subrequirement_old_id' => $subrequirement->id_subrequirement,
        'subrequirement_new_id' => $newSubrequirement->id_subrequirement,
        'instance_new_subrequirement' => $newSubrequirement,
        'instance_old_subrequirement' => $subrequirement,
      ];
      array_push($this->oldSubrequirements, $relationReplicate);
      $this->replicateSubrequirementLegal($subrequirement->articles, $newSubrequirement);
      $this->replicateSubrequirementRecomendations($subrequirement->recomendations, $newSubrequirement->id_subrequirement);
    }
  }

  private function replicateSubrequirementLegal($legals, $subrequirement)
  {
    $valuesAttach = $legals->pluck('id_legal_basis');
    $subrequirement->articles()->attach($valuesAttach);
  }

  private function replicateSubrequirementRecomendations($recomendations, $newSubrequirementId)
  {
    foreach ($recomendations as $recomendation) {
      $newRecomendation = $recomendation->replicate()->fill(['id_subrequirement' => $newSubrequirementId]);
      $newRecomendation->save();
    }
  }

  private function replicateQuestions($questions)
  {
    foreach ($questions as $question) {
      $newQuestion = $question->replicate()->fill(['form_id' => $this->newForm->id]);
      $newQuestion->save();
      $relationReplicate = [
        'question_old_id' => $question->id_question,
        'question_new_id' => $newQuestion->id_question
      ];
      array_push($this->oldQuestions, $relationReplicate);
      $this->replicateQuestionLegal($question->articles, $newQuestion);
      $this->replicateAnswers($question->answers, $newQuestion->id_question);
    }
  }

  private function replicateCustom()
  {
    foreach ($this->oldAnswers as $item) {
      $this->replicateDependency($item);
      $this->replicateRequirementsAssigned($item);
      $this->replicateSubrequirementsAssigned($item);
    }
  }

  private function replicateAnswers($answers, $questionNewId) 
  {
    foreach ($answers as $answer) {
      $newAnswer = $answer->replicate()->fill(['id_question' => $questionNewId]);
      $newAnswer->save();
      $relationReplicate = [
        'answer_question_old_id' => $answer->id_answer_question,
        'answer_question_new_id' => $newAnswer->id_answer_question,
        'instance_new_answer_question' => $newAnswer,
        'instance_old_answer_question' => $answer,
      ];
      array_push($this->oldAnswers, $relationReplicate);
    }
  }

  private function replicateDependency($answer)
  {
    $relationQuestionCollect = collect($this->oldQuestions);
    $oldQuestions = $answer['instance_old_answer_question']->dependency->pluck('id_question');
    $findOldQuestions = $relationQuestionCollect->whereIn('question_old_id', $oldQuestions)->values();
    $findNewQuestions = $findOldQuestions->pluck('question_new_id')->values();
    $answer['instance_new_answer_question']->dependency()->attach($findNewQuestions);
  }

  private function replicateRequirementsAssigned($answer)
  {
    $oldRequirements = $answer['instance_old_answer_question']->requirements_assigned->pluck('id_requirement');
    $relationRequirementCollect = collect($this->oldRequirement);
    $findOldRequirements = $relationRequirementCollect->whereIn('requirement_old_id', $oldRequirements)->values();
    $findNewRequirements = $findOldRequirements->pluck('requirement_new_id')->values();
    $attachValues = [];
    foreach ($findNewRequirements as $requirementId) {
      $attachValues[$requirementId] = [
        'id_question' => $answer['instance_new_answer_question']->id_question,
        'id_subrequirement' => null
      ];
    }
    $answer['instance_new_answer_question']->requirements_assigned()->attach($attachValues);
  }

  private function replicateSubrequirementsAssigned($answer)
  {
    $oldSubrequirements = $answer['instance_old_answer_question']->subrequirements_assigned->pluck('id_subrequirement');
    $relationSubrequirementCollect = collect($this->oldSubrequirements);
    $findOldSubrequirements = $relationSubrequirementCollect->whereIn('subrequirement_old_id', $oldSubrequirements)->values();
    $findNewSubrequirements = $findOldSubrequirements->pluck('subrequirement_new_id')->values();
    $attachValues = [];
    foreach ($findNewSubrequirements as $subrequirementId) {
      $newRequirement = $findOldSubrequirements->firstWhere('subrequirement_new_id', $subrequirementId);
      $attachValues[$subrequirementId] = [
        'id_question' => $answer['instance_new_answer_question']->id_question,
        'id_requirement' => $newRequirement['requirement_new_id']
      ];
    }
    $answer['instance_new_answer_question']->subrequirements_assigned()->attach($attachValues);
  }

  private function replicateQuestionLegal($articles, $question) 
  {
    $valuesAttach = $articles->pluck('id_legal_basis');
    $question->articles()->attach($valuesAttach);
  }

  private function getLastVersion()
  {
    $aspectId = $this->oldForm->aspect_id;
    $forms = Form::where('aspect_id', $aspectId)->get();
    $maxVersion = $forms->unique()->pluck('version')->max();
    $lastVersionForm = is_null($maxVersion) ? 1 : $maxVersion + 1;
    return $lastVersionForm;
  }
}