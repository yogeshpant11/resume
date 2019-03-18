jQuery(document).ready(function(){
	jQuery("#author_list").change(function() {
	    var id = jQuery(this).val();

        if(id!='') {
			jQuery.ajax({
				url: ajaxobject.ajaxurl,
				method: 'post',
				dataType: 'json',
				data: {
					action: 'filter_post',
					id : id,
					nonce: ajaxobject.nonce
				},

				beforeSend: function() {
						jQuery("#author-container").html("loading...");
				},

			    success: function(results) {
			    	if(results.response == 'success') {
				    	jQuery("#author-container").html(results.data);
				    }
			    },

			    error: function() {
			            console.log('Cannot retrieve data.');
			    }
			});
		}

		else {
			jQuery("#author-container").html('');
		}
		
	});
});