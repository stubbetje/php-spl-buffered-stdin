<?php

ini_set( 'date.timezone', 'Europe/Brussels' );
ini_set( 'display_errors', 1 );
error_reporting( E_ALL | E_STRICT );

$file = __DIR__ . '/../vendor/autoload.php';

if( ! file_exists( $file ) ) {
	throw new RuntimeException( 'Install dependencies to run test suite.' );
}

$autoload = require_once $file;
