<?php

include_once("config.php");

function getStoreNames(){
	global $conn;
	$storeNames = array();
	$sql='CALL getStoreNames()'; //counting on this being an index
	$stmt=$conn->prepare($sql);
	$stmt->execute();
	$stmt->bind_result($name,$sid);

	//dictionary with name as index, store sid as key
	while($stmt->fetch()){
		$storeNames[$name] = $sid;
	}

	return $storeNames;
}

//Useful debug function
function debug_to_console($data){
  $output = $data;
  if (is_array($output))
      $output = implode(',', $output);

  echo "<script>console.log('Debug Objects: " . $output . "' );</script>";
}

?>