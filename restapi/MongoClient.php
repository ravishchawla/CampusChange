<?php

public $connection;
public $database;

public function __construct() {
	$connection = new Mongo();
	$database = $connection->selectDB()

}

public function authenticateUser($email, $password) {	


}

public function insertUser($email, $password, $fname, $lname) {


}

public function deleteUser($password, $token){


}

public function changePassword($oldPassword, $newPassword, $token) {


}

public function getAllListings($token, $params) {


}

public function getListing($token, $listingID) {


}

public function insertItem($title, $askingPrice, $category, $description, $images, $token) {


}

public function updateItem($id, $title, $askingPrice, $category, $description, $images, $token) {


}

public function deleteItem($token, $listingID) {


}