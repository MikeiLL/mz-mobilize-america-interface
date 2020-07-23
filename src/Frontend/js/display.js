(function($) {
  $(document).ready(function($) {
    var nonce = mobilize_america_events.nonce,
    atts = mobilize_america_events.atts,
    container = $('#MobilizeEvents');
    $.ajax({
     type : "post",
     dataType : "json",
     url : mobilize_america_events.ajaxurl,
     data : {action: 'mobilize_america_events', nonce: nonce, atts: atts},
     success: function(json) {
        if(json.type == "success") {
            container.toggleClass('loader');
            container.html(json.message);
        } else {
            container.toggleClass('loader');
            container.html(json.message);
        }
      }
    })
    .fail( function( json ) {
        console.log('fail');
        console.log(json);
        container.toggleClass('loader');
        container.html('Sorry but there was an error retrieving events.');
    }); // End Ajax
  });
})( jQuery );

