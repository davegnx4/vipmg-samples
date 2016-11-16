$(document).ready(function() {

	$('#new-form').on("submit", function(event) {
		event.preventDefault();
		profile.submitNew();
	});
	
	$('#edit-form').on("submit", function(event) {
		event.preventDefault();
		profile.submitEdit();
	});

	$("#back").click(function() {
		window.history.back();
	});

	jQuery(function($){
	   $("input[name=phonenum]").mask("(999) 999-9999",{placeholder:"  "});
	   $("input[name=edit-phonenum]").mask("(999) 999-9999",{placeholder:"  "});
	});
	
	$("#new-state").load("/Profiles/states.html");
	$("#edit-state").load("/Profiles/states.html"); 	
});

var profile = (function() {

	var id;
	var profileBox;

	return {

		setID: function(id) {
			console.log(id);
			profile.id = id;
		},

		getID: function() {
			console.log(profile.id);
			return profile.id;
		},

		setBox: function(box) {
			profile.profileBox = box;
		},

		getBox: function() {
			return profile.profileBox;
		},

		getAllProfiles: function() {
			$.ajax({
				url : '/Handler/profile_handler.php',
				type: 'POST',
				data: { 'formType': 'get-profiles' },
				success : function(data) {
					$("#inner-profiles").html(data);
				},
				error(data) {
					console.log(data);
				}
			});
		},

		submitNew: function() {
			if (profile.validateFields("#new-form")) {
				formData = $('#new-form').serializeArray();
				formData.push({ name: 'formType', value: "submitNew" });
				$.ajax({
					url: '/Handler/profile_handler.php',
					type: 'POST',
					data: formData,
					success: function(data) {
						newPopup.close();
						profile.clearRedBoxes('#new-form');
						$("#profiles").append(data);
					},
					error(data) {
						console.log(data);
					}

				});
			}
		},

		submitEdit: function() {
			if (profile.validateFields("#edit-form")) {
				var formData = $('#edit-form').serializeArray();

				var profileID = profile.getID();

				formData.push({ name: 'formType', value: "submitEdit" });
				formData.push({ name: 'profileID', value: profileID });

				$.ajax({
					url: '/Handler/profile_handler.php',
					type: 'POST',
					data: formData,
					success: function(data) {
						console.log(data);
						editPopup.close();
						profile.clearRedBoxes('#edit-form');
						var box = profile.getBox();
						$(box).replaceWith(data);
					},
					error(data) {
						console.log(data);
					}
				});
			}
		},

		validateFields:  function(formID){
			var valid = true;
			$(formID + ' *').filter(':input').each(function() {
				if (!$(this).val() && $(this).hasClass("required-field")) {
					$(this).addClass('red-box');
					$('.required').show();
					valid = false;
				} else {
					$(this).removeClass('red-box');
				}
			});
			return valid;
		},

		deleteProfile: function(id, t) {
			if (confirm("Are you sure you want to delete this profile?")) {
				$.ajax({
					url: '/Handler/profile_handler.php',
					type: 'POST',
					data: { 'profileID' : id, 'formType' : 'deleteProfile' },
					success: function(data) {
						console.log(data);
						$(t).parent().parent().remove();
					},
					error: function(data) {
						console.log(data);
					}
				});
			}
		},

		clearRedBoxes: function(formID) {
			$(formID + ' *').filter(':input').each(function() {
			   $(this).removeClass('red-box');
			});
			$(".required").hide();
		},

		selectProfile: function(profileID, ordernum) {
			$.ajax({
				url : '/Handler/profile_handler.php',
				type : 'POST',
				data : { 'formType' : 'selectProfile', 'profileID': profileID },
				success : function(data) {
					console.log(data);
					window.location.assign("../Orders/currOrder.php?ordernum="+ordernum);	
				},
				error : function(data) {
					console.log(data);
				}
			}); 
		}
	};

})();


var newPopup = (function() {

	return {

		open: function() {
			$("#billingPopup").show();
			$("#overlay").show();
		},

		close: function() {
			$("#billingPopup").hide();
			$("#overlay").hide();
			document.getElementById("new-form").reset();
			profile.clearRedBoxes("#new-form");
		}
	};

})();


var editPopup = (function() {

	return {

		open: function(id, t) {
			$("#editPopup").show();
			$("#overlay").show();

			profile.setID(id);

			var box = $(t).parent().parent();
			profile.setBox(box);

			$.ajax({
				url : '/Handler/profile_handler.php',
				type : 'POST',
				data : { 
					'profileID' : id,
					'formType': "getEdit",
				},
				success: function(data) {
					var obj = $.parseJSON(data);
					$('input[name=edit-company]').val(obj[2]);
					$('input[name=edit-firstname]').val(obj[3]);
					$('input[name=edit-lastname]').val(obj[4]);
					$('input[name=edit-address1]').val(obj[5]);
					$('input[name=edit-address2]').val(obj[6]);
					$('input[name=edit-city]').val(obj[7]);
					$('select[name=edit-state]').val(obj[8]);
					$('input[name=edit-zipcode]').val(obj[9]);
					$('input[name=edit-plus4]').val(obj[10]);
				},
				error: function(data) {
					console.log(data);
				}
			});
		},

		close: function() {
			$("#editPopup").hide();
			$("#overlay").hide();
			document.getElementById("edit-form").reset();
			profile.clearRedBoxes("#edit-form");
		}
	};

})();



