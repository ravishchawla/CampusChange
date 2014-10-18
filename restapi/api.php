<?php
abstract class API {
	
	/*
	/<endpoint>/<verb>/<arg0>/<arg1>
	/<endpoint>/<arg0>/<arg1>
	*/
	protected $method = '';
	protected $endpoint = '';
	protected $request = '';
	protected $args = Array();	
	protected $file = Null;
	protected $data = Array();
	protected $sessionID;
	//constructor

	public function __construct($request) {
		header("Access-Control-Allow-Origin: *");
		header("Access-Control-Allow-Methods: *");
		header("Content-Type: application/json");

		$path = array_shift($request);
		$this->args = explode('/', rtrim($path, '/'));

		if(isset($_SERVER['HTTP_X_SESSIONID'])){
			$this->sessionID = $_SERVER['HTTP_X_SESSIONID'];
		}

		$this->endpoint = array_shift($this->args);

		$this->method = $_SERVER['REQUEST_METHOD'];

		switch($this->method) {
			case 'POST':
				$this->request = $this->_cleanInputs($_POST);
				break;
			case 'GET':
				$this->request = $this->_cleanInputs($_GET);
				break;
			case 'PUT':
			case 'DELETE':
				$file = file_get_contents("php://input");
				parse_str($file, $this->request);
				break;
			default:
				$this->_response('Invalid Method', 405);
				break;
		}
	}

		public function processAPI() {
			if ((int)method_exists($this, $this->endpoint) > 0) {
				return $this->_response($this->{$this->endpoint}($this->args, $this->request));
			}
			return $this->_response("No Endpoint: $this->endpoint", 404);
		}

		private function _response($data, $status = 200) {
			header("HTTP/1.1 " . $status . " " . $this->_requestStatus($status));
			return json_encode($data);
		}

		private function _cleanInputs($data) {
			$clean_input = Array();
			if (is_array($data)) {
				foreach ($data as $key => $value) {
					$clean_input[$key] = $this->_cleanInputs($value);
				}
			}
			else {
				$clean_input = trim(strip_tags($data));
			}

			return $clean_input;
		}

		private function _requestStatus($code) {
			$status = array(
				200 => 'OK',
				404 => 'Not Found',
				405 => 'Method Not Allowed',
				500 => 'Internal Server Error',
			);
			return ($status[$code])?$status[$code]:$status[500];
		}

}