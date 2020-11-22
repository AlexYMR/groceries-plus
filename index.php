<!-- find -type f | xargs grep -l "" -->
<?php
session_start();
$_SESSION['came_from_index']=true; //sort of hacky, but prevents infinite redirects
session_write_close();
include("config/session.php");
include("config/config.php");
include("header.php");
unset($_SESSION['came_from_index']);
if(isset($_SESSION['login_user'])){
  header("location:landing.php");
  exit;
}

if($_SERVER["REQUEST_METHOD"] == "POST"){
  // username and password sent from form
  $user = $_POST['username'];
  $passwd = $_POST['password'];
  
  // Try to retrieve user from DB
  $sql = "SELECT uid,password FROM Users WHERE username=?";
  $stmt = $conn->prepare($sql);
  $stmt->bind_param("s", $user);
  $stmt->execute();
  $stmt->bind_result($uid,$pwd);
  $stmt->fetch();
  
  // If user in DB, should only retrieve 1 record
  if(!$stmt->error){
    if(password_verify($passwd,$pwd)){
      $_SESSION['login_user'] = $user;
      $_SESSION['loginID'] = $uid;
      header("location:landing.php");
      exit;
    } else {
?>
      <script>
        document.addEventListener("DOMContentLoaded", function(event){
          const container = document.querySelector(".container");
          createNotification("error",container,container.children[0],"Incorrect username or password.",3);
        },{once:true});
      </script>
<?php
    }
  } else{
?>
    <script>
      document.addEventListener("DOMContentLoaded", function(event){
        const container = document.querySelector(".container");
        createNotification("error",container,container.children[0],"An error has occurred; likely a database error.",3);
      },{once:true});
    </script>
<?php
  }
} 
?>

<header class="hero">
  <section class="container">
    <h1 class="intro">
      <span class="primary"><i class="fas fa-shopping-bag"></i></span>
      <span style="font-size:6.5rem;">G</span>roceries
      <span class="primary">+</span>
    </h1>

    <div style="margin:20px auto 50px auto; width:10%;" class="lineDecoration"></div>

    <ul id="menu">
      <li class="fw">
        <a href="#">
          <button id="loginButton" class="main-select button-primary fw">
          Log in
          </button>
        </a>
      </li>
    </ul>

    <div id="login" class="hidden">
      <form class="loginForm" action = "" method = "post">
          <label for="username">Username</label>
          <input type = "text" name = "username" class = "box fw">
          <label for="password">Password</label>
          <input type = "password" name = "password" class = "box fw">
          <button class="button-primary fw" type="submit">Log In</button>
      </form>
    </div>

  </section>
</header>

<!-- Login Stuff -->
<script>

const menu = document.getElementById("menu");
const loginButton = document.getElementById("loginButton");

loadListeners();  

function loadListeners(){
  loginButton.addEventListener("click",showLogin);
}

function showLogin(e){
  if(e.target === loginButton){
    menu.classList.add("hidden");
    document.querySelector("#login").classList.remove("hidden");
  }
}

</script>

<?php
include("footer.php");
?>