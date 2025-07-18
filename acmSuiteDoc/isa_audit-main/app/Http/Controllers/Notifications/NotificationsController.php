<?php

namespace App\Http\Controllers\Notifications;

use App\Http\Controllers\Controller;
use App\Providers\RouteServiceProvider;
use Illuminate\Http\Request;
use App\Classes\StatusConstants;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use App\Models\Notifications\NotificationsModel;
use Illuminate\Notifications\DatabaseNotification;
use App\User;

class NotificationsController extends Controller
{
    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware('auth');
    }
    /**
     * Show notifications
     */
    public function index(){
        $arrayData['idUser'] = Auth::user()->id_user;
        return view('notifications.main', $arrayData);
    }
    /**
     * Unread notifications datatable
     */
    public function unreadDT(Request $request){
        $page = $request->input('start');
        $rows = $request->input('length');
        $search = $request->input('search');
        $draw = $request->input('draw');
        $order = $request->input('order');
        $idUser = $request->input('idUser');
        $status = $request->input('status');
        $data = NotificationsModel::GetNotificationsDT($page, $rows, $search, $draw, $order, $idUser, $status);
        return response($data);
    }
    /**
     * Marked like read
     */
    public function markedRead(Request $request){
        $idNotification = $request->input('idNotification');
        DatabaseNotification::find($idNotification)->markAsRead();
        $data['status'] = StatusConstants::SUCCESS;
        return response($data);
    }
    /**
     * Delete like read
     */
    public function destroy(Request $request){
        $idNotification = $request->input('idNotification');
        DatabaseNotification::find($idNotification)->delete();
        $data['status'] = StatusConstants::SUCCESS;
        return response($data);
    }
    /**
     * total notification
     */
    public function totalNotification(Request $request){
        $total = Auth::user()->unreadNotifications->count();
        return response($total);
    }
    /**
     * Total notification push
     */
    public static function totalNotificationPush($idUser){
        $total = User::find($idUser)->unreadNotifications->count();
        return $total;
    }
} 