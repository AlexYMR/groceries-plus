<?php
include("config/config.php");
include("config/functions.php");
include("header.php");

$storeNames = array(); //getStoreNames();

if($_SERVER["REQUEST_METHOD"] == "POST"){

	//for add
	if(isset($_POST["addStore"])){
		// info sent from form
		$store = strtolower($_POST['sn']);
		$store = strtoupper($store[0]) . substr($store,1);
	
		//can do more error checking if time allows...    
	
		$sql = "INSERT INTO Stores(name) VALUES (?)";
			
		$stmt = $conn->prepare($sql);
		$stmt->bind_param("s", $store);
	
		if($stmt->execute()){
?>
			<script>
				document.addEventListener("DOMContentLoaded", function(event){
					createNotification("success",document.querySelector("body"),"Successfully added store to database!",3);
				},{once:true});
			</script>
<?php
			} else{
?>
			<script>
				document.addEventListener("DOMContentLoaded", function(event){
					createNotification("error",document.querySelector("body"),"An error has occurred. The store has not been added to the database.",3);
				},{once:true});
			</script>
<?php
		}
	}
	
	//for delete
	if(isset($_POST["deleteStore"])){
		// info sent from form
		$store = $storeNames[$_POST['sn']];
	
		//can do more error checking if time allows...    
	
		$sql = "DELETE FROM Stores WHERE sid=(?)";
			
		$stmt = $conn->prepare($sql);
		$stmt->bind_param("i", $store);
	
		if($stmt->execute()){
?>
			<script>
				document.addEventListener("DOMContentLoaded", function(event){
					createNotification("success",document.querySelector("body"),"Successfully deleted store from database!",3);
				},{once:true});
			</script>
<?php
			} else{
?>
			<script>
				document.addEventListener("DOMContentLoaded", function(event){
					createNotification("error",document.querySelector("body"),"An error has occurred. The store has not been removed from the database.",3);
				},{once:true});
			</script>
<?php
		}
	}
}
?>

<header class="hero">
  <section class="storeContainer">
    <a style="position:absolute; left:0; top:0;" href="index.php">
      <button class="button-primary">
        <i class="fas fa-chevron-left"></i>
      </button>
    </a>
    
    <h1><span class="light">Manage Stores</span></h1>
    <div id="manage_stores_container">
      <div id="store_options">  
        <button class="button-primary btn_sel manage_stores_button">Add Store</button>
        <button class="button-primary manage_stores_button">Delete Store</button>
      </div>

      <div style="margin:30px auto;" class="lineDecoration"></div>

      <!-- Add Store -->
      <div name="add_container">
          <form action="" method="post">
              <label for="sn" class="req">Store Name:</label>
							<input required class="fw" type="text" name="sn">

              <button type="submit" name="addStore" class="button-primary fw">Add Store</button>
          </form>
      </div>
      
      <!-- Delete Store -->
      <div name="delete_container" class="hidden">
        <div class="manage_stores_delete_container">
				  <label for="store">Store to delete:</label>
					<select required class="fw" name="sn">
						<option value="">Select store</option>
						<?php
							// Iterating through the keys of storeNames
							foreach(array_keys($storeNames) as $storeName){
						?>
								<option value="<?php echo $storeName; ?>"><?php echo $storeName;?></option>
						<?php
							}
						?>
					</select>
					
					<button type="submit" name="deleteStore" class="button-primary fw">Delete Store</button>

        </div>
      </div>
    </div>
  </section>
</header>

<!-- Main JS -->
<script>
  const addButton = document.querySelector("#store_options").children[0];
    
  const delButton = document.querySelector("#store_options").children[1];
    
  const filter = document.querySelector("input[name='filter']");
  console.log(filter);
    
  const deleteContainer = document.querySelector(".manage_stores_delete_container");
    
  loadListeners();
  
  function loadListeners(){
    addButton.addEventListener("click",tabClick);
    delButton.addEventListener("click",tabClick);
  }

  function tabClick(e){
    //add selected to button and switch displays
    if(e.target === addButton){
      if(!e.target.classList.contains("btn_sel")){
        document.querySelector("#manage_stores_container").children[3].classList.add("hidden");
        document.querySelector("#manage_stores_container").children[2].classList.remove("hidden");
        addButton.classList.add("btn_sel");
        delButton.classList.remove("btn_sel");
      }
    } else if(e.target === delButton){
      if(!e.target.classList.contains("btn_sel")){
        document.querySelector("#manage_stores_container").children[2].classList.add("hidden");
        document.querySelector("#manage_stores_container").children[3].classList.remove("hidden");
        delButton.classList.add("btn_sel");
        addButton.classList.remove("btn_sel");
      }
    }
  }
</script>

<?php
include("footer.php");
?>