$(document).ready(function(){
    $(".top_alert  .close").on("click", function(){
        $(".top_alert").slideUp();
            $.ajax({
                url: '/ajax/salon/?action=hide_alert',
                success:  function(data) {

                }
            });
    });
});
