<?
require_once 'class/Rest/Rest_Api_Abstract.Class.php';
class Rest_Api extends Rest_Api_Abstract
{    
	 protected function getTest() {
	 	$res = array();
	 	if ($this->method == 'GET') {
			$res = array("visible","1");	
		} else {
            return "Only accepts GET requests";
        }
		return $res;
     }
 }
?>
 
