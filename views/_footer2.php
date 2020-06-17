<script type="text/javascript">

////////////////////////////////////////
// Footer with Javascript for cutting //
////////////////////////////////////////

/*
// order that was clicked comes next
  jQuery("tr").click(function() {
  	var next_id = jQuery(this).attr("id");

    $.ajax({
          url: "tools/move-first.php",
          type: "POST",
          data:{next_id:next_id} ,
          success: function(data) {
            $('#cutting_window').html(data);
            //location.reload();
          },
          error: function(err) {
          console.log('Error:', err);
          alert(err);
          }
      });
  });
*/

function statusFunction(id, status, type) {

  $.ajax({
    url: "tools/order-status.php",
    data: {id:id,status:status,type:type},
    type: "POST",
    success:function(data){
      location.reload();
    }
  });
}

// add new finished pallet
function palletFunction(field, team, type, amount, view, day) {

  $.ajax({
    url: "tools/add-pallet.php",
    data: {field:field,team:team,type:type,amount:amount,day:day},
    type: "POST",
    success:function(data){
      $('#pallets_finish').html(view);
      location.reload();
    }
  });
}

// make table rows clickable with links
jQuery("tr.clickable").click(function() {
  var field = jQuery(this).attr("id");
  var type = jQuery(this).attr("id2");
  var href = "https://turfgrass.site/index.php?field=" + field + "&type=" + type;
  window.location = href;
});

// make table rows clickable with links
jQuery("tr.clickable2").click(function() {
  var order = jQuery(this).attr("id");
  var href = "https://turfgrass.site/index.php?order=" + order;
  window.location = href;
});


// Order was loaded and pickedup (cutting modus 2)
function pickupFunction(id) {
  $.ajax({
    url: "tools/pick-up.php",
    data: {id:id},
    type: "POST",
    success:function(data){
      location.reload();
    }
  });
}

     
</script>

</body>

</html>

     