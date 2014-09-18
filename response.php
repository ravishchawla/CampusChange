<?php
class Response {

	public function createResponse($error) {
		switch($error) {
			case 400:
				return '400: Bad Request';
		}
	}

}