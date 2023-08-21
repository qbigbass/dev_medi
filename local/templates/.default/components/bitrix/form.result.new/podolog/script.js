function focusClick() {
    $('.tooltip-text-hover').each(function(){
        $(this).attr('style', 'opacity:0;transition:0.3s;');
    });
}
function focusOutClick() {

    $('.tooltip-text-hover').each(function(){
        $(this).attr('style', '');
    });
}
function closeTooltip() {
    $(".tooltip").blur();
}
$(function(){

	if ($('.tooltip').length){

		$('#bg-layer-for-tooltip').hide();
		$('.tooltip').focus(function(){
		  $('#bg-layer-for-tooltip').fadeIn("300");
		});
		$('.tooltip').focusout(function(){
		  $('#bg-layer-for-tooltip').fadeOut("300");
		});

	}
});
