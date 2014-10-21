<?php

require_once dirname(__FILE__) . '/../couch\lib\couch.php';
require_once dirname(__FILE__) . '/../couch\lib\couchClient.php';
require_once dirname(__FILE__) . '/../couch\lib\couchDocument.php';
require_once('DatabaseWrapper.php');

class CouchWrapper {

	private static $client = null;
	private static $clientDirName = 'http://ec2-54-172-6-150.compute-1.amazonaws.com:5985/';
	private static $clientDatabaseName = 'thrift_shop';
	private static $user;
	private static $item;
	const ID = 'byID';
	const EMAIL = 'byEmail';
	const TOKEN = 'byToken';
	const USERS = 'users';
	const ITEMS = 'items';


	public function __construct() {
		if(!isset(self::$client))
			self::$client = new couchClient(self::$clientDirName, self::$clientDatabaseName);
	}

	public function getUsersListing() {
		__construct();

	}

	public function insertUser($email, $passwordhash, $fname = null, $lname = null) {
		try{
			$user = self::getUserByOption($email, self::EMAIL);
			if(!is_null($user)) {
				$response = 'user alreaddy exists';
				return $response;
			}
			

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

			
			$response = self::$client->storeDoc(self::$user);
			return $response;
		}
		catch(Exception $e) {
			echo $e->getMessage();	
		}
	}

	public function getUserByOption($searchKey, $option) {
		try{
			$user = self::$client->key($searchKey)->getView('users', $option);
			return self::cleanDatabaseObject($user);
		}
		catch(Exception $e) {
			echo $e->getMessage();
			return null;
		}
	}


	public function getItemByOption($searchKey, $option) {
		try{
			$item = self::$client->key($searchKey)->getView(self::ITEMS, $option);
			$var = self::cleanDatabaseObject($item);
			return $var;
		}
		catch(Exception $e) {
			echo $e->getMessage();
			return null;
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
			return $response;
		}
		catch (Exception $e) {
			echo $e->getMessage();
			return;
		}
	}

	public function changePassword($docID, $newPasswordHash) {
		try {
			self::$user = self::$client->getDoc($docID);
			self::$user->passwordhash = $newPasswordHash;
			$response = self::$client->storeDoc(self::$user);
			return $response;
		}
		catch (Exception $e) {
			echo $e->getMessage();
			return;
		}

	}

	public function getAllItems() {
		try {
			$response = self::$client->getView(self::ITEMS, self::ID);
			return self::cleanDatabaseObject($response);
		}
		catch (Exception $e) {
			echo $e->getMessage();
			return;
		}
	}

	public function insertItem($userid, $title, $askingPrice, $category, $description) {
		try {
			self::$item = new stdClass();
			self::$item->type = 'item';
			self::$item->userID = $userid;
			self::$item->title = $title;
			self::$item->askingPrice = $askingPrice;
			self::$item->category = $category;
			self::$item->description = $description;
			$response = self::$client->storeDoc(self::$item);			
			return $response;

		}
		catch(Exception $e) {
			echo $e->getMessage();
			return null;
		}
	}

	public function updateItem($id, $title, $askingPrice, $category, $description) {
		try {
			$item = self::$client->getDoc($id);
			if(!is_null($title)) $item->title = $title;
			if(!is_null($askingPrice)) $item->askingPrice = $askingPrice;
			if(!is_null($category)) $item->category = $category;
			if(!is_null($description)) $item->description = $description;
			$response = self::$client->storeDoc($item);
			return $response;
		}

		catch(Exception $e) {
			echo $e->getMessage();
			return null;
		}
	}

	public function deleteItem($id) {
		try {
			$item = self::$client->getDoc($id);
			$rev = $item->_rev;
			$response = self::$client->deleteDoc($id, $rev);
			return $response;
		}
		catch(Exception $e) {
			echo $e->getMessage();
			return null;
		}
	}
}