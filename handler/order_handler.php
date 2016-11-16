<?php

require_once $_SERVER['DOCUMENT_ROOT'] . 'Controller/order_controller.php';

if(isset($_POST['action'])) {
	$action = $_POST['action'];
	$orderController = new orderController();

	if ($action == 'new-order') {
		$orderController->new_order();
	} elseif ($action == 'set-order') {
		$orderController->set_order();
	} elseif ($action == 'store-quantity') {
		$orderController->store_quantity();
	} elseif ($action == 'change-ship-type') {
		$orderController->change_ship_type();
	} elseif ($action == 'display-single-order') {
		$orderController->display_single_order();
	} elseif ($action == 'get-totals') {
		$orderController->get_totals();
	} elseif ($action == 'set-sku') {
		$orderController->set_sku();
	} elseif ($action == 'delete-row') {
		$orderController->delete_single_row();
	} elseif ($action == 'delete-order') {
		$orderController->delete_order();
	} elseif ($action == 'display-ship-type') {
		$orderController->display_ship_type();
	} elseif ($action == 'update-ship-type') {
		$orderController->update_ship_type();
	} elseif ($action == 'add-product') {
		$orderController->add_product();
	} elseif ($action == 'get-single-row') {
		$orderController->get_single_row();
	}
} else {
	echo "No action was detected.";
}