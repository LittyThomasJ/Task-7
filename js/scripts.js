( function( $ ) {

    $( document ).ready( function() {
      console.log('test');
      $( '.apply-job' ).on( 'click', '.apply', function( event ) {
          //console.log('test');
            // change label and switch class
            $( this ).text( settings.send_label ).removeClass( 'apply' ).addClass( 'Applying' );

            // show textarea
            $('.job').animate({height:"toggle",opacity:"toggle"},"slow");

        })
        $("#enquiry_email_form").on("submit", function (event) {
            event.preventDefault();

            var form= $(this);
            var ajaxurl = "../Add-jobs.php";
            var detail_info = {
                post_name: form.find("#post_name").val(),
                post_email: form.find("#post_email").val(),
                post_designation: form.find("#post_designation").val()
            }
            if(detail_info.post_name === "" || detail_info.post_email === "" || detail_info.post_designation === "") {
                alert("Fields cannot be blank");
                return;
            }

            $.ajax({

                url: ajaxurl,
                type: 'POST',
                data: {
                    post_details : detail_info,
                    action: 'save_enquiry_form_action'
                },
                error: function(error) {
                    alert("Insert Failed" + error);
                },
                success: function(response) {
                    alert("Insert Success" + response);
                }
            });
        })
    });

})( jQuery );
