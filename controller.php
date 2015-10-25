<?php

	/*
		This is an example class script proceeding secured API
		To use this class you should keep same as query string and function name
		Ex: If the query string value rquest=delete_user Access modifiers doesn't matter but function should be
		     function delete_user(){
				 You code goes here
			 }
		Class will execute the function dynamically;

		usage :

		    $object->response(output_data, status_code);
			$object->_request	- to get santinized input

			output_data : JSON (I am using)
			status_code : Send status message for headers

		Add This extension for localhost checking :
			Chrome Extension : Advanced REST client Application
			URL : https://chrome.google.com/webstore/detail/hgmloofddffdnphfgcellkdfbfbjeloo

		I used the below table for demo purpose.

		CREATE TABLE IF NOT EXISTS `users` (
		  `user_id` int(11) NOT NULL AUTO_INCREMENT,
		  `user_fullname` varchar(25) NOT NULL,
		  `user_email` varchar(50) NOT NULL,
		  `user_password` varchar(50) NOT NULL,
		  `user_status` tinyint(1) NOT NULL DEFAULT '0',
		  PRIMARY KEY (`user_id`)
		) ENGINE=InnoDB DEFAULT CHARSET=latin1 AUTO_INCREMENT=1 ;
 	*/

	require_once("Rest.inc.php");
	require_once("service.php");

	class CONTROLLER extends REST {

		public $data = "";
		public $service; 

		public function __construct(){
			parent::__construct();				// Init parent contructor
			$this->service = new SERVICE;
		}

		/*
		 * Public method for access api.
		 * This method dynmically call the method based on the query string
		 *
		 */
		public function processApi(){
			$func = strtolower(trim(str_replace("/","",$_REQUEST['rquest'])));
			if((int)method_exists($this,$func) > 0)
				$this->$func();
			else
				$this->response('',404);				// If the method not exist with in this class, response would be "Page not found".
		}

		// /utilisateur
		// json{
		// 	nom
		// 	prenom
		// 	photo
		// 	id_facebook (récupéré de l'api fb)
		// }
		// succes = 201
		// bad request = 400
		// unauthorized = 401

		private function creerUtilisateur(){
			// Cross validation if the request method is POST else it will return "Not Acceptable" status
			if($this->get_request_method() != "POST"){
				$this->response('',406);
			}

			$nom = $this->_request['nom'];
			$prenom = $this->_request['prenom'];
			$photo = $this->_request['photo'];
			$id_facebook = $this->_request['id_facebook'];

			// Input validations
			if(!empty($nom) && !empty($prenom) && !empty($photo) && !empty($id_facebook) ){
				if($this->service->_creerUtilisateur($nom, $prenom, $photo, $id_facebook)){
					$this->response('', 201);
				}
				$this->response('', 400);
			}
			// If invalid inputs "Bad Request" status message and reason
			$error = array('status' => "Failed", "msg" => "Invalid json");
			$this->response($this->json($error), 400);
		}

		private function getUtilisateurs(){
			// Cross validation if the request method is GET else it will return "Not Acceptable" status
			if($this->get_request_method() != "GET"){
				$this->response('',406);
			}

			$utilisateurs = $this->service->_getUtilisateurs();
			if($utilisateurs == -1){
				$this->response('',400);
			}
			
			if(sizeof($utilisateurs) > 0){
				// If success everythig is good send header as "OK" and return list of users in JSON format
				$this->response($this->json($utilisateurs), 200);
			}
			$this->response('',204);	// If no records "No Content" status
		}

		// private function deleteUser(){
		// 	// Cross validation if the request method is DELETE else it will return "Not Acceptable" status
		// 	if($this->get_request_method() != "DELETE"){
		// 		$this->response('',406);
		// 	}
		// 	$id = (int)$this->_request['id'];
		// 	if($id > 0){
		// 		mysql_query("DELETE FROM users WHERE user_id = $id");
		// 		$success = array('status' => "Success", "msg" => "Successfully one record deleted.");
		// 		$this->response($this->json($success),200);
		// 	}else
		// 		$this->response('',204);	// If no records "No Content" status
		// }

		/*
		 *	Encode array into JSON
		*/
		private function json($data){
			if(is_array($data)){
				return json_encode($data);
			}
		}
	}

	// Initiiate Library

	$api = new CONTROLLER;
	$api->processApi();
?>