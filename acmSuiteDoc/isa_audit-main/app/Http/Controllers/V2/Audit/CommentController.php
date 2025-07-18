<?php

namespace App\Http\Controllers\V2\Audit;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Traits\V2\ResponseApiTrait;
use App\Models\V2\Audit\Comment;

class CommentController extends Controller
{
  use ResponseApiTrait;

  /**
   * Display a listing of the resource.
   *
   * @return \Illuminate\Http\Response
   */
  public function index()
  {
    $data = Comment::included()->filter()->orderBy('created_at', 'DESC')->getOrPaginate();
    return $this->successResponse($data);
  }

  public function store(Request $request) 
  {
    try {
      $dataRequest = $request->all();
      $dataRequest['id_user'] = Auth::user()->id_user;
      $data = Comment::create($dataRequest);
      return $this->successResponse($data);
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
      $data = Comment::included()->findOrFail($id);
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
      $dataRequest = $request->all();
      $data = Comment::findOrFail($id);
      $dataRequest['id_user'] = Auth::user()->id_user;
      $data->update($dataRequest);
      $data->fresh();
      return $this->successResponse($data);
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
      $data = Comment::findOrFail($id);
      $data->delete();
      return $this->successResponse($data);
    } catch (\Throwable $th) {
      throw $th;
    }
  }
}