;$(document).ready(function() {

	var THIS = null;

	$('[data-toggle="tooltip"]').tooltip();
	$('.table-fixed').fixedtableheader();
/*
	tinymce.init({
		selector:'.html'
	});
*/
	tinymce.init({
		selector: '.html',
		height: 300,
		theme: 'modern',
		plugins: 'print preview searchreplace autolink directionality visualblocks visualchars fullscreen image link media template codesample table charmap hr pagebreak nonbreaking anchor toc insertdatetime advlist lists textcolor wordcount imagetools contextmenu colorpicker textpattern help code',
		toolbar1: 'formatselect | bold italic strikethrough forecolor backcolor | link | alignleft aligncenter alignright alignjustify  | numlist bullist outdent indent  | removeformat | code',
		image_advtab: true
	});

	$(".datepicker").datepicker({
		dateFormat: "yy-mm-dd",
		firstDay: 1,
		changeMonth: true,
		changeYear: true,
		//yearRange: "1000:c"
	    //option: $.datepicker.regional['cz']
	});
	$(".datepicker-nofuture").datepicker({
		dateFormat: "yy-mm-dd",
		firstDay: 1,
		maxDate: 'today',
		changeMonth: true,
		changeYear: true,
		//yearRange: "1000:c"
	    //option: $.datepicker.regional['cz']
	});

    $('.form-group select').selectize({
        create: true,
        sortField: 'text'
    });

	$(document).on('keypress', '.filter input:text', function(e) {
		if(e.which == 13) {
			$(this).closest('form').submit();
		}
	});
	$(document).on('change', '.filter select, .filter .datepicker, .filter .datepicker-nofuture', function() {
		$(this).closest('form').submit();
	});
	$(document).on('change', '#perpage', function() {
		$(this).closest('form').submit();
	});

	$(document).on('click', '.delete', function() {
		THIS = $(this);
		$("#deleteItemModal a.btn-primary").attr('href', THIS.attr('data-href'));
		$("#deleteItemModal").modal("show");
	});

	$(document).on('click', '.results .item button', function() {
		THIS = $(this);
		THIS.closest('.item').remove();
	});

	$(".autocomplete").on("keydown", function( event ) {
		THIS = $(this);
		if(event.keyCode === $.ui.keyCode.TAB && $(this).autocomplete("instance").menu.active) {
			event.preventDefault();
		} else if(event.keyCode === 13 && !$(this).autocomplete("instance").menu.active) {
			THIS.next().append('<div class="item"><input type="hidden" name="'+THIS.attr('id')+'[]" value="0"><input type="hidden" name="'+THIS.attr('id')+'_name[]" value="'+THIS.val()+'"><button>'+THIS.val()+'</button></div>');
			THIS.val('');
			return false;
		}
	}).autocomplete({
		minLength: 1,
		source: function( request, response ) {
			response($.ui.autocomplete.filter(DATA[THIS.attr('id')], request.term));
		},
		focus: function() {
			return false;
		},
		select: function( event, ui ) {
			//var THIS = $(this);
			THIS.next().append('<div class="item"><input type="hidden" name="'+THIS.attr('id')+'[]" value="'+ui.item.value+'"><input type="hidden" name="'+THIS.attr('id')+'_name[]" value="'+ui.item.label+'"><button>'+ui.item.label+'</button></div>');
			THIS.val('');
			return false;
		}
	});
});
