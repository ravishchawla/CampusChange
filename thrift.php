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

		else if(isset($_GET['auth'])) {
			Response::authenticate();
		}

		else {
			Response::sendError(400);
		}

	}


	public function handlePost() {

		if(isset($_GET['id']) && isset($_GET['user']) && isset($_GET['email']) && isset($_GET['passwd'])) {
			$firstName = isset($_GET['fname']) ? $_GET['fname'] : null;
			$lastName = isset($_GET['lname']) ? $_GET['lname'] : null;

			Response::putUser($_GET['id'], $_GET['user'], $_GET['email'], $_GET['passwd'], $firstName, $lastName);
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