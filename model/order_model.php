<?php

class orderModel extends model {


	function get_open_orders($sku, $acctnum) {

		$st = $this->con->prepare("SELECT * FROM orders WHERE acctnum = ? AND status = 'open'");
		$st->execute(array($acctnum));

		$orders = $st->fetchAll(PDO::FETCH_ASSOC);

		//prepare statements
		$order_det_statement = $this->con->prepare("SELECT * FROM orderdetails WHERE ordernum = ?");
		$image_statement = $this->con->prepare("SELECT path FROM prodimages WHERE sku=?");

		foreach ($orders as $row) {

			include 'Templates/Orders/Open_Orders/opening_tags.php';

			$order_det_statement->execute(array($row['ordernum']));
			$order_details = $order_det_statement->fetchAll(PDO::FETCH_ASSOC);

			if (count($order_details) > 0) {

				include 'Templates/Orders/Open_Orders/table_headers.php';

				foreach ($order_details as $row) {

					$image_statement->execute(array($row['sku']));
					$image = $image_statement->fetch(PDO::FETCH_NUM)[0];

					include 'Templates/Orders/Open_Orders/table_data.php';
				}
				include 'Templates/Orders/Open_Orders/add_button.php';
			} else {
				include 'Templates/Orders/Open_Order/empty.php';
			}       

			//closing tags
			?>
					</div> <!-- prod-dets -->
				</div> <!-- order-inner -->
			<?php
		}
	}


	function add_product($sku, $ordernum, $qty) {

		//get details on current product
		$st = $this->con->prepare("SELECT * FROM products WHERE sku = ?");
		$st->execute(array($sku));
		$prod_dets = $st->fetch(PDO::FETCH_ASSOC);

		//check if item has already been added to the order
		$st = $this->con->prepare("SELECT * FROM orderdetails WHERE sku=? AND ordernum=?");
		$st->execute(array($sku, $ordernum));
		$curr_qty = $st->fetch(PDO::FETCH_ASSOC)['quantity'];

		if (count($curr_qty) > 0) {
			//udate sku with new quantity
			$new_qty = $curr_qty + $qty;
			$st = $this->con->prepare("UPDATE orderdetails SET quantity = $new_qty WHERE ordernum = ? AND sku = ?");
			$st->execute(array($ordernum, $sku));
		} else {
			//insert product
			$st = $this->con->prepare("INSERT INTO orderdetails (ordernum, sku, title, price, quantity, ground, twoday) VALUES (?, ?, ?, ?, ?, ?, ?)");
			$st->execute(array($ordernum, $sku, $prod_dets['title'], $prod_dets['price'], $qty, $prod_dets['ground'], $prod_dets['twoday']));
		}

		$this->calc_line_totals($ordernum, $sku);
		$this->calc_order_totals($ordernum);

		echo $ordernum;
	}


	function new_order($acctNum, $qty, $sku) {

		//calculating new order number
		$st = $this->con->prepare("SELECT MAX(ordernum) FROM orders WHERE acctnum = $acctNum");
		$st->execute();
		$max_order_num = $st->fetch(PDO::FETCH_NUM)[0];

		if ($max_order_num  == 0) {
			$ordernum = 100100;
		} else {
			$ordernum = ++$max_order_num ;
		}
		
		//save order to database
		$st = $this->con->prepare("INSERT INTO orders (ordernum, acctnum, status, opendate, shiptype) VALUES (?, $acctNum, 'open', (now()), 'ground')");
		$st->execute(array($ordernum));

		//get product details
		$st = $this->con->prepare("SELECT * FROM products WHERE sku = ?");
		$st->execute(array($sku));
		$product = $st->fetch(PDO::FETCH_ASSOC);

		// insert product into orderdetails
		$st = $this->con->prepare("INSERT INTO orderdetails (ordernum, sku, title, price, quantity, ground, twoday) VALUES (?, ?, ?, ?, ?, ?, ?)");
		$st->execute(array($ordernum, $sku, $product['title'], $product['price'], $qty, $product['ground'], $product['twoday']));

		$this->calc_line_totals($ordernum, $sku);
		$this->calc_order_totals($ordernum);

		echo $ordernum;
	}


	function display_single_order($ordernum, $clean=false) {

		//get data from order table
		$st = $this->con->prepare("SELECT * FROM orders WHERE ordernum = ?");
		$st->execute(array($ordernum));
		$order = $st->fetch(PDO::FETCH_ASSOC);

		//get order details
		$st = $this->con->prepare("SELECT * FROM orderdetails WHERE ordernum = ?");
		$st->execute(array($ordernum));
		$order_dets = $st->fetchAll(PDO::FETCH_ASSOC);
		
		if (count($order_dets) > 0) {

			include 'Templates/Orders/Current_Order/table_headers.php';

			//prepare statement for image sql
			$img_st = $this->con->prepare("SELECT path FROM prodimages WHERE sku=?");

			foreach ($order_dets as $row) {

				//get product image
				$img_st->execute(array($row['sku']));
				$image = $img_st->fetch(PDO::FETCH_NUM)[0];

				if ($order['shiptype'] == "ground") {
					$shipping = $row['ground'];
				} elseif ($order['shiptype'] == "twoday") {
					$shipping = $row['twoday'];
				} else {
					echo "Error getting shipping";
				}
				

				include 'Templates/Orders/Current_Order/table_data.php';

			}

			echo "</table>";

		} else { 
			include 'Templates/Orders/Current_Order/empty.php';
		}

	}


	function display_totals($ordernum) {

		//get data from order table
		$st = $this->con->prepare("SELECT * FROM orders WHERE ordernum = ?");
		$st->execute(array($ordernum));
		$order = $st->fetch(PDO::FETCH_ASSOC);

		if ($order['shiptype'] == "ground") {
			$ship_total = $order['ground'];
		} elseif ($order['shiptype'] == "twoday") {
			$ship_total = $order['twoday'];
		}

		include 'Templates/Orders/Current_Order/totals.php';

	}


	function get_options() {
		for($i=10; $i<100; $i++) {
			echo "<option value='$i'>$i</option>";
		}   
	}


	function delete_order($ordernum) {
		$ordernum = $this->con->quote($ordernum);
		$this->con->query("DELETE FROM orders WHERE ordernum = $ordernum");
		$this->con->query("DELETE FROM orderdetails WHERE ordernum = $ordernum");
	}


	function display_ship_type($ordernum) {
		
		$ship_type = $this->get_ship_type($ordernum);
		
		$ground = 0.00;
		$twoday = 0.00;
		
		$st = $this->con->prepare("SELECT * FROM orders WHERE ordernum = ?");
		$st->execute(array($ordernum));
		$order = $st->fetch(PDO::FETCH_ASSOC);
		
		if ($ship_type == "ground") { 
			include 'Templates/Orders/Current_Order/ground.php';
		} else if($ship_type == "twoday") {
			include 'Templates/Orders/Current_Order/twoday.php';
		}
	}


	function store_quantity($ordernum, $qty, $sku) {

		$st = $this->con->prepare("UPDATE orderdetails SET quantity = ? WHERE ordernum = ? AND sku = ?");
		$st->execute(array($qty, $ordernum, $sku));

		$this->calc_line_totals($ordernum, $sku);
		$this->calc_order_totals($ordernum);

	}

	function calc_line_totals($ordernum, $sku) {

		$ship_type = $this->get_ship_type($ordernum);

		//get product details
		$st = $this->con->prepare("SELECT * FROM orderdetails WHERE ordernum = ? AND sku = ?");
		$st->execute(array($ordernum, $sku));
		$row = $st->fetch(PDO::FETCH_ASSOC);

		//calculate totals
		if ($ship_type == "ground") {
			$ship_total = $row['quantity'] * $row['ground'];
		} elseif ($ship_type == "twoday") {
			$ship_total = $row['quantity'] * $row['twoday'];
		}

		$product_total = $row['quantity'] * $row['price'];

		$line_total = $ship_total + $product_total;

		$st = $this->con->prepare("UPDATE orderdetails SET linetotal = $line_total WHERE ordernum = ? AND sku = ?");
		$st->execute(array($ordernum, $sku));

	}

	function calc_all_line_totals($ordernum) {

		$ship_type = $this->get_ship_type($ordernum);

		//get product details
		$st = $this->con->prepare("SELECT * FROM orderdetails WHERE ordernum = ?");
		$st->execute(array($ordernum));
		$order_dets = $st->fetchAll(PDO::FETCH_ASSOC);

		foreach ($order_dets as $row) {

			//calculate totals
			if ($ship_type == "ground") {
				$ship_total = ($row['quantity'] * $row['ground']);
			} elseif ($ship_type == "twoday") {
				$ship_total = ($row['quantity'] * $row['twoday']);
			}

			$product_total = ($row['quantity'] * $row['price']);

			$line_total = $ship_total + $product_total;

			$st = $this->con->prepare("UPDATE orderdetails SET linetotal = $line_total WHERE ordernum = ? AND sku = ?");
			$st->execute(array($ordernum, $row['sku']));

		}

	}


	function calc_order_totals($ordernum) {

		$ship_type = $this->get_ship_type($ordernum);

		//get order details
		$st = $this->con->prepare("SELECT * FROM orderdetails WHERE ordernum = ?");
		$st->execute(array($ordernum));
		$order_dets = $st->fetchAll(PDO::FETCH_ASSOC);

		//calculate the totals
		$ground = 0.00;
		$twoday = 0.00;
		$subtotal = 0.00;

		foreach ($order_dets as $row) {
			$ground += ($row['ground'] * $row['quantity']);
			$twoday += ($row['twoday'] * $row['quantity']);
			$subtotal += ($row['price'] * $row['quantity']);
		}

		if ($ship_type == "ground") {
			$total = $subtotal + $ground;
		} elseif ($ship_type == "twoday") {
			$total = $subtotal + $twoday;
		}

		//store totals
		$st = $this->con->prepare("UPDATE orders SET ground = $ground, twoday = $twoday, subtotal = $subtotal, total = $total WHERE ordernum = ?");
		$st->execute(array($ordernum));

	}


	function get_order_totals($ordernum) {

		$st = $this->con->prepare("SELECT * FROM orders WHERE ordernum = ?");
		$st->execute(array($ordernum));
		$row = $st->fetch(PDO::FETCH_ASSOC);
		echo json_encode($row);
	}

	function get_single_row($ordernum, $sku, $clean) {

		$shipType = $this->get_ship_type($ordernum);

		//get image
		$img_st = $this->con->prepare("SELECT path FROM prodimages WHERE sku=?");
		$img_st->execute(array($sku));
		$image = $img_st->fetch(PDO::FETCH_NUM)[0];

		$st = $this->con->prepare("SELECT * FROM orderdetails WHERE ordernum = ? AND sku = ?");
		$st->execute(array($ordernum, $sku));
		$row = $st->fetch(PDO::FETCH_ASSOC);

		if ($shipType == "ground") {
			$ship_total = $row['ground'];
		} elseif ($shipType == "twoday") {
			$ship_total = $row['twoday'];
		} else {
			echo "There was an error getting the ship type.";
		}

		include 'Templates/Orders/Current_Order/single_line.php';
	}

	function get_ship_type($ordernum) {

		$st = $this->con->prepare("SELECT shiptype FROM orders WHERE ordernum = ?");
		$st->execute(array($ordernum));
		return $st->fetch(PDO::FETCH_NUM)[0];
	}

	function update_ship_type($ordernum, $type) {

		$st = $this->con->prepare("UPDATE orders SET shiptype='$type' WHERE ordernum = ?");
		$st->execute(array($ordernum));

		$this->calc_all_line_totals($ordernum);
		$this->calc_order_totals($ordernum);
	}

	function delete_single_row($ordernum, $sku) {
		$st = $this->con->prepare("DELETE from orderdetails WHERE ordernum = ? AND sku = ?");
		$st->execute(array($ordernum, $sku));
		$this->calc_all_line_totals($ordernum);
		$this->calc_order_totals($ordernum);
	}

	function display_all_orders($acctnum) {
		$st = $this->con->prepare("SELECT * FROM orders WHERE acctnum = ? AND status = 'open'");
		$st->execute(array($acctnum));
		$orders = $st->fetchAll();
		if($orders[0]['status'] == 'open') {
			include 'Templates/Orders/All_Orders/draft_table_header.php';
			foreach ($orders as $row) {
				include 'Templates/Orders/All_Orders/draft_table_data.php';
			}
			echo "</table>";
		}
		if ($orders[0]['status'] == 'closed') {
			include 'Templates/Orders/All_Orders/closed_table_header.php';
			foreach ($orders as $row) {
				include 'Templates/Orders/All_Orders/closed_table_data.php';
			}
			echo "</table>";
		}
	}

}