<?php

require_once dirname(__FILE__) . '/../couch/lib/couch.php';
require_once dirname(__FILE__) . '/../couch/lib/couchClient.php';
require_once dirname(__FILE__) . '/../couch/lib/couchDocument.php';
require_once('DatabaseWrapper.php');

class CouchWrapper {

	private static $client = null;
	private static $clientDirName = 'http://localhost:5984/';
	private static $clientDatabaseName = 'thrift_shop';
	private static $user;
	private static $item;
	const ID = 'byID';
	const cleanID = 'cleanById';
	const CATEGORY = "byCategory";
	const LISTINGID = 'byListingID';
	const EMAIL = 'byEmail';
	const TOKEN = 'byToken';
	const USERS = 'users';
	const ITEMS = 'items';
	const REPLIES = 'replies';


	public function __construct() {
		if(!isset(self::$client))
			self::$client = new couchClient(self::$clientDirName, self::$clientDatabaseName);
	}

	public function getUsersListing() {
		__construct();

	}

	public function insertUser($email, $passwordhash, $fname = null, $lname = null) {
		try{
			
			self::$user = new stdClass();
			self::$user->type = 'user';
			self::$user->email = $email;
			self::$user->passwordhash = $passwordhash;
			if(!is_null($fname)) {
				self::$user->fname = $fname;
			}
			if(!is_null($lname)) {
				self::$user->lname = $lname;
			}

			
			$clientResponse = self::$client->storeDoc(self::$user);
			if($clientResponse->ok == 'true') {
				return true;			
			}
		}
		catch(Exception $e) {
			return false;
		}
		return false;
	}

	public function getUserByOption($searchKey, $option) {
		try{
			$user = self::$client->key($searchKey)->getView('users', $option);
			return $user;
		}
		catch(Exception $e) {
			return null;
		}
	}

	public function countUsersByOption($searchKey, $option) {
		try {
			$user = self::$client->key($searchKey)->getView('users', $option);
			$response = (array)$user;
			return count($response['rows']);
		}
		catch(Exception $e) {
			return null;
		}
	}


	public function getItemByOption($searchKey, $option) {
		try{
			$item = self::$client->key($searchKey)->getView(self::ITEMS, $option);
			return $item;
		}
		catch(Exception $e) {
			return false;
		}
	}



	private function cleanDatabaseObject($object) {
			$response = (array)$object;
			$solution = array();
			for($i = 0; $i < count($response['rows']); $i++) {
				if(isset($response['rows'][$i])) {
					$row = (array)$response['rows'][$i];
					$valueObj = (array)array_pop($row);
					$merged = array_merge($row,$valueObj);
					$solution[$i] = $merged;
				}
			}
			return $solution;
	}

	public function updateUserWithAttr($docID, $docRev, $updates) {

		try {

			self::$user = self::$client->getDoc($docID);
			foreach($updates as $key => $value) {
				self::$user->$key = $value;
			}	
			$response = self::$client->storeDoc(self::$user);
			return $response;
		}
		catch (Exception $e) {
			echo $e->getMessage();
			return;
		}

	}

	public function deleteUser($docID, $docRev) {
		try{
			$response = self::$client->deleteDoc($docID, $docRev);
			return true;
		}
		catch (Exception $e) {
			return false;
		}
	}

	public function changePassword($docID, $newPasswordHash) {
		try {
			self::$user = self::$client->getDoc($docID);
			self::$user->passwordhash = $newPasswordHash;
			$response = self::$client->storeDoc(self::$user);
			
			if($response->ok === true) {
				return true;
			}

		}
		catch (Exception $e) {
			return false;
		}
		return false;
	}

	public function getAllItems($params) {
		$responseData = array();
		try {
			if(isset($params['category']))
				self::$client->key($params['category']);
			
				$response = self::$client->getView(self::ITEMS, self::CATEGORY);
			
			if(isset($params['query']))
				$query = $params['query'];
			else 
				$query = false;

			foreach($response->rows as $jsonElement) {
				if(($query === false) || ($query !== false && strpos($jsonElement->value->title, $query) !== false))
					array_push($responseData, $jsonElement->value);
			}
			return $responseData;
		}
		catch (Exception $e) {
			return false;
		}
	}

	public function insertItem($userid, $email, $title, $askingPrice, $category, $description, $images) {
		try {
			self::$item = new stdClass();
			self::$item->type = 'item';
			self::$item->userID = $userid;
			self::$item->userEmail = $email;
			self::$item->title = $title;
			self::$item->askingPrice = $askingPrice;
			self::$item->category = $category;
			self::$item->description = $description;
			self::$item->imageUrls = $images;
			self::$item->replies = 0;
			$response = self::$client->storeDoc(self::$item);			
			
			if($response->ok === true) {
				return true;
			}
			else {
				return false;
			}


		}
		catch(Exception $e) {
			
			return false;
		}
	}

	public function updateItem($id, $title, $askingPrice, $category, $description, $images) {
		try {
			$item = self::$client->getDoc($id);
			if($item !== false) {
				if(!is_null($title)) $item->title = $title;
				if(!is_null($askingPrice)) $item->askingPrice = $askingPrice;
				if(!is_null($category)) $item->category = $category;
				if(!is_null($description)) $item->description = $description;
				if(!is_null($description)) $item->imageUrls = $images;
				$response = self::$client->storeDoc($item);
				
				if($response->ok === true) {
					return true;
				}
			}
			return false;
		}

		catch(Exception $e) {
			return false; 
		}
	}

	public function deleteItem($id) {
		try {
			$item = self::$client->getDoc($id);
			if($item !== false) {
				$rev = $item->_rev;
				$response = self::$client->deleteDoc($id, $rev);	

				if($response->ok === true) {
					return true;
				}
			}
			return false;
		}
		catch(Exception $e) {
			return null;
		}
	}

	public function insertReply($listingID, $text, $dateTime) {
			try {
			$reply = new stdClass();
			$reply->type = 'reply';
			$reply->listingID = $listingID;
			$reply->text = $text;
			$reply->dateTime = $dateTime;
			$response = self::$client->storeDoc($reply);			
			
			if($response->ok === true) {
				return true;
			}
			else {
				return false;
			}


		}
		catch(Exception $e) {
			
			return false;
		}
	}

	public function updateReplyCount($listingID) {
		try {
			$reply = self::$client->getDoc($listingID);
			if($reply !== false) {
				if(isset($reply->replies))
					$reply->replies = $reply->replies + 1;
				else 
					$reply->replies = 1;

				$response = self::$client->storeDoc($reply);

				if($response->ok === true) {
					return true;
				}
			}
			return false;
		}

		catch(Exception $e) {
			return false; 
		}
	}

	public function getReplies($listingID) {
		$responseData = array();
		try {

			$response = self::$client->key($listingID)->getView(self::REPLIES, self::LISTINGID);
			foreach($response->rows as $jsonElement) {
				array_push($responseData, $jsonElement->value);
			}
			return $responseData;
		}
		catch(Exception $e) {
			return false;
		}
	}
}
