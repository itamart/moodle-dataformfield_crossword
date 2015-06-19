// A javascript-enhanced crossword puzzle [c] Jesse Weisbeck, MIT/GPL 
(function($) {
	$(function() {
        var puzzleData = $.parseJSON($('.puzzle-data').val());
		$('#puzzle-wrapper').crossword(puzzleData);		
	})
	
})(jQuery)
