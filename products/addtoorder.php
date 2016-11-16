<?php
    include $_SERVER['DOCUMENT_ROOT'] . "/Controller/product_controller.php";
    include $_SERVER['DOCUMENT_ROOT'] . "/Controller/user_controller.php";
    include $_SERVER['DOCUMENT_ROOT'] . "/Controller/order_controller.php";
    $productController = new productController;
    $userController = new userController;
    $orderController = new orderController;
	$_SESSION['sku'] = $_GET['sku'];
    $userController->require_log_in();
?>

<!DOCTYPE html>
<html>

<head>
	<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
	<title>Add to Order</title>
	<link rel=StyleSheet href="/CSS/vipmusicgear.css" type="text/css">
	<link rel=StyleSheet href="/CSS/add.css" type="text/css">
    <script type="text/javascript" src="/JS/jquery-1.12.3.js"></script>
	<script src="/JS/add.js"></script>
</head>

<body>
        <div class="container">

                <div id="overlay"></div>
        
            <aside id="qtyPopup">
                <h3>Select Quantity</h3>
                <div id="popup-inner">
                    <div id="qty-buttons">
                        <button class="square-button">1</button>
                        <button class="square-button">2</button>
                        <button class="square-button">3</button>
                        <button class="square-button">4</button>
                        <button class="square-button">5</button>
                        <button class="square-button">6</button>
                        <button class="square-button">7</button>
                        <button class="square-button">8</button>
                        <button class="square-button">9</button>
                        <select id="qty-select" value="other">
                                <option>+</option>
                                <?php $productController->get_options(); ?>
                        </select>
                    </div>
                    <div id="qty-cancel">   
                        <button class="med-button" onclick="currOrder.closeQtyPopup()" style="button">Cancel</button>
                    </div>
                </div>
            </aside>

                <div class="navbar">
                    <h2>vipMusicGear</h2>
                    <a href="/Products/product/productroom.php">Product Room</a>
                    <a href="/custaccount.php">Account</a>
                    <a href="/Orders/orders.php">Orders</a>
                    <a href="/Help/help.php">Help</a>
                    <div class="login">
                        <?php $userController->display_log_status(); ?>
                    </div>
                </div>
    
                <div class="content">
                    <div class="header">
                        <a href="productpage.php?sku=<?php echo $_GET['sku']; ?>"> <img src="/Icons/left164.png"> </a>
                        <h2>Add to Order</h2>       
                    </div>

                        <div class="inner-content grey-box">
                            <?php $productController->get_add_data(); ?>
                        </div>
        
                        <div id="open-orders">
                            <?php $orderController->get_open_orders(); ?>
                        </div>

                        <div id="new-order" class="black-box">
                            <button class="med-button" style="button">New Order</button>
                        </div>   

               </div> <!-- content -->

        </div> <!-- container -->

       <div class="footer">
            <div class="inner-footer">
                <div class="rights">
                    <p>&copy <?php echo date("Y"); ?> VIP Music Gear. All rights reserved.</p>
                </div>
                <div class="contact">
                    <a href="/contactus.php"><p>Contact Us</p></a>
                </div>
            </div>
        </div>

</body>
</html>
