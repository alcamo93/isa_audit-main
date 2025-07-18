<?php

namespace App\Classes;

use App\Classes\Mailing;

class Source
{
    /**
     * Create the password
     */
    public function randomPassword() {
        $alphabet = 'abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ1234567890';
        $pass = array();
        $alphaLength = strlen($alphabet) - 1;
        for ($i = 0; $i < 8; $i++) {
            $n = rand(0, $alphaLength);
            $pass[] = $alphabet[$n];
        }
        return implode($pass); //turn the array into a string
    }
    /**
     * Send params to class email
     */
    public function sendEmail($subject, $title, $email, $name, $secondName, $password, $templatePath) {
        $mail = new Mailing;
        $params = array(
            'subject' => $subject.' || ISA Ambiental',
            'title' => $title,
            'email' => $email,
            'templatePath' => $templatePath,
            'data' => array('name' => $name.' '.$secondName,
                'user' => $email,
                'password' => $password)
            );
        return $mail->sendEmail($params);
    }
}