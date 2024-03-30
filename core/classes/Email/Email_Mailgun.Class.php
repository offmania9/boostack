<?php

/**
 * Boostack: Email_Mailgun.Class.php
 * ========================================================================
 * Copyright 2015-2016  Spagnolo Stefano
 * Licensed under MIT (https://github.com/offmania9/Boostack/blob/master/LICENSE)
 * ========================================================================
 * @author Spagnolo Stefano <s.spagnolo@hotmail.it>
 * @version 4
 */
// require ROOTPATH . '../vendor/autoload.php';

use Mailgun\Mailgun;

class Email_Mailgun extends Email_Basic
{

//     /**
//      * @param $path
//      */
//     public function addAttachmentPath($path)
//     {
//         $this->attachment = array_merge($this->attachment, array($path));
//     }

//     /**
//      * @return bool
//      */
//     public function send()
//     {

//         $mgClient = Mailgun::create(Config::get('mailgun_key'), Config::get('mailgun_endpoint')); // For EU servers

//         //$mgClient = new Mailgun(Config::get("mailgun_key"),Config::get('mailgun_endpoint')); // For EU servers
//         $domain = Config::get("mailgun_domain");
//         $harr = array(
//             'from'    => $this->from_name . ' <' . $this->from_mail . '>',
//             'subject' => $this->subject,
//             'html'    => '<html>' . $this->message_clean . '</html>'
//         );

//         $attarr = array();
//         if (count($this->attachment) > 0) {
//             $attarr["inline"] = $this->attachment;
//         }

//         foreach ($this->to_list as $value) {

//             $harr["to"] = $value;

//             if (count($this->cc) > 0) {
//                 $harr["cc"] = implode(", ", $this->cc);
//             }
//             if (count($this->bcc) > 0) {
//                 $harr["bcc"] = implode(", ", $this->bcc);
//             }

//             $result = $mgClient->messages()->send($domain, $harr, $attarr);
//         }
//         return true;
//     }
}
