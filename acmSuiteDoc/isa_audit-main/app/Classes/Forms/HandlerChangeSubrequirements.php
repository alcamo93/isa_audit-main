<?php

namespace App\Classes\Forms;

use App\Models\V2\Audit\ProcessAudit;
use App\Models\V2\Catalogs\Form;
use App\Models\V2\Catalogs\Question;
use App\Models\V2\Catalogs\Requirement;
use App\Models\V2\Catalogs\Subrequirement;
use App\Classes\Forms\HandlerChangeArticles;
use App\Classes\Forms\HandlerChangeRecomendations;

class HandlerChangeSubrequirements
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
	 * @param \App\Models\V2\Catalogs\Subrequirement $subrequirement
	 * @param array $oldData
   * @param string $method
	 * @return array
	 */
	public function canModifySubrequirement($subrequirement, $oldData =[], $method = null) 
	{

    if ( !is_null($method) && $method == 'update' ) {
			// rule for change data in subrequirement
		}

    // search subrequirement in use
    if ( !is_null($method) && $method == 'destroy' ) {
      $verify = $this->verifyInProcess($subrequirement, $method);
      if ( !$verify['success'] ) {
        $data['success'] = false;
        $data['messages'] = $verify['messages'];
        return $data;
      }
			$destroy = $this->destroyContentSubrequirements([$subrequirement->id_subrequirement]);
			$data['success'] = $destroy['success'];
			$data['messages'] = $destroy['messages'];
			return $data;
		}
  }

  /**
	 * Verify if can update requirement
	 *
	 * @param \App\Models\V2\Catalogs\Subrequirement $subrequirement
	 * @param string $method
	 * @return array
	 */
  private function verifyInProcess($subrequirement, $method) 
  {
    $action = $method == 'update' ? ' actualizar ' : ' eliminar ';
    $relationships = 'aplicability_register.evaluate_requirements';
    $condition = fn($query) => $query->where('id_subrequirement', $subrequirement->id_subrequirement);
    $inProcess = ProcessAudit::whereHas($relationships, $condition);
    if ( $inProcess->exists() ) {
      $count = $inProcess->count();
      $data['success'] = false;
      $data['messages'] = "No es posible {$action} el Valor de Subrequeriento ya que se usa en {$count} ejercicios";
      return $data;
    }
    $data['success'] = true;
    $data['messages'] = "Puede continuar";
    return $data;
  }

  /**
   * @param array $subrequirementsIds
   * @param string $levelString
   * example: all|legals|recomendations|subrequirements
   */
  public function destroyContentSubrequirements($subrequirementsIds, $levelString = 'all')
  {
    try {
      $level = collect( explode('.', $levelString) );
      $subrequirements = Subrequirement::with('articles')->whereIn('id_subrequirement', $subrequirementsIds)->get();
      // destroy legals
      if ( $level->contains('all') || $level->contains('subrequirements') || $level->contains('legals') ) {
        foreach ($subrequirements as $subrequirement) {
          $handlerArticles = new HandlerChangeArticles();
          $destroy = $handlerArticles->destroyRelationArticles('subrequirement', $subrequirement);
          if ( !$destroy['success'] ) return $destroy;
        }
      }
      // destroy recomendations
      if ( $level->contains('all') || $level->contains('subrequirements') || $level->contains('recomendations') ) {
        foreach ($subrequirements as $subrequirement) {
          $handlerArticles = new HandlerChangeRecomendations();
          $destroy = $handlerArticles->destroyRelationRecomendations('subrequirement', $subrequirement);
          if ( !$destroy['success'] ) return $destroy;
        }
      }
      // destroy subrequirements
      if ( $level->contains('all') || $level->contains('subrequirements') ) {
        foreach ($subrequirements as $subrequirement) {
          $destroySubrequirement = $subrequirement->delete();
          if (!$destroySubrequirement) {
            $data['success'] = false;
            $data['messages'] = "No se pueden eliminar el subrequerimiento";
            return $data; 
          }
        }
      }

      $data['success'] = true;
      $data['messages'] = 'Eliminación exitosa';
      return $data;
    } catch (\Throwable $th) {
      $data['success'] = false;
      $data['messages'] = "No se pueden eliminar el recurso (subrequirement)";
      return $data; 
    }
  }
}