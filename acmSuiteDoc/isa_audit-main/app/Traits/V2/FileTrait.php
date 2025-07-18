<?php

namespace App\Traits\V2;

use App\Models\V2\Audit\Library;
use App\Models\V2\Audit\File;
use Illuminate\Support\Facades\Storage;

trait FileTrait 
{
  /**
   * store disk: define in config/filesystems.php : disks []
   */
  public function getStorageEnviroment($disk) {
    $productionStorage = Config('enviroment.production_storage');
    $disks = [
      'public' => ($productionStorage) ? 'publicProduction' : 'publicDeveloper',
      'legals' => ($productionStorage) ? 'legalsProduction' : 'legalsDeveloper',
      'questionsHelpers' => ($productionStorage) ? 'questionsHelpersProduction' : 'questionsHelpersDeveloper',
      'files' => ($productionStorage) ? 'filesProduction' : 'filesDeveloper',
      'library' => ($productionStorage) ? 'libraryProduction' : 'libraryDeveloper',
      'image' => ($productionStorage) ? 'imageProduction' : 'imageDeveloper',
      'customer' => ($productionStorage) ? 'customerProduction' : 'customerDeveloper',
      'corporate' => ($productionStorage) ? 'corporateProduction' : 'corporateDeveloper',
      'user' => ($productionStorage) ? 'userProduction' : 'userDeveloper',
      'audit' => ($productionStorage) ? 'auditProduction' : 'auditDeveloper',
      'backup' => ($productionStorage) ? 'backupProduction' : 'backupDeveloper',
      'news' => ($productionStorage) ? 'newsProduction' : 'newsDeveloper',
    ];
    return $disks[$disk];
  }

  private function removeFiles($removeIds)
  {
    try {
      $libraries = Library::whereIn('id', $removeIds)->get();
      $files = $libraries->pluck('files')->collapse();
  
      foreach ($files as $file) {
        $file->delete();
      }

      foreach ($libraries as $library) {
        $library->delete();
      }

      foreach ($files as $file) {
        $disk = $this->getStorageEnviroment($file->store_origin);
        $deleteFile = Storage::disk($disk)->delete($file->directory_path);
      }

      $response['success'] = true;
      $response['message'] = 'Registro exitoso';
      return $response;
    } catch (\Throwable $th) {
      $response['success'] = false;
      $response['message'] = 'Error al eliminar Evidencia/documentos';
      return $response;
    }
  }

  public function deleteFile($idFile) {
    try {
        // delete record
        $file = File::findOrFail($idFile);
        $file->delete();
        $path = str_replace('storage/files/', '', $file->url);
        // delete file
        $disk = $this->getStorageEnviroment('files');
        $deleteFile = Storage::disk($disk)->delete($path);
        return $deleteFile;
    } catch (\Throwable $th) {
        return false;
    }      
  }
}