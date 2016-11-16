<?php
    include $_SERVER['DOCUMENT_ROOT'] . "/Controller/user_controller.php";
    include $_SERVER['DOCUMENT_ROOT'] . "/Controller/profile_controller.php";
    $userController = new userController;
    $profileController = new profileController;
	$_SESSION['ordernum'] = $_GET['ordernum'];
	$_SESSION['profileType'] = $_GET['profileType'];
    $userController->require_log_in();
?>

<!DOCTYPE html>
<html>

<head>
	<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
	<title>Profiles</title>
	<link rel=StyleSheet href="/CSS/vipmusicgear.css" type="text/css">
	<link rel=StyleSheet href="/CSS/profiles.css" type="text/css">
	<script type="text/javascript" src="/JS/jquery-1.12.3.js"></script>
	<script src="/JS/maskedinput.js" type="text/javascript"></script>
	<script src="/JS/profiles.js"></script>
</head>

<body>
	<div class="container">

		<div id="overlay"></div>

		<aside id="billingPopup">
			<div id="inner-popup">
				<form id="new-form">
					<h2>New Profile</h2>
					<div class="required-message">
						<h4>Required fields *</h4>
					</div>
					<div class="error-box">
							<div class="required"> <h4>Please enter the required fields</h4> </div>
					</div>
					<div class="popup-content">
						<table>
							<tr>
								<td class="go-right"><h4>Company:</h4></td>
								<td><input type="text" size="20" maxlength="20" name="company"/></td>
							</tr>
							<tr>
								<td class="go-right"><h4>First Name:</h4><h4 class="red-star">*</h4></td>
								<td><input class="required-field" type="text" size="20" maxlength="20" name="firstname"/></td>
							</tr>
							<tr>
								<td class="go-right"><h4>Last Name:</h4><h4 class="red-star">*</h4></td>
								<td><input class="required-field" type="text" size="20"  maxlength="20" name="lastname"/></td>
							</tr>
							<tr>
								<td class="go-right"><h4>Address1:</h4><h4 class="red-star">*</h4></td>
								<td><input class="required-field" type="text" size="40"  maxlength="40" name="address1"/></td>
							</tr>
							<tr>
								<td class="go-right"><h4>Address2:</h4></td>
								<td><input type="text" size="40"  maxlength="40" name="address2"/></td>
							</tr>
							<tr>
								<td class="go-right"><h4>City:</h4><h4 class="red-star">*</h4></td>
								<td><input class="required-field" type="text" size="20"  maxlength="20" name="city"/></td>
							</tr>
							<tr>
								<td class="go-right"><h4>State:</h4><h4 class="red-star">*</h4></td>
								<td><select class="required-field" id="new-state" name="state"></select></td>
							</tr>
							<tr>
								<td class="go-right"><h4>Zipcode:</h4><h4 class="red-star">*</h4></td>
								<td><input class="required-field" type="text" size="5" maxlength="5" name="zipcode"/><input type="text" size="4" maxlength="4" name="plus4"/></td>
							</tr>
						</table>
					</div>
					<div id="bottom">
							<input class="med-button" type="submit" value="Submit"/>
							<button class="med-button" type="button" onclick="newPopup.close()">Cancel</button>
					</div>
				</form>
			</div>
		</aside> <!-- Popup -->

		<aside id="editPopup">
			<div id="inner-popup">
				<form id="edit-form">
					<h2>Edit Profile</h2>
					<div class="required-message">
						<h4>Required fields *</h4>
					</div>
					<div class="error-box">
							<div class="required"> <h4>Please enter the required fields</h4> </div>
					</div>
					<div class="popup-content">
						<table>
							<tr>
								<td class="go-right"><h4>Company:</h4></td>
								<td><input type="text" size="20" maxlength="20" name="edit-company"/></td>
							</tr>
							<tr>
								<td class="go-right"><h4>First Name:</h4><h4 class="red-star">*</h4></td>
								<td><input class="required-field" type="text" size="20" maxlength="20" name="edit-firstname"/></td>
							</tr>
							<tr>
								<td class="go-right"><h4>Last Name:</h4><h4 class="red-star">*</h4></td>
								<td><input class="required-field" type="text" size="20"  maxlength="20" name="edit-lastname"/></td>
							</tr>
							<tr>
								<td class="go-right"><h4>Address1:</h4><h4 class="red-star">*</h4></td>
								<td><input class="required-field" type="text" size="40"  maxlength="40" name="edit-address1"/></td>
							</tr>
							<tr>
								<td class="go-right"><h4>Address2:</h4></td>
								<td><input type="text" size="40"  maxlength="40" name="edit-address2"/></td>
							</tr>
							<tr>
								<td class="go-right"><h4>City:</h4><h4 class="red-star">*</h4></td>
								<td><input class="required-field" type="text" size="20"  maxlength="20" name="edit-city"/></td>
							</tr>
							<tr>
								<td class="go-right"><h4>State:</h4><h4 class="red-star">*</h4></td>
								<td><select class="required-field" id="edit-state" name="edit-state"></select></td>
							</tr>
							<tr>
								<td class="go-right"><h4>Zipcode:</h4><h4 class="red-star">*</h4></td>
								<td><input class="required-field" type="text" size="5" maxlength="5" name="edit-zipcode"/><input type="text" size="4" maxlength="4" name="edit-plus4"/></td>
							</tr>
						</table>
					</div>
					<div id="bottom">
							<input class="med-button" type="submit" value="Submit"/>
							<button class="med-button" type="button" onclick="editPopup.close()">Cancel</button>
					</div>
				</form>
			</div>
		</aside> <!-- Popup -->

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

            <div class="header">
                <a href="/Orders/currOrder.php?ordernum=<?php echo $_GET['ordernum']; ?>"> <img src="/Icons/left164.png"> </a>
                <h2>Profiles</h2>       
            </div>

			<div id="profiles" class="grey-box">
				<?php $profileController->display_all_profiles(); ?>
			</div>

			<div id="new-profile" class="grey-box">
				<button class="med-button" style="button" onclick="newPopup.open()">Create New Profile</button>
			</div>

	   </div> <!-- content -->

	</div> <!-- container-->

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
