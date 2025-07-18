<?php

namespace App\Classes\Forms;

class HandlerChangeArticles
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
  public function destroyRelationArticles($type, $record)
  {
    try {
      $options = collect(['question', 'requirement', 'subrequirement']);
      if ( !$options->contains($type) ) {
        $data['success'] = false;
        $data['messages'] = "No se pueden eliminar articulos del recurso ({$type})";
        return $data; 
      }
      // destroy relations
      $detachLegal = $record->articles->pluck('id_legal_basis')->toArray();
      $record->articles()->detach($detachLegal);
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