<?php

namespace App\Classes\Files;

use App\Models\V2\Audit\Audit;
use App\Models\V2\Admin\Corporate;
use App\Models\V2\Admin\Customer;
use App\Models\V2\Admin\Image;
use App\Models\V2\News\News;
use App\Models\V2\Admin\User;
use App\Traits\V2\FileTrait;
use Illuminate\Http\File;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;

class StoreImage
{
  use FileTrait;
  
  public $loadFile = null;
  public $imageableId = null;
  public $imageableType = null;
  public $origin = null;
  public $usage = null;
  public $toDelete = [];

  public function __construct($loadFile, $imageableId, $origin, $usage = null) 
  {
    $this->loadFile = $loadFile;
    $this->imageableId = $imageableId;
    $this->origin = $origin;
    $this->usage = $usage;
    $this->getClassname($origin);
  }

  private function getClassname($origin) 
  {
    $names = [
      'customer' => Customer::class,
      'corporate' => Corporate::class,
      'user' => User::class,
      'audit' => Audit::class,
      'news' => News::class,
    ];
    $this->imageableType = $names[$origin];
  }

  private function buildStructureStroreFile()
  {
    // directory
    $storeVersion = 'storage/v2';
    $directory = "{$this->imageableId}/image";
    // attributes
    $fileSize = $this->loadFile->getSize();
    $fileType = $this->loadFile->getMimeType();
    $extension = Str::of( $this->loadFile->getClientOriginalExtension() )->lower();
    // filename
    $hash = hash('md5', microtime());
    $fileName = "{$hash}.{$extension}";
    $originalName = $this->loadFile->getClientOriginalName();
    // structure
    return [
      'attributes' => [
        'original_name' => $originalName,
        'hash_name' => $fileName,
        'store_version' => $storeVersion,
        'store_origin' => $this->origin,
        'directory' => $directory,
        'extension' => $extension,
        'file_type' => $fileType,
        'file_size' => $fileSize,
        'usage' => $this->usage,
        'imageable_id' => $this->imageableId,
        'imageable_type' => $this->imageableType,
      ],
      'store' => [
        'original_name' => $originalName,
        'directory' => $directory,
        'file_name' => $fileName,
        'file' => $this->loadFile,
      ]
    ];
  }

  public function storeImage()
  {
    try {
      DB::beginTransaction();
      $build = $this->buildStructureStroreFile();
      // store in db
      $store = $this->setImageable($build['attributes']);
      if ( !$store['success'] ) {
       DB::rollback();
        $response['success'] = false;
        $response['message'] = $store['message'];
        return $response;
      }
      // store in disk
      $put = $this->setImageInDisk($build['store']);
      if ( !$put['success'] ) {
        DB::rollback();
        $response['success'] = false;
        $response['message'] = $put['message'];
        return $response;
      }
      // delete if exist
      $delete = $this->deleteImageInDisk();
      if ( !$delete['success'] ) {
        DB::rollback();
        $response['success'] = false;
        $response['message'] = $delete['message'];
        return $response;
      }

      $response['success'] = true;
      $response['message'] = 'Registro exitoso';
      DB::commit();
      return $response;
    } catch (\Throwable $th) {
      DB::rollback();
      $response['success'] = false;
      $response['message'] = 'Algo salio mal cargando la imagen';
      $response['error'] = $th;
      return $response;
    }
  }

  private function setImageable($build)
  {
    try {
      // for customers
      if ($this->origin == 'customer') {
        $record = Customer::find($this->imageableId);
        $hasImage = $record->images->firstWhere('usage', $build['usage']);
      }
      // for corporates
      if ($this->origin == 'corporate') {
        $record = Corporate::find($this->imageableId);
        $hasImage = $record->image;
      }
      // for users
      if ($this->origin == 'user') {
        $record = User::find($this->imageableId);
        $hasImage = $record->image;
      }
      // for audits
      if ($this->origin == 'audit') {
        $record = Audit::find($this->imageableId);
        $hasImage = null;
      }
      // for news
      if ($this->origin == 'news') {
        $record = News::find($this->imageableId);
        $hasImage = $record->image;
      }
      // update image for customers, corporates, users, news
      if ( !is_null($hasImage) ) {
        array_push($this->toDelete, $hasImage);
        $imgInstance = Image::find($hasImage->id);
        $imgInstance->update($build);
      }
      // create image
      if ( is_null($hasImage) ) {
        $image = Image::create($build);
      }
      
      $response['success'] = true;
      $response['message'] = 'Registro exitoso';
      return $response;
    } catch (\Throwable $th) {
      $response['success'] = false;
      $response['message'] = "Algo salio mal al guardar información de la imagen {$build['original_name']}, intente nuevamente";
      $response['error'] = $th;
      return $response;
    }
  }

  private function setImageInDisk($build)
  {
    try {
      $disk = $this->getStorageEnviroment($this->origin);
      $put = Storage::disk($disk)->putFileAs($build['directory'], new File($build['file']), $build['file_name']);
      if ( Storage::disk($disk)->missing($put) ) {
        $response['success'] = false;
        $response['message'] = "No se pudo guardar la imagen {$build['original_name']}, intente nuevamente";
        return $response;
      }
      $response['success'] = true;
      $response['message'] = 'Registro exitoso';
      return $response;
    } catch (\Throwable $th) {
      $response['success'] = false;
      $response['message'] = "Algo salio mal en la carga de la imagen {$build['original_name']}, intente nuevamente";
      $response['error'] = $th;
      return $response;
    }
  }

  private function deleteImageInDisk()
  {
    try {
      foreach ($this->toDelete as $file) {
        $disk = $this->getStorageEnviroment($file->store_origin);
        $deleteFile = Storage::disk($disk)->delete($file->directory_path);
      }
      $this->toDelete = [];
      $response['success'] = true;
      $response['message'] = 'Registro exitoso';
      return $response;
    } catch (\Throwable $th) {
      $response['success'] = false;
      $response['message'] = 'Error al eliminar Imagen';
      $response['error'] = $th;
      return $response;
    }
  }
} 