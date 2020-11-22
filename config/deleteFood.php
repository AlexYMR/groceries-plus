<?php
	include("config.php");

	if(isset($_GET['id'])){
		$fid = (int)$_GET['id'];
		$sql = "DELETE FROM Foods WHERE fid=?";
		if($stmt = $conn->prepare($sql)){
			$stmt->bind_param("i", $fid);
			$stmt->execute();

			if(!$stmt->error){
				//could do some error checking
			}			
		}
	}
?>