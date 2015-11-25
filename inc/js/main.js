jQuery(function($){
	
	$.datepicker.setDefaults($.datepicker.regional['fr']);
	
	var datepickers = $('.datepicker').datepicker({
		dateFormat: 'yy-mm-dd', 
		minDate: 0,
		onSelect : function(date){
			var option = this.id == 'date_arrivee' ? 'minDate' : 'maxDate';
			datepickers.not('#' + this.id).datepicker('option',option,date);
		}
	})
});