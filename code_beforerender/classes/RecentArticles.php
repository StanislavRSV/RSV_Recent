<?php

/*

$artid     	= 	$config['pk'];
if(is_file(JPATH_ROOT.'/plugins/cck_field/code_beforerender/classes/RecentArticles.php')){
  require JPATH_ROOT.'/plugins/cck_field/code_beforerender/classes/RecentArticles.php';
} else {
    echo 'no file';
}
$myObj    	=   new RecentArticles($artid);
$myMet	 	=   $myObj->getMethod();

*/

defined('_JEXEC') or die('Restricted access');


class RecentArticles {
	
	public 	$id;											// item(s) ID	
	public 	$date_now;										// date now
	public 	$userIP;										// user IP 
	
	
	public function __construct($id) {		
		$this->id 			=  $id;
		$this->date_now		=  JFactory::getDate()->toSQL();
		$this->userIP 		=  $_SERVER['REMOTE_ADDR'];			
    }
	
	public function getApp(){		
		$app 	= 	JFactory::getApplication();
		return	$app;
	}	
	
	public function getId(){
		
		if($this->id ) {
			$id		=	$this->id;	
		} else {					
			$app 	= 	$this->getApp();
			$app->enqueueMessage('No article ID for Recently Articles!','warning');	
		}		
		return $id;		
	}
	
	public function getIP(){
		
		if($this->userIP ) {
			$userIP	=	$this->userIP;	
		} else {					
			$app 	= 	$this->getApp();
			$app->enqueueMessage('No User IP for Recently Articles!','warning');	
		}		
		return $userIP;		
	}
	
	public function setRecent(){
		
		$app 		= 	$this->getApp();
		//prepare for new recent
		$art_rec 	= 	new JCckContentFree;		
		$tbl_rec	=	'#__cck_store_form_recently';		
		$art_rec->setTable($tbl_rec);		
		$content_type = 'recently';		
		//data for new recent
		$data_rec   = 	array(	
			'rec_id'    => 	$this->id,
			'rec_date'  => 	$this->date_now,
			'rec_ip'    => 	$this->userIP			
		);			
		//array existing recents for IP
		$my_query 		= 	'SELECT * FROM '.$tbl_rec.' WHERE rec_ip LIKE "'.$this->userIP.'"';
		$tbl_arr 		= 	JCckDatabase::loadAssocList($my_query);	
		
		//if exist recents
		if($tbl_arr){	
			
			$ids_arr		=	array();			
			foreach ($tbl_arr as $ra) {					
				$ids_arr[] = $ra['rec_id'];					
			}			
			//there is item in recents - update date
			if(in_array($this->id, $ids_arr)) {	

				$app->enqueueMessage('update date','message');	/*tmp*/
		
				$my_query1 		= 	'SELECT id FROM '.$tbl_rec.' WHERE rec_id = '.$this->id;
				$rec_pk 		= 	JCckDatabase::loadResult($my_query1);				
				
				$object 			= 	new stdClass();
				$object->id 		= 	$rec_pk;
				$object->rec_date	= 	$this->date_now;								
				$update 			= 	JFactory::getDbo()->updateObject($tbl_rec, $object, 'id');
				
			//no item in recents - add item to recents	
			} else {				
				//$art_rec->create($content_type, $data_rec);
				
				/*tmp*/
				if($art_rec->create($content_type, $data_rec)->isSuccessful()) {			
					$app->enqueueMessage('OK item added','message');					
				} else {
					$app->enqueueMessage('NO','error');
				}				
			}			
		//first recent	
		} else {			
			//$art_rec->create($content_type, $data_rec);	
			
			/*tmp*/			
			if($art_rec->create($content_type, $data_rec)->isSuccessful()) {			
				$app->enqueueMessage('OK item added','message');					
			} else {
				$app->enqueueMessage('NO','error');
			}			
		}		
	}
	
}