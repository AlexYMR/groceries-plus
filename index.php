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
  
  if(isset($_POST["login"])){
    echo "something";
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
  
  if(isset($_POST["register"])){
    // username and password sent from form
    $fn = $_POST['fn'];
    $ln = $_POST['ln'];
    $user = $_POST['username'];
    $passwd = $_POST['password'];
    $repasswd = $_POST['re-password'];
    
    if(empty($fn) || !ctype_alpha($fn[0])){
      //error
    } elseif(empty($ln) || !ctype_alpha($ln[0])){
      //error
    } elseif($passwd == $repasswd){
      $hash = password_hash($passwd,PASSWORD_DEFAULT);
      
      // Insert user into table
      $sql = "INSERT INTO Users(first_name,last_name,uid,username,password) VALUES (?,?,?,?,?)";
      $stmt = $conn->prepare($sql);
      $n = NULL;
      $stmt->bind_param("ssiss",$fn,$ln,$n,$user,$hash);
      $stmt->execute();
      
      // If user in DB, should only retrieve 1 record
      if(!$stmt->error){
?>
        <script>
          document.addEventListener("DOMContentLoaded", function(event){
            const container = document.querySelector(".container");
            createNotification("success",container,container.children[0],"Successfully registered user.",3);
          },{once:true});
        </script>
<?php
        // header("location:index.php");
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
          <button id="loginButton" class="main-select button-primary block fw">
          Log in
          </button>
          <button id="registerButton" class="main-select button-primary block fw">
          Register
          </button>
        </a>
      </li>
    </ul>

    <div id="login" class="hidden">
      <form class="loginForm" action = "" method = "post">
          <label for="username">Username</label>
          <input type = "text" name = "username" class = "fw req">
          <label for="password">Password</label>
          <input type = "password" name = "password" class = "fw req">
          <button class="button-primary fw" name="login" type="submit">Log In</button>
      </form>
      <button class="button-primary return">Back</button>
    </div>
    
    <div id="registration" class="hidden">
      <form class="registrationForm" action = "" method = "post">
          <label for="fn" class="req">First Name</label>
          <input type = "text" name = "fn" class = "fw">
          <label for="ln" class="req">Last Name</label>
          <input type = "text" name = "ln" class = "fw">
          <label for="username" class="req">Username</label>
          <input type = "text" name = "username" class = "fw">
          <label for="password" class="req">Password</label>
          <input type = "password" name = "password" class = "fw">
          <label for="re-password" class="req">Re-type Password</label>
          <input type = "password" name = "re-password" class = "fw">
          <button class="button-primary fw" name="register" type="submit">Create New Account</button>
      </form>
      <button class="button-primary return">Back</button>
    </div>

  </section>
</header>

<!-- Login Stuff -->
<script>

const menu = document.getElementById("menu");
const login = document.getElementById("login");
const registration = document.getElementById("registration");

const loginButton = document.getElementById("loginButton");
const registerButton = document.getElementById("registerButton");

const backButtons = document.querySelectorAll(".return");

loadListeners();  

function loadListeners(){
  loginButton.addEventListener("click",showLogin);
  registerButton.addEventListener("click",showRegistration);
  backButtons.forEach(e => {
    e.addEventListener("click",goBack);
  });
  // backButton.
}

function showLogin(e){
  if(e.target === loginButton){
    menu.classList.add("hidden");
    document.querySelector("#login").classList.remove("hidden");
  }
}

function showRegistration(e){
  if(e.target === registerButton){
    menu.classList.add("hidden");
    document.querySelector("#registration").classList.remove("hidden");
  }
}

function goBack(e){
  // if(backButtons.contains(e.target)){
    if(!login.classList.contains("hidden")){
      login.classList.add("hidden");
    } else if(!registration.classList.contains("hidden")){
      registration.classList.add("hidden");
    }
    menu.classList.remove("hidden");
  // }
}

</script>

<?php
include("footer.php");
?>