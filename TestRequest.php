<?php
class TestRequest {
	
	public function sendRequest() {
		$request = new HttpRequest('http://localhost//thrift.php', HttpRequest::METH_GET);
		$request->setOptions(array('list' => 'users'));

		try {
			$requst->send();
			echo $request->getResponseBody();
		}

		catch (HttpException $hex) {
			echo $hex;
		}
	}
}

TestRequest::sendRequest();