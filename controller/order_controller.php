<?php

require_once 'controller.php';

class orderController extends controller {

	function __construct() {
		parent::__construct();
		require_once $_SERVER['DOCUMENT_ROOT'] . '/Model/order_model.php';
		$this->orderModel = new orderModel;
	}

	function new_order() {
		$acctNum = $_SESSION['loggedIn']['acctnum'];
		$qty = $_POST['qty'];
		$sku = $_SESSION["sku"];
		$this->orderModel->new_order($acctNum, $qty, $sku);
	}

	function get_open_orders() {
		$sku = $_GET['sku'];
		$acctnum = $_SESSION['loggedIn']['acctnum'];
		$this->orderModel->get_open_orders($sku, $acctnum);
	}

	function add_product() {
		$sku = $_SESSION['sku'];
		$ordernum = $_POST['ordernum'];
		$qty = $_POST['qty'];
		$this->orderModel->add_product($sku, $ordernum, $qty);
	}

	function get_options() {
		$this->orderModel->get_options();
	}

	function display_single_order($clean=false) {
		$ordernum = $_SESSION['ordernum'];
		$this->orderModel->display_single_order($ordernum, $clean);
	}

	function delete_order() {
		$ordernum = $_SESSION['ordernum'];
		$this->orderModel->delete_order($ordernum);
	}

	function display_ship_type() {
		$ordernum = $_SESSION['ordernum'];
		$this->orderModel->display_ship_type($ordernum);
	}

	function store_quantity() {
		$ordernum = $_SESSION['ordernum'];
		$qty = $_POST['qty'];
		$sku = $_POST['sku'];
		$this->orderModel->store_quantity($ordernum, $qty, $sku);
	}

	function get_totals() {
		$ordernum = $_SESSION['ordernum'];
		$this->orderModel->display_totals($ordernum);
	}

	function get_single_row() {
		$ordernum = $_SESSION['ordernum'];
		$sku = $_POST['sku'];
		$this->orderModel->get_single_row($ordernum, $sku, false);
	}

	function update_ship_type() {
		$ordernum = $_SESSION['ordernum'];
		$type = $_POST['type'];
		$this->orderModel->update_ship_type($ordernum, $type);
	}

	function delete_single_row() {
		$ordernum = $_SESSION['ordernum'];
		$sku = $_POST['sku'];
		$this->orderModel->delete_single_row($ordernum, $sku);
	}

	function display_all_orders() {
		$acctnum = $_SESSION['loggedIn']['acctnum'];
		$this->orderModel->display_all_orders($acctnum);
	}

}