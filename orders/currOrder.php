<?php
    include $_SERVER['DOCUMENT_ROOT'] . "/Controller/user_controller.php";
    include $_SERVER['DOCUMENT_ROOT'] . "/Controller/order_controller.php";
    include $_SERVER['DOCUMENT_ROOT'] . "/Controller/profile_controller.php";
    include $_SERVER['DOCUMENT_ROOT'] . "/Controller/contact_controller.php";
    $userController = new userController;
    $orderController = new orderController;
    $profileController = new profileController;
    $contactController = new contactController;
	$_SESSION['ordernum'] = $_GET['ordernum'];
    $userController->require_log_in();
?>

<!DOCTYPE html>
<html>

<head>
	<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
	<title>Draft Order</title>
	<link rel=StyleSheet href="/CSS/vipmusicgear.css" type="text/css">
	<link rel=StyleSheet href="/CSS/currOrder.css" type="text/css">
	<script type="text/javascript" src="/JS/jquery-1.12.3.js"></script>
	<script src="/JS/currOrder.js"></script>
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
							<?php $orderController->get_options(); ?>
					</select>
				</div>
				<div id="qty-cancel">	
					<button class="med-button" onclick="currOrder.closeQtyPopup()" style="button">Cancel</button>
				</div>
			</div>
		</aside>

        <div class="navbar">
            <h2>vipMusicGear</h2>
            <a href="/Products/productroom.php">Product Room</a>
            <a href="/custaccount.php">Account</a>
            <a href="/Orders/orders.php">Orders</a>
            <a href="/Help/help.php">Help</a>
            <div class="login">
                <?php $userController->display_log_status(); ?>
            </div>
        </div>

		<div class="content">
			<div class="title2">
                <div class="header">
                    <a href="/Products/productroom.php"> <img src="/Icons/left164.png"> </a>
                    <h2>Draft Order</h2>       
                </div>
            </div>

             <div id="order-wrapper">
            	<div class="black-box-top">
					<h4 id="order-number">Order Number: <?php echo $_GET['ordernum']; ?></h4>
				</div>
				<div class="black-box">
					<div id="single-order">
						<?php $orderController->display_single_order(); ?>
					</div>
					<div id="order-lower">
						<div id="cont-shopping">
							<a href="/Products/productroom.php">Continue Shopping</a>
						</div>
						<div id="ship-type">
							<h4>Shipping Service</h4>
							<div id="ship-type-wrapper">
								<?php $orderController->display_ship_type(); ?> 
							</div>
						</div>
						<div id="totals">
							<?php $orderController->get_totals($_GET['ordernum']); ?>
						</div>
					</div>
				</div>
			</div>

        	<div id="profiles">
				<div id="billing" class="outer-profile">
					<div class="black-box-top">
						<h4>Billing</h4>
					</div>
					<div class="profile black-box">
						<?php $profileController->display_single_profile("billing", false); ?> 
					</div>
				</div>

				<div id="shipping" class="outer-profile">
					<div class="black-box-top">
						<h4>Shipping</h4>
					</div>
					<div class="profile black-box"> 
						<?php $profileController->display_single_profile("shipping", false); ?> 
					</div>
				</div>
			</div>

			<div id="contacts">
				<div class="black-box-top">
					<h4>Contact Preferences</h4>
				</div>
				<div id="inner-contacts" class="black-box">
					<?php $contactController->display_single_contact(false); ?>
				</div>
			</div>

			<div id="outer-final">
				<div id="final" class="black-box">
					<div id="delete">
						<button class="med-button" style="button" onclick="currOrder.deleteOrder()">Delete Order</button>
					</div>
					<div id="checkout">
						<button class="med-button" style="button"> <a href="finalReview.php?ordernum=<?php echo $_GET['ordernum']; ?>">Continue to Checkout</a> </button>
					</div>
				</div>
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
