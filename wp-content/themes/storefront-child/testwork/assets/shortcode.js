jQuery(document).ready(function($){
    console.log('Shortcode JS for Testwork is loaded');

    $('#tw-citysearch-submit').on('click', function(){
        tw_loading();
        
        let data = {
            action: $('#tw_action').val(), 
            wpnonce: $('#tw_wpnonce').val(),
            s: $('#tw-searchinput').val(),
        }


        $.ajax({
            method: 'POST',
            url: twjs.ajax_url,
            dataType: 'json',
            data: data,
            success: function(response){
                tw_loading();
                console.log(response.data.length);
                if(response.data.length > 0){
                    let output = city_table_template(response.data)
                    $('#tw-citylist').html(output);
                }
            },
            error: function(xhr){
                tw_loading();
            }
        });

    });

    $('#tw-searchinput').on('keyup', function(){
        let thisvalue = $(this).val();
        if(thisvalue == ''){
            $('#tw-inputclear').hide();
        }else{
            $('#tw-inputclear').show();
        }
    });
    $('#tw-inputclear').on('click', function(){
        $('#tw-searchinput').val('');
        $(this).hide();
        $('#tw-citysearch-submit').trigger('click');
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

function city_table_template(cities){
    let output = '';
    for (let i = 0; i < cities.length; i++) {
        let city = cities[i];
        output += city_table_row_tpl(city);
    }

    return output;
}

function city_table_row_tpl(city){
    return `<tr><td>${city['post_title']}</td><td>${city['country']}</td><td>${city['cu_latitude']}, ${city['cu_longitude']}</td><td>${city['temp']}</td></tr>`;
}