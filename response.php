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

	public function authenticate($user_id, $password) {
		$uuid = AWSWrapper::authenticateClient($user_id, $password);
		echo $uuid['S'];
	}

	

	public function querySendUsers($authToken) {
		$users = AWSWrapper::scanAllUsers($authToken);
		if(isset($users)) {
			echo json_encode($users->toArray(), JSON_PRETTY_PRINT);
		}

	}

	public function queryGetUser($user, $token) {
		$user = AWSWrapper::getUserDetails($user, $token);
		if(isset($user)) {
			echo json_encode($user->toArray(), JSON_PRETTY_PRINT);
		}
	}

	public function putUser($token, $username, $email, $password, $firstName, $lastName) {
		AWSWrapper::putUser($username, $email, $password, $firstName, $lastName);
	}

	public function deleteUser($user, $password, $token) {
		$response = AWSWrapper::deleteUser($user, $password, $token);
		echo var_export($response, true);
	}

	public function changePassword($token, $user, $oldpassword, $newpassword) {
		
		$response = AWSWrapper::changePassword($token, $user, $oldpassword, $newpassword);
		if(isset($response))
				echo var_export($response, true);
	}

}