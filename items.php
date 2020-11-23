<?php
include("config/session.php");
include("config/config.php");
include("config/functions.php");
include("header.php");

$storeNames = getStoreNames();

if($_SERVER["REQUEST_METHOD"] == "POST"){
  // info sent from form
  $name = $_POST['fn'];//strtolower($_POST['fn']);
  //$name = strtoupper($name[0]) . substr($name,1);
  $price = $_POST['price'];
  $oz = $_POST['oz'];
  $store = ucwords(strtolower($_POST['store'])); //title case
  if(array_key_exists($store,$storeNames)){
    $store = $storeNames[$store];
  } else{
    $store = "";
  }
  if(!ctype_alpha($name[0])){
?>
    <script>
      document.addEventListener("DOMContentLoaded", function(event){
        const container = document.querySelector(".foodContainer");
        createNotification("error",document.querySelector(".foodContainer"),container.children[1],"Invalid food name.",3);
      },{once:true});
    </script>

<?php
  //can do more error checking if time allows...
  } else{    

    $sql = "INSERT INTO Foods(name,price,oz,sid) VALUES (?,?,?,?)";
      
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("sddi", $name,$price,$oz,$store);
    $stmt->execute();
    if(!$stmt->error){ // Successful insert
?>
      <script>
        document.addEventListener("DOMContentLoaded", function(event){
          const container = document.querySelector(".foodContainer");
          createNotification("success",container,container.children[1],"Successfully added food item to database!",3);
        },{once:true});
      </script>
<?php
    } else{
      echo $stmt->error;
?>
      <script>
        document.addEventListener("DOMContentLoaded", function(event){
          const container = document.querySelector(".foodContainer");
          createNotification("error",document.querySelector(".foodContainer"),container.children[1],"An error has occurred. The item has not been added to the database.",3);
        },{once:true});
      </script>
<?php
    }
  }
}

?>
<header class="hero">
  <section class="itemsContainer">
    <a class="backButton" href="index.php">
      <button class="button-primary">
        <i class="fas fa-chevron-left"></i>
      </button>
    </a>

    <h1><span class="light">Select Items</span></h1>
    <div id="select_items_container">
      <div id="item_options">  
        <button class="button-primary btn_sel">All Items</button>
        <button class="button-primary">View Selected Items</button>
      </div>

      <div style="margin:30px auto;" class="lineDecoration"></div>

      <!-- All Foods -->
      <div name="items_container">
        <input style="margin:0 0 30px 0" type="text" name="filter" placeholder="Filter By Food Name">
        <button class="button-primary clear_button">Clear Cart</button>
        <table class="u-full-width" cellspacing="0">
          <thead>
            <tr>
              <th>Food Name</th>
            </tr>
          </thead>
          <tbody id="select_foods_list">
            <!-- Template -->
            <!-- 
            <tr>
              <td>Chicken</td>
            </tr>
            -->
<?php
            //will want to index this for best efficiency
            // $sql = 'SELECT name,min(price/oz),sid,price,oz,fid from Foods group by name having min(price/oz) order by name';
            $sql = 'SELECT f.name,min(price/oz),s.name,f.price,f.oz from Foods as f inner join Stores as s on s.sid = f.sid group by f.name order by f.name';
            $stmt = $conn->prepare($sql);
            $stmt->execute();
            $stmt->bind_result($fn,$minCost,$sname,$price,$oz);
            
            while($stmt->fetch())
            {
?>
              <tr>
                <td><?php echo ucwords(strtolower($fn)); ?></td>
                <td style="display:none"><?php echo $sname; ?></td>
                <td style="display:none"><?php echo "$" . number_format($price,2); ?></td>
                <td style="display:none"><?php echo number_format($oz,1); ?></td>
                <td style="display:none"><?php echo number_format($price,2); ?></td>
              </tr>
<?php
            }
?>
          </tbody>
        </table>
      </div>
      
      <!-- Stuff in List -->
      <div name="cart_container" class="hidden">
        <div class="cart_items_container">
          <table class="u-full-width" cellspacing="0">
              <thead>
                <tr>
                  <th>Food Name</th>
                  <th>Store</th>
                  <th>Price</th>
                  <th>Ounces</th>
                </tr>
              </thead>
              <tbody id="cart_list">
                <!-- Template -->
                <!-- 
                <tr>
                  <td>Chicken</td>
                  <td>$.33</td>
                  <td>Walmart</td>
                </tr>
                -->
              </tbody>
          </table>
          
          <div style="margin:30px auto;" class="lineDecoration"></div>

          <!-- Total amount to spend -->
          <div id="total_frame">
            <h5>Total Cost:</h3>
            <h5 class="total">$0.00</h3>
          </div>

        </div>
      </div>
    </div>
  </section>
</header>

<!-- Main JS -->
<script>
  const itemSelectionButton = document.querySelector("#item_options").children[0];  
  const viewCartButton = document.querySelector("#item_options").children[1];
  const filterByName = document.querySelector("input[name='filter']");

  const cart_list = document.getElementById("cart_list");
  const clearButton = document.querySelector(".clear_button");
  
  // const total = document.getElementById("total");
  const totalLabel = document.querySelector(".total");

  let totalAmount = 0;

  let selectedItems = [];

  loadListeners();
  
  function loadListeners(){
    filterByName.addEventListener("keyup",filterFood);
    document.querySelectorAll("#select_foods_list tr").forEach(function(item){		
        item.addEventListener("mouseenter",onHover);
        item.addEventListener("mouseleave",onLeave);
        item.addEventListener("mouseup",onClick);
    });
    itemSelectionButton.addEventListener("click",tabClick);
    viewCartButton.addEventListener("click",tabClick);
    clearButton.addEventListener("click",clearList);
  }
  
  function tabClick(e){
    //add selected to button and switch displays
    if(e.target === itemSelectionButton){
      if(!e.target.classList.contains("btn_sel")){
        document.querySelector("#select_items_container").children[3].classList.add("hidden");
        document.querySelector("#select_items_container").children[2].classList.remove("hidden");
        itemSelectionButton.classList.add("btn_sel");
        viewCartButton.classList.remove("btn_sel");
      }
    } else if(e.target === viewCartButton){
      if(!e.target.classList.contains("btn_sel")){
        document.querySelector("#select_items_container").children[2].classList.add("hidden");
        document.querySelector("#select_items_container").children[3].classList.remove("hidden");
        viewCartButton.classList.add("btn_sel");
        itemSelectionButton.classList.remove("btn_sel");
      }
    }
  }

  function onClick(e){
    if(e.button === 0){ //this is the left mouse button
      //add item to cart (other page)
      //add btn_sel class permanently (don't remove on mouseleave)
      if(selectedItems.includes(e.target)){
        e.target.classList.remove("btn_sel");
        selectedItems.splice(selectedItems.indexOf(e.target),1);
        //--
        //remove item from cart
        const name = e.target.parentElement.children[0].textContent;
        for(i = 0; i < cart_list.children.length; i++){
          console.log(cart_list.children[i]);
          if(cart_list.children[i].children[0].textContent === name){
            totalAmount -= parseFloat(cart_list.children[i].children[4].textContent);
            cart_list.children[i].remove();
          }
        }
      } else{
        e.target.classList.add("btn_sel");
        selectedItems.push(e.target);
        //add item to cart
        const row = document.createElement("tr");
        // console.log(e.target.parentElement.children);
        row.innerHTML = `
          <td>${e.target.parentElement.children[0].textContent}</td>
          <td>${e.target.parentElement.children[1].textContent}</td>
          <td>${e.target.parentElement.children[2].textContent}</td>
          <td>${e.target.parentElement.children[3].textContent}</td>
          <td style="display:none">${e.target.parentElement.children[4].textContent}</td>
        `;
        row.setAttribute("name",e.target.parentElement.children[0].textContent);
        totalAmount += parseFloat(e.target.parentElement.children[4].textContent);
        cart_list.appendChild(row);
      }
      totalLabel.textContent = "$" + parseFloat(totalAmount).toFixed(2);
    }
  }

  function clearList(){
    //deselect things on item selection page
    for(i = 0; i < selectedItems.length; i++){
      console.log(selectedItems[i]);
      selectedItems[i].classList.remove("btn_sel");
    }
    selectedItems = [];
    //remove things from cart page
    while(cart_list.children.length > 0){
      cart_list.children[0].remove();
    }
    totalAmount = 0;
    totalLabel.textContent = "$0.00";
  }

  function onHover(e){
    if(!selectedItems.includes(e.target)){
      e.target.classList.add("btn_sel");
    }
  }

  function onLeave(e){
    if(!selectedItems.includes(e.target)){
      e.target.classList.remove("btn_sel");
    }
  }

  function filterFood(e){
    const txt = e.target.value.toLowerCase();

    document.querySelectorAll("#select_foods_list tr").forEach(function(item){
      const fullname = item.firstElementChild.textContent;
      if(fullname.toLowerCase().indexOf(txt) != -1){
        item.style.display = "table-row";
      } else{
        item.style.display = "none";
      }
    });
  }
</script>

<?php
include("footer.php");
?>