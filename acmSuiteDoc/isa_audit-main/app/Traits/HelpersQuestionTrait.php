<?php

namespace App\Traits;

use App\Classes\StatusConstants;
use App\Models\Audit\AplicabilityModel;
use App\Models\Catalogues\AspectsModel;
use App\Models\Catalogues\QuestionsModel;
use App\Models\Catalogues\AnswersQuestionModel;
use App\Models\Catalogues\AnswerQuestionsDepedencyModel;
use Illuminate\Support\Facades\DB;

trait HelpersQuestionTrait {

    public function init($idQuestion) {
        $relationship = ['matter', 'aspect', 'type', 'answers.block'];
        $question = QuestionsModel::with($relationship)->findOrFail($idQuestion);
        $questionsPool =  QuestionsModel::where([
            ['form_id', $question->form_id],
            ['order', '>', $question->order],
            ['id_question_type', $question->id_question_type],
            ['id_status', StatusConstants::ACTIVE]
        ])->orderBy('order', 'asc')->get();
        
        $data['idQuestion'] = $idQuestion;
        $data['question'] = $question->toArray();
        $data['questionsPool'] = $questionsPool->toArray();
        return $data;
    }

    public function getPoolQuestion($idAnswerQuestion) {
        $answerQuestion = AnswersQuestionModel::with(['question'])->findOrFail($idAnswerQuestion);
        $questionsPool =  QuestionsModel::where([
            ['form_id', $answerQuestion->question->form_id],
            ['order', '>', $answerQuestion->question->order],
            ['id_question_type', $answerQuestion->question->id_question_type],
            ['id_status', StatusConstants::ACTIVE]
        ])->get()->pluck('id_question')->toArray();

        $poolDependency = [];
        foreach ($questionsPool as $key => $q) {
            array_push($poolDependency, [
                'id_question' => $q,
                'id_answer_question' => (int)$idAnswerQuestion
            ]);
        }
        return $poolDependency;
    }

    public function typeDependency($dependency) {
        if ( !is_null($dependency['id_question']) ) {
            $dependencyData = [
                [
                    'id_question' => $dependency['id_question'],
                    'id_answer_question' => $dependency['id_answer_question']
                ]
            ];
        }
        else {
            $dependencyData = $this->getPoolQuestion($dependency['id_answer_question']);
        }
        return $dependencyData;
    }

    public function setDependency($dependency) {
        try {
            if ( sizeof($dependency) > 1 ) {
                $dependencyCollect = collect($dependency);
                $records = AnswerQuestionsDepedencyModel::select('id_question', 'id_answer_question')
                    ->where('id_answer_question', $dependencyCollect->first()['id_answer_question'])->get()->toArray();
                $allInsert = $dependencyCollect->merge($records);
                $duplicates = $allInsert->duplicates('id_question');
                $dependency = $allInsert->whereNotIn('id_question', $duplicates)->toArray();
            }
            AnswerQuestionsDepedencyModel::insert($dependency);
            return StatusConstants::SUCCESS;
        } catch (\Throwable $th) {
            return StatusConstants::ERROR;
        }
    }

    public function removeDependency($dependency) {
        try {
            $dependency = collect($dependency);
            $dependencyQuestionPool = $dependency->pluck('id_question');
            AnswerQuestionsDepedencyModel::where('id_answer_question', $dependency->first()['id_answer_question'])
                ->whereIn('id_question', $dependencyQuestionPool)->delete();
            return StatusConstants::SUCCESS;
        } catch (\Throwable $th) {
            return StatusConstants::ERROR;
        }
    }

    public function getGroupsQuestionByAnswer($currentPosition, $idAnswerQuestion, $dependencyInFormParam) {
        // get questions null values to block questions
        $answerQuestion = AnswersQuestionModel::with('block')->find($idAnswerQuestion);
        $allowedQuestions = $answerQuestion->block->pluck('id_question')->toArray();
        $dependencyInForm = QuestionsModel::whereIn('id_question', $dependencyInFormParam)
            ->where('order', '>', $currentPosition)->get()->pluck('id_question')->toArray();
        $dependencyInForm = collect( $dependencyInForm );
        $blockedQuestions = $dependencyInForm->diff( $allowedQuestions )->values()->toArray();
        $data['blocked'] = $blockedQuestions;
        $data['allowed'] = $allowedQuestions;
        return $data;
    }

    public function previousQuestionDependency($idQuestion, $currentPosition, $questionType, $idContractAspect, $dependencyInForm, $dependencyFreeInForm, $questionInForm) {
        // Data next Current position in form
        $aplicabilityNext = AplicabilityModel::with('question')
            ->where('id_contract_aspect', $idContractAspect)
            ->whereHas('question', function($filter) use ($currentPosition) {
                $filter->where('order', '>', $currentPosition);
            })->get();
        $nextQuestionsNotNull = $aplicabilityNext->whereNotNull('id_answer_question')->pluck('id_question');
        $nextQuestionsNull = $aplicabilityNext->whereNull('id_answer_question')->pluck('id_question');
        // Data previous Current position in form
        $aplicability = AplicabilityModel::where('id_contract_aspect', $idContractAspect)->get();
        $aplicabilityNextQuestion = $aplicabilityNext->pluck('id_question');
        $aplicabilityNextAnswerQuestion = $aplicabilityNext->pluck('id_answer_question');
        $previousAnswerQuestion = $aplicability->pluck('id_answer_question')->diff($aplicabilityNextAnswerQuestion)->unique()->values();
        $previousQuestions = $aplicability->pluck('id_question')->diff($aplicabilityNextQuestion)->unique()->values();
        // Get question allow and block by answer and make only group
        $allAllowDependecy = collect([]);
        $allBlockDependecy = collect([]);
        foreach ($previousAnswerQuestion as $idAnswerQuestion) {
            if ( is_null( $idAnswerQuestion ) ) continue;
            $tmp = $this->getGroupsQuestionByAnswer($currentPosition, $idAnswerQuestion, $dependencyInForm);
            $allAllowDependecy->push($tmp['allowed']);
            $allBlockDependecy->push($tmp['blocked']);
        }
        $allAllowDependecy = $allAllowDependecy->collapse()->unique()->values();
        $allBlockDependecy = $allBlockDependecy->collapse()->unique()->values();
        // Get questions to unlock and blocked
        $unlockingQuestion = $allAllowDependecy;
        $blockingQuestion = $allBlockDependecy->filter(function($item) use ($allAllowDependecy) {
            return !$allAllowDependecy->contains($item);
        });
        // Keep answer less than at current position in new form dependency
        $mergeGroups['allowed'] = $unlockingQuestion->filter(function($item) use ($previousQuestions) {
            return !$previousQuestions->contains($item);
        })->values();
        $mergeGroups['blocked'] = $blockingQuestion->filter(function($item) use ($previousQuestions) {
            return !$previousQuestions->contains($item);
        })->values();
        $mergeGroups['deleted'] = $mergeGroups['allowed']->filter(function($item) use ($nextQuestionsNotNull) {
            return !$nextQuestionsNotNull->contains($item);
        })->values();

        $deleteingQuestion = $mergeGroups['deleted'];
        if ( $previousQuestions->isEmpty() ) {
            $deleteingQuestion = $aplicabilityNext->pluck('id_question')->unique()->values();
        }
        // delete group 'delete' for general dependecy
        $toDelete = AplicabilityModel::where('id_contract_aspect', $idContractAspect)
            ->whereIn('id_question', $deleteingQuestion)->delete();

        // Get dependency for type application
        $mergeApplicationType = $this->getTypeApplication($currentPosition, $questionType, $idContractAspect, $mergeGroups, $questionInForm);
        $deleteingQuestion = $mergeApplicationType['deleted'];
        // verify dependency empty
        if ( $previousQuestions->isEmpty() ) {
            // set default type application
            $mergeApplicationType = $this->defaultTypeApplication($questionInForm);
            $deleteingQuestion = $mergeApplicationType['deleted'];
        }
        // delete group 'delete'
        $toDelete = AplicabilityModel::where('id_contract_aspect', $idContractAspect)
            ->whereIn('id_question', $deleteingQuestion)->delete();
        // return groups to set in aplicability
        return $mergeApplicationType;
    }

    public function defaultTypeApplication($idsInForm) {
        // get all question in form
        $questionInForm = QuestionsModel::with(['answers.block'])
            ->whereIn('id_question', $idsInForm)->orderBy('order', 'ASC')->get();
        // 
        $allowedAll = collect([]);
        $blockedAll = collect([]);
        $types = collect([
            ['name' => 'federal', 'type' => StatusConstants::FEDERAL, 'priority' => 1],
            ['name' => 'state', 'type' => StatusConstants::STATE, 'priority' => 2],
            ['name' => 'local', 'type' => StatusConstants::LOCAL, 'priority' => 3],
        ]);
        // Get data first question
        $firstPosition = $questionInForm->min('order');
        $firstQuestion = $questionInForm->where('order', $firstPosition)->first();
        $defaultType = $types->where('type', $firstQuestion->id_question_type)->first();
        $blockTypes = $types->where('priority', '>', $defaultType['priority'])->pluck('type');
        // Get all question blocked or with dependecy
        foreach ($questionInForm as $key => $question) {
            foreach ($question['answers'] as $k => $answer) {
                $allowed = collect($answer['block'])->pluck('id_question')->toArray();
                $allowedAll = $allowedAll->merge($allowed);
                $blocked = collect( $questionInForm )->where('order', '>', $question['order'])
                    ->whereNotIn('id_question', $allowed)->pluck('id_question')->toArray();
                $blockedAll = $blockedAll->merge($blocked);
            }
        }
        $allowedAll = $allowedAll->unique();
        $blockedAll = $blockedAll->unique();
        // Get all question to allow or free dependency
        $allFreeDependency = $blockedAll->diff( $allowedAll->toArray() )->unique()->values()->toArray();
        $allDependency = $blockedAll->diff( $allFreeDependency )->values()->unique()->toArray();
        $blockedGroups = $questionInForm->whereIn('id_question_type', $blockTypes)
            ->pluck('id_question')->unique()->values()->toArray();
        $allowedGroups = $questionInForm->where('id_question_type', $defaultType['type'])
            ->whereIn('id_question', $allFreeDependency)->pluck('id_question')->unique()->values()->toArray();
        // merge groups and default dependency
        $toBlocked = collect([]);
        $data['currentPosition'] = $firstPosition;
        $data['allowed'] = $allowedGroups;
        $data['blocked'] = $toBlocked->merge($allDependency)->merge($blockedGroups)->unique()->values();
        $data['deleted'] = $allowedGroups;
        return $data;
    }

    public function getTypeApplication($currentPosition, $questionType, $idContractAspect, $mergeGroups, $questionInForm) {
        // Get total questions by group type 
        $form = QuestionsModel::whereIn('id_question', $questionInForm)->orderBy('order', 'asc')->get();
        $groupFederal = $form->where('id_question_type', StatusConstants::FEDERAL);
        $groupState = $form->where('id_question_type', StatusConstants::STATE);
        $groupLocal = $form->where('id_question_type', StatusConstants::LOCAL);

        $types = collect([
            [
                'name' => 'federal', 'type' => StatusConstants::FEDERAL, 'nextType' => StatusConstants::STATE, 
                'priority' => 1, 'total' => $groupFederal->count(), 'lastPosition' => $groupFederal->pluck('order')->max(),
                'getTypes' => [StatusConstants::FEDERAL, StatusConstants::STATE]
            ],
            [
                'name' => 'state', 'type' => StatusConstants::STATE, 'nextType' => StatusConstants::LOCAL, 
                'priority' => 2, 'total' => $groupState->count(), 'lastPosition' => $groupState->pluck('order')->max(),
                'getTypes' => [StatusConstants::FEDERAL, StatusConstants::STATE, StatusConstants::LOCAL]
            ],
            [
                'name' => 'local', 'type' => StatusConstants::LOCAL, 'nextType' => null, 
                'priority' => 3, 'total' => $groupLocal->count(), 'lastPosition' => $groupLocal->pluck('order')->max(),
                'getTypes' => []
            ],
        ]);

        $dataGroups['federal'] = [];
        $dataGroups['state'] = [];
        $dataGroups['local'] = [];
        foreach ($types as $group) {
            // get all questions by group
            $curentQuestionsAnswerd = AplicabilityModel::with('question')
                ->where('id_contract_aspect', $idContractAspect)
                ->whereHas('question', fn($filter) => $filter->where('id_question_type', $group['type']))->get();
            // evaluate position, if the group is complete, so overwrite the current position by the last position in the group
            $countCurentQuestionsAnswerd = $curentQuestionsAnswerd->pluck('id_question')->unique()->count();
            $totalQuestions = $types->where('type', $group['type'])->first()['total'];
            if ($countCurentQuestionsAnswerd == $totalQuestions) {
                $currentPosition = $group['lastPosition'];
            }
            // get all questions with the lowest order to which they are tested
            $questions = $curentQuestionsAnswerd->where('question.order', '<=', $currentPosition);
            // Total questions in forms by type
            $totalQuestions = $types->where('type', $group['type'])->first()['total']; 
            // Total questions with at least one answer per answer type
            $totalQuestionsAswered = $questions->pluck('id_question')->unique()->count();
            // Total questions with positive answer 
            $totalPositive = $questions->where('id_answer_value', StatusConstants::AFFIRMATIVE)->count();
            // Check: group is completed
            $groupCompleted = $totalQuestions == $totalQuestionsAswered;
            // Check: evaluate next group
            $nextGroup = $groupCompleted && $totalPositive == 0;
            // defines whether the group will be blocked or allowed 
            $stopInThisGroup = $nextGroup == false && $groupCompleted == true;
            // build data to analyze
            $dataGroups[$group['name']]['getTypes'] = $group['getTypes'];
            $dataGroups[$group['name']]['priority'] = $group['priority'];
            $dataGroups[$group['name']]['lastPosition'] = $group['lastPosition'];
            $dataGroups[$group['name']]['type'] = $group['type'];
            $dataGroups[$group['name']]['nextType'] = $group['nextType'];
            $dataGroups[$group['name']]['inEvaluation'] = $group['type'] == $questionType;
            $dataGroups[$group['name']]['nextGroup'] = $nextGroup;
            $dataGroups[$group['name']]['groupCompleted'] = $groupCompleted;
            $dataGroups[$group['name']]['stopInThisGroup'] = $stopInThisGroup;
        }
        $groupsReduce = $this->evaluateGroupPhase($dataGroups, $questionType, $mergeGroups, $questionInForm, $currentPosition);
        return $groupsReduce;
    }

    public function evaluateGroupPhase($dataGroups, $questionType, $mergeGroups, $questionInForm) {
        $groupCollection = collect($dataGroups)->sortBy('priority');
        $currentGroup = $groupCollection->filter(fn($item) => $item['inEvaluation'])->first();
        // primera si no hay nada, evalua la prioridad en que esta evaluado
        if ($currentGroup['groupCompleted'] == false && $currentGroup['stopInThisGroup'] == false) {
            $currentGroupNext = $groupCollection->where('type', $currentGroup['type'])->first();
            $typesBlocks = $groupCollection->filter(fn($item) => $item['priority'] > $currentGroupNext['priority'])
                ->map(fn($item) => $item['type'])->values();
            // add question blocks in group
            $newBlockeds = QuestionsModel::whereIn('id_question', $questionInForm)
                ->whereIn('id_question_type', $typesBlocks)->get()->pluck('id_question');
            $oldBlockeds = $mergeGroups['blocked'];
            $merge = collect( $oldBlockeds )->merge($newBlockeds)->unique()->values();
            $mergeGroups['blocked'] = $merge;
            return $mergeGroups;
        }
        // Validation that a group has applicability identification
        if ($currentGroup['groupCompleted'] && $currentGroup['stopInThisGroup']) {
            $currentGroupNext = $groupCollection->where('type', $currentGroup['type'])->first();
            $typesBlocks = $groupCollection->filter(fn($item) => $item['priority'] > $currentGroupNext['priority'])
                ->map(fn($item) => $item['type'])->values();
            // add question blocks in group
            $newBlockeds = QuestionsModel::whereIn('id_question', $questionInForm)
                ->whereIn('id_question_type', $typesBlocks)->get()->pluck('id_question');
            $oldBlockeds = $mergeGroups['blocked'];
            $merge = collect( $oldBlockeds )->merge($newBlockeds)->unique()->values();
            $mergeGroups['blocked'] = $merge;
            return $mergeGroups;
        }
        // Validation that a group must unblock the next group in priority
        if ($currentGroup['groupCompleted'] && $currentGroup['stopInThisGroup'] == false) {
            // if the nextType is null, you are evaluating LOCAL no filtering is needed 
            if ( is_null($currentGroup['nextType']) || sizeof($currentGroup['getTypes']) == 0 ) {
                return $mergeGroups;
            }
            $currentGroup = $groupCollection->filter(fn($item) => $item['inEvaluation'])->first();
            // add question blocks in group
            $newBlockeds = QuestionsModel::whereIn('id_question', $questionInForm)
                ->whereNotIn('id_question_type', $currentGroup['getTypes'])->get()->pluck('id_question');
            $oldBlockeds = $mergeGroups['blocked'];
            $mergeBlock = collect( $oldBlockeds )->merge($newBlockeds)->unique()->values();
            $mergeGroups['blocked'] = $mergeBlock;
            // add question allows in group
            $newAlloweds = QuestionsModel::whereIn('id_question', $questionInForm)
                ->where('id_question_type', $currentGroup['nextType'])->get()->pluck('id_question');
            $oldAlloweds = $mergeGroups['allowed'];
            $mergeAllow = collect( $oldAlloweds )->merge($newAlloweds)->unique()->values();
            $mergeGroups['allowed'] = $mergeAllow;
            // add question allows in group
            $newDeleteds = QuestionsModel::whereIn('id_question', $questionInForm)
                ->where('id_question_type', $currentGroup['nextType'])->get()->pluck('id_question');
            $oldDeleteds = $mergeGroups['deleted'];
            $mergeDelete = collect( $oldDeleteds )->merge($newDeleteds)->unique()->values();
            $mergeGroups['deleted'] = $mergeDelete;
            return $mergeGroups;
        }
    }
    
    public function setDependencyByAnswer($currentPosition, $requestData, $groups) {
        try {
            $requestData['setAnswer'] = 'true'; // set variable setAnswer in true if there are records in aplicability of same question
            $unlockingQuestion = $groups['allowed'];
            $blockingQuestion = $groups['blocked'];
            // search questions blocked with answer in aplicability and set values null (id_answer_value, id_answer_question)
            $blockedInNull = AplicabilityModel::where([
                ['id_audit_processes', $requestData['idAuditProcess']],
                ['id_contract_aspect', $requestData['idContractAspect']]
            ])
            ->whereNotNull(['id_answer_value', 'id_answer_question'])
            ->whereIn('id_question', $blockingQuestion)
            ->update(['id_answer_value' => null, 'id_answer_question' => null]);
            // set or delete questions blocked with answer in aplicability anser values null (id_answer_value, id_answer_question)
            if ( sizeof($blockingQuestion) > 0) {
                $dataBlock = $requestData;
                $dataBlock['idAnswerQuestion'] = null;
                $dataBlock['idAnswerValue'] = null;
                foreach ($blockingQuestion as $q) {
                    $dataBlock['idQuestion'] = $q;
                    // set or delete answer about field setAnswer
                    $setBlock = AplicabilityModel::SetAplicability($dataBlock);
                    if ($setBlock['status'] != StatusConstants::SUCCESS) {
                        $data['status'] = StatusConstants::ERROR;
                        $data['msg'] = 'Error al registrar dependecias de respuesta intentelo nuevamente';
                        return $data;
                    }
                }
            } 
            return true;
        } catch (\Throwable $th) {
            return false;
        }
    }
}