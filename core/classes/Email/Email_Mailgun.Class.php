<?php
/**
 * Boostack: Email_Mailgun.Class.php
 * ========================================================================
 * Copyright 2015-2016  Spagnolo Stefano
 * Licensed under MIT (https://github.com/offmania9/Boostack/blob/master/LICENSE)
 * ========================================================================
 * @author Spagnolo Stefano <s.spagnolo@hotmail.it>
 * @version 3.0
 */
require ROOTPATH .'vendor/autoload.php';
//use Mailgun\Mailgun;

class Email_Mailgun extends Email_Basic {

    /**
     * @var string
     */
    private $key = "YOUR-API-KEY";
    /**
     * @var string
     */
    private $domain = "YOUR-DOMAIN";

    /**
     * @param $path
     */
    public function addAttachmentPath($path) {
        $this->attachment = array_merge($this->attachment, array($path));
    }

    /**
     * @return bool
     */
    public function send(){
        $mgClient = new Mailgun($this->key);
        $domain = $this->domain;
        $harr = array(
            'from'    => $this->from_name.' <'.$this->from_mail.'>',
            'subject' => $this->subject,
            'html'    => '<html>'.$this->message_clean.'</html>' );

        $attarr = array();
        if(count($this->attachment)>0) {
            $attarr["inline"] = $this->attachment;
        }

        foreach($this->to_list as $value){

            $harr["to"] = $value;

            if(count($this->cc)>0) {
                $harr["cc"] = implode(", ", $this->cc);
            }
            if(count($this->bcc)>0) {
                $harr["bcc"] = implode(", ", $this->bcc);
            }

            $result = $mgClient->sendMessage($domain, $harr, $attarr);
        }
        return true;
    }

}
?>