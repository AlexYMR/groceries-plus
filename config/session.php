<?php
session_start();
if(!isset($_SESSION['login_user'])){
	//to protect pages that require you to be logged in to access
	if(!isset($_SESSION['came_from_index'])){
		header("location:index.php");
		exit;
	}
}
?>