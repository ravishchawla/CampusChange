<?php
class Response {

	public function createResponse($error, $message) {
		$response = array();
		switch($error) {
			case 200:
				$response['200'] = 'OK';
				break;
			case 204:
				$resposne['204'] = 'No Content';
				break;
			case 400:
				$response['400'] = 'Bad Request';
				break;
			case 401:
				$response['401'] = 'Not Authorized';
				break;
			case 404:
				$response['404'] = 'Not Found';
				break;
			case 500:
				$response['500'] = 'Internal Server Error';
				$response['error'] = $message;

		}
		return $response;

	}

	public function sendError($error, $message = null) {
		$response = self::createResponse($error, $message);
		if(isset($response)) {
			return $response;
		}
		else 
			return self::createResponse(500, 'Invalid Status code error');
	}
}