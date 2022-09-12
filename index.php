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
    <link rel="stylesheet" href="styles/style-home.css">
    <title>McNeese Bookstore</title>
</head>
<body> 
    <?php
        session_start();
        require __DIR__ . '/alert.php';
        require_once('mysql.php');

        // Check if page number is specified
        if(isset($_GET['pageno']))
        {
            $pageno = $_GET['pageno'];
        }
        else
        {
            $pageno = 1;
        }


        // Get number of pages
        $results_per_page = 16;  
        $page_first_result = ($pageno-1) * $results_per_page;
        
        $query = "SELECT ItemID FROM mcneesebookstore.bookinfo";  
        $result = mysqli_query($connection, $query);  
        $total_rows = mysqli_num_rows($result);
        $total_pages = ceil($total_rows / $results_per_page);
        if(isset($_GET["search"]))
        {
            $searchTerm = $_GET["search"];
        }
        else
            $searchTerm = "";

        $query = " SELECT b.ItemID, b.Title, b.Rating, b.Major, b.ISBN10, inv.BuyPrice, inv.ImagePath, a.Authors 
        FROM mcneesebookstore.bookinfo AS b 
        JOIN mcneesebookstore.inventory AS inv ON b.ItemID = inv.ItemID 
        JOIN (SELECT ItemID, GROUP_CONCAT(Name SEPARATOR ', ') AS Authors 
        FROM (SELECT BA.ItemID, BA.AuthorID, AI.Name 
        FROM mcneesebookstore.bookauthors AS BA 
        INNER JOIN (SELECT AuthorID, CONCAT(FirstName, ' ', LastName) AS 
            Name FROM mcneesebookstore.authorinfo) AS AI ON BA.AuthorID = AI.AuthorID) t
        GROUP BY ItemID) AS a ON b.ItemID = a.ItemID
        WHERE b.Title LIKE '%".$searchTerm."%'
            OR b.ISBN10 LIKE '%".$searchTerm."%'
            OR b.Major LIKE '%".$searchTerm."%'
            OR a.Authors LIKE '%".$searchTerm."%'"
        ;

        // Check what to order by from drop down menu
        if(isset($_POST['selector']) )
        {
            $_SESSION['index_selector'] = $_POST['selector'];

            if($_SESSION['index_selector'] === "az")
            {
                $query .= " ORDER BY Title ASC ";
            }
            else if($_SESSION['index_selector'] === "za")
            {
                $query .= " ORDER BY Title DESC ";
            }
            else if($_SESSION['index_selector'] === "major")
            {
                $query .= " ORDER BY Major ASC ";
            }
            else if($_SESSION['index_selector'] === "lowestrating")
            {
                $query .= " ORDER BY Rating ASC ";
            }
            else if($_SESSION['index_selector'] === "highestrating")
            {
                $query .= " ORDER BY Rating DESC ";
            }
            else if($_SESSION['index_selector'] === "lowestprice")
            {
                $query .= " ORDER BY BuyPrice ASC ";
            }
            else if($_SESSION['index_selector'] === "highestprice")
            {
                $query .= " ORDER BY BuyPrice DESC ";
            }
            else
            {
                $query .= " ORDER BY Title ASC ";
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
            <a href="wishlist.php">Wishlist</a>
            <a href="cart.php">Cart</a>
            <a href="account.php">Account</a>
            <a href="login.php">Login</a>
            <a href="orders.php">Orders</a>
        </div>
    </div>
    <div class="main-content">
            <div class="store-control">
                <div class="store-control-left">
                    <p>Books</p>
                </div>
                <div class="store-control-center">
                    <div class="pagination">
                        <a href="?pageno=1">First</a>
                        <a href="<?php if($pageno <= 1){ echo '#'; } else { echo "?pageno=".($pageno - 1); } ?>">Prev</a>
                        <a Prev><?php echo $pageno; ?></a>
                        <a href="<?php if($pageno >= $total_pages){ echo '#'; } else { echo "?pageno=".($pageno + 1); } ?>">Next</a>
                        <a href="?pageno=<?php echo $total_pages; ?>">Last</a>
                    </div>
                </div>
                <div class="store-control-right">
                <form method="post" action="index.php">
                    <select class="selector" name="selector" onChange="this.form.submit()">
                        <?php  if (!isset($_SESSION['index_selector']) or $_SESSION['index_selector'] === "") : ?>
                            <option value="az" name="az">A - Z</option>
                            <option value="za" name="za">Z - A</option>
                            <option value="major" name="major">Major</option>
                            <option value="lowestrating" name="lowestrating">Lowest Rating</option>
                            <option value="highestrating" name="highestrating">Highest Rating</option>
                            <option value="lowestprice" name="lowestprice">Lowest Price</option>
                            <option value="highestprice" name="highestprice">Highest Price</option>
                        <?php endif ?>
                        <?php  if ($_SESSION['index_selector'] === "az") : ?>
                            <option value="az" selected="selected" name="az">A - Z</option>
                            <option value="za" name="za">Z - A</option>
                            <option value="major" name="major">Major</option>
                            <option value="lowestrating" name="lowestrating">Lowest Rating</option>
                            <option value="highestrating" name="highestrating">Highest Rating</option>
                            <option value="lowestprice" name="lowestprice">Lowest Price</option>
                            <option value="highestprice" name="highestprice">Highest Price</option>
                        <?php endif ?>
                        <?php  if ($_SESSION['index_selector'] === "za") : ?>
                            <option value="az" name="az">A - Z</option>
                            <option value="za" selected="selected" name="za">Z - A</option>
                            <option value="major" name="major">Major</option>
                            <option value="lowestrating" name="lowestrating">Lowest Rating</option>
                            <option value="highestrating" name="highestrating">Highest Rating</option>
                            <option value="lowestprice" name="lowestprice">Lowest Price</option>
                            <option value="highestprice" name="highestprice">Highest Price</option>
                        <?php endif ?>
                        <?php  if ($_SESSION['index_selector'] === "major") : ?>
                            <option value="az" name="az">A - Z</option>
                            <option value="za" name="za">Z - A</option>
                            <option value="major" selected="selected" name="major">Major</option>
                            <option value="lowestrating" name="lowestrating">Lowest Rating</option>
                            <option value="highestrating" name="highestrating">Highest Rating</option>
                            <option value="lowestprice" name="lowestprice">Lowest Price</option>
                            <option value="highestprice" name="highestprice">Highest Price</option>
                        <?php endif ?>
                        <?php  if ($_SESSION['index_selector'] === "lowestrating") : ?>
                            <option value="az" name="az">A - Z</option>
                            <option value="za" name="za">Z - A</option>
                            <option value="major" name="major">Major</option>
                            <option value="lowestrating" selected="selected" name="lowestrating">Lowest Rating</option>
                            <option value="highestrating" name="highestrating">Highest Rating</option>
                            <option value="lowestprice" name="lowestprice">Lowest Price</option>
                            <option value="highestprice" name="highestprice">Highest Price</option>
                        <?php endif ?>
                        <?php  if ($_SESSION['index_selector'] === "highestrating") : ?>
                            <option value="az" name="az">A - Z</option>
                            <option value="za" name="za">Z - A</option>
                            <option value="major" name="major">Major</option>
                            <option value="lowestrating" name="lowestrating">Lowest Rating</option>
                            <option value="highestrating" selected="selected" name="highestrating">Highest Rating</option>
                            <option value="lowestprice" name="lowestprice">Lowest Price</option>
                            <option value="highestprice" name="highestprice">Highest Price</option>
                        <?php endif ?>
                        <?php  if ($_SESSION['index_selector'] === "lowestprice") : ?>
                            <option value="az" name="az">A - Z</option>
                            <option value="za" name="za">Z - A</option>
                            <option value="major" name="major">Major</option>
                            <option value="lowestrating" name="lowestrating">Lowest Rating</option>
                            <option value="highestrating" name="highestrating">Highest Rating</option>
                            <option value="lowestprice" selected="selected" name="lowestprice">Lowest Price</option>
                            <option value="highestprice" name="highestprice">Highest Price</option>
                        <?php endif ?>
                        <?php  if ($_SESSION['index_selector'] === "highestprice") : ?>
                            <option value="az" name="az">A - Z</option>
                            <option value="za" name="za">Z - A</option>
                            <option value="major" name="major">Major</option>
                            <option value="lowestrating" name="lowestrating">Lowest Rating</option>
                            <option value="highestrating" name="highestrating">Highest Rating</option>
                            <option value="lowestprice" name="lowestprice">Lowest Price</option>
                            <option value="highestprice" selected="selected" name="highestprice">Highest Price</option>
                        <?php endif ?>
                    </select>
                    </form>
                </div>
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
                $image = $row['ImagePath'];
                $itemID = $row['ItemID'];

                // If name too long, cut name and ...
                if(strlen($title) > 30)
                {
                    $title = substr($row['Title'],0,27) . "...";
                }

                // If authors is too long, cut and ...
                if(strlen($authors) > 30)
                {
                    $authors = substr($row['Authors'],0,27) . "...";
                }

                $stars = "" . str_repeat("<span class='book-rating material-icons'>star</span>", intval($rating)) . "";
                if($rating - intval($rating) > 0.3 and $rating - intval($rating) < 0.7)
                {
                    $stars .= "<span class='book-rating material-icons'>star_half</span>";
                }





                echo '
                    <div class="card" onclick="location.href="book.php"">
                        <div class="card-img-container">
                            <form action="book.php" method="post">
                                <input type="image" class=""item-img" src='. $image . ' alt="book" width="200px"
                                    height="200px" />
                                <input type="hidden" name="ItemID" value=' . $itemID . '>
                            </form>
                        </div>
                        <div class="card-details">
                            <div class="card-detail-title">
                                <p class="book-title">'. $title .'</p>
                            </div>
                            <div class="card-detail-author">
                                <p class="book-author">' . $authors . '</p>
                            </div>
                            <div class="card-detail-rating">' . $stars . '</div>
                            <div class="card-detail-price">
                                <div class="card-detail-price">
                                    <p class="book-price">$' . $price . '</p>
                                </div>
                            </div>
                        </div>
                    </div>
                ';
            } 
            ?>
        </div>
    </div>
</body>
</html>