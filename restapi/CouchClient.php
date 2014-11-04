<?php

require_once dirname(__FILE__) . '/../couch/lib/couch.php';
require_once dirname(__FILE__) . '/../couch/lib/couchClient.php';
require_once dirname(__FILE__) . '/../couch/lib/couchDocument.php';
require_once('CouchWrapper.php');
require_once('utility.php');

class CouchDriver {

	private static $client = null;
	private static $clientDirName = 'http://localhost:5984/';
	private static $clientDatabaseName = 'thrift_shop';

	public function __construct() {
		if(!isset(self::$client))
			self::$client = new couchClient(self::$clientDirName, self::$clientDatabaseName);
	}


	public function authenticateUser($email, $password) {	
		$responseData = array();

		try{
			$response = CouchWrapper::getUserByOption($email, CouchWrapper::EMAIL);
			
			//$value = $response->rows[0]->value;
			if(count($response->rows) === 0) {
				$responseData['err'] = 401;
				return $responseData;
			}

			
			if(count($response->rows) !== 0) {
			//if(isset($response->rows[0]->value->passwordhash)){ //value will always be there. 
				$value = $response->rows[0]->value;
				$passwordhash = $value->passwordhash;
				$verified = Utility::verifyPassword($password, $passwordhash);
				if($verified == true) {
					if(isset($value->token)){
						$responseData['token'] = $value->token;
					}
					else {
						$uuid = Utility::createV4Uid();
						$updates = array('token' => $uuid);
						CouchWrapper::updateUserWithAttr($value->id, $value->rev, $updates);
						$responseData['token'] = $uuid;
					}
				$responseData['err'] = 200;
				}
				else {
						$responseData['err'] = 401;
				}
			}
			else {
						$respnseData['err'] = 500;
			}
			return $responseData;
			
		}

		catch(Exception $e) {		
			$respnseData['err'] = 500;
			return $responseData;
		}

	}

	public function insertUser($email, $password, $fname, $lname) {
		$responseData = array();
		try{
			$users = CouchWrapper::getUserByOption($email, CouchWrapper::EMAIL);
				if(count($users->rows) === 0) {
					$passwordhash = Utility::encrypt($password);
					$response = CouchWrapper::insertUser($email, $passwordhash, $fname, $lname);		
					
					if($response === true) {
						$responseData['err'] = 200;
					}
					else {
						throw new Exception('Server error');
					}
				}
				else {
					$responseData['err'] = 400;
				}
		}
		catch(Exception $e) {
			$responseData['err'] = 500;
		}
		return $responseData;

	}

	public function deleteUser($password, $token){
		$return = array();
		try{
			$response = CouchWrapper::getUserByOption($token, CouchWrapper::TOKEN);
			if(count($response->rows) !== 0  ) {
				$value = $response->rows[0]->value;
				if(isset($value->passwordhash)){
					$passwordhash = $value->passwordhash;
					$verified = Utility::verifyPassword($password, $passwordhash);
					if($verified == true) {
						$response = CouchWrapper::deleteUser($value->id, $value->rev);
						if($response === true) {
							$return['err'] = 200;
							return $return;
						}
						else {
							throw new Exception('server error');
						}
					}
				}
			}
			
			$return['err'] = 401;
			return $return;
		}
		catch(Exception $e){
			$return['err'] = 500;
			return $return;
		}

	}

	public function changePassword($oldPassword, $newPassword, $token) {
		$responseData = array();
		try{
			$response = CouchWrapper::getUserByOption($token, CouchWrapper::TOKEN);
			if(count($response->rows) !== 0){
				$value = $response->rows[0]->value;
				$passwordhash = $value->passwordhash;
				$verified = Utility::verifyPassword($oldPassword, $passwordhash);
				if($verified == true) {
					$newPasswordHash = Utility::encrypt($newPassword);
					$response = CouchWrapper::changePassword($value->id, $newPasswordHash);
					
					if($response === true) {
							$responseData['err'] = 200;
							return $responseData;	
					}
					else {
						throw new Exception('server error');
					}	
				}
			}
				$responseData['err'] = 401;	
				return $responseData;
			
		}
		catch(Exception $e){
			$responseData['err'] = 500;
			return $responseData;
		}
	}

	public function getAllListings($token, $params) {
		$responseData = array();
		try{
			$response = CouchWrapper::getUserByOption($token, CouchWrapper::TOKEN);
			if(count($response->rows) !== 0) {
		
			$response = CouchWrapper::getAllItems($params);
			if($response !== false) {
				if(isset($params['start'], $params['stop']))
					$responseData = array_slice($response, $params['start'], $params['stop'] - $params['start'], true);
				else 
					$responseData = $response;
				
				$responseData['err'] = 200;	
				return $responseData;
			}

			$responseData['err'] = 401;
		}
	}
		catch(Exception $e) {
			$responseData['err'] = 500;
		}

		return $responseData;
	}

	public function getListing($token, $listingID) {
		$responseData = array();
		try{
			$response = CouchWrapper::getUserByOption($token, CouchWrapper::TOKEN);
			if(count($response->rows) !== 0) {
				$value = $response->rows[0]->value;
				$response = CouchWrapper::getItemByOption($listingID, CouchWrapper::cleanID);
				if($response !== false) {
					if(count($response->rows) !== 0) {
						$value = $response->rows[0]->value;
						$responseData[0] = $value;
						$responseData['err'] = 200;
					}
					else {
						$responseData['err'] = 400;
					}
					return $responseData;
				}
			}

			$responseData['err'] = 401;
			return $responseData;
		}
		catch(Exception $e) {
			$responseData['err'] = 500;
		}
	}

	public function insertItem($title, $askingPrice, $category, $description, $images, $token) {
		$responseData = array();
		try {
			$response = CouchWrapper::getUserByOption($token, CouchWrapper::TOKEN);
			if(count($response->rows) !== 0) {
				$value = $response->rows[0]->value;
				$id = $value->id;
				$email = $value->email;
				$response = CouchWrapper::insertItem($id, $email, $title, $askingPrice, $category, $description, $images);
				
				if($response === true) {
					$responseData['err'] = 200;
					return $responseData;
				}
				else {
					throw new Exception('Server error');
				}
			}

			$responseData['err'] = 401;
			return $responseData;


		}
		catch (Exception $e) {
				$responseData['err'] = 500;
		}

		return $responseData;
	}

	public function updateItem($id, $title, $askingPrice, $category, $description, $images, $token) {
		$responseData = array();
		try {
			$response = CouchWrapper::getUserByOption($token, CouchWrapper::TOKEN);
			if(count($response->rows) !== 0) {
				$response = CouchWrapper::updateItem($id, $title, $askingPrice, $category, $description, $images);
				if($response === true) {
					$responseData['err'] = 200;
					return $responseData;
				}
				else if($response === false) {
					$responseData['err'] = 400;
					return $responseData;
				}
				else if($responseData === null) {
					throw new Exception('Server error');
				}
			}

			$responseData['err'] = 401;
			return $responseData;

		}
		catch (Exception $e) {
			$responseData['err'] = 500;
			return $responseData;
		}
	}

	public function deleteItem($token, $listingID) {
		$responseData = array();
		try {
			$response = CouchWrapper::getUserByOption($token, CouchWrapper::TOKEN);
			if(count($response->rows) !== 0){
				$response = CouchWrapper::deleteItem($listingID);
				if($response === true) {
					$responseData['err'] = 200;
					return $responseData;
				}
				else {
					$responseData['err'] = 400;
					return $responseData;
				}

			}
			$responseData['err'] = 401;
			return $responseData;

		}
		catch (Exception $e) {
			$responseData['err'] = 500;
			return $responseData;
		}
	}

	public function insertReply($token, $listingID, $text, $dateTime) {
		$responseData = array();
		try {
			$response = CouchWrapper::getUserByOption($token, CouchWrapper::TOKEN);
			if(count($response->rows) !== 0) {
				$response = CouchWrapper::getItemByOption($listingID, CouchWrapper::ID);
				if(count($response->rows) !== 0) {
					$response = CouchWrapper::insertReply($listingID, $text, $dateTime);
					$response = ($response && CouchWrapper::updateReplyCount($listingID));
					
					if($response === true) {
						$responseData['err'] = 200;
					}
				}
				else {
					$responseData['err'] = 400;
				}
			}
			else {
				$responseData['err'] = 401;
			}

			return $responseData;
		}

		catch(Exception $e) {
			$responseData['err'] = 500;
			return $responseData;
		}
	}

	public function getReplies($token, $listingID) {
		$responseData = array();
		try {
			$response = CouchWrapper::getUserByOption($token, CouchWrapper::TOKEN);

			if(count($response->rows) !== 0) {
				$response = CouchWrapper::getReplies($listingID);
				if($response !== false) {
					$responseData = $response;
					$responseData['err'] = 200;
				}
			}
			else {
				$responseData['err'] = 401;
			}
			return $responseData;
		}
		catch(Exception $e) {
			$responseData['err'] = 500;
			return $responseData;
		}
	}
}
