<?
$obElement = CIBlockElement::GetList(["SORT"=>"ASC"], ["IBLOCK_ID"=>27, "ACTIVE_DATE"=>"Y", "ACTIVE"=>"Y"], false, false, ["ID"]);
if ($arElement = $obElement->GetNext() ){
    if (!$_SESSION['medi_popup_once']){?>    <div id="medi_popup_once" data-id="closed-salons" data-src="/ajax/salon/?action=alert" data-title="" data-enable="Y"></div>
    <script>
    $(function(){
    	if ($('#medi_popup_once').length) {
    		if ($('#medi_popup_once').data("enable") == "Y"){
    			var $this = $('#medi_popup_once').addClass("loading");
    	        var popupID = $this.data("id");
    	        var popupTitle = $this.data("title");
    	        var popupSrc = $this.data("src");
    	        var popupAction = $this.data("action");
    			if (popupSrc)
    	        {
    	            if (popupID > 0) popupSrc = popupSrc + '&p='+popupID;
    	            $.ajax({
    	                url: popupSrc,
    	                success:  function(data) {
    	                    $popupHtml  = '<div class="medi_popup onload_alert" ><div class="medi_popup_wrap"><div class="medi_popup_Container"><div class ="medi_popup_ContentBox"><div class="medi_popup_Heading">'+ popupTitle +' <a href="#" class="medi_popup_Exit black"></a></div>' +
    	                  '<div class="medi_popup_Content">'+data+'</div>' + '</div></div></div></div>';
    	                    $(".medi_popup").remove();
    	                    $("body").append($popupHtml);
    	                    $this.removeClass("loading");
    	                    medi_popup_Open = true;
    	                }
    	            });
    	        }
    		}
            BX.setCookie('medi_popup_once', true, {expires: 86400});
    	}
    	$(document).on("click", ".medi_popup .popup_Exit", function(event){
    		$(".medi_popup").remove();
    		medi_popup_Open = false;
    		return event.preventDefault();
    	});
    });
    </script>
    <?
    $_SESSION['medi_popup_once'] = true;
    }
}?>