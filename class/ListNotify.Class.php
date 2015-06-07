<?
/**
 * Boostack: ListNotify.Class.php
 * ========================================================================
 * Copyright 2015 Spagnolo Stefano
 * Licensed under MIT (https://github.com/offmania9/Boostack/blob/master/LICENSE)
 * ========================================================================
 * @author Spagnolo Stefano <s.spagnolo@hotmail.it>
 * @version 2
 */
require_once("class/Email.Class.php");
class ListNotify{
	const TABLENAME = "notify";

	public function __construct(){
	
	}
	
	public function getUserNotify($id_u_to,$limit0=0,$limit1=40){
		$sql = "SELECT id,visited FROM ".self::TABLENAME." WHERE id_u_to='$id_u_to' ORDER BY join_date DESC LIMIT $limit0,$limit1";
		$fields2 = mysql_query($sql)or die (mysql_error().":<br><br> $sql");
		if(mysql_num_rows($fields2) > 0){
			$notvis = 0;
			while ($fields = mysql_fetch_array($fields2)){
				$res_arr[] = new Notify($fields["id"]);
				if($fields["visited"] == "0")
					$notvis++;
			}
			
			$sql = "SELECT id FROM ".self::TABLENAME." WHERE visited='0' AND id_u_to='$id_u_to' 
			ORDER BY join_date DESC LIMIT $notvis,999999 ";
			$fields2 = mysql_query($sql)or die (mysql_error().":<br><br> $sql");			
			if(mysql_num_rows($fields2) > 0){
				while ($fields = mysql_fetch_array($fields2)){
					$res_arr[] = new Notify($fields["id"]);
				}
			}
			
			return $res_arr;
		}	
		else{
			return NULL;
		}			
	}

	public function getNumberOfUserNotify($id_u_to){
		$sql = "SELECT COUNT(id) FROM ".self::TABLENAME." WHERE id_u_to='$id_u_to' AND visited='0'  ";
		$fields2 = mysql_query($sql)or die (mysql_error().":<br><br> $sql");
		$f = mysql_fetch_array($fields2);
		return $f[0];
	}
		
	public function sendNotify($id_u_from,$id_u_to_array,$id_m,$code,$sendmail=true){
		global $mail_admin,$lang_notify_mail;
		if(!empty($id_u_to_array)){
			foreach($id_u_to_array as $id_u_to){
				if($id_u_to == $id_u_from) // non la invio a me stesso
					continue;
				$n = new Notify();
				$n->insert($id_u_from,$id_u_to,$id_m,$code);
				if($sendmail){
					$mailto = mysql_fetch_array(mysql_query("SELECT email FROM user WHERE id=".$id_u_to." "));
					$m = new Email($mail_admin,$lang_notify_mail["$code"]["msg"],$lang_notify_mail["$code"]["subject"],$mailto[0]);
					$m->Send();
				}
			}
			unset($n,$m);
			return true;
		}
		return false;
	}	

	public function getLastNotificationId($id_u){
		$sql = "SELECT id FROM ".self::TABLENAME." WHERE id_u_to='".$id_u."' AND visited='1' AND active='1'
		ORDER BY join_date DESC LIMIT 0,1  ";
		$fields2 = mysql_query($sql)or die (mysql_error().":<br><br> $sql");
		$f = mysql_fetch_array($fields2);
		return $f[0];
	}

	public function sendAdmNotify($id_u_from,$code,$id_m=-1){
				$n = new Notify();
				$n->insert($id_u_from,-1,$id_m,$code);
			unset($n);
			return true;
	}	
	
}
?>