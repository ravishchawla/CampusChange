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
use Aws\DynamoDb\Enum\Type;
use Aws\Common\Credentials\Credentials;
use Aws\DynamoDb\Enum\ComparisonOperator;

class AWSWrapper {

	static $authentication;
	static $s3Client;
	static $ec2Client;
	static $ddbClient;

	static $profile;
	static $credentials = array('profile' => PROFILE, 'region' => REGION);

	public function scanAllUsers($authToken) {
		$accountValidation = self::validateToken($authToken);
		if($accountValidation === False) {
			echo Response::sendError(401);
			return;
		}
		self::authenticateDDB();
		$response = self::$ddbClient->scan(array("TableName" => USERS));
		
		return $response;

		
	}

	public function putUser($token, $username, $email, $password, $firstName = "", $lastName = "") {
		
		$accountValidation = self::validateToken($token);
		if($accountValidation === False) {
			echo Response::sendError(401);
			return;
		}

		self::authenticateDDB();
		$uuid = Utility::createV5Uid($username);
		$encryptedPassword = Utility::encrypt($password);

		$item = array(
					"user_id" => array(Type::STRING => $uuid),
					"user_name" => array(Type::STRING => $username),
					"password" => array(Type::STRING => $encryptedPassword),
					"email" => array(Type::STRING => $email));

		if(!is_null($firstName)) {
			$item['firstName'] = array(Type::STRING => $firstName);
		}
		
		if(!is_null($lastName)) {
			$item['lastName'] = array(Type::STRING => $lastName);
		}			

		$response = self::$ddbClient->putItem(array("TableName" => USERS, "Item" => $item,
												)
											);

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

	public function authenticateClient($ip_addr) {
		self::authenticateDDB();
		$storedTokens = self::getClientToken($ip_addr);

		if($storedTokens['Count'] !== 0) {
			return $storedTokens['Items'][0]['client_token']['S'];
		}

		$uuid = Utility::createV4Uid();
		$respone = self::$ddbClient->putItem(array(
						"TableName" => "tokens", 
						"Item" => array(
							"client_ip" => array(Type::STRING => $ip_addr),
							"client_token" => array(Type::STRING => $uuid),
						)
					)
		);

		return $uuid;
	}

	private function getClientToken($ip_addr) {
		self::authenticateDDB();
		$response = self::$ddbClient->query(array(
						"TableName" => "tokens",
						"KeyConditions" => array(
							"client_ip" => array(
								"ComparisonOperator" => ComparisonOperator::EQ,
								"AttributeValueList" => array(
									array(Type::STRING => $ip_addr)
								)
							)
						)
		));

		return $response;

	}

	private function validateToken($client_token) {
		self::authenticateDDB();
		$response = self::$ddbClient->query(array(
							'TableName' => "tokens",
							'IndexName' => "client_token-index",
							"KeyConditions" => array(
								"client_token" => array(
									"ComparisonOperator" => ComparisonOperator::EQ,
									"AttributeValueList" => array(
										array(Type::STRING => $client_token)
									)
								)
							)
						)
		);

		if($response['Count'] != 0) {
			return True;
		}

		else return False;

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