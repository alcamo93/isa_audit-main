<?php

namespace App\Classes\Forms;

use App\Models\V2\Catalogs\RequirementRecomendation;
use App\Models\V2\Catalogs\SubrequirementRecomendation;
class HandlerChangeRecomendations
{
  /**
   * constructor
   */
  public function __construct()
  {

  }

  /**
   * Destroy relations articles and questions
   */
  public function destroyRelationRecomendations($type, $record)
  {
    try {
      $options = collect(['requirement', 'subrequirement']);
      if ( !$options->contains($type) ) {
        $data['success'] = false;
        $data['messages'] = "No se pueden eliminar recomendaciones del recurso ({$type})";
        return $data; 
      }
      // destroy relations
      $items = $record->recomendations->pluck('id_recomendation')->toArray();
      if ($type == 'requirement') {
        $recomendations = RequirementRecomendation::whereIn('id_recomendation', $items);
      } else {
        $recomendations = SubrequirementRecomendation::whereIn('id_recomendation', $items);
      }
      $recomendations->delete();
      $data['success'] = true;
      $data['messages'] = 'Eliminación exitosa';
      return $data;
    } catch (\Throwable $th) {
      $data['success'] = false;
      $data['messages'] = "Algo salio mal al eliminar los artículos ({$type})";
      return $data;
    }
  }
}