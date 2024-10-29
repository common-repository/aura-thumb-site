jQuery(document).ready(function($){
		$('.slider').on('keydown, change', function(){
			var val = this.value;
			$('#val_'+this.id).html(val);
			$('input[name="aura_thumb_site_img_'+this.id+'"]').val(val);
			$('input[name="aura_thumb_site_title_'+this.id+'"]').val(val);
		});	
	});
    jQuery(document).ready(function($){
    	$("#addRow").click(function () {
			var val = $('.url').val();
			var key = $('.row').length + 1;
			var html = '';
			html += '<tr><td>';
			html += '<div class="row">';
			html += key+' - '+ val+'<br>';
			html += '</div>';	
			html += '</td><tr>';
        	$('#newRow').append(html);
			$.ajax({
				type: 'POST',
				url: 'admin-ajax.php', 
				data: {action:'update_url', url:val},
			});				
		});
    });

    jQuery(document).ready(function($){
    	$('.remove').click(function () {
        	$(this).closest('div').remove();
			val = this.id;
			$.ajax({
				type: 'POST',
				url: 'admin-ajax.php', 
				data: {action:'update_url', url:val, remove:true},
			});				
		});	
    });