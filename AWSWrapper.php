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
use Aws\DynamoDb\Enum\AttributeAction;

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

	public function putUser($username, $email, $password, $firstName = "", $lastName = "") {
		/*
		$accountValidation = self::validateToken($token);
		if($accountValidation === False) {
			echo Response::sendError(401);
			return;
		}
*/
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

		echo var_export($item, true);
		$response = self::$ddbClient->putItem(array("TableName" => USERS, "Item" => $item,
												)
											);

		echo var_export($response, true);
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

	public function authenticateClient($user_name, $password) {
		self::authenticateDDB();
		$storedTokens = self::getClientToken($user_name, $password);

		if(is_null($storedTokens)) {
			return;
		}

		if($storedTokens['Count'] !== 0) {
			$details = $storedTokens['Items'][0];
			if(array_key_exists('client_token', $details))
				return $details['client_token'];
		

			$user_id = $details['user_id']['S'];

		$uuid = Utility::createV4Uid();
		$response = self::$ddbClient->updateItem(array(
						"TableName" => "users",
							"Key" => array(
								"user_id" => array(Type::STRING => $user_id),
							),

							"AttributeUpdates" => array(
								"client_token" => array(
										"Action" => AttributeAction::PUT,
										"Value" => array(Type::STRING => $uuid),
									)
								)

						));

		}

		return $uuid;
	}

	private function getClientToken($user_id, $password) {
		self::authenticateDDB();
		$response = self::$ddbClient->query(array(
						"TableName" => "users",
						"IndexName" => "user_name-index",
						"KeyConditions" => array(
							"user_name" => array(
								"ComparisonOperator" => ComparisonOperator::EQ,
								"AttributeValueList" => array(
									array(Type::STRING => $user_id)
								)
							)
						),
						"AttributesToGet" => array("user_id", "client_token", "password")
		));

		if($response['Count'] == 0) {
			echo Response::sendError(401);
			return;
		}

		
		$hashedPassword = $response['Items'][0]['password']['S'];
		if(Utility::verifyPassword($password, $hashedPassword)){
			return $response;
		}

		else {
			echo Response::sendError(401);
			return null;
		}
	}


	private function validateToken($client_token) {
		self::authenticateDDB();
		$response = self::$ddbClient->query(array(
							'TableName' => "users",
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

	private function getUser($user, $attributes) {
		$response = self::$ddbClient->query(array(
									'TableName' => "users",
									'IndexName' => 'user_name-index',
									"KeyConditions" => array(
										"user_name" => array(
											"ComparisonOperator" => ComparisonOperator::EQ,
											"AttributeValueList" => array(
												array(Type::STRING => $user)
											)
										)
									),
									"AttributesToGet" => $attributes,
								));

		return $response;

	}

	public function getUserDetails($user, $token) {
		self::authenticateDDB();
		$validateToken = self::validateToken($token);

		if($validateToken === false) {
			echo Response::sendError(401);
			return;
		}


		$attributes = array("user_name", "email", "firstName", "lastName");
		$response = self::getUser($user, $attributes);
	

	if($response['Count'] == 0) {
		echo Response::sendError(404);
		return;
	}

	else {
		return $response;
	}
	
	}

	public function deleteUser($user, $password, $token) {

		self::authenticateDDb();
		$validateToken = self::validateToken($token);

		if($validateToken === false) {
			echo Response::sendError(401);
			return;
		}

		$attributes = array("user_id", "password");
		$response = self::getUser($user, $attributes);

		if($response['Count'] == 0) {
			echo Response::sendError(401);
			return;
		}
		
		else {
			$hashedPassword = $response['Items'][0]['password']['S'];
			$user_id = $response['Items'][0]['user_id']['S'];
			echo 'user id ' . $user_id;

			if(Utility::verifyPassword($password, $hashedPassword)) {
				$response = self::$ddbClient->deleteItem(array(
												'TableName' => 'users',
												'Key' => array(
													'user_id' => array(
														Type::STRING => $user_id)
													)
												)
								);
				
				return $response;
			}
			else {
				Response::sendError(401);
				return;
			}
		}

	}

	public function changePassword($token, $user, $oldPassword, $newPassword) {


		self::authenticateDDB();
		$validateToken = self::validateToken($token);

		if($validateToken === false) {
			echo Response::sendError(401);
			return;
		}

		$attributes = array("user_id", "password");
		$response = self::getUser($user, $attributes);

		if($response['Count'] == 0) {
			echo Response::sendError(401);
			return;
		}
		
		else {
			$hashedPassword = $response['Items'][0]['password']['S'];
			$encryptedPassword = Utility::encrypt($newPassword);
			$user_id = $response['Items'][0]['user_id']['S'];
			if(Utility::verifyPassword($oldPassword, $hashedPassword)) {
				$response = self::$ddbClient->updateItem(array(
											"TableName" => 'users',
											"Key" => array(
													"user_id" => array(
														Type::STRING => $user_id
														)
											),

											"AttributeUpdates" => array(
													"password" => array(
														"Action" => AttributeAction::PUT,
														"Value" => array(
															Type::STRING => $encryptedPassword
														)
													)
											)
										)
				);


				return $response;
			}
			else {
				Response::sendError(401);
				return;
			}

		}


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