<?php

namespace App\Classes\Forms;

use App\Models\V2\Catalogs\LegalBasi;
use App\Models\V2\Catalogs\Guideline;

class HandlerChangeGuideline
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
	 * Verify if can update guideline
	 *
	 * @param \App\Models\V2\Catalogs\Guideline $guideline
	 * @param array $oldData
	 * @param string $method
	 * @return array
	 */
  public function canModifyGuideline($guideline, $oldData = [], $method = null) 
	{
    if ( !is_null($method) && $method == 'update' ) {
			$oldIdApplicationType = $oldData['id_application_type'];
			$oldIdState = $oldData['id_state'];
      $oldIdCity = $oldData['id_city'];
			$newIdApplicationType = $guideline->id_application_type;
			$newIdState = $guideline->id_state;
      $newIdCity = $guideline->id_city;
			if ($oldIdApplicationType == $newIdApplicationType && $oldIdState == $newIdState && $oldIdCity == $newIdCity) {
				$data['success'] = true;
				$data['messages'] = 'No se debe actualizar Artículos';
				return $data;
			}
		}

    if ( !is_null($method) && $method == 'update' ) {
      $verify = $this->verifyInRelation($guideline, $method);
      if ( !$verify['success'] ) {
        $data['success'] = false;
        $data['messages'] = $verify['messages'];
        return $data;
      }
			$update = $this->updateGuidelineChilds($guideline->id_guideline);
			$data['success'] = $update['success'];
			$data['messages'] = $update['messages'];
			return $data;
		}
	}

  /**
	 * Verify if can update guideline
	 *
	 * @param \App\Models\V2\Catalogs\Guideline $guideline
	 * @param string $method
	 * @return array
	 */
  private function verifyInRelation($guideline, $method)
  {
    $action = $method == 'update' ? 'actualizar' : 'eliminar';
		$hasQuestions = $guideline->articles->sum(fn($item) => $item->questions()->count() );
    $hasRequirements = $guideline->articles->sum(fn($item) => $item->requirements()->count() );
    $hasSubrequirements = $guideline->articles->sum(fn($item) => $item->subrequirements()->count() );
    $types = collect([
      [ 'name' => 'Preguntas', 'has' => boolval($hasQuestions) ],
      [ 'name' => 'Requerimientos', 'has' => boolval($hasRequirements) ],
      [ 'name' => 'Subrequerimientos', 'has' => boolval($hasSubrequirements) ],
    ]);
    
    $groups = $types->where('has', true);
    if ( $groups->isNotEmpty() ) {
      $string = $groups->pluck('name')->join(', ');
      $data['success'] = false;
      $data['messages'] = "El marco jurídico no puede ser {$action} ya que tiene artículos en {$string} relacionados";
      return $data;
    }

    $data['success'] = true;
    $data['messages'] = "Puede continuar";
    return $data;
  }

  /**
   * @param int $idGuideline
   */
  private function updateGuidelineChilds($idGuideline)
  {
    try {
      $guideline = Guideline::findOrFail($idGuideline); 
      $legalBasis = LegalBasi::where('id_guideline', $idGuideline);
      $legalBasis->update(['id_application_type' => $guideline->id_application_type]);
      $data['success'] = true;
      $data['messages'] = "Actualización exitosa";
      return $data;
    } catch (\Throwable $th) {
      $data['success'] = false;
      $data['messages'] = "Algo salio mal, al actulizar la competencia en artículos";
      return $data;
    }
  }
}