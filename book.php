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
    <link rel="stylesheet" href="styles/style-book.css">
    <title>McNeese Bookstore</title>
</head>
<body>
<?php
session_start();
require __DIR__ . '/alert.php';
require_once('mysql.php');

$ItemID = $_POST["ItemID"];


if(isset($_POST['addToWishlist']))
{
    $ItemName = $_POST["ItemName"];
    $Stock = $_POST["Stock"];
    $StudentID = $_SESSION["StudentID"];

    if(isset($_SESSION['Loggedin']))
    {
        //echo "$StudentID $ItemID $ItemName $Stock";

        $wishlistQuery = 'INSERT INTO mcneesebookstore.wishlist
                        (StudentID, ItemID, ItemName, inStock)
                        VALUES
                        ('.$StudentID.', '.$ItemID.', "'.$ItemName.'", '.$Stock.')';

        $wishListResult = mysqli_query($connection,$wishlistQuery);

        if(!$wishListResult == false)
        {
            header("location: wishlist.php");
        }
        else
            alert("Item could not be added to wishlist.");
    }
    else
        header("Location: login.php");
}

if(isset($_POST['addToCart']))
{
    $ItemName = $_POST["ItemName"];
    $Stock = $_POST["Stock"];
    $StudentID = $_SESSION["StudentID"];

    if(isset($_SESSION['Loggedin']))
    {
        $cartQuery = 'INSERT INTO mcneesebookstore.cart
                        (StudentID, ItemID, ItemName, RentOrBuy, Quantity)
                        VALUES
                        ('.$StudentID.', '.$ItemID.', "'.$ItemName.'", "Rent", 1)';

        $cartResult = mysqli_query($connection,$cartQuery);

        if(!$cartResult == false)
        {
            header("location: cart.php");
        }
        else
            alert("Item could not be added to cart.");
    }
    else
        header("Location: login.php");
}
else

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
</div>
<div class="main-content">
    <?php
    $sql = "SELECT inventory.ItemID, inventory.ItemName, bookinfo.ISBN10, bookinfo.Edition, bookinfo.Rating, inventory.Stock, 
                inventory.RentPrice, inventory.BuyPrice, inventory.ImagePath, bookinfo.Major, bookinfo.Type
                FROM mcneesebookstore.inventory
                INNER JOIN mcneesebookstore.bookinfo ON bookinfo.ItemID = inventory.ItemID
                WHERE inventory.ItemID = ".$ItemID."
                ORDER BY inventory.ItemID";
    $Authors = "SELECT bookinfo.ItemID, authorinfo.FirstName, authorinfo.LastName
                    FROM mcneesebookstore.bookinfo
                    INNER JOIN mcneesebookstore.bookauthors ON bookauthors.ItemID = bookinfo.ItemID
                    INNER JOIN mcneesebookstore.authorinfo ON authorinfo.AuthorID = bookauthors.AuthorID
                    WHERE bookinfo.ItemID = ".$ItemID."
                    ORDER BY bookinfo.ItemID";

    $result = mysqli_fetch_array(mysqli_query($connection, $sql));
    $ImagePath = $result["ImagePath"];
    $rating = $result['Rating'];
    $AuthorInfoResult = mysqli_query($connection, $Authors);



    echo "<div class='main-wrapper'>
            <div class='card'>
                <div class='another-wrapper-class'>
                    <div class='card-img-container'>
                        <img class='item-img' src=".$ImagePath." alt='bookImage' style='width:100%'>
                    </div>
                    <div class='card-details'>
                        <div class='card-detail-title'>
                            <p class='book-title'>".$result["ItemName"]."</p>
                        </div>";

    $stars = "" . str_repeat("<span class='book-rating material-icons'>star</span>", intval($rating)) . "";
    if($rating - intval($rating) > 0.3 and $rating - intval($rating) < 0.7)
    {
        $stars .= "<span class='book-rating material-icons'>star_half</span>";
    }

    echo               "<div class='card-detail-author'>
                            <p class='book-author'>";
    while($row = mysqli_fetch_assoc($AuthorInfoResult))
    {
        echo $row["FirstName"]." ".$row["LastName"]."<br>";
    }
    echo                    "</p>
                        </div>
                        <div class='card-detail-price'>
                            <div class='card-detail-price'>
                                <p class='book-price'>Buy Price: $".$result["BuyPrice"]."<br>
                                                      Rent Price: $".$result["RentPrice"]."
                                </p>
                            </div>
                        </div>
                        <div class='card-detail-date-added'>
                                <p class='book-date-added'>Major: ".$result["Major"]."</p>
                        </div>
                        <div class='card-detail-date-added'>
                                <p class='book-date-added'>Type: ".$result["Type"]."</p>
                        </div>
                        <div class='card-detail-date-added'>
                                <p class='book-date-added'>ISBN10: ".$result["ISBN10"]."</p>
                        </div>
                    </div>
                </div>
                <div class='card-buttons'>
                    <form action='book.php' method='post'>
                        <button class='card-cart-button' type='submit' name='addToCart' placeholder='cart' >Add to cart</button>
                        <input type='hidden' name='ItemID' value='".$ItemID."'/>
                        <input type='hidden' name='ItemName' value='".$result["ItemName"]."' />
                        <input type='hidden' name='Stock' value='".$result["Stock"]."' />
                    </form>
                    <form action='book.php' method='post'>
                        <button class='card-remove-button' type='submit' name='addToWishlist' placeholder='wishlist'>Add to wishlist</button>
                        <input type='hidden' name='ItemID' value='".$ItemID."'/>
                        <input type='hidden' name='ItemName' value='".$result["ItemName"]."' />
                        <input type='hidden' name='Stock' value='".$result["Stock"]."' />
                    </form>
                </div>
            </div>
           </div>";
    ?>
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