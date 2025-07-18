<?php

namespace App\Http\Controllers\V2\Files;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use App\Models\V2\Audit\File;
use App\Traits\V2\ResponseApiTrait;
use App\Traits\V2\FileTrait;

class FileController extends Controller
{
  use ResponseApiTrait, FileTrait;

  /**
   * Display a listing of the resource.
   *
   * @return \Illuminate\Http\Response
   */
  public function index()
  {
    $data = File::included()->filter()->getOrPaginate();
    return $this->successResponse($data);
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
      DB::beginTransaction();
      
      DB::commit();
    } catch (\Throwable $th) {
      DB::rollback();
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
      $data = File::included()->findOrFail($id);
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
      DB::beginTransaction();
      
      DB::commit();
      return $this->successResponse($id);
    } catch (\Throwable $th) {
      DB::rollback();
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
      DB::beginTransaction();
      
      DB::commit();
      return $this->successResponse($id);
    } catch (\Throwable $th) {
      DB::rollback();
      throw $th;
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
      $file = File::findOrFail($id);
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

  /**
   * Download the specified resource from storage.
   *
   * @param  int  $id
   * @return \Illuminate\Http\Response
   */
  public function downloadBase($id)
  {
    try {
      $file = File::findOrFail($id);
      $disk = $this->getStorageEnviroment($file->store_origin);
      $exists = Storage::disk($disk)->exists($file->directory_path);

      if (!$exists) {
        return $this->errorResponse('No se puede obtener el contenido de este Archivo, intente nuevamente');
      }

      $mimeType = $file->file_type;
      $contents = Storage::disk($disk)->get($file->directory_path);
      $base64 = base64_encode($contents);
      $dataUri['data'] = "data:{$mimeType};base64,{$base64}";

      return $this->successResponse($dataUri);
    } catch (\Throwable $th) {
      throw $th;
    }
  }
}