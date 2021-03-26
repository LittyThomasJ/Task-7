jQuery(document).ready(function($) {
  // Delete records on click delete anchor tag
  $('.delete').click(function(event){
    event.preventDefault();
    var tr = $(this).closest('tr');
    var form= $(this);
    var id= form.data('id');
    var link= form.data('url');
    // ajax
    jQuery.ajax({
        type: 'POST',
        url: link,
        data: {"action": "your_delete_action", "element_id": id},
        error: function(error) {
            alert("Insert Failed" + error);
        },
        success: function (data) {
         alert("Deleted");
         // Fadeout and remove table row
         setTimeout(function () {
                     tr.fadeOut(1000,function(){
                       tr.remove();
                     });
                 }, 300);
        }

    });

  });
});
