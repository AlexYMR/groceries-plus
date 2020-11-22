<?php
	include("config/config.php");
	session_start();
	
	$error="";
	if($_SERVER["REQUEST_METHOD"] == "POST") 
	{
		// username and password sent from form 
		$user = $_POST['username'];
		$passwd = $_POST['password'];
		
		// Try to retrieve user from DB
		$sql = "SELECT user_id, password, admin FROM users WHERE username = ?";
		$stmt = $conn->prepare($sql);
		$stmt->bind_param("s", $user);
		$stmt->execute();
		$result = $stmt->get_result();
		
		// If user in DB, should only retrieve 1 record
		$count = mysqli_num_rows($result);
		if($count == 1){
		$row = $result->fetch_row();
		if(password_verify($passwd, $row[1])){
		$_SESSION['login_user'] = $user;
			$_SESSION['loginID'] = $row[0];
				$_SESSION['last_login_time'] = time();
				$_SESSION['is_admin'] = ($row[2] == 'Y'? true:false);
			//--
			$date = getdate();
			$month = ($date['mon'] > 9) ? $date['mon'] : '0'.$date['mon'];
			$mday = ($date['mday'] > 9) ? $date['mday'] : '0'.$date['mday'];  
			$loginDate = ''.$date['year'].'-'.$month.'-'.$mday;
			$logQuery = "UPDATE users SET last_login = ? WHERE user_id = ?";
			$stmt = $conn->prepare($logQuery);
			$stmt->bind_param("si", $loginDate, $row[0]);
			$stmt->execute();
			//--
			header("location:landing.php");
			exit;
		} else {
?>
         <script>
            document.addEventListener("DOMContentLoaded", function(event){
               //document is fully loaded 
               createNotification("error",document.querySelector("body"),"Your username or password is invalid",3)
            },{once:true})
         </script>
<?php
		 }
      } else {
?>
         <script>
            document.addEventListener("DOMContentLoaded", function(event){
               //document is fully loaded 
               createNotification("error",document.querySelector("body"),"Your username or password is invalid",3)
            },{once:true})
         </script>
<?php
		}
	}
include("header.php");
include("sidebar.php");
?>

<!-- Select sidebar page -->
<script>
	const btn = document.getElementById('sidebarLogin');
	btn.classList.add('sidebarItemSelect');
</script>

<head>
   <title>Login Page</title>
</head>
   
<div id="login">
   <div class="loginHeader"><b>Login</b></div>
      
   <form class="loginForm" action = "" method = "post">
      <label for="username">Username  :</label><input type = "text" name = "username" class = "box"/>
      <label for="password">Password  :</label><input type = "password" name = "password" class = "box" />
      <input class = "submit" type = "submit" value = " Submit "/>
      <!-- <div class="errorMsg"><?php //echo $error; ?></div> -->
   </form>
   
</div>
      
<?php
	include ("footer.php");
?>

<script src="config/notification.js"></script>