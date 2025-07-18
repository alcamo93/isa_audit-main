<?php

namespace App\Classes\Applicability;

use App\Models\V2\Admin\Address;
use App\Models\V2\Audit\Aplicability;
use App\Models\V2\Audit\AplicabilityRegister;
use App\Models\V2\Audit\ContractAspect;
use App\Models\V2\Audit\ContractMatter;
use App\Models\V2\Audit\EvaluateApplicabilityQuestion;
use App\Models\V2\Audit\EvaluateRequirement;
use App\Models\V2\Audit\ProcessAudit;
use App\Models\V2\Catalogs\AnswerQuestionRequirement;
use App\Models\V2\Catalogs\AnswerValue;
use App\Models\V2\Catalogs\ApplicationType;
use App\Models\V2\Catalogs\Question;
use App\Models\V2\Catalogs\QuestionType;
use App\Models\V2\Catalogs\Requirement;
use App\Models\V2\Catalogs\RequirementType;
use App\Models\V2\Catalogs\Subrequirement;

class Evaluate
{
	protected $logClass = [];
	protected $idApplicationType = null;
	protected $idRequirementsArray = [];
	protected $requirements = [];
	protected $idRequirementsLocalesArray = [];
	protected $idRequirementsSpecificArray = [];
	protected $idSubrequirementsArray = [];
	protected $idAuditProcess = null;
	protected $idApplicabilityRegister = null;
	protected $idContractMatter = null;
	protected $idContractAspect = null;
	protected $idState = null;
	protected $idCity = null;
	protected $idForm = null;
	protected $idAspect = null;
	protected $evaluateSpecific = false;
	protected $idCorporate = null;
	protected $noModifyEvaluates = false;

	public function __construct($idContractAspect, $noModifyEvaluates = false)
	{
		$this->noModifyEvaluates = $noModifyEvaluates;
		$contractAspect = ContractAspect::findOrFail($idContractAspect);
		$contractMatter = ContractMatter::without(['contract_aspects'])->findOrFail($contractAspect->id_contract_matter);
		$process = ProcessAudit::without(['aplicability_register', 'auditors'])->findOrFail($contractAspect->id_audit_processes);
		$address = $process->corporate->addresses->firstWhere('type', Address::PHYSICAL);
		// set values
		$this->idAuditProcess = $contractAspect->id_audit_processes;
		$this->idApplicabilityRegister = $contractMatter->id_aplicability_register;
		$this->idContractMatter = $contractMatter->id_contract_matter;
		$this->idContractAspect = $contractAspect->id_contract_aspect;
		$this->idForm = $contractAspect->form_id;
		$this->idAspect = $contractAspect->id_aspect;
		$this->idState = $address->id_state;
		$this->idCity = $address->id_city;
		$this->evaluateSpecific = $process->evaluate_especific == 1;
		$this->idCorporate = $process->id_corporate;
	}

	/**
	 * set info forms
	 */
	public function getApplicationTypeAspect()
	{
		try {
			$this->idApplicationType = ApplicationType::FEDERAL;
			$applicationTypeInit = ApplicationType::find($this->idApplicationType);
			array_push($this->logClass, "Se inicio como {$applicationTypeInit->application_type}");

			$this->getRequiremntsByAplicability();
			$this->getSpecificRequirements();

			$applicationTypeFinal = ApplicationType::find($this->idApplicationType);
			array_push($this->logClass, "Termino como: {$applicationTypeFinal->application_type}");

			$setRequirements = $this->setEvaluate($this->idContractAspect, $this->idApplicabilityRegister);
			if (!$setRequirements['success']) return $setRequirements;

			$info['success'] = true;
			$info['message'] = "Aspecto clasificado: {$applicationTypeFinal->application_type}";
			$info['logs'] = $this->logClass;
			return $info;
		} catch (\Throwable $th) {
			$info['success'] = false;
			$info['message'] = 'Algo salio mal al tratar de clasificar el aspecto';
			$info['logs'] = $this->logClass;
			return $info;
		}
	}

	/**
	 * Get requirements to Audit
	 */
	private function getRequiremntsByAplicability($recursive = false, $idApplicationTypeNew = null)
	{
		$idApplicationType = ($recursive) ? $idApplicationTypeNew : $this->idApplicationType;

		$this->idRequirementsArray = [];
		$this->idRequirementsLocalesArray = [];
		$this->idRequirementsSpecificArray = [];
		$this->idSubrequirementsArray = [];

		// Get requirements
		switch ($idApplicationType) {
			case ApplicationType::FEDERAL:
				$federal = $this->onlyFederalIdentification();
				foreach ($federal as $requerimientFI) {
					array_push($this->idRequirementsArray, $requerimientFI);
				}
				break;
			case ApplicationType::STATE:
				$state = $this->onlyEstatalIdentification();
				foreach ($state as $requerimientSI) {
					array_push($this->idRequirementsArray, $requerimientSI);
				}
				break;
			case ApplicationType::LOCAL:
				$local = $this->onlyLocalIdentification();
				foreach ($local as $requerimientLI) {
					array_push($this->idRequirementsArray, $requerimientLI);
				}
				break;
		}
		// Requirements locales Defualt
		$isNotLocal = $this->idApplicationType == ApplicationType::FEDERAL || $this->idApplicationType == ApplicationType::STATE;
		if ($isNotLocal && sizeof($this->idRequirementsLocalesArray) == 0) {
			array_push($this->logClass, 'Evaluacion de: Requerimientos Locales');

			$localDefault = $this->onlyLocalRequirements();
			foreach ($localDefault as $id) {
				array_push($this->idRequirementsLocalesArray, $id);
				array_push($this->idRequirementsArray, $id);
			}

			$isLocal = (sizeof($localDefault) > 0) ? 'Si' : 'No';
			array_push($this->logClass, $isLocal . ' existen requerimientos Locales por sumar');
		}

		$hasRequirements = sizeof($this->requirements) > 0;

		$data['success'] = $hasRequirements;
		$data['message'] = $hasRequirements ? 'Iniciando Auditoría' : 'No hay REQUERIMIENTOS para comenzar auditoria';
		$data['idAapplicationType'] = $this->idApplicationType;
		$data['requirements'] = $this->requirements;
		return $data;
	}

	/**
	 * EVALUATE ONLY "FEDERAL IDENTIFICATION"
	 */
	private function onlyFederalIdentification()
	{
		$groupReq = [];
		array_push($this->logClass, 'Inicia evaluación de Clasificación Federal');
		array_push($this->logClass, 'Evaluación de: Identificación federal');

		$requirementTypes = [RequirementType::IDENTIFICATION_FEDERAL,  RequirementType::REQUIREMENT_IDENTIFICATION_COMPOSE];
		$identification = $this->getIdentificationRequirements(QuestionType::FEDERAL, $requirementTypes, ApplicationType::FEDERAL);

		foreach ($identification as $id) {
			array_push($groupReq, $id);
		}

		if (sizeof($identification) > 0) {
			$this->idApplicationType = ApplicationType::FEDERAL;
			$isFederal = 'Es FEDERAL por identificación';
		} else $isFederal = 'No hay Requerimientos Federales por identificación';

		array_push($this->logClass, $isFederal);

		// init Recursive
		if (sizeof($identification) == 0) {
			array_push($this->logClass, 'Pasa a Evaluar Sección Estatal');
			$this->getRequiremntsByAplicability(true, ApplicationType::STATE);
		}

		return $groupReq;
	}

	/**
	 * EVALUATE ONLY "STATE IDENTIFICATION"
	 */
	public function onlyEstatalIdentification()
	{
		$groupReq = [];
		array_push($this->logClass, 'Inicia evaluación de Clasificación Estatal');

		$filterQuestion = function ($query) {
			$query->where('id_question_type', QuestionType::STATE)->where('id_state', $this->idState)->whereNull('id_city');
		};
		$relationships = ['question' => $filterQuestion];
		$thereAreQuestions = EvaluateApplicabilityQuestion::with($relationships)->where('id_contract_aspect', $this->idContractAspect)
			->whereHas('question', $filterQuestion)->exists();

		$thereAreQuestionsText = $thereAreQuestions ? 'Si' : 'No';
		array_push($this->logClass, "{$thereAreQuestionsText} existen preguntas de identificación Estatales");

		if ($thereAreQuestions > 0) {
			// search state indentification requirements
			$requirementTypes = [RequirementType::IDENTIFICATION_STATE, RequirementType::REQUIREMENT_IDENTIFICATION_COMPOSE];
			$state = $this->getIdentificationRequirements(QuestionType::STATE, $requirementTypes, ApplicationType::STATE);

			foreach ($state as $id) {
				array_push($groupReq, $id);
			}

			if (sizeof($state) > 0) {
				$this->idApplicationType = ApplicationType::STATE;
				$isState = 'Es ESTATAL por identificación';
			} else $isState = 'No hay requerimientos Estatales por identificación';

			array_push($this->logClass, $isState);
		} else {
			// search state normal requirements
			$requirementTypes = [RequirementType::REQUIREMENT_STATE, RequirementType::REQUIREMENT_COMPOSE];
			$state = $this->getNormalRequirements($requirementTypes, ApplicationType::STATE);

			foreach ($state as $id) {
				array_push($groupReq, $id);
			}

			if (sizeof($state) > 0) {
				$this->idApplicationType = ApplicationType::STATE;
				$isState = 'Es ESTATAL por requerimientos';
			} else $isState = 'No hay Requerimientos Estatal';

			array_push($this->logClass, $isState);
		}
		// init Recursive
		if (sizeof($state) == 0) {
			array_push($this->logClass, 'Pasa a Evaluar Sección Local');
			$this->getRequiremntsByAplicability(true, ApplicationType::LOCAL);
		}

		return $groupReq;
	}

	/**
	 * EVALUATE ONLY "LOCAL IDENTIFICATION"
	 */
	private function onlyLocalIdentification()
	{
		$groupReq = [];
		array_push($this->logClass, 'Inicia evaluación de Clasificación Local');

		$filterQuestion = function ($query) {
			$query->where('id_question_type', QuestionType::LOCAL)->where('id_state', $this->idState)->where('id_city', $this->idCity);
		};
		$relationships = ['question' => $filterQuestion];
		$thereAreQuestions = EvaluateApplicabilityQuestion::with($relationships)->where('id_contract_aspect', $this->idContractAspect)
			->whereHas('question', $filterQuestion)->exists();

		$thereAreQuestionsText = $thereAreQuestions ? 'Si' : 'No';
		array_push($this->logClass, "{$thereAreQuestionsText} existen preguntas de indentificación Local");

		if ($thereAreQuestions) {
			// search local indentification requirements
			$requirementTypes = [RequirementType::IDENTIFICATION_LOCAL, RequirementType::REQUIREMENT_IDENTIFICATION_COMPOSE];
			$local = $this->getIdentificationRequirements(QuestionType::LOCAL, $requirementTypes, ApplicationType::LOCAL);

			foreach ($local as $id) {
				array_push($groupReq, $id);
			}

			if (sizeof($local) > 0) {
				$this->idApplicationType = ApplicationType::LOCAL;
				$isLocal = 'Es LOCAL por Requerimientos Locales de Identificación';
			} else $isLocal = 'No existen requerimientos Locales por identificación';

			array_push($this->logClass, $isLocal);
		} else {
			// search local indentification requirements
			$requirementTypes = [RequirementType::REQUIREMENT_LOCAL, RequirementType::REQUIREMENT_COMPOSE];
			$local = $this->getNormalRequirements($requirementTypes, ApplicationType::LOCAL);

			foreach ($local as $id) {
				array_push($groupReq, $id);
			}

			if (sizeof($local) > 0) {
				$this->idApplicationType = ApplicationType::LOCAL;
				$isLocal = 'Es LOCAL por requerimientos';
			} else $isLocal = 'No existen requerimientos Locales';

			array_push($this->logClass, $isLocal);
		}
		if (sizeof($local) == 0) {
			$this->idApplicationType = ApplicationType::NOT_APPLICABLE;
			array_push($this->logClass, 'Es No Aplica');
		}

		return $groupReq;
	}

	/**
	 * EVALUATE ONLY "LOCAL REQUIREMENT" FOR FEDERAL AND STATE
	 */
	private function onlyLocalRequirements()
	{
		$groupReq = [];
		array_push($this->logClass, 'Evaluación Local: requerimientos Locales por sumar');

		$thereReqLocal = Requirement::where('id_requirement_type', RequirementType::REQUIREMENT_LOCAL)
			->where('form_id', $this->idForm)->where('id_state', $this->idState)->where('id_city', $this->idCity)->exists();

		if ($thereReqLocal) {

			array_push($this->logClass, 'Si existen requerimientos Locales');
			$thereAreQuestions = Question::where('id_question_type', QuestionType::LOCAL)
				->where('id_status', Question::ACTIVE)->where('form_id', $this->idForm)
				->where('id_state', $this->idState)->where('id_city', $this->idCity)->exists();

			if ($thereAreQuestions) {
				array_push($this->logClass, 'Si existen preguntas de identificación Locales');

				$requirementTypes = [RequirementType::IDENTIFICATION_LOCAL, RequirementType::REQUIREMENT_IDENTIFICATION_COMPOSE];
				$identificationlocal = $this->getIdentificationRequirements(QuestionType::LOCAL, $requirementTypes, ApplicationType::LOCAL);

				array_push($this->logClass, 'Agregando Requerimientos de Identificación Locales');

				foreach ($identificationlocal as $id) {
					array_push($groupReq, $id);
				}
			} else {
				array_push($this->logClass, 'No existen preguntas de identificación Locales');

				$requirementTypes = [RequirementType::REQUIREMENT_LOCAL, RequirementType::REQUIREMENT_COMPOSE];
				$localRequirements = $this->getNormalRequirements($requirementTypes, ApplicationType::LOCAL);

				array_push($this->logClass, 'Agregando Requerimientos Locales');

				foreach ($localRequirements as $id) {
					array_push($groupReq, $id);
				}
			}
		} else {
			array_push($this->logClass, 'No existen requerimientos Locales');

			$requirementTypes = [RequirementType::IDENTIFICATION_LOCAL, RequirementType::REQUIREMENT_IDENTIFICATION_COMPOSE];
			$identificationlocal = $this->getIdentificationRequirements(QuestionType::LOCAL, $requirementTypes, ApplicationType::LOCAL);

			array_push($this->logClass, 'Agregando Requerimientos de Identificación Locales');

			foreach ($identificationlocal as $id) {
				array_push($groupReq, $id);
			}
		}

		return $groupReq;
	}

	/**
	 * GET ONLY "Specific Requirements"
	 */
	private function getSpecificRequirements()
	{
		// Get only "SPECIFIC REQUIREMENTS"
		if ($this->evaluateSpecific) {
			array_push($this->logClass, 'Evaluación de: Requerimientos especificos');
			$specific = $this->onlySpecificRequirements();
			foreach ($specific as $requerimientSQ) {
				array_push($this->idRequirementsArray, $requerimientSQ);
			}

			$thereAreSpecific = (sizeof($specific) > 0);
			$thereAreSpecificText = $thereAreSpecific ? 'Si' : 'No';
			array_push($this->logClass, "{$thereAreSpecificText}.' existen Requerimientos especificos");

			// Classify SPECIFIC
			if ($thereAreSpecific && $this->idApplicationType == ApplicationType::NOT_APPLICABLE) {
				array_push($this->logClass, 'Es ESPECIFICO, solo existen Requerimientos especificos');
				$this->idApplicationType = ApplicationType::SPECIFIC;
			}
		}
	}

	/**
	 * Get requerimients
	 */
	private function getIdentificationRequirements($idQuestionType, $idRequirementTypeArray, $idApplicationType, $idAnswerValue = AnswerValue::AFFIRMATIVE)
	{
		// get evaluate per applicaction type
		$filterQuestionType = fn($query) => $query->where('id_question_type', $idQuestionType);
		$filterAnswerValue = fn($query) => $query->where('id_answer_value', $idAnswerValue);
		$relationshipsEvaluate = ['question' => $filterQuestionType, 'applicability' => $filterAnswerValue];
		$evaluates = EvaluateApplicabilityQuestion::with($relationshipsEvaluate)->where('id_contract_aspect', $this->idContractAspect)
			->whereHas('question', $filterQuestionType)->whereHas('applicability', $filterAnswerValue)->get();

		// set filters
		$idAnswerQuestionsArray = $evaluates->flatMap(fn($item) => $item->applicability)->pluck('id_answer_question');
		$filterInRequirement = function ($query) use ($idApplicationType, $idRequirementTypeArray) {
			$query->where('id_application_type', $idApplicationType)->whereIn('id_requirement_type', $idRequirementTypeArray);
			if ($idApplicationType == ApplicationType::STATE) {
				$query->where('id_state', $this->idState)->whereNull('id_city');
			}
			if ($idApplicationType == ApplicationType::LOCAL) {
				$query->where('id_state', $this->idState)->where('id_city', $this->idCity);
			}
		};
		$relationshipsAnswerQuestion = ['requirement' => $filterInRequirement];

		// get requirements
		$idRequirementsArray = AnswerQuestionRequirement::with($relationshipsAnswerQuestion)->whereIn('id_answer_question', $idAnswerQuestionsArray)
			->whereHas('requirement', $filterInRequirement)->whereNull('id_subrequirement')->get()->pluck('id_requirement');

		// get subrequirements
		$idSubrequirementsArray = AnswerQuestionRequirement::with($relationshipsAnswerQuestion)->whereIn('id_answer_question', $idAnswerQuestionsArray)
			->whereHas('requirement', $filterInRequirement)->whereNotNull('id_subrequirement')->get();

		foreach ($idSubrequirementsArray as $idSubrequirement) {
			array_push($this->idSubrequirementsArray, $idSubrequirement);
		}

		return $idRequirementsArray;
	}

	/**
	 * Get requirements without relation to questions
	 */
	private function getNormalRequirements($idRequirementTypeArray, $idApplicationType)
	{
		// get requirements
		$queryRequirements = Requirement::whereIn('id_requirement_type', $idRequirementTypeArray)
			->where('id_application_type', $idApplicationType)->where('form_id', $this->idForm);

		if ($idApplicationType == ApplicationType::STATE) {
			$queryRequirements->where('id_state', $this->idState)->whereNull('id_city');
		}
		if ($idApplicationType == ApplicationType::LOCAL) {
			$queryRequirements->where('id_state', $this->idState)->where('id_city', $this->idCity);
		}

		$idRequirementsArray = $queryRequirements->get()->pluck('id_requirement');

		// get subrequirements
		$querySubrequirements = Subrequirement::whereIn('id_requirement', $idRequirementsArray)->where('id_application_type', $idApplicationType);

		if ($idApplicationType == ApplicationType::STATE) {
			$querySubrequirements->where('id_state', $this->idState)->whereNull('id_city');
		}
		if ($idApplicationType == ApplicationType::LOCAL) {
			$querySubrequirements->where('id_state', $this->idState)->where('id_city', $this->idCity);
		}

		$idSubrequirementsArray = $querySubrequirements->select('id_requirement', 'id_subrequirement')->get()->toArray();

		foreach ($idSubrequirementsArray as $idSubrequirement) {
			array_push($this->idSubrequirementsArray, $idSubrequirement);
		}

		return $idRequirementsArray;
	}

	/**
	 * Evaluate ONLY "SPECIFIC REQUIREMENTS"
	 */
	private function onlySpecificRequirements()
	{
		$idRequirementsArray = Requirement::where('id_requirement_type', RequirementType::REQUIREMENT_SPECIFIC)
			->where('id_aspect', $this->idAspect)->where('id_corporate', $this->idCorporate)->get()->pluck('id_requirement');

		return $idRequirementsArray;
	}

	/**
	 * Set requirements evaluate
	 */
	private function setEvaluate()
	{
		if ( $this->noModifyEvaluates ) {
			$info['success'] = true;
			$info['message'] = 'Solo clasificación visual';
			return $info;
		}
		
		try {
			$queryEvaluate = EvaluateRequirement::where('id_contract_aspect', $this->idContractAspect)->get();

			$newEvaluateIds = [];
			$previuosEvaluateIds = $queryEvaluate->pluck('id_evaluate_requirement')->toArray();

			foreach ($this->idRequirementsArray as $idRequirement) {
				$item = [
					'id_contract_aspect' => $this->idContractAspect,
					'id_requirement' => $idRequirement,
					'id_subrequirement' => null,
					'aplicability_register_id' => $this->idApplicabilityRegister
				];
				$evaluateReq = EvaluateRequirement::updateOrCreate($item, $item);
				array_push($newEvaluateIds, $evaluateReq->id_evaluate_requirement);
			}

			$uniqueListSubrequirements = collect($this->idSubrequirementsArray)
				->unique(fn($item) => $item['id_requirement'] . $item['id_subrequirement'])->values();

			if ($uniqueListSubrequirements->isNotEmpty()) {
				foreach ($uniqueListSubrequirements as $subrequirement) {
					$item = [
						'id_contract_aspect' => $this->idContractAspect,
						'id_requirement' => $subrequirement['id_requirement'],
						'id_subrequirement' => $subrequirement['id_subrequirement'],
						'aplicability_register_id' => $this->idApplicabilityRegister
					];
					$evaluateSub = EvaluateRequirement::updateOrCreate($item, $item);
					array_push($newEvaluateIds, $evaluateSub->id_evaluate_requirement);
				}
			}

			// destroy records
			$newEvaluateIds = collect($newEvaluateIds);
			$deleteEvaluateIds = collect($previuosEvaluateIds)->filter(function($previousId) use ($newEvaluateIds) {
				return !$newEvaluateIds->contains($previousId);
			});
			EvaluateRequirement::destroy($deleteEvaluateIds);

			$update = $this ->updateDataApplicability();
			if (!$update['success']) return $update;

			$info['success'] = true;
			$info['message'] = 'Requerimientos a evaluar establecidos';
			return $info;
		} catch (\Throwable $th) {
			$info['success'] = false;
			$info['message'] = 'Error al establecer requerimientos a evaluar';
			return $info;
		}
	}

	/**
	 * change status aspects and matters and set applicability_type
	 */
	private function updateDataApplicability()
	{
		try {
			$contractAspect = ContractAspect::findOrFail($this->idContractAspect);
      $contractAspect->update([
				'id_status' => Aplicability::CLASSIFIED_APLICABILITY, 
				'id_application_type' => $this->idApplicationType
			]);
			
			$contractMatter = ContractMatter::with('contract_aspects')->findOrFail($this->idContractMatter);
			$aspectStatusIds = $contractMatter->contract_aspects->pluck('id_status');
			$aspectSameStatus = $aspectStatusIds->unique()->count() == 1;
			if ( $aspectSameStatus ) {
				$contractMatter->update([ 'id_status' => $aspectStatusIds[0] ]);
			} else {
				$contractMatter->update(['id_status' => Aplicability::EVALUATING_APLICABILITY]);
			}

			$relationships = ['contract_matters', 'contract_matters.contract_aspects'];
			$aplicabilityRegister = AplicabilityRegister::with($relationships)->findOrFail($this->idApplicabilityRegister);
			$matterStatusIds = $aplicabilityRegister->contract_matters->pluck('id_status');
			$matterSameStatus = $matterStatusIds->unique()->count() == 1;
			if ( $matterSameStatus ) {
				$aplicabilityRegister->update([ 'id_status' => $matterStatusIds[0] ]);
			} else {
				$aplicabilityRegister->update(['id_status' => Aplicability::EVALUATING_APLICABILITY]);
			}

			$info['success'] = true;
			$info['message'] = 'Aplicabilidad actualizada';
			return $info;
		} catch (\Throwable $th) {
			$info['success'] = false;
			$info['message'] = 'Error al actualizar la competencia';
			return $info;
		}
	}
}
