<?php

if(isset($message)){
   foreach($message as $message){
      echo '
      <div class="message">
         <span>'.$message.'</span>
         <i class="fas fa-times" onclick="this.parentElement.remove();"></i>
      </div>
      ';
   }
}

?>

<header class="header">

   <div class="flex">

      <a href="newhome.php" class="logo"><span><b style="color: red;">e</b>SugboMarket</span></a>

      <nav class="navbar">
         <a href="newhome.php">home</a>
         <a href="newshop.php">shop</a>
         <a href="newabout.php">about</a>
         <a href="newcontact.php">contact</a>
      </nav>

      <div class="icons">
         <div id="menu-btn" class="fas fa-bars"></div>
         <div id="user-btn" class="fas fa-user"></div>
         <a href="newsearch_page.php" class="fas fa-search"></a>
         <a href="login.php"><i class="fas fa-shopping-cart"></i></a>
      </div>

      <div class="profile">
         <a href="login.php" class="btn">login</a>
         <a href="register.php" class="btn">register</a>
      </div>

   </div>

</header>