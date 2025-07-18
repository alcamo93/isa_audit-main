<?php

namespace App\Http\Controllers\V2\Audit;

use App\Http\Controllers\Controller;
use App\Http\Requests\CommentRequest;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Traits\V2\ResponseApiTrait;
use App\Models\V2\Audit\Comment;
use App\Models\V2\Audit\Task;

class TaskCommentController extends Controller
{
  use ResponseApiTrait;

  /**
   * Display a listing of the resource.
   *
   * @param  int  $idAuditProcess
   * @param  int  $idAplicabilityRegister
   * @param  string  $section
   * @param  int  $idSectionRegister
   * @param  int  $idActionRegister
   * @param  int  $idActionPlan
   * @param  int  $idTask
   * @return \Illuminate\Http\Response
   * 
   */
  public function index($idAuditProcess, $idAplicabilityRegister, $section, $idSectionRegister, $idActionRegister, $idActionPlan, $idTask)
  {
    $data = Comment::where('commentable_id', $idTask)->where('commentable_type', Task::class)
      ->commentData()->included()->filter()->getOrPaginate();
    return $this->successResponse($data);
  }

  /**
   * Display a listing of the resource.
   *
   * @param  CommentRequest  $request
   * @param  int  $idAuditProcess
   * @param  int  $idAplicabilityRegister
   * @param  string  $section
   * @param  int  $idSectionRegister
   * @param  int  $idActionRegister
   * @param  int  $idActionPlan
   * @param  int  $idTask
   * @return \Illuminate\Http\Response
   * 
   */
  public function store(CommentRequest $request, $idAuditProcess, $idAplicabilityRegister, $section, $idSectionRegister, $idActionRegister, $idActionPlan, $idTask) 
  {
    try {
      $dataRequest = $request->all();
      $dataRequest['id_user'] = Auth::user()->id_user;
      
      $task = Task::find($idTask);
      $data = $task->comments()->create($dataRequest);
      return $this->successResponse($data);
    } catch (\Throwable $th) {
      throw $th;
    }
  }

  /**
   * Display the specified resource.
   *
   * @param  int  $idAuditProcess
   * @param  int  $idAplicabilityRegister
   * @param  string  $section
   * @param  int  $idSectionRegister
   * @param  int  $idActionRegister
   * @param  int  $idActionPlan
   * @param  int  $idTask
   * @param  int  $idCommet
   * @return \Illuminate\Http\Response
   */
  public function show($idAuditProcess, $idAplicabilityRegister, $section, $idSectionRegister, $idActionRegister, $idActionPlan, $idTask, $idComment) 
  {
    try {
      $comment = Comment::included()->find($idComment);
      return $this->successResponse($comment);
    } catch (\Throwable $th) {
      throw $th;
    }
  }

  /**
   * Remove the specified resource from storage.
   *
   * @param  CommentRequest  $request
   * @param  int  $idAuditProcess
   * @param  int  $idAplicabilityRegister
   * @param  string  $section
   * @param  int  $idSectionRegister
   * @param  int  $idActionRegister
   * @param  int  $idActionPlan
   * @param  int  $idTask
   * @param  int  $idCommet
   * @return \Illuminate\Http\Response
   */
  public function update(CommentRequest $request, $idAuditProcess, $idAplicabilityRegister, $section, $idSectionRegister, $idActionRegister, $idActionPlan, $idTask, $idComment) 
  {
    try {
      $dataRequest = $request->all();
      $dataRequest['id_user'] = Auth::user()->id_user;
      
      $comment = Comment::find($idComment);
      $comment->update($dataRequest);
      $comment->fresh();

      return $this->successResponse($comment);
    } catch (\Throwable $th) {
      throw $th;
    }
  }

  /**
   * Remove the specified resource from storage.
   *
   * @param  int  $idAuditProcess
   * @param  int  $idAplicabilityRegister
   * @param  string  $section
   * @param  int  $idSectionRegister
   * @param  int  $idActionRegister
   * @param  int  $idActionPlan
   * @param  int  $idTask
   * @param  int  $idCommet
   * @return \Illuminate\Http\Response
   */
  public function destroy($idAuditProcess, $idAplicabilityRegister, $section, $idSectionRegister, $idActionRegister, $idActionPlan, $idTask, $idComment) 
  {
    try {
      $comment = Comment::find($idComment);
      $comment->delete();

      return $this->successResponse($comment);
    } catch (\Throwable $th) {
      throw $th;
    }
  }
}