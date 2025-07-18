<?php

namespace App\Classes\Forms;

use App\Models\V2\Audit\ProcessAudit;
use App\Models\V2\Catalogs\Form;
use App\Models\V2\Catalogs\Question;
use App\Models\V2\Catalogs\Requirement;
use App\Models\V2\Catalogs\Subrequirement;
use App\Classes\Forms\HandlerChangeQuestions;
use App\Classes\Forms\HandlerChangeRequirements;

class HandlerChangeForms
{
  public $section = null;
  public $idSection = null;
  public $changes = [];

  /**
   * constructor
   */
  public function __construct()
  {

  }

  /**
	 * Verify if can update form
	 *
	 * @param \App\Models\V2\Catalogs\Form $form
	 * @param array $oldData
	 * @param string $method
	 * @return array
	 */
	public function canModifyForm($form, $oldData = null, $method = null) 
	{
		if ( !is_null($method) && $method == 'update' ) {
			$oldIdMatter = $oldData['matter_id'];
			$oldIdAspect = $oldData['aspect_id'];
			$newIdMatter = $form->matter_id;
			$newIdAspect = $form->aspect_id;
			if ($oldIdMatter == $newIdMatter && $oldIdAspect == $newIdAspect) {
				$data['success'] = true;
				$data['messages'] = 'No se debe actualizar datos de Preguntas o Requerimientos';
				return $data;
			}
		}
		
		if ( !is_null($method) && $method == 'update' ) {
			$verify = $this->verifyInProcess($form, $method);
      if ( !$verify['success'] ) {
        $data['success'] = false;
        $data['messages'] = $verify['messages'];
        return $data;
      }
			$update = $this->updateMatterAspect($form->id);
			$data['success'] = $update['success'];
			$data['messages'] = $update['messages'];
			return $data;
		}

		if ( !is_null($method) && $method == 'destroy' ) {
			$verify = $this->verifyInProcess($form, $method);
      if ( !$verify['success'] ) {
        $data['success'] = false;
        $data['messages'] = $verify['messages'];
        return $data;
      }
			$destroy = $this->destroyContentForm($form->id);
			$data['success'] = $destroy['success'];
			$data['messages'] = $destroy['messages'];
			return $data;
		}
	}

	/**
	 * Verify if can update form
	 *
	 * @param \App\Models\V2\Catalogs\Form $form
	 * @param string $method
	 * @return array
	 */
	private function verifyInProcess($form, $method)
	{
		$action = $method == 'update' ? ' actualizar ' : ' eliminar ';
		$fields = $method == 'update' ? ' la" Materia/Aspecto" ' : '';
		$relationships = 'aplicability_register.contract_matters.contract_aspects';
		$condition = fn($query) => $query->where('form_id', $form->id);
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
	 * Update matter and aspect in cascade
	 *
	 * @param int $id
	 * @return array
	 */
  private function updateMatterAspect($idForm)
	{
		try {
			$form = Form::findOrFail($idForm);
			// update questions
			$questions = Question::where('form_id', $idForm);
			$questions->update(['id_matter' => $form->matter_id, 'id_aspect' => $form->aspect_id]);
			// update requirements
			$requirements = Requirement::with('subrequirements')->where('form_id', $idForm);
			$requirements->update(['id_matter' => $form->matter_id, 'id_aspect' => $form->aspect_id]);
			// update subrequirements
			$subrequirementsIds = $requirements->get()->pluck('subrequirements')->collapse()->pluck('id_subrequirement');
			$subrequirements = Subrequirement::whereIn('id_subrequirement', $subrequirementsIds);
			$subrequirements->update(['id_matter' => $form->matter_id, 'id_aspect' => $form->aspect_id]);

			$data['success'] = true;
			$data['messages'] = 'Actualización exitosa';
			return $data;
		} catch (\Throwable $th) {
			$data['success'] = false;
			$data['messages'] = 'Algo salio mal al actualizar los Cuestionarios de Aplicabilidad y Requerimientos de Auditoría';
		}
	}

  /**
   * 
   */
  public function destroyContentForm($idForm)
	{
		try {
			// destroy questions
			$questions = Question::where('form_id', $idForm)->get();
      $questionsIds = $questions->pluck('id_question')->toArray();
      $handlerQuestion = new HandlerChangeQuestions();
      $destroyQuestions = $handlerQuestion->destroyContentQuestions( $questionsIds );
      if ( !$destroyQuestions['success'] ) {
        $data['success'] = false;
        $data['messages'] = $destroyQuestions['messages'];
        return $data; 
      }
      // destroy requirements 
			$requirements = Requirement::where('form_id', $idForm)->get();
      $requirementsIds = $requirements->pluck('id_requirement')->toArray();
      $handlerRequirements= new HandlerChangeRequirements();
      $destroyRequirements = $handlerRequirements->destroyContentRequirements( $requirementsIds );
      if ( !$destroyRequirements['success'] ) {
        $data['success'] = false;
        $data['messages'] = $destroyRequirements['messages'];
        return $data; 
      }
			// destroy form
			$form = Form::findOrFail($idForm);
			$destroyForm = $form->delete();
			if ( !$destroyForm ) {
        $data['success'] = false;
        $data['messages'] = 'No se pudo eliminar el Formulario';
        return $data; 
      }
			$data['success'] = true;
			$data['messages'] = 'Eliminación exitosa';
			return $data;
		} catch (\Throwable $th) {
			$data['success'] = false;
			$data['messages'] = 'Algo salio mal al eliminar el Formulario';
			return $data;
		}
	}
}