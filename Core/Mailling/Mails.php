<?php

namespace Mailling;
use FormViewCreation\MailConfiguration;
use GlobalsFunctions\Globals;
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\SMTP;
use PHPMailer\PHPMailer\Exception;

use ConfigurationSetting\ConfigureSetting;

class Mails
{
    /**
     * @param $data
     * @return true
     * send mails data variable structure
     * $data = [
     * "subject"=> string,
     * "message"=> string,
     * "altbody=> string,
     * "user"=>array(mail1,mail2,mail3),
     * "reply"=>boolean,
     * "attached"=>boolean,
     * "attachement"=>array(
           array("filedata"=>data, "filename"=> string),
           array("filedata"=>data, "filename"=> string),
     *    ),
     *
     * ];
     */
   public static function send($data = [], string $mailConfigName = "chance-website"){
       $mailconfig = ConfigureSetting::getConfig('mail');

       if(empty($mailconfig)){
           $mailconfig = MailConfiguration::getMailConfiguration($mailConfigName);
       }

       $mail = new PHPMailer(true);

       try {
           //Server settings
          // $mail->SMTPDebug = SMTP::DEBUG_SERVER;                      //Enable verbose debug output
           $mail->isSMTP();                                            //Send using SMTP
           $mail->Host       = $mailconfig['smtp'];                     //Set the SMTP server to send through
           $mail->SMTPAuth   = true;                                   //Enable SMTP authentication
           $mail->Username   = $mailconfig['user'];                     //SMTP username
           $mail->Password   = $mailconfig['password'];                               //SMTP password
           $mail->SMTPSecure = PHPMailer::ENCRYPTION_SMTPS;            //Enable implicit TLS encryption
           $mail->Port       = 465;                                    //TCP port to connect to; use 587 if you have set `SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS`

           //Recipients
           $mail->setFrom($mailconfig['user'], Globals::view()['view_name']);

           if(gettype($data['user']) === 'array'){
               foreach ($data['user'] as $rec){
                   $mail->addAddress($rec, 'user');
               }
           }

           if($data['reply'] === true){
               $mail->addReplyTo($mailconfig['user'], Globals::view()['view_name']);
           }              //Name is optional
           //$mail->addCC('cc@example.com');
           //$mail->addBCC('bcc@example.com');

           //Attachments
           if($data['attached'] === true){
               if(gettype($data['attachment']) === 'array'){
                   foreach ($data['attachment'] as $file){
                       $mail->addAttachment($file['filedata'], $file['filename']);
                   }
               }
           }


           //Content
           $mail->isHTML(true);                                  //Set email format to HTML
           $mail->Subject = $data['subject'];
           $mail->Body    = $data['message'];
           $mail->AltBody = $data['altbody'];

           if($mail->send()){
               return true;
           }
          return false;
       } catch (Exception $e) {
           echo $e->getMessage();
          return false;
       }


   }

}