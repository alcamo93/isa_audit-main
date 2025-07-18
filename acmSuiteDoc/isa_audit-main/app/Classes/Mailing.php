<?php

namespace App\Classes;

use App\Mail\HandlerEmail;
use Illuminate\Contracts\Filesystem\FileNotFoundException;
use App\Classes\StatusConstants;
use Carbon\Carbon;
use Mail;

class Mailing
{            
    private $sender = 'noreply@isaambiental.com';
    /**
     * Send an email with attachments if set
     */
    public function sendEmail($params){
        $paramTemp = $this->standardizeParams($params);
        $subject = $paramTemp['subject'];
        $title = $paramTemp['title'];        
        $content = $paramTemp['content'];        
        $to = $paramTemp['email'];
        $templatePath = $paramTemp['templatePath'];
        $attachments = $paramTemp['attachments'];        
        $data = $paramTemp['data'];
        $cc = $paramTemp['cc'];
        $this->checkAttachments($attachments);
        $dataMail = new HandlerEmail($this->sender, $templatePath, $subject, $title, $content, $attachments, $data, $cc);
        try {
            Mail::to($to)->send($dataMail); 
            return StatusConstants::SUCCESS;
        } catch (\Exception $e) {
            return StatusConstants::ERROR;
        }
    }
    /**
     * standardize data to send
     */
    private function standardizeParams($params)
    {
        $paramsTemp['subject'] = (isset($params['subject']) ? $params['subject'] : '');
        $paramsTemp['title'] = (isset($params['titulo']) ? $params['titulo'] : '');
        $paramsTemp['content'] = (isset($params['content']) ? $params['content'] : '');
        $paramsTemp['email'] = (isset($params['email']) ? $params['email'] : $this->sender);            
        $paramsTemp['attachments'] = (isset($params['attachments']) ? $params['attachments'] : '');
        $paramsTemp['templatePath'] = (isset($params['templatePath']) ? $params['templatePath'] : 'emails.welcome');
        $paramsTemp['data'] = (isset($params['data']) ? $params['data'] : array());
        $paramsTemp['cc'] = (isset($params['cc']) ? $params['cc'] : array());
        return $paramsTemp;
    }
    /**
     * Throws an exception if an attachment does not exist
     */
    private function checkAttachments($attachments)
    {
        if(isset($attachments) && is_array($attachments) && sizeof($attachments) > 0)
        {            
            foreach ($attachments as $attachment) {
                $file = $attachment['file'];                
                if(!\File::exists($file)){                    
                    throw new FileNotFoundException("El archivo {$file} no existe. Es necesario verificar
                                                    este hecho si requiere enviar un correo con archivos adjuntos.");
                }
            }
        }
    }
}
