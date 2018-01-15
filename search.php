<?php
require_once 'scripts/functions.php';
    session_start();

    //Get $brands,$type,$q(Search),etc.
    extract($_GET);
    if(!isset($page)) $page = 1;
    loadHead();
    loadTopBar();
    loadNavBar();
    beginContent();

    //Echo page nav
    echo "<div class='col-md-12'>
              <ul class='breadcrumb'>
                  <li><a href='index.php' class='nav-link'>Home</a>
                  </li>
                  <li>Search</li>
              </ul>
          </div>";

    //Load side categories
    loadSideCategories();
    echo "</div>";

    //Connect to DB
    $conn = sqlConnect();

    //Build SQL statement
    $sql = "SELECT id FROM ";

    //If there's a search term
    if(isset($q)) {
        $q = explode(" ",$q);

        $sql .= "(SELECT * FROM products WHERE LOWER(name) LIKE LOWER(\"%$q[0]%\") OR LOWER(brand) LIKE LOWER(\"%$q[0]%\") OR LOWER(product_type) LIKE LOWER(\"%$q[0]%\")";

        for($i=1;$i<count($q);$i++) {
            $sql .= " AND LOWER(name) LIKE LOWER(\"%$q[$i]%\") OR LOWER(brand) LIKE LOWER(\"%$q[$i]%\") OR LOWER(product_type) LIKE LOWER(\"%$q[$i]%\")";
        }

        $sql .= ") as search ";
    }

    else $sql .= "products ";

    //If there's brand append brand
    if(isset($brand)) $sql .= "WHERE LOWER(brand) = LOWER(\"$brand\") ";

    //If there's brand and type append type
    if(isset($type) && isset($brand)) $sql .= "AND LOWER(product_type) = LOWER(\"$type\") ";

    //If there's only type append type
    else if(isset($type)) $sql .= "WHERE LOWER(product_type) = LOWER(\"$type\") ";

    //End SQL statement
    $sql .= ";";

    //Get result
    $result = mysqli_query($conn,$sql);

    if($result) {
        while($row = mysqli_fetch_assoc($result)) {
            $id[] = $row['id'];
        }
    }

    //Close DB connection
    mysqli_close($conn);

    //Get total for printing/adding pages
    $total = count($id);
    $end = (30*$page);
    $start = ($end-29);
    if($end>$total) $end = $total;
    if($start>$total) $start = $total;

    //Echo products
    echo "<div class='col-md-9'>
                    <div class='box'>
                        <h1>Search</h1>
                        <p>Showing products containing your search</p>
                    </div>

                    <div class='box info-bar'>
                        <div class='row'>
                            <div class='col-sm-12 products-showing'>
                                Showing <strong>$start</strong> to <strong>$end</strong> of <strong>$total</strong> products.
                            </div>
                        </div>
                    </div>

                    <div class='row products'>";

    for($i=$start-1;$i<$end;$i++) {
        if($id[$i]>0) loadProduct($id[$i],'search');
    }

    //Echo page links
    echo "</div>
        <!-- /.products -->
        <div class='pages'>
            <ul class='pagination'>";
    $currentURL = (isset($_SERVER['HTTPS']) ? "https" : "http") . "://$_SERVER[HTTP_HOST]/BeautyHub/search.php?";
    if(isset($brand)) $currentURL .= "brand=$brand&";
    if(isset($type)) $currentURL .= "type=$type&";
    if(isset($q)) $currentURL .= "q=$q&";
    for($i=0;$i<(ceil($total/30));$i++) {
        $page = $i+1;
        $thisURL = $currentURL . "p=$page";
        echo "<li id='$page'><a href='$thisURL'>$page</a>
              </li>";
    }

    //End page content
    echo "</ul>
      </div>
    </div>
    <!-- /.col-md-9 -->";

    //Load footer
    loadFoot();
?>
