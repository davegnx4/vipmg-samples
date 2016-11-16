$(document).ready(function() {

		//open qty popup
		$(".qtyEdit").click(this, function() {
			currOrder.setRow($(this).parent().parent());
			$("#qtyPopup").show();
			$("#overlay").show();
		});

        //change quantity
        $(".square-button").click(this, function() {
        	var qty = $(this).html();
			currOrder.storeQuantity(qty);
			$("#qtyPopup").hide();
			$("#overlay").hide();
        });
        
        $('#qty-select').on('change', function() {
			var qty = this.value;
			if (qty != "+") {
				currOrder.storeQuantity(qty);
				$("#qtyPopup").hide();
				$("#overlay").hide();
			}
        });

        $('#ship-select').on('change', function() {
			currOrder.updateShipType(this.value);
		});

});

var currOrder = (function() {

	var row;
	
	return {

		setRow: function(row) {
			currOrder.row = row;
			console.log(row);
		},
		
		refreshTotals: function() {
			$.ajax({
				url: '/Handler/order_handler.php',
				data: { 'action' : 'get-totals' },
				type: 'POST',
				success: function(data) {
					console.log(data);
					$("#totals").html(data);
				}
			});
		},

		openQtyPopup: function(sku) {
			console.log(this);
			currOrder.setSku(sku);
			currOrder.setRow();
			$("#qtyPopup").show();
			$("#overlay").show();
		},

		closeQtyPopup: function() {
			$("#qtyPopup").hide();
			$("#overlay").hide();
		},

		storeQuantity: function(qty) {
			var sku = $(currOrder.row).find('td:eq(1)').text();
			$.ajax({
				url: '/Handler/order_handler.php',
				data: { 'action' : 'store-quantity', 'qty' : qty, 'sku' : sku },
				type: 'POST',
				success: function(data) {
					console.log(data);
					currOrder.refreshRow(sku);
					currOrder.refreshTotals();
					currOrder.getShipType();
				}
			}); 
		},

		refreshRow: function(sku) {
			$.ajax({
				url: '/Handler/order_handler.php',
				data: { 'action' : 'get-single-row', 'sku' : sku },
				type: 'POST',
				success: function(data) {
					console.log(data);
			
					$(currOrder.row).html(data);

					$(".qtyEdit").click(this, function() {
						currOrder.setRow($(this).parent().parent());
						$("#qtyPopup").show();
						$("#overlay").show();
					});
				}
			}); 
		},

		refreshAllRows: function() {
			$.ajax({
				url: '/Handler/order_handler.php',
				data: { 'action' : 'display-single-order', 'clean' : false },
				type: 'POST',
				success: function(data) {
					console.log(data);
					$("#single-order").html(data);
					$(".qtyEdit").click(this, function() {
						currOrder.setRow($(this).parent().parent());
						$("#qtyPopup").show();
						$("#overlay").show();
					});
				}
			});
		},

		deleteRow: function(t, sku) {
			if (confirm("Are you sure you want to delete this row?")) {
				$.ajax({
					url: '/Handler/order_handler.php',
					data: { 'action' : 'delete-row', 'sku' : sku },
					type: 'POST',
					success: function(data) {
						console.log(data);
						$(t).parent().parent().remove();
						currOrder.refreshAllRows();
						currOrder.refreshTotals();
						currOrder.getShipType();
					}
				});
			}        
		},

		deleteOrder: function() {
			if (confirm("Are you sure you want to delete this order?")) {
				$.ajax({
					url : '/Handler/order_handler.php',
					data: { 'action' : 'delete-order'},
					type: 'POST',
					success : function(data) {
						console.log(data);
						window.location.assign("orders.php");
					}
				});
			}
		},

		getShipType: function() {
			$.ajax({
				url : '/Handler/order_handler.php',
				type : 'POST',
				data: { 'action' : 'display-ship-type' },
				success : function(data) {
					console.log(data);
					document.getElementById("ship-type-wrapper").innerHTML = data;
					//bind change function to new select input
					$('#ship-select').bind('change', function(){
						currOrder.updateShipType(this.value);
					});
				}
			});
		},
		
		updateShipType: function(type) {
			$.ajax({
				url : '/Handler/order_handler.php',
				type : 'POST',
				data : { 'action' : 'update-ship-type', 'type' : type, 'clean' : 'false' },
				success : function(data) {
					console.log(data);
					currOrder.refreshAllRows();
					currOrder.refreshTotals();
					currOrder.getShipType();
				}
			});
		}
		
		
	};
	
})();




