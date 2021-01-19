$(function(){
	var abc = 0;
	$('body').on('click','#add_more',function(){
		$(this).before($("<div/>", {
			id: 'filediv',
			class:'',
			style:'margin-top:5px;margin-bottom:5px'
		}).fadeIn('slow').append($("<input/>", {
			name: 'file_title[]',
			type: 'text',
			placeholder: 'Title',
			style: 'margin-top:10px;',
			id: 'file_title',
			class:'form-control'
		}), $("")).fadeIn('slow').append($("<input/>", {
			name: 'file_description[]',
			type: 'text',
			placeholder: 'Description',
			style: 'margin-top:10px;',
			id: 'file_description',
			class:'form-control'
		}), $("")).fadeIn('slow').append($("<input/>", {
			name: 'file[]',
			type: 'file',
			style: 'margin-top:10px;',
			id: 'file',
			class:'form-control'
		}), $("")));
	});
	$('body').on('change', '#file', function() {
		if (this.files && this.files[0]) {
			abc += 1; // Incrementing global variable by 1.
			var z = abc - 1;
			var x = $(this).parent().find('#previewimg' + z).remove();
			$(this).before("<div id='abcd" + abc + "' class='thumbnail abcd'><img style='height:200px;' id='previewimg" + abc + "' src=''/></div>");
			var reader = new FileReader();
			reader.onload = imageIsLoaded;
			reader.readAsDataURL(this.files[0]);
			$(this).hide();
			$("#abcd" + abc).append($("<button/>", {
				id: 'img',
				class:'btn btn-danger btn-sm',
				html:"<fa class='fa fa-close'></fa>",
				style:'',
				alt: 'delete'

			}).click(function() {
				$(this).parent().parent().remove();
			}));
		}
	});
	function imageIsLoaded(e) {
		$('#previewimg' + abc).attr('src', e.target.result);
	}
	$('#upload').click(function(e) {
		var name = $(":file").val();
		if (!name) {
			alert("First Image Must Be Selected");
			e.preventDefault();
		}
	});
});

