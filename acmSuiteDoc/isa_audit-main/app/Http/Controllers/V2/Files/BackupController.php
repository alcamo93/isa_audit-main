<?php

namespace App\Http\Controllers\V2\Files;

use App\Http\Controllers\Controller;
use App\Http\Requests\BackupRequest;
use App\Jobs\BuildBackupFiles;
use App\Models\V2\Admin\User;
use App\Models\V2\Audit\Backup;
use App\Models\V2\Audit\EvaluateRequirement;
use App\Models\V2\Audit\ProcessAudit;
use App\Traits\V2\FileTrait;
use App\Traits\V2\ResponseApiTrait;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;

class BackupController extends Controller
{
	use ResponseApiTrait, FileTrait;
	/**
	 * Redirect to view.
	 * @param integer $idBackup
	 */
	public function view($idBackup)
	{
		return view('v2.library.backup', ['idBackup' => $idBackup]);
	}

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
	 * @param  App\Http\Requests\BackupRequest  $request
	 * @return \Illuminate\Http\Response
	 */
	public function store(BackupRequest $request)
	{
		try {
			$idAuditProcess = $request->input('id_audit_processes');

			$noLoadRelations = ['customer','corporate','aplicability_register','evaluation_type','scope','auditors'];
      $process = ProcessAudit::without($noLoadRelations)->find($idAuditProcess);
			$data = EvaluateRequirement::forLibraryZip($idAuditProcess)->customOrder()->get();

			if ($data->isEmpty()) return $this->errorResponse('No hay archivos para Generar Respaldo.');

			$user = User::findOrFail(Auth::id());
			$evaluateIds = $data->pluck('id_evaluate_requirement');
			BuildBackupFiles::dispatch($idAuditProcess, $process->audit_processes, Auth::id(), $evaluateIds);

			$message = "Te avisaremos con notificaciones y un correo a la dirección {$user->email} cuando este listo para descargar";
			return $this->successResponse(['evaluate' => $idAuditProcess], Response::HTTP_OK, $message);
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
			$data = Backup::withDownload()->findOrFail($id);
			return $this->successResponse($data);
		} catch (\Throwable $th) {
			throw $th;
		}
	}

	/**
	 * Update the specified resource in storage.
	 *
	 * @param  \Illuminate\Http\Request  $request
	 * @param  int  $id
	 * @return \Illuminate\Http\Response
	 */
	public function update(Request $request, $id)
	{
		//
	}

	/**
	 * Remove the specified resource from storage.
	 *
	 * @param  int  $id
	 * @return \Illuminate\Http\Response
	 */
	public function destroy($id)
	{
		//
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
      $backup = Backup::findOrFail($id);
			$file = $backup->file;
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
      return $this->errorResponse('No se puede obtener el contenido del backup, intente nuevamente');
    } catch (\Throwable $th) {
      throw $th;
    }
  }
}
