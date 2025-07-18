<?php

namespace App\Http\Controllers\Audit;

use App\Http\Controllers\Controller;
use App\Providers\RouteServiceProvider;
use Illuminate\Foundation\Auth\AuthenticatesUsers;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Session;
use App\Classes\StatusConstants;
use App\Models\Audit\CommentsModel;
use App\Models\Catalogues\StatusModel;
use App\Models\Catalogues\PeriodModel;
use App\Models\Catalogues\ConditionsModel;
use App\User;

class CommentsController extends Controller
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
     * Get comments
     */
    public function getComments(Request $request){
        $idActionPlan = $request->input('idActionPlan');
        $idTask = $request->input('idTask');
        $idObligation = $request->input('idObligation');
        $comments = CommentsModel::with(['task','user.person'])->where([
            ['id_action_plan', $idActionPlan],
            ['id_task', $idTask],
            ['id_obligation', $idObligation]
        ])->orderBy('created_at', 'desc')->get();
        return response($comments);
    }
    /**
     * Set comment
     */
    public function setComment(Request $request) {
        $idComment = $request->input('idComment');
        $comment = $request->input('comment');
        $idUser = Auth::user()->id_user;
        $idActionPlan = $request->input('idActionPlan');
        $idTask = $request->input('idTask');
        $idObligation = $request->input('idObligation');
        $stage = $request->input('stage');
        $set = CommentsModel::SetComment($idComment, $comment, $idUser, $idActionPlan, $idTask, $idObligation, $stage);
        if ($set != StatusConstants::SUCCESS) {
            $data['status'] = StatusConstants::ERROR;
            $data['msg'] = 'Algo salio mal, intente nuevamente';            
            return response($data);
        }
        $data['status'] = StatusConstants::SUCCESS;
        $data['msg'] = 'Comentario exitoso';            
        return response($data);
    }
    /**
    * Delete comment
    */
    public function deleteComment(Request $request) {
        $idComment = $request->input('idComment');
        $delete = CommentsModel::deleteComment($idComment);
        if ($delete != StatusConstants::SUCCESS) {
            $data['status'] = StatusConstants::ERROR;
            $data['msg'] = 'Algo salio mal, intente nuevamente';            
            return response($data);
        }
        $data['status'] = StatusConstants::SUCCESS;
        $data['msg'] = 'Comentario eliminado';            
        return response($data);
    }
}