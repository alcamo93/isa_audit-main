<?php

namespace App\Http\Controllers\V2;

use App\Classes\Utilities\DataSection;
use App\Http\Controllers\Controller;
use App\Models\Admin\ModulesModel;
use App\Models\Admin\SubModulesModel;
use App\Models\V2\Audit\ProcessAudit;
use App\Models\V2\Catalogs\EvaluationType;
use App\Models\V2\Catalogs\Form;
use App\Models\V2\Catalogs\Scope;
use Illuminate\Support\Facades\DB;
use App\Models\V2\Catalogs\LegalBasi;
use App\Models\V2\Catalogs\Periodicity;
use Illuminate\Support\Facades\Storage;
use Intervention\Image\Facades\Image;
use App\Classes\Files\ImageDecodeToFile;
use App\Models\V2\Admin\Address;
use App\Models\V2\Audit\Aplicability;
use App\Models\V2\Audit\EvaluateApplicabilityQuestion;
use App\Models\V2\Catalogs\AnswersQuestion;
use App\Models\V2\Catalogs\Question;
use App\Models\V2\Catalogs\LegalClassification;
use App\Models\V2\Catalogs\Topic;
use App\Models\V2\Catalogs\Requirement;
use App\Models\V2\Catalogs\Subrequirement;
use App\Traits\FileTrait;

class DefaultController extends Controller
{
	use FileTrait;
	//modules
	public function modules() 
	{
		SubModulesModel::findOrFail(4)->update(['path' => 'v2/catalogs/guideline/view']);
		SubModulesModel::findOrFail(1)->update(['path' => 'v2/catalogs/industry/view']);
		SubModulesModel::findOrFail(3)->update(['path' => 'v2/catalogs/matter/view']);
		SubModulesModel::findOrFail(7)->update(['path' => 'v2/catalogs/risk/view']);
		ModulesModel::findOrFail(15)->update(['path' => 'v2/specific/requirement/view']);
		ModulesModel::findOrFail(9)->update(['path' => 'v2/new/view', 'id_status' => 1]);
		ModulesModel::create([
			'name_module'=> 'Biblioteca Legal',
			'pseud_module'=> 'Biblioteca Legal',
			'path' => 'v2/knowledge/view',
			'color_module' => 'white',
			'icon_module' => 'tap-01',
			'id_status' => 1,
			'has_submodule' => 0,
			'sequence' => 16,
			'owner' => 1,
			'description' => 'En este módulo podrás encontrar la base de conocimiento de ACM SUITE.'
		]);
		LegalClassification::insert([
		  ['legal_classification' => 'Reforma'],
          ['legal_classification' => 'Resolución']
		]);
        Topic::insert([
		  ['name' => 'Medio Ambiente'],
		  ['name' => 'Residuos Peligrosos'],
		  ['name' => 'Residuos No Peligrosos'],
		  ['name' => 'Aire'],
		  ['name' => 'Cambio climático'],
		  ['name' => 'Forestal'],
		  ['name' => 'Materiales Peligrosos'],
		  ['name' => 'Seguridad'],
		  ['name' => 'Salud ocupacional'],
		  ['name' => 'Organizacional'],
		  ['name' => 'Especifca'],
		  ['name' => 'Control Sanitario'],
		  ['name' => 'Salud Ambiental'],
		  ['name' => 'Protección Civil']
	    ]);
		dd('Modules Listo');
	}

	public function process() 
	{
		try {
			DB::beginTransaction();
			// php artisan migrate
			$process = ProcessAudit::where('id_scope', Scope::CORPORATE)->where('evaluation_type_id', EvaluationType::EVALUATE_BOTH)->get();
			
			$forms = Form::where('is_current', 1)->get();
			$allAspects = $forms->pluck('aspect_id')->sort()->values()->toArray();
			
			$completeProjects = $process->map(function($project) use ($allAspects) {
				$currentAspects = $project->aplicability_register->contract_matters->pluck('contract_aspects')->collapse()->pluck('id_aspect')->sort()->values()->toArray();
				if ( $currentAspects === $allAspects ) return $project->id_audit_processes;
			})->filter(fn($item) => !is_null($item))->values();

			$processUseKpi = ProcessAudit::whereIn('id_audit_processes', $completeProjects);
			$update = $processUseKpi->update(['use_kpi' => 1]);
			DB::commit();
			dd('Modules process Listo', $update);
		} catch (\Throwable $th) {
			DB::rollback();
			dd('error process', $th);
		}
	}

	public function files()
	{
		try {
			$diskKey = 'legals';
			$disk = $this->getStorageEnviroment($diskKey);
			
			$files = Storage::disk($disk)->files();
			
			foreach ($files as $file) {
				$record = LegalBasi::where('legal_quote', 'like', "%{$file}%")->get()->first();
				
				if (!is_null($record)) {
					$newPath = "{$record->id_legal_basis}/{$file}";
					Storage::disk($disk)->move($file, $newPath);
					$decodeImgObject = new ImageDecodeToFile($diskKey, $record->id_legal_basis, false);
					$decodeImgObject->decodeImg64ToLink($record->legal_quote);
					$record->update(['legal_quote' => $decodeImgObject->richText]);
				}
			}
			
			dd('listo files');
		} catch (\Throwable $th) {
			dd($th);
		}

	}

	public function evaluates() 
	{
		try {
			$allProcess = ProcessAudit::get();
			DB::beginTransaction();
			foreach ($allProcess as $process) {
				// find data process
				$contractMatters = $process->aplicability_register->contract_matters;
				$address = $process->corporate->addresses->where('type', Address::PHYSICAL)->first();
				foreach ($contractMatters as $matter) {
					// iterate each aspect
					$contractAspects = $matter->contract_aspects;
					foreach ($contractAspects as $aspect) {
						// evaluate if question is exist in aplicability
						if ( $aspect->id_status == Aplicability::NOT_CLASSIFIED_APLICABILITY || $aspect->id_status == Aplicability::EVALUATING_APLICABILITY) {
							// get question per aspect
							$questioIds = Question::where('form_id', $aspect->form_id)->where('id_status', Question::ACTIVE)->where(function ($query) use ($address) {
								$query->whereNull('id_state')->whereNull('id_city')
									->orWhere( fn($query) => $query->where('id_state', $address->id_state)->whereNull('id_city') )
									->orWhere( fn($query) => $query->where('id_state', $address->id_state)->where('id_city', $address->id_city) );
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
						} 
						if ( $aspect->id_status == Aplicability::CLASSIFIED_APLICABILITY || $aspect->id_status == Aplicability::FINISHED_APLICABILITY || $aspect->id_status == Aplicability::EVALUATING_APLICABILITY ) {
							// set existentes
							$records = Aplicability::where('id_contract_aspect', $aspect->id_contract_aspect)->customOrderEvaluateQuestion()->get();
							foreach ($records as $item) {
								$data = [
									'id_contract_aspect' => $item->id_contract_aspect,
									'id_question' => $item->id_question,
								];
								$evaluate = EvaluateApplicabilityQuestion::updateOrCreate($data, $data);
								$aplicability = Aplicability::find($item->id_aplicability);
								$update = $aplicability->update(['id_evaluate_question' => $evaluate->id]);
							}
						}
					}
				}
			}
			DB::commit();
			dd('listo');
		} catch (\Throwable $th) {
			DB::rollback();
			dd($th);
		}
	}

	public function track()
	{
		try {
			DB::beginTransaction();
			
			$allForms = Form::get();

			$versions = range(1, $allForms->pluck('version')->unique()->sort()->max());
			
			foreach ($versions as $version) {
				$originalForms = $allForms->where('version', $version);

				foreach ($originalForms as $form) {
					$previousVersionForm = $allForms->where('matter_id', $form->matter_id)->where('aspect_id', $form->aspect_id)
						->where('version', '<', $version)->sortByDesc('version')->first();

					$hasPreviousVersions = is_null($previousVersionForm);

					if ( $hasPreviousVersions ) {
						$form->update(['id_track' => $form->id]);
						$this->setNewTrack($form->id);
					} else {
						$form->update(['id_track' => $previousVersionForm->id]);
						$this->setTrack($previousVersionForm->id, $form->id);
					}
				}			
			}
			DB::commit();
			dd('listo track');
		} catch (\Throwable $th) {
			DB::rollback();
			dd($th);
		}
	}

	private function setNewTrack($idForm) 
	{
		$originalRequirements = Requirement::where('form_id', $idForm)->get();
		foreach ($originalRequirements as $requirement) {
			$requirement->update(['id_track' => $requirement->id_requirement]);
		}

		$originalSubrequirements = Subrequirement::whereIn('id_requirement', $originalRequirements->pluck('id_requirement'))->get();
		foreach ($originalSubrequirements as $subrequirement) {
			$subrequirement->update(['id_track' => $subrequirement->id_subrequirement]);
		}
		
		$originalQuestions = Question::where('form_id', $idForm)->get();
		foreach ($originalQuestions as $question) {
			$question->update(['id_track' => $question->id_question]);
		}
		
		$originalAnswers = AnswersQuestion::whereIn('id_question', $originalQuestions->pluck('id_question'))->get();
		foreach ($originalAnswers as $answer) {
			$answer->update(['id_track' => $answer->id_answer_question]);
		}
	}

	private function setTrack($orginalIdForm, $replicateIdForm)
	{
		/**
		 * Requirements
		 */
		$originalRequirements = Requirement::where('form_id', $orginalIdForm)->get();
		// get all requirements
		$replicateRequirements = Requirement::where('form_id', $replicateIdForm)->get();
		// get original requirements in replicate
		$originalNoRequirements = $originalRequirements->pluck('no_requirement');
		$allSameReplicateRequirements = $replicateRequirements->whereIn('no_requirement', $originalNoRequirements)->values();
		$allNoSameReplicateRequirements = $replicateRequirements->whereNotIn('no_requirement', $originalNoRequirements)->values();
		// set original id to replicates requirements
		foreach ($allSameReplicateRequirements as $requirement) {
			$originalRequirement = $originalRequirements->where('no_requirement', $requirement->no_requirement)->first();
			$requirement->update(['id_track' => $originalRequirement->id_track]);
		}
		// set new id to new requirements
		foreach ($allNoSameReplicateRequirements as $newRequirement) {
			$newRequirement->update(['id_track' => $newRequirement->id_requirement]);
		}

		$originalSubrequirements = Subrequirement::whereIn('id_requirement', $originalRequirements->pluck('id_requirement'))->get();
		// get all subrequirement
		$replicateSubrequirements = Subrequirement::whereIn('id_requirement', $replicateRequirements->pluck('id_requirement'))->get();
		// get original subrequirements in replicate
		$originalNoSubrequirements = $originalSubrequirements->pluck('no_subrequirement');
		$allSameReplicateSubrequirements = $replicateSubrequirements->whereIn('no_subrequirement', $originalNoSubrequirements)->values();
		$allNoSameReplicateSubrequirements = $replicateSubrequirements->whereNotIn('no_subrequirement', $originalNoSubrequirements)->values();
		// set original id to replicates subrequirements
		foreach ($allSameReplicateSubrequirements as $subrequirement) {
			$originalSubrequirement = $originalSubrequirements->where('no_subrequirement', $subrequirement->no_subrequirement)->first();
			$subrequirement->update(['id_track' => $originalSubrequirement->id_track]);
		}
		// set new id to new subrequirements
		foreach ($allNoSameReplicateSubrequirements as $newSubrequirement) {
			$newSubrequirement->update(['id_track' => $newSubrequirement->id_requirement]);
		}


		/**
		 * Questions
		 */
		$originalQuestions = Question::where('form_id', $orginalIdForm)->get();
		// get all question
		$replicateQuestions = Question::where('form_id', $replicateIdForm)->get();
		// get original question in replicate
		$originalContQuestion = $originalQuestions->pluck('question');
		$allSameReplicateQuestions = $replicateQuestions->whereIn('question', $originalContQuestion)->values();
		$allNoSameReplicateQuestions = $replicateQuestions->whereNotIn('question', $originalContQuestion)->values();
		// set original id to replicates question
		foreach ($allSameReplicateQuestions as $requirement) {
			$originalRequirement = $originalQuestions->where('question', $requirement->question)->first();
			$requirement->update(['id_track' => $originalRequirement->id_track]);
		}
		// set new id to new question
		foreach ($allNoSameReplicateQuestions as $newQuestion) {
			$newQuestion->update(['id_track' => $newQuestion->id_question]);
		}

		$originalAnswers = AnswersQuestion::whereIn('id_question', $originalQuestions->pluck('id_question'))->get();
		// get all answers
		$replicateAnswers = AnswersQuestion::whereIn('id_question', $replicateQuestions->pluck('id_question'))->get();
		// get original answers in replicate
		$originalContAnswer = $originalAnswers->pluck('description');
		$allSameReplicateAnswers = $replicateAnswers->whereIn('description', $originalContAnswer)->values();
		$allNoSameReplicateAnswers = $replicateAnswers->whereNotIn('description', $originalContAnswer)->values();
		// set original id to replicates subrequirements
		foreach ($allSameReplicateAnswers as $answer) {
			$original = $originalAnswers->where('description', $answer->description)->first();
			$answer->update(['id_track' => $original->id_track]);
		}
		// set new id to new subrequirements
		foreach ($allNoSameReplicateAnswers as $newAnswer) {
			$newAnswer->update(['id_track' => $newAnswer->id_answer_question]);
		}
	}
}
