<?php
include("config/config.php");
include("config/functions.php");
include("header.php");

$storeNames = array(); //getStoreNames();

if($_SERVER["REQUEST_METHOD"] == "POST"){
  // info sent from form
  $name = strtolower($_POST['fn']);
  $name = strtoupper($name[0]) . substr($name,1);
  $price = $_POST['price'];
  $oz = $_POST['oz'];
  $store = strtolower($_POST['store']);
  $store = strtoupper($store[0]) . substr($store,1)
  $store = $storeNames[$store];

  if(!ctype_alpha($name[0])){
?>
    <script>
      document.addEventListener("DOMContentLoaded", function(event){
        createNotification("error",document.querySelector("body"),"Invalid food name.",3);
      },{once:true});
    </script>

<?php
  //can do more error checking if time allows...
  } else{    

    $sql = "INSERT INTO Foods(name,price,oz,sid) VALUES (?,?,?,?)";
      
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("sddi", $name,$price,$oz,$store);

    if($stmt->execute()){ // Successful insert
?>
      <script>
        document.addEventListener("DOMContentLoaded", function(event){
          createNotification("success",document.querySelector("body"),"Successfully added food item to database!",3);
        },{once:true});
      </script>
<?php
    }
    else{
?>
      <script>
        document.addEventListener("DOMContentLoaded", function(event){
          createNotification("error",document.querySelector("body"),"An error has occurred. The item has not been added to the database.",3);
        },{once:true});
      </script>
<?php
    }
  }
}

?>
<header class="hero">
  <section class="foodContainer">
    <a style="position:absolute; left:0; top:0;" href="index.php">
      <button class="button-primary">
        <i class="fas fa-chevron-left"></i>
      </button>
    </a>
    
    <h1><span class="light">Manage Foods</span></h1>
    <div id="manage_foods_container">
      <div id="food_options">  
        <button class="button-primary btn_sel manage_foods_button">Add Food</button>
        <button class="button-primary manage_foods_button">Delete Food</button>
      </div>

      <div style="margin:30px auto;" class="lineDecoration"></div>

      <!-- Add Food -->
      <div name="add_container">
          <form action="" method="post">
              <label for="fn" class="req">Food Name:</label>
              <input required class="fw" type="text" name="fn">
              <label for="price" class="req">Price:</label>
              <input required class="fw" type="number" min=".01" step=".01" name="price" placeholder="0.00">
              <label for="oz" class="req">Ounces:</label>
              <input required class="fw" type="number" min="0" step=".1" name="oz" placeholder="0.0">
              <label for="store" class="req">Associated Store:</label>
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

              <div style="margin:30px auto;" class="lineDecoration"></div>

              <button type="submit" class="button-primary fw">Add Food</button>
          </form>
      </div>
      
      <!-- Delete Food -->
      <div name="delete_container" class="hidden">
        <div class="manage_foods_delete_container">
          <input style="margin:0 0 30px 0" type="text" name="filter" placeholder="Filter By Food Name">
          <!-- <div style="width:100%" class="lineDecoration"></div> -->
          <iframe src="" name="deleteFood_hack" style="display:none"></iframe>

          <table class="u-full-width" cellspacing="0">
              <thead>
                <tr>
                  <th>Food Name</th>
                  <th>Store</th>
                  <th>Price per Oz</th>
                  <th></th>
                </tr>
              </thead>
              <tbody id="delete_foods_list">
                <!-- Template -->
                <tr>
                  <td>Chicken</td>
                  <td>Walmart</td>
                  <td>$.33</td>
                  <td><a href="#" class="delete" style="text-align:left">X</a></td>
                </tr>

                <!-- MUST DYNAMICALLY CREATE TABLES WITH DATABASE -->

              </tbody>
          </table>

        </div>
      </div>
    </div>
  </section>
</header>

<!-- Main JS -->
<script>
  const addButton = document.querySelector("#food_options").children[0];
    
  const delButton = document.querySelector("#food_options").children[1];
    
  const filter = document.querySelector("input[name='filter']");
  console.log(filter);
    
  const deleteContainer = document.querySelector(".manage_foods_delete_container");
    
  loadListeners();
  
  function loadListeners(){
    addButton.addEventListener("click",tabClick);
    delButton.addEventListener("click",tabClick);
    filter.addEventListener("keyup",filterFood);
    deleteContainer.addEventListener("click",removeFood);
  }

  function tabClick(e){
    //add selected to button and switch displays
    if(e.target === addButton){
      if(!e.target.classList.contains("btn_sel")){
        document.querySelector("#manage_foods_container").children[3].classList.add("hidden");
        document.querySelector("#manage_foods_container").children[2].classList.remove("hidden");
        addButton.classList.add("btn_sel");
        delButton.classList.remove("btn_sel");
      }
    } else if(e.target === delButton){
      if(!e.target.classList.contains("btn_sel")){
        document.querySelector("#manage_foods_container").children[2].classList.add("hidden");
        document.querySelector("#manage_foods_container").children[3].classList.remove("hidden");
        delButton.classList.add("btn_sel");
        addButton.classList.remove("btn_sel");
      }
    }
  }

  function filterFood(e){
    const txt = e.target.value.toLowerCase();

    document.querySelectorAll("#delete_foods_list tr").forEach(function(food){
      const fullname = food.firstElementChild.textContent;
      if(fullname.toLowerCase().indexOf(txt) != -1){
        food.style.display = "table-row";
      } else{
        food.style.display = "none";
      }
    });
  }

  function removeFood(e){
    if(e.target.name === "delete"){
      // Ask user for confirmation
      if(confirm("Are you sure you would like to delete this food?")){
        // Run SQL through PHP to delete from database somehow;
        const idTag = e.target.parentElement.previousElementSibling.textContent;
        const source = "config/deleteFood.php?id=" + idTag;
        // Hacky way of avoiding XHRs and jQuery/AJAX
        window.frames['deleteFood_hack'].location.replace(source);
        // Delete from DOM
         e.target.parentElement.parentElement.remove();
      }
    }
  }
</script>

<?php
include("footer.php");
?>