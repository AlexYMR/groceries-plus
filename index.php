<!-- find -type f | xargs grep -l "" -->
<?php
include("header.php");
?>

<header class="hero">
  <section class="container">
    <h1 class="intro">
      <span class="primary"><i class="fas fa-shopping-bag"></i></span>
      Groceries
      <span class="primary">+</span>
    </h1>

    <div style="margin:20px auto 50px auto; width:10%;" class="lineDecoration"></div>

    <ul>
      <li class="fw">
        <a href="#">
          <button class="main-select button-primary fw">
            Select Items
          </button>
        </a>
      </li>
      <li class="fw">
        <a href="foods.php">
          <button class="main-select button-primary fw">
            Manage Foods
          </button>
        </a>
      </li>
      <li class="fw">
        <a href="stores.php">
          <button class="main-select button-primary fw">
            Manage Stores
          </button>
        </a>
      </li>
    </ul>
  </section>
</header>

<?php
include("footer.php");
?>