( function( $ ) {

    $( document ).ready( function() {
      console.log('test');
      // Toggle the form on click of button
      $( '.apply-job' ).on( 'click', '.apply', function( event ) {
            $(".job").toggle();

        })
        // validating the form for application
        $("#name_error_message").hide();
        $("#email_error_message").hide();
        $("#designation_error_message").hide();
        var error_name = false;
        var error_email = false;
        var error_designation = false;
        $("#post_name").focusout(function() {
          check_name();
          //alert();
        })
        $("#post_email").focusout(function() {
          check_email();
        })
        $("#post_designation").focusout(function() {
          check_designation();
        })
        function check_name() {
          // alert();
      		var name = $("#post_name").val();
      		var pattern = /^[a-zA-Z]*$/;
          // var no=name.length;
      		if(name.length !=0)
      		{
      			$("#name_error_message").hide();
      		} else{
      			$("#name_error_message").html("Enter valid name");
      			$("#name_error_message").show();
      			error_name = true;
      			//$( "#FirstName" ).focus();
      		}
        }
        function check_email() {
          // alert();
      		var email = $("#post_email").val();
          var pattern = /^(([^<>()[\]\\.,;:\s@"]+(\.[^<>()[\]\\.,;:\s@"]+)*)|(".+"))@((\[[0-9]{1,3}\.[0-9]{1,3}\.[0-9]{1,3}\.[0-9]{1,3}\])|(([a-zA-Z\-0-9]+\.)+[a-zA-Z]{2,}))$/;
      		if(pattern.test(email))
      		{
      			$("#email_error_message").hide();
      		} else{
      			$("#email_error_message").html("Enter valid email");
      			$("#email_error_message").show();
      			error_email = true;
      			//$( "#FirstName" ).focus();
      		}
        }
        function check_designation() {
          // alert();
      		var designation = $("#post_designation").val();
      		if(designation.length != 0)
      		{
      			$("#designation_error_message").hide();
      		} else{
      			$("#designation_error_message").html("Enter valid designation");
      			$("#designation_error_message").show();
      			error_designation = true;
      			//$( "#FirstName" ).focus();
      		}
        }
        // Insert data on submit of 'enquiry_email_form'
        $("#application_form").on("submit", function (event) {
            // event.preventDefault();
            var form= $(this);
            var link = form.data("url");
            var post_id = settings.post_id;

            var detail_info = {
                post_id: post_id,
                post_name: form.find("#post_name").val(),
                post_email: form.find("#post_email").val(),
                post_designation: form.find("#post_designation").val()
            }

            error_email = false;
        		error_name = false;
        		error_designation = false;
        		check_email();
        		check_name();
        		check_designation();
        		// Checking each variable, whether variable is set to false,then it will return false and else returns true
        		if ( error_name || error_email || error_designation) {
              alert("Enter valid Details");
        			event.preventDefault();
        		} else{
        			// alert("success");
              event.preventDefault();
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
                  // Display the preview
                  $('#apply').attr('disabled', false);
                  var html = '';
                   html += '<p>Name : '+result.data.name+'</p>';
                   html += '<p>Email : '+result.data.email+'</p>';
                   html += '<p>Designation : '+result.data.designation+'</p>';
                   // html += '<p><button id="okmessage">OK</button></p>';
                   $('#result_msg').prepend(html);
                   $(".job").toggle();
                   alert("Applied Successfully");
                }

            });
        })

    });

})( jQuery );
