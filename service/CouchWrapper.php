<?php

require_once dirname(__FILE__) . '..\couch\couch.php';
require_once dirname(__FILE__) . '..\couch\couchClient.php';
require_once dirname(__FILE__) . '..\couch\couchDocument.php';

class CouchWrapper {

	$client = null;
	$clientDirName = 'http://ec2-54-172-6-150.compute-1.amazonaws.com:5985/';
	$clientDatabaseName = 'thrift_shop';

	public function __construct() {
		if(!isset($client))
			$client = new couchClient($clientDirName, $clientDatabaseName);
	}

	public function getUsersListing() {
		__construct();

	}

	public function authenticateUser($email, $passwordhash) {
		try{
			$doc = self::$client->getDoc('a48014598aba7d3239c3fdc24400122c');
		}

		catch(Exception $e) {
			if($e->getCode() == 404) {
				echo 'doc doesn\'t exist';			
			}
		}

		echo $doc->_rev;
	}
}