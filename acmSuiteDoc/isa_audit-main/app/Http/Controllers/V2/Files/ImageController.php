<?php

namespace App\Http\Controllers\V2\Files;

use App\Classes\Files\StoreImage;
use App\Http\Controllers\Controller;
use App\Models\V2\Admin\Image;
use App\Traits\V2\ResponseApiTrait;
use App\Traits\V2\FileTrait;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Storage;

class ImageController extends Controller
{
  use ResponseApiTrait, FileTrait;

  /**
   * Display a listing of the resource.
   *
   * @return \Illuminate\Http\Response
   */
  public function index()
  {
    // 
  }
  
  /**
   * Store a newly created resource in storage.
   *
   * @param  \App\Http\Requests\ProcessAuditRequest $request
   * @return \Illuminate\Http\Response
   */
  public function store(Request $request) 
  {
    try {
      $file = $request->file('file');
      $imageableId = $request->input('imageable_id');
      $origin = $request->input('imageable_type');
      $usage = $request->input('usage');
      $store = new StoreImage($file, $imageableId, $origin, $usage);
      $image = $store->storeImage();
      if ( !$image['success'] ) {
        return $this->errorResponse($image['message']);
      }
      return $this->successResponse($image);
    } catch (\Throwable $th) {  
      throw $th;
    }
  }

  /**
   * Display the specified resource.
   *
   * @param  int  $id
   * @return \Illuminate\Http\Response
   */
  public function show($id) 
  {
    try {
      $data = Image::included()->findOrFail($id);
      return $this->successResponse($data);
    } catch (\Throwable $th) {
      throw $th;
    }
  }

  /**
   * Remove the specified resource from storage.
   *
   * @param  Request  $request
   * @param  int  $id     
   * @return \Illuminate\Http\Response
   */
  public function update(Request $request, $id) 
  {
    try {
      // 
    } catch (\Throwable $th) {
      throw $th;
    }
  }

  /**
   * Remove the specified resource from storage.
   *
   * @param  int  $id
   * @return \Illuminate\Http\Response
   */
  public function destroy($id) 
  {
    try {
      // delete record
      $file = Image::findOrFail($id);
      // delete file
      $disk = $this->getStorageEnviroment($file->store_origin);
      $deleteFile = Storage::disk($disk)->delete($file->directory_path);
      if ( !$deleteFile ) {
        return $this->errorResponse('Hubo un problema al intentar eliminar la imagen');
      }
      $file->delete();
      return $this->successResponse($file);
    } catch (\Throwable $th) {
      return false;
    }    
  }

  /**
   * Download the specified resource from storage.
   *
   * @param  int  $id
   * @return \Illuminate\Http\Response
   */
  public function download($id)
  {
    try {
      $file = Image::findOrFail($id);
      $disk = $this->getStorageEnviroment($file->store_origin);
      $exist = Storage::disk($disk)->exists($file->directory_path);
      if ($exist) {
        $mimeType = $file->file_type;
        $fileSize = $file->file_size;
        $fileName = $file->name_download;
        $contents = Storage::disk($disk)->get($file->directory_path);
        // response
        $response = new Response($contents);
        $response->header('Content-Type', $mimeType);
        $response->header('Content-Disposition', 'attachment; filename="' . $fileName . '"');
        $response->header('Content-Length', $fileSize);
        $response->header('filename', $fileName);
        return $response;
      }
      return $this->errorResponse('No se puede obtener el contenido de este Archivo, intente nuevamente');
    } catch (\Throwable $th) {
      throw $th;
    }
  }
}