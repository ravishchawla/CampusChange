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

		if(isset($_GET['list']) && isset($_GET['id'])) {
			self::$list = $_GET['list'];
			$authToken = $_GET['id'];

			switch(self::$list) {
				case 'users':
					Response::querySendUsers($authToken);
					break;
				case 'items':
					self::querySendItems();
					break;
				default:
					Response::sendError(400);
			}
		}

		else if(isset($_GET['auth']) && isset($_GET['passwd'])) {
			Response::authenticate($_GET['auth'], $_GET['passwd']);
		}

		else if (isset($_GET['user']) && isset($_GET['oldpasswd']) && isset($_GET['newpasswd']) && isset($_GET['id']))
		{
			
			Response::changePassword($_GET['id'], $_GET['user'], $_GET['oldpasswd'], $_GET['newpasswd']);
		}

		else if(isset($_GET['user']) && isset($_GET['id'])) {
			Response::queryGetUser($_GET['user'], $_GET['id']);
		}

		else if (isset($_GET['delete']) && isset($_GET['passwd']) && isset($_GET['id']))
		{
			Response::deleteUser($_GET['delete'], $_GET['passwd'], $_GET['id']);
		}

		else {
			Response::sendError(400);
		}

	}

	public function handlePost() {

		if(isset($_POST['id']) && isset($_POST['user']) && isset($_POST['email']) && isset($_POST['passwd'])) {
			$firstName = isset($_POST['fname']) ? $_POST['fname'] : null;
			$lastName = isset($_POST['lname']) ? $_POST['lname'] : null;


			Response::putUser($_POST['id'], $_POST['user'], $_POST['email'], $_POST['passwd'], $firstName, $lastName);
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