<?php

namespace App\Http\Controllers\V2;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Notifications\DatabaseNotification;
use App\Models\V2\Admin\Notification;
use App\Traits\V2\ResponseApiTrait;

class NotificationController extends Controller
{
  use ResponseApiTrait;

  /**
   * Redirect to view.
   */
  public function view() 
  {
    // 
  }

  /**
   * Display a listing of the resource.
   *
   * @return \Illuminate\Http\Response
   */
  public function index()
  {
    $data = Notification::included()->filter()->authFilters()->getOrPaginate();
    return $this->successResponse($data);
  }

  /**
   * Store a newly created resource in storage.
   *
   * @param  \App\Http\Requests\NotificationRequest $request
   * @return \Illuminate\Http\Response
   */
  public function store(Request $request) 
  {
    // 
  }

  /**
   * Display the specified resource.
   *
   * @param  int  $id
   * @return \Illuminate\Http\Response
   */
  public function show($id) 
  {
    // 
  }

  /**
   * Update the specified resource in storage.
   *
   * @param  \App\Http\Requests\NotificationRequest  $request
   * @param  int  $id
   * @return \Illuminate\Http\Response
   */
  public function update(Request $request) 
  {
    try {
      $id = $request->input('id');
      $data = DatabaseNotification::find($id)->markAsRead();
      return $this->successResponse(['id' => $id]);
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
      $data = DatabaseNotification::find($id)->delete();
      return $this->successResponse($data);
    } catch (\Throwable $th) {
      throw $th;
    }
  }

  /**
   * Get notification unread
   */
  public function total(){
    try {
      $data = Notification::authFilters()->unreadNotifications()->count();
      return $this->successResponse(['total' => $data]);
    } catch (\Throwable $th) {
      throw $th;
    }
  }
} 