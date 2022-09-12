<!-- McNeese Online Bookstore Website -->
<!-- Author: Michael Boudreaux, Thien Le, Collin Ardoin, and Ian Andrepont -->
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="https://fonts.googleapis.com/css?family=Inter">
    <link href="https://fonts.googleapis.com/icon?family=Material+Icons"
          rel="stylesheet">
    <link rel="stylesheet" href="styles/style.css">
    <link rel="stylesheet" href="styles/style-nav.css">
    <link rel="stylesheet" href="styles/style-foot.css">
    <link rel="stylesheet" href="styles/style-account.css">
    <title>McNeese Bookstore</title>
</head>
<body>
<?php
session_start();
require_once('mysql.php');
require('alert.php');

// Ensure user is logged in to use this page
if(!isset($_SESSION['Loggedin']))
{
    header("Location: login.php");
}
else
{
    $id = $_SESSION['StudentID'];

    if(isset($_POST['save']))
    {
        $street = trim($_POST['street']);
        $city = trim($_POST['city']);
        $state = trim($_POST['state']);
        $zip = trim($_POST['zip']);
        $cardnum = trim($_POST['cardnum']);
        $cardexp = trim($_POST['cardexp']);
        $cvv = trim($_POST['cvv']);

        $sql = "UPDATE mcneesebookstore.studentinfo SET Street = '$street', City = '$city', State = '$state', ZIP = '$zip', CardNum = '$cardnum', CardExp = '$cardexp', CardCVV = '$cvv' 
                    WHERE StudentID = " . $id . "";
        $result = mysqli_query($connection,$sql);

        if(!$result == false)
        {
            alert("Account information updated successfully.");
        }
    }

    // Load address and payment info from database
    $sql = "SELECT FirstName, LastName, Street, City, State, ZIP, CardNum, CardExp, CardCVV FROM mcneesebookstore.studentinfo WHERE StudentID = '$id'";
    $result = mysqli_query($connection,$sql);

    // Save values in variable for refernce later
    if(mysqli_num_rows($result) === 1)
    {
        $row = mysqli_fetch_assoc($result);
        $street = $row['Street'];
        $city = $row['City'];
        $state = $row['State'];
        $zip = $row['ZIP'];
        $cardnum = $row['CardNum'];
        $cardexp = $row['CardExp'];
        $cvv = $row['CardCVV'];
        $firstName = $row['FirstName'];
        $lastName = $row['LastName'];

    }
}

mysqli_close($connection);
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
    <input class="menu-search" type="text" placeholder="Search...">
    <div class="menu-links">
        <a href="wishlist.php">Wishlist</a>
        <a href="cart.php">Cart</a>
        <a href="account.php">Account</a>
        <a href="login.php">Login</a>
        <a href="orders.php">Orders</a>
    </div>
</div>
<div class="main-content">
    <div class="wrapper">
        <div class="main-content-wrapper">
            <form id="fillout" action="http://localhost/McNeeseBookstore/account.php" method="post">
                <div class="partition">
                    <p class="name"><?php echo "$firstName $lastName" ?></p>
                </div>
                <div class="partition">
                    <p class="name">Shipping Address</p>
                    <div class="form-txt">
                        <label for="street">Street</label>
                        <input type="text-box" name="street" placeholder="Street" value="<?php echo $street ?>" required>
                    </div>
                    <div class="form-txt">
                        <label for="city">City</label>
                        <input type="text-box" name="city" placeholder="City" value="<?php echo $city ?>" required>
                    </div>
                    <div class="form-txt">
                        <label for="state">State</label>
                        <input type="text-box" name="state" placeholder="State" value="<?php echo $state ?>" required>
                    </div>
                    <div class="form-txt">
                        <label for="zip">Zip</label>
                        <input type="text-box" name="zip" placeholder="ZIP Code" value="<?php echo $zip ?> "required>
                    </div>
                </div>
                <div class="partition">
                    <p class="name">Payment Information</p>
                    <div class="form-txt">
                        <label for="cardnum">Card Number</label>
                        <input type="text-box" name="cardnum"placeholder="Card #" value="<?php echo $cardnum ?>" required>
                    </div>
                    <div class="form-txt">
                        <label for="cardexp">Card Expiration Date</label>
                        <input type="text-box" name="cardexp" placeholder="Expiration Date" value="<?php echo $cardexp ?>" required>
                    </div>
                    <div class="form-txt">
                        <label for="cvv">CVV</label>
                        <input type="text-box" name="cvv" placeholder="CVV" value="<?php echo $cvv ?>" required>
                    </div>
                </div>
                <div class="partition">
                    <button class="button" type="submit" name="save">Save</button>
                </div>
            </form>
        </div>
    </div>
</div>
</body>
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
</html>