<?php

namespace App\Classes\Library;

use App\Models\V2\Audit\Library;
use Illuminate\Support\Facades\Auth;

class LibraryAction 
{

  protected $libraryId = null;
  protected $library = null;

  public function __construct($libraryId)
  {
    $this->libraryId = $libraryId;
    $this->library = Library::find($libraryId);
  }

  /**
   * 
   * @param strign $action
   * @param array $values
   * 
   */
  public function setAction($action, $values)
  {
    try {
      $data['action'] = $action;
      $data['data'] = json_encode($values);
      $data['user_id'] = Auth::id();

      $this->library->actions()->create($data);

      $record['success'] = true;
      $record['message'] = 'Seguimiento guardado exitosamente';
      return $record;
    } catch (\Throwable $th) {
      $record['success'] = false;
      $record['message'] = 'No se ha podido guardar datos de seguimiento de la acción, intenta nuevamente';
      return $record;
    }
  }
}