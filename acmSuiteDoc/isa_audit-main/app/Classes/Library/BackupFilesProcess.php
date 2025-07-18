<?php

namespace App\Classes\Library;

use App\Models\V2\Admin\Image;
use App\Models\V2\Admin\User;
use App\Models\V2\Audit\Backup;
use App\Models\V2\Audit\EvaluateRequirement;
use App\Notifications\BackupFilesNotification;
use App\Traits\V2\FileTrait;
use App\Traits\V2\RequirementTrait;
use Carbon\Carbon;
use Illuminate\Http\File;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Storage;
use ZipArchive;

class BackupFilesProcess
{
  use FileTrait, RequirementTrait;

  protected $idAuditProcess = null;
  protected $processName = null;
  protected $user = null;
  protected $data = null;
  protected $files = null;
  protected $evaluateIds = [];

  /**
   * @param integer  $idAuditProcess
   * @param integer  $processName
   * @param integer App\Models\V2\Admin\User $idUser
   * @param integer Array App\Models\V2\Admin\User $evaluateIds
   * 
   */
  public function __construct($idAuditProcess, $processName, $idUser, $evaluateIds)
  {
    $this->idAuditProcess = $idAuditProcess;
    $this->processName = $processName;
    $this->user = User::findOrFail($idUser);
    $this->evaluateIds = $evaluateIds;
  }

  public function buildBackup()
  {
    try {
      Log::channel('backups')->info("********* Backup build - {$this->idAuditProcess} *********");
      /**
       * Always create a zip file locally and then send it to the disk where it will 
       * be stored AWS (backupLibraryProduction) or locally (backupLibraryDevelop).
       */
      $diskTemp = 'backupTmp';
      $hash = hash('md5', microtime());
      $zipFileName = "backup_{$hash}.zip";
      $zip = new ZipArchive();

      $zipFilePath = Storage::disk($diskTemp)->path($zipFileName);

      if ( !$zip->open($zipFilePath, ZipArchive::CREATE) ) {
        $notification = $this->notifcationMessages('error');
        Log::channel('backups')->error(['email' => $this->user['email'], 'title' => $notification['title'] ]);
        $this->user->notify( new BackupFilesNotification( $notification ) );
        return;
      }

      $files = $this->buildFiles();
      if ( $files->isEmpty() ) {
        $notification = $this->notifcationMessages('void');
        Log::channel('backups')->error(['email' => $this->user['email'], 'title' => $notification['title'] ]);
        $this->user->notify( new BackupFilesNotification( $notification ) );
        return;
      }

      $files->each(function($file) use ($zip) {
        $fileContent = Storage::disk($file['disk'])->get($file['path']);
        $zip->addFromString(basename($file['name']), $fileContent);
      });
      
      $zip->close();

      $timezone = Config('enviroment.time_zone_carbon');
      $today = Carbon::now($timezone);
      
      $infoBackup['init_date'] = $today->toDatetimeString();
      $infoBackup['end_date'] = $today->addDay()->toDatetimeString();
      $infoBackup['id_user'] = $this->user['id_user'];
      $infoBackup['id_audit_processes'] = $this->idAuditProcess;
      
      $backup = Backup::create($infoBackup);
      if ( !$backup ) {
        $notification = $this->notifcationMessages('error');
        Log::channel('backups')->error(['email' => $this->user['email'], 'title' => $notification['title'] ]);
        $this->user->notify( new BackupFilesNotification( $notification ) );
        return;
      }

      $finalDisk = 'backup';
      $infoFile['original_name'] = $zipFileName;
      $infoFile['hash_name'] = $zipFileName;
      $infoFile['store_version'] = 'storage/v2';
      $infoFile['store_origin'] = $finalDisk;
      $infoFile['directory'] = $this->user['id_user'];
      $infoFile['extension'] = 'zip';
      $infoFile['file_type'] = 'application/zip';
      $infoFile['file_size'] = Storage::disk($diskTemp)->size($zipFileName);
      $infoFile['imageable_id'] = $backup['id'];
      $infoFile['imageable_type'] = Backup::class;
      
      $image = Image::create($infoFile);
      if ( !$image ) {
        $notification = $this->notifcationMessages('error');
        Log::channel('backups')->error(['email' => $this->user['email'], 'title' => $notification['title'] ]);
        $this->user->notify( new BackupFilesNotification( $notification ) );
        return;
      }

      $diskForUser = $this->getStorageEnviroment($finalDisk);
      $setFile = Storage::disk($diskForUser)->putFileAs($this->user['id_user'], new File($zipFilePath), $zipFileName);
      Storage::disk($diskTemp)->delete($zipFileName);
      
      if ( !$setFile ) {
        $notification = $this->notifcationMessages('error');
        Log::channel('backups')->error(['email' => $this->user['email'], 'title' => $notification['title'] ]);
        $this->user->notify( new BackupFilesNotification( $notification ) );
        return;
      }

      $domain = Config('enviroment.domain_frontend');
      $downloadUrl = "{$domain}v2/backup/{$backup['id']}/view";
      
      $info = $this->notifcationMessages('success');
      $info['link'] = $downloadUrl;
      Log::channel('backups')->info([ 
        'id_audit_processes' => $this->idAuditProcess,
        'backup_id' => $backup['id'],
        'email' => $this->user['email'],
        'link' => $downloadUrl
      ]);
      $this->user->notify( new BackupFilesNotification( $info ) );
    } catch (\Throwable $th) {
      $notification = $this->notifcationMessages('error');
      Log::channel('backups')->error(['email' => $this->user['email'], 'title' => $notification['title'], 'general' => 'Error general' ]);
      $this->user->notify( new BackupFilesNotification( $notification ) );
    }
  }
  /**
   * Generate Files
   */
  private function buildFiles()
  {
    $relationships = ['library.files', 'requirement.matter', 'requirement.aspect', 'subrequirement.matter', 'subrequirement.aspect'];
    $this->data = EvaluateRequirement::with($relationships)
      ->whereIn('id_evaluate_requirement', $this->evaluateIds)->customOrder()->get();
    
    $files = $this->data->map(function ($evaluate) {
      try {
        $info['matter'] = $this->getFieldRequirement($evaluate, 'matter');
        $info['aspect'] = $this->getFieldRequirement($evaluate, 'aspect');
        $info['no_requirement'] = $this->getFieldRequirement($evaluate, 'no_requirement');
        return $evaluate->library->files->map(function ($file) use ($info) {
          $info['init_date'] = $file['init_date_format'];
          $info['end_date'] = $file['end_date_format'];
          $info['extension'] = $file['extension'];
          // info file
          $data['name'] = $this->buildNameFile($info);
          $data['disk'] = $this->getStorageEnviroment($file['store_origin']);
          $data['path'] = $file['directory_path'];
          return $data;
        });
      } catch (\Throwable $th) {
        Log::channel('backups')->error($evaluate['id_evaluate_requirement']);
      }
    })->collapse()->filter(function($file) {
      return Storage::disk($file['disk'])->exists($file['path']);
    })->values();

    return $files;
  }

  private function buildNameFile($data)
  {
    $uniqId = uniqid();
    $filename = "{$data['matter']}_{$data['aspect']}_{$data['no_requirement']}_{$data['init_date']}_{$data['end_date']}_{$uniqId}.{$data['extension']}";
    $fileNameNormal = Str::of($filename)->replace(' ', '_')->replace('\\', '-')->replace('/', '-');
    return $fileNameNormal;
  }

  /**
   * @param string $type
   */
  private function notifcationMessages($type)
  {
    $message['success'] = [
      'success' => true,
      'title' => 'Respaldo de Mi Biblioteca Listo',
      'body' => "El respaldo solicitado de los archivos de la evaluación {$this->processName}
        ya esta listo para su descarga",
      'description' => '',
      'title_button' => 'Descargar',
      'link' => '/'
    ];

    $message['void'] = [
      'success' => false,
      'title' => 'Nada que respaldar de Mi Biblioteca',
      'body' => "El respaldo solicitado de los archivos de la evaluación {$this->processName}
        tiene registros en biblioteca pero no el contenido de los archivos",
      'description' => '',
      'title_button' => 'ir a mi biblioteca',
      'link' => '/'
    ];

    $message['error'] = [
      'success' => false,
      'title' => 'Error al generar respaldo de Mi Biblioteca',
      'body' => "El respaldo solicitado de los archivos de la evaluación {$this->processName}
        tuvo errores al generarse por favor intenta de nuevo o contacta al administrador",
      'description' => '',
      'title_button' => 'ir a mi biblioteca',
      'link' => '/'
    ];

    return $message[$type] ?? $message['error'];
  }
}
