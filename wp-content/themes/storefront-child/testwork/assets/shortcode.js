jQuery(document).ready(function($){
    console.log('Shortcode JS for Testwork is loaded');

    $('#tw-citysearch-submit').on('click', function(){
        tw_loading();
        
        let data = {
            wpnonce: $('#tw_wpnonce').val(),
            s: $('#tw-searchinput').val(),
        }


        // $.ajax({
            
        // });

    });
});

function tw_loading(){
    let jq = jQuery;
    if(jq('.tw-citytablelist .tw-loading').length == 0) {
        jq('.tw-citytablelist').append('<div class="tw-loading"><span><i class="fa-solid fa-circle-notch fa-spin fa-2xl"></i></span></div>');
    }else{
        jq('.tw-citytablelist .tw-loading').remove();
    }
    
}