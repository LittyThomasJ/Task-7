( function( $ ) {

    $( document ).ready( function() {
      console.log('test');
      $( '.apply-job' ).on( 'click', '.apply', function( event ) {
            $(".job").toggle();

        })
        $("#enquiry_email_form").on("submit", function (event) {
            event.preventDefault();
            var form= $(this);
            var link = form.data("url");
            //alert(settings.ajaxurl);
            var detail_info = {
                post_name: form.find("#post_name").val(),
                post_email: form.find("#post_email").val(),
                post_designation: form.find("#post_designation").val()
            }
            // alert(detail_info['post_designation']);
            if(detail_info.post_name === "" || detail_info.post_email === "" || detail_info.post_designation === "") {
                alert("Fields cannot be blank");
                return;
            }

            $.ajax({
                url: settings.ajaxurl,
                type: 'POST',
                data: {
                    post_details : detail_info,
                    action: 'save_post_details_form'
                },
                error: function(error) {
                    alert("Insert Failed" + error);
                },
                success: function(result) {
                  //alert(result.data.name);
                  $('#apply').attr('disabled', false);
                  var html = '';
                   html += '<p>Name : '+result.data.name+'</p>';
                   html += '<p>Email : '+result.data.email+'</p>';
                   html += '<p>Designation : '+result.data.designation+'</p>';
                   $('#result_msg').prepend(html);
                   $(".job").toggle();
                }

            });
        })
    });

})( jQuery );
