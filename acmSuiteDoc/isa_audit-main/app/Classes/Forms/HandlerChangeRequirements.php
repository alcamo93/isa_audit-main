<?php

namespace App\Classes\Forms;

use App\Models\V2\Audit\ProcessAudit;
use App\Models\V2\Catalogs\Form;
use App\Models\V2\Catalogs\Question;
use App\Models\V2\Catalogs\Requirement;
use App\Models\V2\Catalogs\Subrequirement;
use App\Classes\Forms\HandlerChangeArticles;
use App\Classes\Forms\HandlerChangeSubrequirements;

class HandlerChangeRequirements
{
  /**
   * constructor
   */
  public function __construct()
  {

  }

  /**
	 * Verify if can update requirement
	 *
	 * @param \App\Models\V2\Catalogs\Requirement $requirement
	 * @param array $oldData
	 * @return array
	 */
	public function canModifyRequirement($requirement, $oldData =[], $method = null) 
	{
    if ( !is_null($method) && $method == 'update' ) {
			$oldIdAnswerValue = $oldData['id_answer_value'];
			$newIdAnswerValue = $requirement->id_answer_value;
			if ($oldIdAnswerValue == $newIdAnswerValue) {
				$data['success'] = true;
				$data['messages'] = 'No se debe actualizar datos de Requerimiento';
				return $data;
			}
		}

    if ( !is_null($method) && $method == 'update' ) {
      $verify = $this->verifyInProcess($requirement, $method);
      if ( !$verify['success'] ) {
        $data['success'] = false;
        $data['messages'] = $verify['messages'];
        return $data;
      }
			$update = $this->updateRequirement($requirement->id_requirement);
			$data['success'] = $update['success'];
			$data['messages'] = $update['messages'];
			return $data;
		}

    if ( !is_null($method) && $method == 'destroy' ) {
      $verify = $this->verifyInProcess($requirement, $method);
      if ( !$verify['success'] ) {
        $data['success'] = false;
        $data['messages'] = $verify['messages'];
        return $data;
      }
			$destroy = $this->destroyContentRequirements([$requirement->id_requirement]);
			$data['success'] = $destroy['success'];
			$data['messages'] = $destroy['messages'];
			return $data;
		}
  }

  /**
	 * Verify if can update requirement
	 *
	 * @param \App\Models\V2\Catalogs\Requirement $requirement
	 * @param string $method
	 * @return array
	 */
  private function verifyInProcess($requirement, $method)
  {
    $action = $method == 'update' ? ' actualizar ' : ' eliminar ';
    $fields = $method == 'update' ? ' el "Requeriento" ' : '';
		$relationships = 'aplicability_register.evaluate_requirements';
		$condition = fn($query) => $query->where('id_requirement', $requirement->id_requirement);
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
   * @param array $requirementsIds
   * @param string $levelString
   * example: all|legals|recomendations|requirements|subrequirements
   */
  public function destroyContentRequirements($requirementsIds, $levelString = 'all')
  {
    try {
      $level = collect( explode('.', $levelString) );

      $requirements = Requirement::with('articles')->whereIn('id_requirement', $requirementsIds)->get();
      // destroy legals
      if ( $level->contains('all') || $level->contains('requirements') || $level->contains('legals') ) {
        foreach ($requirements as $requirement) {
          $handlerArticles = new HandlerChangeArticles();
          $destroyArticles = $handlerArticles->destroyRelationArticles('requirement', $requirement);
          if ( !$destroyArticles['success'] ) return $destroyArticles;
        }
      }
      // destroy recomendations
      if ( $level->contains('all') || $level->contains('requirements') || $level->contains('recomendations') ) {
        foreach ($requirements as $requirement) {
          $handlerArticles = new HandlerChangeRecomendations();
          $destroy = $handlerArticles->destroyRelationRecomendations('requirement', $requirement);
          if ( !$destroy['success'] ) return $destroy;
        }
      }
      // destroy subrequirements
      if ( $level->contains('all') || $level->contains('requirements') || $level->contains('subrequirements') ) {
        foreach ($requirements as $requirement) {
          $subrequirementIds = $requirement->subrequirements->pluck('id_subrequirement')->toArray();
          $handlerSubrequirements = new HandlerChangeSubrequirements();
          $destroySubrequirements = $handlerSubrequirements->destroyContentSubrequirements( $subrequirementIds );
          if ( !$destroySubrequirements['success'] ) return $destroySubrequirements;
        }
      }
      // destroy requirements
      if ( $level->contains('all') || $level->contains('requirements') ) {
        foreach ($requirements as $requirement) {
          $destroyRequirement = $requirement->delete();
          if (!$destroyRequirement) {
            $data['success'] = false;
            $data['messages'] = "No se pueden eliminar el requerimiento";
            return $data; 
          }
        }
      }

      $data['success'] = true;
      $data['messages'] = 'Eliminación exitosa';
      return $data;
    } catch (\Throwable $th) {
      $data['success'] = false;
      $data['messages'] = "No se pueden eliminar el recurso (requirement)";
      return $data; 
    }
  }

  public function updateRequirement() 
  {
    
  }
}