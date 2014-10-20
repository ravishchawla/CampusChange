<?php
require_once 'api.php';
require_once 'CouchClient.php';

class ThriftAPI extends API
{
	protected $User;
	protected static $dbWrapper;

	public function __construct($request, $origin) {
		parent::__construct($request);

		self::$dbWrapper = new CouchWrapper();
		//Check for API key and if so, create a 
		//User variable based on it, and call it user

		$user = null;
		$this->User = $user;
	}

	public function example() {
		if($this->method == 'GET') {
			return "example returned";
		}
		else {
			return "not get example";
		}
	}

	public function auth($args, $data) {
		$return = array();
		if($this->method == 'POST') {
			if(isset($data['email']) && isset($data['password'])){
				$return = CouchDriver::authenticateUser($data['email'], $data['password']);
			}
		}
		return json_encode($return);
	}

	public function user($args, $data) {
		$return = array();
		if($this->method == 'DELETE') {
			if(isset($data['password']) && isset($this->sessionID))
				$return = CouchDriver::deleteUser($data['password'], $this->sessionID);
			else
				$return['409'] = 'Not Authorized';
		}
		else if($this->method == 'PUT') {
			if(isset($data['oldPassword']) && isset($data['newPassword']) && isset($this->sessionID))
				$return = CouchDriver::changePassword($data['oldPassword'], $data['newPassword'], $this->sessionID);
			else
				$return['401'] = 'Not Authorized';
		}
		else if($this->method == 'POST') {
			if(isset($data['email']) && isset($data['password'])){
				$fname = isset($data['fname']) ? $data['fname'] : null;
				$lname = isset($data['lname']) ? $data['lname'] : null;
				$return = CouchDriver::insertUser($data['email'], $data['password'], $fname, $lname);
			}
			else 
				$return['401'] = 'Not Authorized';
		}

		return json_encode($return);
	}

	public function listings($args, $data) {
		$return = null;
		if($this->method == 'GET') {
			if(isset($this->sessionID)){
				if(empty($args)){
					$return = CouchDriver::getAllListings($this->sessionID);
				}
				else{
					$return = CouchDriver::getListing($args[0], $this->sessionID);
				}
			}
		}

		else if($this->method == 'POST') {
			if(isset($data['name']) && isset($data['askingPrice']) && isset($data['category']) && isset($data['description']) && isset($this->sessionID)){
				$return = CouchDriver::insertItem($data['name'], $data['askingPrice'], $data['category'], $data['description'], $this->sessionID);
			}
		}

		else if($this->method == 'PUT') {
			if(!empty($args) && isset($this->sessionID)) {
				$title = isset($data['name']) ? $data['name'] : null;
				$askingPrice = isset($data['askingPrice']) ? $data['askingPrice'] : null;
				$category = isset($data['category']) ? $data['category'] : null;
				$description = isset($data['description']) ? $data['description'] : null;
				$return = CouchDriver::updateItem($args[0], $title, $askingPrice, $category, $description, $this->sessionID);
			}

		}

		else if($this->method == 'DELETE') {
			if(!empty($args) && isset($this->sessionID)) {
				$return = CouchDriver::deleteItem($args[0], $this->sessionID);
			}
		}
		
		else {
			$return['401'] = 'not working';
		}

		return $return;
	}
}