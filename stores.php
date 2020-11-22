<?php
include("config/session.php");
include("config/config.php");
include("config/functions.php");
include("header.php");

/* 
there's some hacky code here, but it's the only way I knew how to fix the issue. 

basically, after adding/removing, the page reloads, but I suspect it reloads too quickly for changes to be reflected on the database, so the delete store options don't properly reflect what's in the database at the current time

to fix this, I made use of localStorage, and basically editted the webpage once loaded after a successful database update query to reflect the expected changes to the database

...actually, all I had to really do was refresh the $storeNames variable, fml. I'm keeping this in here anyway as a lesson
*/

$storeNames = getStoreNames();

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
		$stmt->execute();
		if(!$stmt->error){
?>
			<script>
				document.addEventListener("DOMContentLoaded", function(event){
					const container = document.querySelector(".storeContainer");
					createNotification("success",container,container.children[1],"Successfully added store to database!",3);

					const selection = document.getElementById("delete_store_select");
					function createNewStore(){
						//need store name from localStorage
						let storeInfo = localStorage.getItem("storeToAdd");
						console.log(storeInfo);
						const option = document.createElement("option");

						const capName = storeInfo.charAt(0).toUpperCase() + storeInfo.slice(1);

						option.setAttribute("value",capName);
						option.appendChild(document.createTextNode(capName));
				
						selection.appendChild(option);
					}

					createNewStore();

				},{once:true});
			</script>
<?php
			} else{
?>
			<script>
				document.addEventListener("DOMContentLoaded", function(event){
					const container = document.querySelector(".storeContainer");
					createNotification("error",container,container.children[1],"An error has occurred. The store has not been added to the database.",3);
				},{once:true});
			</script>
<?php
		}
	}
	//for delete
	if(isset($_POST["deleteStore"])){
		// info sent from form
		$store = (int)$storeNames[$_POST['sn']];
	
		//can do more error checking if time allows...    
	
		$sql = "DELETE FROM Stores WHERE sid=(?)";

		$stmt = $conn->prepare($sql);
		$stmt->bind_param("i", $store);
		$stmt->execute();
		if(!$stmt->error){
?>
			<script>
				document.addEventListener("DOMContentLoaded", function(event){
					const container = document.querySelector(".storeContainer");
					createNotification("success",container,container.children[1],"Successfully deleted store from database!",3);

					function deleteStore(){
						let storeInfo = localStorage.getItem("storeToRemove");
						//find option with certain value
						const optionToDelete = document.querySelector('#delete_store_select option[value="'+ storeInfo +'"]').remove();
					}

					deleteStore();

				},{once:true});
			</script>
<?php
			} else{
?>
			<script>
				document.addEventListener("DOMContentLoaded", function(event){
					const container = document.querySelector(".storeContainer");
					createNotification("error",container,container.children[1],"An error has occurred. The store has not been removed from the database.",3);
				},{once:true});
			</script>
<?php
		}
	}
}
?>

<header class="hero">
  <section class="storeContainer">
    <a class="backButton" href="index.php">
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
				<form action="" id="add_store_form" method="post">
						<label for="sn" class="req">Store Name:</label>
						<input required id="add_store_input" class="fw" type="text" name="sn">

						<button type="submit" name="addStore" class="button-primary fw">Add Store</button>
				</form>
      </div>
      
      <!-- Delete Store -->
      <div name="delete_container" class="hidden">
				<div class="manage_stores_delete_container">
					<form action="" id="delete_store_form" method="post">
						<label for="sn" class="req">Store to delete:</label>
						<select required id="delete_store_select" class="fw" name="sn">
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
					</form>
        </div>
      </div>
    </div>
  </section>
</header>

<!-- Main JS -->
<script>
  const addButton = document.querySelector("#store_options").children[0];
  const delButton = document.querySelector("#store_options").children[1];
	
	const addStoreInput = document.getElementById("add_store_input");
	const addStoreButton = document.getElementById("add_store_form");
	
	const store_selection = document.getElementById("delete_store_select");
	const deleteStoreButton = document.getElementById("delete_store_form");

    
  loadListeners();
  
  function loadListeners(){
    addButton.addEventListener("click",tabClick);
		delButton.addEventListener("click",tabClick);
		addStoreButton.addEventListener("submit",queueInLocalToAdd);
		deleteStoreButton.addEventListener("submit",queueInLocalToRemove);
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
	
	function queueInLocalToAdd(e){
		if(addStoreInput.value != null && addStoreInput.value != ""){
			localStorage.setItem("storeToAdd",addStoreInput.value);
		}
	}
	
	function queueInLocalToRemove(e){
		if(store_selection.value != "" && store_selection.value != null){
			localStorage.setItem("storeToRemove",store_selection.selectedOptions[0].value);
		}
	}
</script>

<?php
include("footer.php");
?>