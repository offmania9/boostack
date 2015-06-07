<?php
/**
 * Boostack: Email.Class.php
 * ========================================================================
 * Copyright 2015 Spagnolo Stefano
 * Licensed under MIT (https://github.com/offmania9/Boostack/blob/master/LICENSE)
 * ========================================================================
 * @author Spagnolo Stefano <s.spagnolo@hotmail.it>
 * @version 2
 */
class Email{
	 
	private $from_mail;
	private $from_name;
	private $reply_to_mail;
	private $reply_to_name;
	private $subject;
	private $cc;
	private $headers;
	private $attachment = array(); 
	private $message;
	private $date_send;
	
	private $to_list = array();
	
	private $mime_boundary;

	public function __construct($from_mail,$message,$subject="",$single_to=NULL,$from_name="",$reply_to_mail="",$reply_to_name="",$cc=NULL){
		
		$this->from_mail = $from_mail;
		$this->from_name = $from_name;
		$this->reply_to_mail = $reply_to_mail; 
		$this->reply_to_name = $reply_to_name;
		$this->subject = $subject;
		$this->cc = $cc;

		if($single_to !== NULL) {	
			if(is_array($single_to)){
				foreach($single_to as $v)
					$this->to_list[] = $v;
			}
			else
				$this->to_list[] = $single_to;
		}
		
				$semi_rand = md5(time());
				$this->mime_boundary = "==Multipart_Boundary_x{$semi_rand}x";
		
				$this->headers = "From: ".$this->from_mail;
				$this->headers .= "\nMIME-Version: 1.0\n" .
				"Content-Type: multipart/mixed;\n" .
				" boundary=\"".$this->mime_boundary."\"";
				
				$this->message = "This is a multi-part message in MIME format.\n\n" .
				"--".$this->mime_boundary."\n" .
				"Content-Type:text/html; charset=\"iso-8859-1\"\n" .
				"Content-Transfer-Encoding: 7bit\n\n" .
				$message ."\n\n";	
					
	}

	public function AddAddressToList($emailaddress){
		$this->to_list[] = $emailaddress;
	}
	
	public function addAttachment($path,$type){

			$this->attachment[] = $path; 
			$data ="";
			$file = fopen($path,'rb');
				while(!feof($file)) {
					$data .= fread($file, 2048);
				}
			fclose($file);
			
			$this->message .= "--".$this->mime_boundary."\n" .
			"Content-Type: ".$type.";\n" .
			" name=\"".basename($path)."\"\n" .
			"Content-Disposition: attachment;\n" .
			" filename=\"".basename($path)."\"\n" .
			"Content-Transfer-Encoding: base64\n\n" .
			chunk_split(base64_encode($data)) . "\n\n";
	}
	
	public function Send(){
		$this->message .= "--".$this->mime_boundary."--\n";
		$this->message = wordwrap($this->message, 70, "\r\n");
		foreach($this->to_list as $value){
			$ok = mail($value, $this->subject, $this->message, $this->headers);
			if(!$ok){
				#throw new Exception("Can't Send mail to: ".$value); exit("Can't Send mail to: ".$value);
				return false;
			}
		}
		return true;
	}
	
	public function __get($property_name) {
		if(isset($this->$property_name)) {
			return($this->$property_name);
		}
		else{
			return(NULL);
		} 
    }

    public function __set($property_name, $val) {

		$this->$property_name = $val;
		#$sql = "UPDATE ".self::TABLENAME." SET $property_name='".$val."'  WHERE id ='".$this->id."' ";
		#mysql_query($sql)or die (mysql_error().": $sql");
    }
	
}
?>