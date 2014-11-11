<?php
require 'vendor/autoload.php';
require 'CouchClient.php';
\Slim\Slim::registerAutoloader();

$app = new \Slim\Slim();
$dbWrapper = new CouchWrapper();
const SESSIONID = 'X-Sessionid';

function writeResponse($status, $return) {
	global $app;

	if($status == 200) {
		header("Content-Type: application/json");
		echo json_encode($return);
	}
	else {
		$app->halt($status);
	}	
}

function getJsonData() {
	$jsonMessage = \Slim\Slim::getInstance()->request();
	$message = json_decode($jsonMessage->getBody());
	return $message;
}

function getSessionID() {
	global $app;
	$headers = $app->request->headers;
	if(isset($headers[SESSIONID])) {
		return $headers[SESSIONID];
	}
	else {
		return null;
	}
}


$app->get('/api/hello/:name', function($name){
	echo "Hello, $name";
});

$app->post('/api/auth', function(){
	$message = getJsonData();

	if(isset($message->email, $message->password)) {
		$return = CouchDriver::authenticateUser($message->email, $message->password);
		$status = array_pop($return);
	}
	else {
		$status = 400;
		$return = null;
	}

	writeResponse($status, $return);


});

$app->post('/api/user', function(){
	$message = getJsonData();
	
	if(isset($message->email, $message->password, $message->fname, $message->lname)) {
		$return = CouchDriver::insertUser($message->email, $message->password, $message->fname, $message->lname);
		$status = array_pop($return);
	}
	else {
		$status = 400;
		$return = null;
	}

	writeResponse($status, $return);

});

$app->delete('/api/user', function() {
	$message = getJsonData();
	$sessionID = getSessionID();
	$return = null;
	if(isset($message->password) && !is_null($sessionID)) {
		$return = CouchDriver::deleteUser($message->password, $sessionID);
		$status = array_pop($return);

	}
	else {
		$status = 400;
	}

	writeResponse($status, $return);
});

$app->put('/api/user', function() {
	$message = getJsonData();
	$sessionID = getSessionID();
	$return = null;
	if(isset($message->oldPassword, $message->newPassword)) {
		$return = CouchDriver::changePassword($message->oldPassword, $message->newPassword, $sessionID);
		$status = array_pop($return);
	}
	else {
		$status = 400;
	}

	writeResponse($status, $return);


});

$app->get('/api/listings', function() {
	$sessionID = getSessionID();
	$return = null;
	$queries = parse_str($_SERVER['QUERY_STRING'], $params);

	$return = CouchDriver::getAllListings($sessionID, $params);
	$status = array_pop($return);

	writeResponse($status, $return);

});

$app->get('/api/listings/:listingID', function($listingID) {
	$sessionID = getSessionID();
	$return = null;

	$return = CouchDriver::getListing($sessionID, $listingID);
	$status = array_pop($return);

	writeResponse($status, $return);


});

$app->post('/api/listings', function() {
	$message = getJsonData();
	$sessionID = getSessionID();
	$return = null;

	if(isset($message->title, $message->askingPrice, $message->category, $message->description, $message->images)) {
		$return = CouchDriver::insertItem($message->title, $message->askingPrice, $message->category, $message->description, $message->images, $sessionID);
		$status = array_pop($return);
	}
	else {
		$status = 400;
	}

	writeResponse($status, $return);

});

$app->put('/api/listings/:listingID', function($listingID) {
	$message = getJsonData();
	$sessionID = getSessionID();
	$return = null;

	$return = CouchDriver::updateItem(
							$listingID,
							isset($message->title) ? $message->title : null,
							isset($message->askingPrice) ? $message->askingPrice : null,
							isset($message->category) ? $message->category : null,
							isset($message->description) ? $message->description : null,
							isset($message->images) ? $message->images : null,
							$sessionID);

	$status = array_pop($return);
	writeResponse($status, $return);

});

$app->delete('/api/listings/:listingID', function($listingID) {
	$sessionID = getSessionID();
	$return = null;

	$return = CouchDriver::deleteItem($sessionID, $listingID);
	$status = array_pop($return);
	writeResponse($status, $return);

});

$app->post('/api/replies/:listingID', function($listingID) {
	$message = getJsonData();
	$sessionID = getSessionID();


	if(isset($message->text, $message->dateTime)) {
		$return = CouchDriver::insertReply($sessionID, $listingID, $message->text, $message->dateTime);
		$status = array_pop($return);
	}
	else {
		$status = 400;
	}

	writeResponse($status, $return);



});

$app->get('/api/replies/:listingID', function($listingID) {
	$sessionID = getSessionID();

	$return = CouchDriver::getReplies($sessionID, $listingID);
	$status = array_pop($return);

	writeResponse($status, $return);
});

$app->notFound(function () use ($app) {

        // build response
        $response = array(
            'type' => 'not_found',
            'message' => 'The requested resource does not exist.'
        );

       // output response
       $app->halt(404, json_encode($response));

});



$app->run();
