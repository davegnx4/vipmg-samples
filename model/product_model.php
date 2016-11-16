<?php

class productModel extends model {

	function display_all_products() {

		$st = $this->con->query("SELECT * FROM products");
		$products = $st->fetchAll(PDO::FETCH_ASSOC);
		if(!empty($products)) {

			// prepare image statement
			$img_st = $this->con->prepare("SELECT path FROM prodimages WHERE sku=?");

			foreach($products as $row) {

				$img_st->execute(array($row['sku']));
				$image = $img_st->fetch(PDO::FETCH_NUM)[0];

				include 'Templates/Products/item_box.php';
			}
		} else {
			echo "<h4>There are no products available at this time</h4>";
		}
	}

	function display_single_product($sku) {

		$sku = $this->con->quote($sku);

		$st = $this->con->query("SELECT * FROM products WHERE sku=$sku");
		$row = $st->fetch(PDO::FETCH_ASSOC);

		$st = $this->con->query("SELECT path FROM prodimages WHERE sku=$sku");
		$image = $st->fetch(PDO::FETCH_NUM)[0];

		include 'Templates/Products/product_content.php';
	}

	function display_images($sku) {

		$st = $this->con->prepare("SELECT path FROM prodimages WHERE sku=?");

		$st->execute(array($sku));
		$row = $st->fetchAll(PDO::FETCH_ASSOC);

		include 'Templates/Products/large_images.php';
	}

	function get_options() {
		for($i=10; $i<100; $i++) {
            echo "<option value='$i'>$i</option>";
        }  
	}

	function get_add_data($sku) {

		$sku = $this->con->quote($sku);

		$st = $this->con->query("SELECT * FROM products WHERE sku=$sku");
		$row = $st->fetch(PDO::FETCH_ASSOC);

		$st = $this->con->query("SELECT path FROM prodimages WHERE sku=$sku");
		$image = $st->fetch(PDO::FETCH_NUM)[0];

		include 'Templates/Products/add_product.php';
	}

}