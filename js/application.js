( function( $ ) {

    $( document ).ready( function() {
      console.log('test');

      $('.delete').click(function(){
        var form= $(this);
        var id= form.data('id');
        var link= form.data('url');
        alert(id);
        alert(link);
        $.ajax({
            type: 'POST',
            url: link,
            data: {"action": "your_delete_action", "element_id": id},
            success: function (data) {
               alert("Deleted");
            }
            error: function(data){
              alert("Deletion Failed");
            }
        });

      });

    });

})( jQuery );
