<?php
    require_once 'scripts/functions.php';
    loadHead();
    loadTopBar();
    loadNavBar();
    beginContent();
?>
    <div class='col-md-12'>
        <div class='box'>
            <h1>New Product</h1>
            <p class='lead'>Add a new product for the BeautyHub community to find dupes and prices for and enjoy.</p>

            <h3>Product Info</h3>

            <form action='javascript:void(0)' id='add-new-product'>
                <div class='row'>
                    <div class='col-sm-6 text-center'>
                        <div class='form-group'>
                            <label for='productName'>Product Name*</label>
                            <input type='text' class='form-control' id='productName' placeholder='Custom Enhancer Drops'>
                        </div>
                    </div>
                        <div class='col-sm-6 text-center'>
                        <div class='form-group'>
                            <label for='productBrand'>Brand*</label>
                            <input type='text' class='form-control' id='productBrand' placeholder='Cover FX'>
                        </div>
                    </div>
                    <div class='col-sm-6 text-center'>
                        <div class='form-group'>
                            <label for='productShades'>Shades</label>
                            <input type='text' class='form-control' id='productShades' placeholder='Halo, Celestial, Moonlight'>
                        </div>
                    </div>
                        <div class='col-sm-6 text-center'>
                        <div class='form-group'>
                            <label for='productType'>Type*</label>
                            <select class='form-control' id='productType'>
                            <?php
                                $conn = sqlConnect();
                                $sql = "SELECT DISTINCT product_type
                                        FROM products
                                        ORDER BY product_type ASC;";
                                $result = mysqli_query($conn,$sql);
                                while($row = mysqli_fetch_assoc($result)) {
                                    $types[] = $row["product_type"];
                                }
                                foreach($types as $t) echo "<option value='$t'>$t</option>";
                            ?>
                            </select>
                        </div>
                    </div>
                    <div class='col-sm-6 text-center'>
                        <div class='form-group'>
                            <label for='productSite'>Shop for this at</label>
                            <input type='text' class='form-control' id='productSite' placeholder='https://www.sephora.com/product/custom-enhancer-drops-P409765'>
                        </div>
                    </div>
                    <div class='col-sm-6 text-center'>
                        <div class='form-group'>
                            <label for='productPrice'>Price at Site</label>
                            <input type='text' class='form-control' id='productPrice' placeholder='$42.00 USD'>
                        </div>
                    </div>
                    <div class='col-sm-6 text-center'>
                        <div class='form-group'>
                            <label for='productRating'>Rating<br><i class='fa fa-star-o' id='star1'></i><i class='fa fa-star-o' id='star2'></i><i class='fa fa-star-o' id='star3'></i><i class='fa fa-star-o' id='star4'></i><i class='fa fa-star-o' id='star5'></i></label>
                            <input type='number' class='form-control' id='productRating' style='display:none;'>
                        </div>
                    </div>
                    <div class='col-sm-6 text-center'>
                        <div class='form-group'>
                            <button type='submit' class='btn btn-primary'>Submit New Product</button>
                        </div>
                    </div>
                </div>
                <!-- /.row -->
            </form>
        </div>
<?php
    loadFoot();
?>
