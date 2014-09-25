<?php
define("USERSPACE", '0b699c75-afd3-4bae-b6d3-c73bc6ae72f9');

class Utility {

	
	public function encrypt($plaintext) {
		$salt = self::createBlowfishSalt();
		$encryptedText = crypt($plaintext, $salt);

		return $encryptedText;
	}

	private function createBlowfishSalt($rounds = 8) {	
		//http://www.the-art-of-web.com/php/blowfish-crypt/

		$salt = "";
		$chars = array_merge(range('A','Z'), range('a','z'), range(0,9));

		for($i = 0; $i< 22; $i++) {
			$salt .= array_rand($chars);;
		}

		$salt = sprintf('$2a$%02d$', $rounds) . $salt;
		return $salt;

	}


	public function createV4Uid() {
		//obtained at http://php.net/manual/en/function.uniqid.php
	 return sprintf('%04x%04x-%04x-%04x-%04x-%04x%04x%04x',
	      mt_rand(0, 0xffff), mt_rand(0, 0xffff),
	      mt_rand(0, 0xffff),
	      mt_rand(0, 0x0fff) | 0x4000,
	      mt_rand(0, 0x3fff) | 0x8000,
	      mt_rand(0, 0xffff), mt_rand(0, 0xffff), mt_rand(0, 0xffff)
	    );
   }

   public function createV5Uid($name, $namespace = USERSPACE) {
		//obtained at http://php.net/manual/en/function.uniqid.php

   		$namehex = str_replace(array('-',',', '{', '}'), '', $namespace);
   		$namestr = "";

   		for($i = 0; $i < strlen($namehex); $i+=2) {
   			$namestr .= chr(hexdec($namehex[$i] . $namehex[$i+1]));
   		}

   		$namehash = sha1($namestr . $name);

   		$uuid = sprintf('%08s-%04s-%04x-%04x-%12s', 
   						substr($namehash, 0, 8),
   						substr($namehash, 8, 4), 
   						(hexdec(substr($namehash, 12, 4)) & 0x0fff) | 0x5000,
   						(hexdec(substr($namehash,16,4)) & 0x3fff) | 0x8000,
						substr($namehash, 20, 12));

   		return $uuid;


   }

}