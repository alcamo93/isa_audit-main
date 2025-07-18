<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Contracts\Queue\ShouldQueue;

class HandlerMail extends Mailable
{
	//implements ShouldQueue
    use Queueable, SerializesModels;

    protected $sender;
    protected $templatePath;
    public $subject;
    public $content;
    public $title;    
    //protected $theme = 'email-default';
    public $attachedFiles;
    public $data; //you can pass an array of data and manipulate it in the markdown view
    public $moreUsers; //cc
    
    /**
     * Create a new message instance.
     *
     * @return void
     */
    public function __construct($sender, $templatePath, $subject, $title, $content, $attachments, $data, $cc)
    {
        $this->sender = $sender;
        $this->templatePath = $templatePath;
        $this->content = $content;
        $this->title = $title;
        $this->subject = $subject;
        $this->attachedFiles = $attachments;
        $this->data = $data;
        $this->moreUsers = $cc;
    }
    
    /*
     *You can find mime types at https://www.sitepoint.com/web-foundations/mime-types-complete-list/
     **/
    private function checkAttachments()
    {
        if(isset($this->attachedFiles) && is_array($this->attachedFiles) && sizeof($this->attachedFiles) > 0)
        {            
            foreach ($this->attachedFiles as $attachment) {
                $file = $attachment['file'];
                $fileName = $attachment['fileName'];
                $mime = $attachment['mime'];
                //$this->attachData($file, $fileName, ['mime' => $mime]);
                $this->attach($file, ['mime' => $mime,
                                      'as' => $fileName]);
            }
        }
    }
    
    /*
     *Add carbon copies
     **/
    private function checkCc()
    {
        if(isset($this->moreUsers) && is_array($this->moreUsers) && sizeof($this->moreUsers) > 0)
        {            
            foreach ($this->moreUsers as $c) {
                if(isset($c['email']))
                {
                    $this->cc($c['email']);
                }                
            }
        }
    }

    /**
     * Build the message.
     *
     * @return $this
     */
    public function build()
    {
        $this->subject($this->subject);
        HandlerMail::checkAttachments();
        HandlerMail::checkCc();        
        return $this->from($this->sender)->markdown($this->templatePath);        
    }   
}