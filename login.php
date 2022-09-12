<!-- McNeese Online Bookstore Website -->
<!-- Author: Michael Boudreaux, Thien Le, Collin Ardoin, and Ian Andrepont -->
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link href="https://fonts.googleapis.com/icon?family=Material+Icons"
      rel="stylesheet">
    <link rel="stylesheet" href="styles/style.css">
    <link rel="stylesheet" href="styles/style-nav.css">
    <link rel="stylesheet" href="styles/style-foot.css">
    <link rel="stylesheet" href="styles/style-login.css">
</head>
<body>
  <?php
    require __DIR__ . '/alert.php';
    session_start();


    // Ensure user is not logged in
    if(isset($_SESSION['Loggedin']))
    {
        header("Location: index.php");
    }

    if(isset($_POST['login']))
    {
      if(!empty($_POST['username']) && !empty($_POST['password']))
      {
        require_once('mysql.php');

        $username = trim($_POST['username']);
        $password = trim($_POST['password']);
        
        $sql = "SELECT StudentID, Username FROM mcneesebookstore.studentinfo WHERE Username = '$username' and Password = '$password'";
        $result = mysqli_query($connection,$sql);
        
        if(mysqli_num_rows($result) === 1)
        {
          $row = mysqli_fetch_assoc($result);
          $_SESSION['Loggedin'] = true; 
          $_SESSION['StudentID'] = $row['StudentID'];
          $_SESSION['Username'] = $row['Username'];
          header("Location: index.php");
          exit();
        }
        else
        {
          alert("Incorrect username or password, please try again.");
        }
      }
    }
  ?>
    <nav class="nav">
        <div class="nav-elements">
            <div class="nav-left">
                <a class="nav-title" href="index.php">McNeese Bookstore</a>
            </div>
            <div class="nav-center">
                <div class="nav-searchbar">
                    <form action="index.php" method="get">
                        <button class="nav-search-button material-icons">search</button>
                        <input class="nav-search nav-elem-toggle" type="text" placeholder="Search..." name="search">
                    </form>
                </div>
            </div>
            <div class="nav-right">
                <?php if (isset($_SESSION['Loggedin'])) : ?>
                    <span class="nav-menu material-icons" onClick="toggleMenu()">menu</span>
                    <div class="nav-links nav-elem-toggle">
                        <a href="wishlist.php">Wishlist</a>
                        <a href="cart.php">Cart</a>
                        <a href="orders.php">Orders</a>
                        <a href="account.php">Account</a>
                        <a href="logout.php">Logout</a>
                    </div>
                <?php endif ?>
                <?php  if (!isset($_SESSION['Loggedin'])) : ?>
                    <span class="nav-menu material-icons" onClick="toggleMenu()">menu</span>
                    <div class="nav-links nav-elem-toggle">
                        <a href="login.php">Login</a>
                    </div>
                <?php endif ?>

            </div>
        </div>
    </nav>
  <div class="menu" id="side-menu">
      <form action="index.php" method="get">
          <button class="nav-search-button material-icons">search</button>
          <input class="nav-search nav-elem-toggle" type="text" placeholder="Search..." name="search">
      </form>
      <div class="menu-links">
          <a href="wishlist.php">Wishlist</a>
          <a href="cart.php">Cart</a>
          <a href="account.php">Account</a>
          <a href="login.php">Login</a>
          <a href="orders.php">Orders</a>
      </div>
  </div>
    <div class="main-content">
      <div class="login-form-wrapper">
        <div class="login-form">
          <h1>Login</h1>
          <form method="post" action="login.php">
            <div class="form-txt">
              <input type="text-box" name="username" placeholder="Username" required>
            </div>
            <div class="form-txt">
              <input type="password" name="password" placeholder="Password" required>
            </div>
            <button class="form-button" type="submit" name="login">Login</button>
          </form>
        </div>
      </div>
    </div>
  <script>

    function toggleMenu() {
        if(document.getElementById("side-menu").style.display === "flex") {
            document.getElementById("side-menu").style.display = "none";
        }
        else {
            document.getElementById("side-menu").style.display = "flex"; 
        }
    }

    function correctMenu(menuStatus) {
    if (menuStatus.matches) {
        document.getElementById("side-menu").style.display = "flex";
    } else {
        document.getElementById("side-menu").style.display = "none";
    }
    }

    var menuStatus = window.matchMedia("(max-width: 900px)")
    correctMenu(menuStatus)
    menuStatus.addListener(correctMenu)
</script>
</body>
</html>