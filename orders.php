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
    <link rel="stylesheet" href="styles/style-wishlist.css">
    <title>McNeese Bookstore</title>
</head>
<body>
    <?php
        session_start();
        require __DIR__ . '/alert.php';
        require_once('mysql.php');


        // Ensure user is logged in to use this page
        if(!isset($_SESSION['Loggedin']))
        {
            header("Location: login.php");
        }

        // Check if page number is specified
        if(isset($_GET['pagenoorders']))
        {
            $pageno = $_GET['pagenoorders'];
        }
        else
        {
            $pageno = 1;
        }


        // Get number of pages
        $results_per_page = 16;  
        $page_first_result = ($pageno-1) * $results_per_page;
        
        $query = "SELECT ItemID FROM mcneesebookstore.purchasehistory";  
        $result = mysqli_query($connection, $query);  
        $total_rows = mysqli_num_rows($result);
        $total_pages = ceil($total_rows / $results_per_page);

        $query = " SELECT * 
        FROM mcneesebookstore.purchasehistory AS ph
        JOIN mcneesebookstore.bookinfo AS b ON ph.ItemID = b.ItemID 
        JOIN mcneesebookstore.inventory AS inv ON b.ItemID = inv.ItemID 
        JOIN (SELECT ItemID, GROUP_CONCAT(Name SEPARATOR ', ') AS Authors 
        FROM (SELECT BA.ItemID, BA.AuthorID, AI.Name 
        FROM mcneesebookstore.bookauthors AS BA 
        INNER JOIN (SELECT AuthorID, CONCAT(FirstName, ' ', LastName) AS 
        Name FROM mcneesebookstore.authorinfo) AS AI ON BA.AuthorID = AI.AuthorID) t 
        GROUP BY ItemID)
        AS a ON b.ItemID = a.ItemID
        WHERE ph.StudentID = " . $_SESSION['StudentID'] . " ";

        // Check what to order by from drop down menu
        if(isset($_POST['selector']) )
        {
            $_SESSION['orders_selector'] = $_POST['selector'];

            if($_SESSION['orders_selector'] === 'asc')
            {
                $query .= " ORDER BY DatePurchased ASC ";
            }
            else if($_SESSION['orders_selector'] === 'desc')
            {
                $query .= " ORDER BY DatePurchased DESC ";
            }
            else
            {
                $query .= " ORDER BY DatePurchased ASC ";
                $_SESSION['orders_selector'] = 'asc';
            }
        }

        // Get page results form databse
        $query .= " LIMIT " . $page_first_result . ' , ' . $results_per_page ." ";
        $result = mysqli_query($connection, $query);

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
            <a href="wishlist.html">Wishlist</a>
            <a href="cart.html">Cart</a>
            <a href="account.html">Account</a>
            <a href="login.html">Login</a>
            <a href="orders.html">Orders</a>
        </div>
    </div>
    <div class="main-content">
        <div class="store-control">
            <div class="store-control-left">
                <p>Orders</p>
            </div>
            <div class="store-control-center">
                <div class="pagination">
                <a href="?pagenoorders=1">First</a>
                        <a href="<?php if($pageno <= 1){ echo '#'; } else { echo "?pagenoorders=".($pageno - 1); } ?>">Prev</a>
                        <a Prev><?php echo $pageno; ?></a>
                        <a href="<?php if($pageno >= $total_pages){ echo '#'; } else { echo "?pagenoorders=".($pageno + 1); } ?>">Next</a>
                        <a href="?pagenoorders=<?php echo $total_pages; ?>">Last</a>
                  </div>
            </div>
            <form method="post" action="orders.php">
            <div class="store-control-right">
                    <select class="selector" name="selector" onChange="this.form.submit()">
                        <?php  if (!isset($_SESSION['orders_selector'])) : ?>
                                <option value=”asc”>Date ASC</option>
                                <option value=”desc”>Date DESC</option>
                            <?php endif ?>
                            <?php  if ($_SESSION['orders_selector'] === 'asc') : ?>
                                <option value="asc" selected="selected">Date ASC</option>
                                <option value="desc">Date DESC</option>
                            <?php endif ?>
                            <?php  if ($_SESSION['orders_selector'] === 'desc') : ?>
                                <option value="asc">Date ASC</option>
                                <option value="desc" selected="selected">Date DESC</option>
                            <?php endif ?>
                    </select>
            </div>
            </form">
        </div>
        <div class="store-items">
        <?php
            //display the retrieved result on the webpage  
            while($row = mysqli_fetch_array($result))
            { 
                $title = $row['Title'];
                $authors = $row['Authors'];
                $price = $row['BuyPrice'];
                $rating = $row['Rating'];
                $date = $row['DatePurchased'];
                $status = $row['PurchaseStatus'];
                $cost = $row['Cost'];
                $orderid = $row['PurchaseID'];


                // If name too long, cut name and ...
                if(strlen($title) > 100)
                {
                    $title = substr($row['Title'],0,97) . "...";
                }

                // If authors is too long, cut and ...
                if(strlen($authors) > 100)
                {
                    $authors = substr($row['Authors'],0,97) . "...";
                }

                echo ' 
                <div class="card">
                    <div class="another-wrapper-class">
                        <div class="card-details">
                            <div class="card-detail-order">
                                <p class="book-title">Order: '. $orderid .'</p>
                                <p class="book-title">Order Date: '. $date .'</p>
                            </div>
                            <div class="card-detail-status">
                                <p class="order-text">Status: '. $status .'</p>
                            </div>
                            <div class="card-detail-item">
                                <p class="book-author">'. $title .'</p>
                                <p class="book-author">Total: $'. $cost .'</p>
                            </div>
                        </div>
                    </div>
                </div>';
            } 
            ?>
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