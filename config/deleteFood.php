<?php
	include("config.php");

	if(isset($_GET['id'])){
		$fid = (int)$_GET['id'];
		$sql = "DELETE FROM Foods WHERE fid=?";
		if($stmt = $conn->prepare($sql)){
			$stmt->bind_param("i", $fid);
			$stmt->execute();

			if(!$stmt->error){

			}
			
			//could do some error checking as below, but idk the details tbh (cus this is very hacky, but idk AJAX or jQuery)
			//so I'm omitting it
			//if ($stmt->execute()){}
			//else{}
		} //else{}
	}
?>