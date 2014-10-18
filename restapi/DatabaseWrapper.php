<?php
abstract class DatabaseWrapper {


	public function __construct() {

	}

	abstract public function authenticateUser($email, $password);



}