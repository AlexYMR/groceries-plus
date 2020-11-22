<?php
session_start();
if(isset($_SESSION['login_user'])){
	// $timedOut=isset($_SESSION['login_timed_out']);
	session_unset();
	session_destroy();
	header("location:index.php");
	exit;
} else{
	if(!isset($_SESSION['came_from_index'])){
		header("location:index.php");
		exit;
	}
}
?>