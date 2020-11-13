<?php
include("config/config.php");
include("config/functions.php");
include("header.php");
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

              <button type="submit" class="button-primary fw">Add Store</button>
          </form>
      </div>
      
      <!-- Delete Store -->
      <div name="delete_container" class="hidden">
        <div class="manage_stores_delete_container">
				  <label for="store">Store to delete:</label>
					<select required class="fw" name="store">
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
					
					<button type="submit" class="button-primary fw">Delete Store</button>

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