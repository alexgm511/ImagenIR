<?php

function generateRandomString($length = 10) {
    $characters = '0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ';
    $randomString = '';
    for ($i = 0; $i < $length; $i++) {
        $randomString .= $characters[rand(0, strlen($characters) - 1)];
    }
    return $randomString;
}

function generatePwdHash($username, $password) {
	// Create a random string to mix in with username
	$randString = generateRandomString();
	
	// Create a 256 bit (64 characters) long random salt
	// Let's add 'something random' and the username
	// to the salt as well for added security
	
	$salt = hash('sha256', uniqid(mt_rand(), true) . $randString . strtolower($username));
	
	// Prefix the password with the salt
	$hash = $salt . $password;
	
	// Hash the salted password a bunch of times
	for ( $i = 0; $i < 100000; $i ++ ) {
	  $hash = hash('sha256', $hash);
	}
	
	// Prefix the hash with the salt so we can find it back later
	$hash = $salt . $hash;
    return $hash;
}

function compareHashToPwd($password, $strHash) {

	// The first 64 characters of the hash is the salt
	$salt = substr($strHash, 0, 64);
	
	$hash = $salt . $password;

	// Hash the password as we did before
	for ( $i = 0; $i < 100000; $i ++ ) {
	  $hash = hash('sha256', $hash);
	}
	
	$hash = $salt . $hash;
	
	if ( $hash == $strHash ) {
	  // Ok!
	  return True;
	}
	else {
	  return False;
	}
	
	
}

 ?>