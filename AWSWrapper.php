<?php
include dirname(__FILE__) . '/AWS/vendor/autoload.php';
define('PROFILE', 'rchawla8');
define('ACCESS_ID', 'AKIAJBWHSSMUPS7W3X3A'); //TODO: DELETE
define('ACCESS_SECRET', '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'); //TODO: DELETE
define('REGION', 'us-east-1');
define('USERS', 'users');
define('ITEMS', 'items');


use Aws\S3\S3Client;
use Aws\Ec2\Ec2Client;
use Aws\DynamoDb\DynamoDbClient;
use Aws\Common\Credentials\Credentials;

class AWSWrapper {

	static $authentication;
	static $s3Client;
	static $ec2Client;
	static $ddbClient;

	static $profile;
	static $credentials = array('profile' => PROFILE, 'region' => REGION);

	public function scanAllUsers() {
		
		self::authenticateDDB();
		$response = self::$ddbClient->scan(array("TableName" => USERS));
		return $response;

		
	}

	private function authenticateS3() {
		if(!isset(self::$s3Client)) {
			self::createProfile();
			self::$s3Client = S3Client::factory(self::$credentials);
		}
		else {
			return;
		}
	}

	private function authenticateEC2() {
		if(!isset(self::$ec2Client)) {
			self::createProfile();
			self::$ec2Client = Ec2Client::factory(self::$credentials);
		}
		else {
			return;
		}
	}

	private function authenticateDDB() {
		if(!isset(self::$ddbClient)) {
			self::createProfile();
			self::$ddbClient = DynamoDbClient::factory(self::$credentials);
		}
		else {
			return;
		}
	}

	private function authenticate() {
		authenticateS3();
		authenticateEC2();
		authenticateDDB();
		return;
	}

	private function createProfile() {
		if(!isset(self::$profile)) {
			self::$profile = new Credentials(ACCESS_ID, ACCESS_SECRET);
			self::$credentials = array('credentials' => self::$profile, 'region' => REGION);
		}
		else {
			return;
		}
	}

}