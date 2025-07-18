<?php

namespace App\Traits;

use App\Models\Audit\TasksModel;
use App\Models\Admin\ProcessesModel;
use App\Models\Audit\ObligationsModel;
use App\Models\Files\FilesModel;
use KubAT\PhpSimple\HtmlDomParser;
use Config;

trait FileTrait {
    /**
     * store disk: define in config/filesystems.php : disks []
     */
    public function getStorageEnviroment($disk) {
        $productionStorage = Config('enviroment.production_storage');
        $disks = [
            'public' => ($productionStorage) ? 'publicProduction' : 'publicDeveloper',
            'legals' => ($productionStorage) ? 'legalsProduction' : 'legalsDeveloper',
            'questionsHelpers' => ($productionStorage) ? 'questionsHelpersProduction' : 'questionsHelpersDeveloper',
            'files' => ($productionStorage) ? 'filesProduction' : 'filesDeveloper',
            'newsBody' => ($productionStorage) ? 'newsBodyProduction' : 'newsBodyDeveloper'
        ];
        return $disks[$disk];
    }

    public function deleteFile($idFile) {
        try {
            // delete record
            $file = FilesModel::findOrFail($idFile);
            $file->delete();
            $path = str_replace('storage/files/', '', $file->url);
            // delete file
            $disk = $this->getStorageEnviroment('files');
            $deleteFile = Storage::disk($disk)->delete($path);
            return $deleteFile;
        } catch (\Throwable $th) {
            return false;
        }      
    }
    /**
     * set url in production for view
     */
    public function setUrlInRichText($richText) {
        $productionStorage = Config('enviroment.production_storage');
        $imageProductionInDev = Config('enviroment.view_image_production_in_dev');
        $domain = Config('enviroment.aws_url_view');
        if ($productionStorage || $imageProductionInDev) {
            $html = HtmlDomParser::str_get_html($richText);
            foreach ($html->find('img') as $tag) {
                $imgSource = $tag->src;
                $tag->src = $domain.$imgSource;
            }
            return $html->save();
        } else {
            return $richText;
        }
    }

    public function buildActionNotify($idTask, $documentName) {
        $processCallback = function($query) {
            $query->addSelect([
                'audit_processes' => ProcessesModel::select('audit_processes')
                ->whereColumn('id_audit_processes', 't_action_plans.id_audit_processes')
                ->take(1)
            ]);
        };
        $relationships = [
            'users.user.person',
            'action' => $processCallback,
            'action.requirement', 
            'action.subrequirement',
            'action.users.user.person',
        ];
        $task = TasksModel::with($relationships)->find($idTask);
        $forName = $task->action;
        $reqName = ( is_null($forName->subrequirement) ) ? $forName->requirement->no_requirement : $forName->requirement->no_requirement.' - '.$forName->subrequirement->no_subrequirement;
        $data['notifyData'] = [
            'title' => 'Documento para revisión',
            'body' => 'Se ha cargado el documento <b>'.$documentName.'</b>                 
                en la tarea <b>'.$task->title.'</b> 
                para el requerimiento: <b>'.$reqName.'</b> correspodiente 
                a la auditoría <b>'.$forName->audit_processes.'</b>',
            'description' => '',
            'link' => 'action'
        ];
        // responsible for closing
        $taskUser = collect($task->action->users);
        $user = $taskUser->firstWhere('level', 1);
        $data['userToNotify'] = $user->id_user;
        $data['data'] = $task->toArray();
        return $data;
    }

    public function buildObligationNotify($idObligation, $documentName) {
        $relationships = [
            'users.user.person',
            'process'
        ];
        $obligation = ObligationsModel::with($relationships)->find($idObligation);
        $data['notifyData'] = [
            'title' => 'Documento para revisión',
            'body' => 'Se ha cargado el documento <b>'.$documentName.'</b> 
                en la obligación <b>'.$obligation->title.'</b> correspodiente 
                a la auditoría <b>'.$obligation->process->audit_processes.'</b>',
            'description' => '',
            'link' => 'obligations'
        ];
        // responsible for closing
        $obligationUser = collect($obligation->users);
        $user = $obligationUser->firstWhere('level', 1);
        $data['userToNotify'] = $user->id_user;
        $data['data'] = $obligation->toArray();
        return $data;
    }
}