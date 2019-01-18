$(function() {
	$('.alert').delay(5000).fadeOut('slow');

	$('#modalPHP').modal({
		keyboard: false,
		show: true
	});

	$('.selectpicker').selectpicker({
		style: 'btn-warning'
	});

	var updateForm = function() {
		if ( $('#pluginChoice').length <= 0 ) {
			return;
		}

		var id = parseInt($('#pluginChoice')[0].value);

		if (id > 0) {
			$.ajax({
				url: 'http://localhost:81/rattrapage/ajax/listPluginModif.php',
				type: 'POST',
				data: 'pluginId=' + id,
				dataType: 'html',
				success: function(dataString, statut) {
					var data = JSON.parse(dataString);

					$('#name_modif').val(data[0]['p_name']);
					$('#description_modif').val(data[0]['p_description']);
					$('#version_modif').val(data[0]['p_version']);
					$('#modif_modif').attr('name', 'plugin_' + id);

					$('#formShow').show();
					$('#formShowAdd').hide();
				},
				error: function(data, statut) {
					alert('error');
				}
			});
		} else if (id == -1) {
			$('#formShow').hide();
			$('#formShowAdd').hide();
		} else if (id == -2) {
			$('#formShow').hide();
			$('#formShowAdd').show();
		}
	};

	updateForm();

	$('#pluginChoice').change(function() {
		updateForm();		
	});
});

