<?php

require_once dirname(__FILE__) . '/../couch/lib/couch.php';
require_once dirname(__FILE__) . '/../couch/lib/couchClient.php';
require_once dirname(__FILE__) . '/../couch/lib/couchDocument.php';
require_once('CouchWrapper.php');
require_once('utility.php');

class CouchDriver {

	private static $client = null;
	private static $clientDirName = 'http://ec2-54-172-6-150.compute-1.amazonaws.com:5985/';
	private static $clientDatabaseName = 'thrift_shop';

	public function __construct() {
		if(!isset(self::$client))
			self::$client = new couchClient(self::$clientDirName, self::$clientDatabaseName);
	}


	public function authenticateUser($email, $password) {	
		try{

			$response = CouchWrapper::getUserByOption($email, CouchWrapper::EMAIL);
			if(isset($response['passwordhash'])){
				$passwordhash = $response['passwordhash'];
				$verified = Utility::verifyPassword($password, $passwordhash);
				$verified = true;
				if($verified == true) {
					if(isset($response['token'])){
						return $response['token'];
					}
					else {
						$uuid = Utility::createV4Uid();
						$updates = array('token' => $uuid);
						CouchWrapper::updateUserWithAttr($response['id'], $response['rev'], $updates);
						return $uuid;
					}
				}
				else {
					return 'Not Authorized';
				}
			}
			else {
				echo '500 internal server error';
			}
			
		}

		catch(Exception $e) {
			if($e->getCode() == 404) {
				return 'doc doesn\'t exist';
			}
		}

	}

	public function insertUser($email, $password, $fname, $lname) {
		$passwordhash = Utility::encrypt($password);
		$response = CouchWrapper::insertUser($email, $passwordhash, $fname, $lname);
		return $response;

	}

	public function deleteUser($password, $token){
		try{
			$response = CouchWrapper::getUserByOption($token, CouchWrapper::TOKEN);
			if(isset($response['passwordhash'])){
				$passwordhash = $response['passwordhash'];
				$verified = Utility::verifyPassword($password, $passwordhash);
				$verified = true;
				if($verified == true) {
					$response = CouchWrapper::deleteUser($response['id'], $response['rev']);
					return $response;
				}
				else{
					return 'Not Authorized';
				}
			}
		}
		catch(Exception $e){
			return $e->getMessage();
		}

	}

	public function changePassword($oldPassword, $newPassword, $token) {
		try{
			$response = CouchWrapper::getUserByOption($token, CouchWrapper::TOKEN);
			if(isset($response['passwordhash'])){
				$passwordhash = $response['passwordhash'];
				$verified = Utility::verifyPassword($oldPassword, $passwordhash);
				if($verified == true) {
					$newPasswordHash = Utility::encrypt($newPassword);
				//	$newPasswordHash = $newPassword;
					$response = CouchWrapper::changePassword($response['id'], $newPasswordHash);
					return $response;
				}
				else{
					return 'Not Authorized';
				}
			}
			else {
				return 'Not Authorized';
			}
		}
		catch(Exception $e){
			return $e->getMessage();
		}
	}

	public function getAllListings($token) {
		$response = CouchWrapper::getUserByOption($token, CouchWrapper::TOKEN);
		if(!is_null($response)) {
			$response = CouchWrapper::getAllItems();
			return $response;
		}
	}

	public function getListing($id, $token) {
		$response = CouchWrapper::getUserByOption($token, CouchWrapper::TOKEN);
		if(!is_null($response)) {
			$response = CouchWrapper::getItemByOption($id, CouchWrapper::ID);
			return $response;
		}
	}

	public function insertItem($title, $askingPrice, $category, $description, $token) {
		try {
			$response = CouchWrapper::getUserByOption($token, CouchWrapper::TOKEN);
			if(!is_null($response)) {
				$id = $response['id'];
				$response = CouchWrapper::insertItem($id, $title, $askingPrice, $category, $description);
				return $response;
			}
		}
		catch (Exception $e) {
			return $e->getMessage();
		}
	}

	public function updateItem($id, $title, $askingPrice, $category, $description, $token) {
		try {
			$response = CouchWrapper::getUserByOption($token, CouchWrapper::TOKEN);
			if(!is_null($response)) {
				$response = CouchWrapper::updateItem($id, $title, $askingPrice, $category, $description);
				return Response::sendStatus(200);
			}

		}
		catch (Exception $e) {
			return Response::sendStatus(500, $e->getMessage());
		}
	}

	public function deleteItem($id, $token) {
		try {
			$response = CouchWrapper::getUserByOption($token, CouchWrapper::TOKEN);
			if(!is_null($response)) {
				$response = CouchWrapper::deleteItem($id);
				return Response::sendStatus(200);
			}

		}
		catch (Exception $e) {
			return Response::sendStatus(500, $e->getMessage());
		}
	}
}