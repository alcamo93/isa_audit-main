<?php

namespace App\Http\Controllers\Catalogues;

use App\Http\Controllers\Controller;
use App\Providers\RouteServiceProvider;
use Illuminate\Http\Request;
use App\Classes\StatusConstants;
use App\Models\Catalogues\AnswerQuestionsDepedencyModel;
use App\Traits\HelpersQuestionTrait;

class AnswerQuestionsDepedencyController extends Controller
{
    use HelpersQuestionTrait;
     /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware('auth');
    }

    public function index(Request $request) {
        $idQuestion = $request->input('idQuestion');
        return response($this->init($idQuestion));
    }

    public function set(Request $request) {
        $requestData = $request->all();
        $dependencyData = $this->typeDependency($requestData);
        $setDependency = $this->setDependency($dependencyData);
        if ($setDependency != StatusConstants::SUCCESS) {
            $data['status'] = StatusConstants::ERROR;
            $data['msg'] = 'Algo salio mal, intente nuevamente';
            return response($data);
        }
        $data['status'] = StatusConstants::SUCCESS;
        $data['msg'] = 'Pregunta bloqueada para esta respuesta';
        return response($data);
    }

    public function remove(Request $request) {
        $requestData = $request->all();
        $dependencyData = $this->typeDependency($requestData);
        $removeDependency = $this->removeDependency($dependencyData);
        if ($removeDependency != StatusConstants::SUCCESS) {
            $data['status'] = StatusConstants::ERROR;
            $data['msg'] = 'Algo salio mal, intente nuevamente';
            return response($data);
        }
        $data['status'] = StatusConstants::SUCCESS;
        $data['msg'] = 'Pregunta desbloqueda para esta respuesta';
        return response($data);
    }
}
