<?php
class Response {

	public function createResponse($error) {
		switch($error) {
			case 204:
				return '204: No Content';
			case 400:
				return '400: Bad Request';
			case 401:
				return '401: Not Authorized';
			case 404:
				return '404: Not Found';
			case 500:
				return '500: Internal Server Error';
		}

	}

	public function sendError($error) {
		$response = self::createResponse($error);
		if(isset($response)) {
			echo $response;
		}
	}

	public function authenticate() {
		$user_ip = $_SERVER['REMOTE_ADDR'];

		$uuid = AWSWrapper::authenticateClient($user_ip);
		echo $uuid;
	}

	

	public function querySendUsers($authToken) {
		$users = AWSWrapper::scanAllUsers($authToken);
		if(isset($users)) {
			echo json_encode($users->toArray());
		}

	}

	public function putUser($token, $username, $email, $password, $firstName, $lastName) {
		AWSWrapper::putUser($token, $username, $email, $password, $firstName, $lastName);
	}

}