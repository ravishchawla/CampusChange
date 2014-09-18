<?php
class Response {

	public function createResponse($error) {
		switch($error) {
			case 204:
				return '204: No Content';
			case 400:
				return '400: Bad Request';
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
			return 0;
		}
		else {
			return -1;
		}

	}

}