<?php
include 'response.php';
include 'AWSWrapper.php';
include 'Utility.php';
class Request {

	public static $request_type;
	public static $uri;
	public static $parameters;

	public static $list;

	public function init() {
		//AWSWrapper::putUser();
		self::$uri = $_SERVER['REQUEST_URI'];
		self::$request_type = $_SERVER['REQUEST_METHOD'];
		self::handleRequest();
	}

	public function handleRequest() {
		
		switch(self::$request_type) {
			case 'GET': 
				self::handleGet();
				break;
			case 'POST': 
				self::handlePost();
				break;
			case 'PUT':
				self::handlePut();
				break;
			case 'DELETE': 
				self::handleDelete();
				break;
			default:
				Response::sendError(400);
		}

	}

	//TODO: fix how url params are handled. very bad way done in which it is now. 
	public function handleGet() {

		if(isset($_GET['action'])) {

			$action = $_GET['action'];
			if(isset($_GET['token'])) {
				$authToken = $_GET['token'];
			}

			switch($action) {
				case 'list_users':
					Response::querySendUsers($authToken);
					break;
				case 'list_items':
					Response::queryGetItems($authToken);
					break;

				case 'auth':
					if(isset($_GET['user']) && isset($_GET['passwd']))
						Response::authenticate($_GET['user'], $_GET['passwd']);
					else
						Response::sendError(400);
					break;

				case 'get':
					if(isset($_GET['user']))
						Response::queryGetUser($_GET['user'], $authToken);
					else if(isset($_GET['item']))
						Response::queryGetItem($_GET['item'], $authToken);
					else
						Response::sendError(400);
					break;

				case 'changePasswd':
					if (isset($_GET['user']) && isset($_GET['oldpasswd']) && isset($_GET['newpasswd']))		
						Response::changePassword($authToken, $_GET['user'], $_GET['oldpasswd'], $_GET['newpasswd']);
					else
						Response::sendError(400);
					break;

				case 'delete':
					if(isset($_GET['user']) && isset($_GET['passwd']))
						Response::deleteUser($_GET['user'], $_GET['passwd'], $authToken);
					else 
						Response::sendError(400);
					break;

				default:
				defaultLabel:
					Response::sendError(400);
			}
		}

	}

	public function handlePost() {

		if(isset($_POST['action'])) {
			$action = $_POST['action'];

			switch ($action) {
				case 'insert':
					if(isset($_POST['user']) && isset($_POST['email']) && isset($_POST['passwd'])) {
						$firstName = isset($_POST['fname']) ? $_POST['fname'] : null;
						$lastName = isset($_POST['lname']) ? $_POST['lname'] : null;

						Response::putUser($_POST['user'], $_POST['email'], $_POST['passwd'], $firstName, $lastName);
					}

					else if(isset($_POST['item']) && isset($_POST['poster']) && isset($_POST['date']) && isset($_POST['price'])) {

						Response::putItem($_POST['item'], $_POST['poster'], $_POST['date'], $_POST['price']);
					}
					else {
						Response::sendError(400);
					}
					break;

				
				default:
					# code...
					break;
			}
		}

		
		}


	

	public function querySendItems() {

	}


	public function handlePut() {

	}

	public function handleDelete() {

	}
}

Request::init();