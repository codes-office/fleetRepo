<?php

/*
@copyright

Fleet Manager v6.5

Copyright (C) 2017-2023 Hyvikk Solutions <https://hyvikk.com/> All rights reserved.
Design and developed by Hyvikk Solutions <https://hyvikk.com/>

 */

require '../../framework/vendor/autoload.php';
require '../../framework/bootstrap/autoload.php';

$app = require_once '../../framework/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Http\Kernel::class);

$response = $kernel->handle(
	$request = Illuminate\Http\Request::capture()
);

$subscription = json_decode(file_get_contents('php://input'), true);

// dd($subscription);
if (!isset($subscription['endpoint'])) {
	echo 'Error: not a subscription';
	//return;
}

$method = $_SERVER['REQUEST_METHOD'];
$host = env('DB_HOST');
$username = env('DB_USERNAME');
$password = env('DB_PASSWORD');
$database = env('DB_DATABASE');
$user_id = $subscription['loggedinuser'];
$user_type = $subscription['user_type'];

switch ($method) {
case 'POST':
	// create a new subscription entry in your database (endpoint is unique)
	$conn = mysqli_connect($host, $username, $password, $database);
	if ($conn) {
		echo "connection successful.";
		$endpoint = $subscription['endpoint'];
		$publickey = $subscription['publicKey'];
		$authtoken = $subscription['authToken'];
		$contentencoding = $subscription['contentEncoding'];

		$query = "insert into `push_notification`(`user_type`,`user_id`,`endpoint`,`publickey`,`authtoken`,`contentencoding`) VALUES('" . $user_type . "','" . $user_id . "','" . $endpoint . "','" . $publickey . "','" . $authtoken . "','" . $contentencoding . "')";

		$insert = mysqli_query($conn, $query);
		if ($insert) {
			echo "inserted successful";
		} else {
			echo "conncetion made but data is not inserted.";
		}
	} else {
		echo "connection not made with database.";
	}
	break;

case 'PUT':
	// update the key and token of subscription corresponding to the endpoint
	$conn = mysqli_connect($host, $username, $password, $database);
	if ($conn) {
		echo "connection successful.";
		$endpoint = $subscription['endpoint'];
		$publickey = $subscription['publicKey'];
		$authtoken = $subscription['authToken'];
		$contentencoding = $subscription['contentEncoding'];

		$query = "update `push_notification` SET `authtoken`='" . $authtoken . "',`contentencoding`='" . $contentencoding . "',`endpoint`='" . $endpoint . "',`publickey`='" . $publickey . "' ,`user_id`='" . $user_id . "',`user_type`='" . $user_type . "' WHERE `endpoint` = '" . $endpoint . "'";
		$insert = mysqli_query($conn, $query);
		if ($insert) {
			echo "updated successful";
			/*$query = "select * from details where `endpoint` ='".$endpoint."'";
	            $fetch = mysqli_query($conn,$query);
	            $retrive = mysqli_fetch_array($fetch);
*/
		} else {
			echo "conncetion made but data is not updated.";
		}
	} else {
		echo "connection not made with database.";
	}
	break;

case 'DELETE':
	// delete the subscription corresponding to the endpoint
	$conn = mysqli_connect($host, $username, $password, $database);
	if ($conn) {
		echo "connection successful.";
		$endpoint = $subscription['endpoint'];

		$query = "delete from `push_notification` where endpoint='" . $endpoint . "'";
		$delete = mysqli_query($conn, $query);
		if ($delete) {
			echo "deleted successful";
		} else {
			echo "conncetion made but data is not deleted.";
		}
	} else {
		echo "connection not made with database.";
	}
	break;

default:
	echo "Error: method not handled";
	return;
}
