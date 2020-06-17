<script type="text/javascript">

/////////////////////////////////////////////////////////
// Footer with Javascript for all pages except cutting //
/////////////////////////////////////////////////////////


/////////////
// General //
/////////////

// set up links
$(function(){       
    $('*[data-href]').click(function(){
        window.location = $(this).data('href');
        return false;
    });
});

// stop autorefreshing page
function stopRefresh () {
  clearTimeout(timeout);
}

///////////////////////
// sales, listpoints //
///////////////////////


// change order status
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

// change order status 2 (advance payment, no reload)
function statusFunction2(id, status, type) {

  $.ajax({
    url: "tools/order-status.php",
    data: {id:id,status:status,type:type},
    type: "POST",
    success:function(data){
       $('#advance_payment').html(data);
    }
  });
}

// handle deleting in edit
function ConfirmDelete()
    {
      var x = confirm("Biztos?");
      if (x)
          return true;
      else
        return false;
    }

// delete assigned number
function deleteId2(id) {
   var x = confirm("Biztos?");
    if (x) {
      //delete id2
      $.ajax({
        url: "tools/delete-id2.php",
        data:'id='+id,
        type: "POST",
        success:function(data){
          document.location = 'sales.php';
        }
      });
    } else {
      return false;
    };
}

// delete assigned number (cutting modus 2)
function deleteId3(id) {
   var x = confirm("Biztos?");
    if (x) {
      //delete id2
      $.ajax({
        url: "tools/delete-id3.php",
        data:'id='+id,
        type: "POST",
        success:function(data){
          document.location = 'sales.php';
        }
      });
    } else {
      return false;
    };
}

// start cutting on a field
function switchMode(mode) {

  $.ajax({
    url: "tools/switch-modus.php",
    data: {mode:mode},
    type: "POST",
    success:function(data){
      document.location = 'sales.php';
    }
  });
}


// get sum of selected orders
function addAmounts() {    // team 1
  var sum = 0;

  for(var i=1;i<200;i++) {
    str1 = "sum_";
    str2 = i;
    var a = str1.concat(str2);

    if (document.getElementById(a).checked == true) {
      var amount = parseInt(document.getElementById(a).value);
      sum = sum + amount;
    };
    
    document.getElementById("showSum").innerHTML = sum + "m&sup2;";

  }
}

/////////////////
// order, edit //
/////////////////

// actions when selecting a date in table
function dateFunction (i, t, available) {
  str4 = "datum";
  str5 = i;
  var c = str4.concat(str5);
  var datum = document.getElementById(c).value;

  if (t = 1) {    // no project
    //change time view
    $.ajax({
      url: "tools/get-time.php",
      data: {datum:datum,available:available},
      type: "POST",
      success:function(data){
        $('#time-output').html(data);
      }
    });
  };
  if (t = 2) {    // project
    var length = document.getElementById("projectLength").value;
    // show number of trucks
    $.ajax({
      url: "tools/get-truck.php",
      data: {datum:datum,length:length},
      type: "POST",
      success:function(data){
        $('#truck-output').html(data);
      }
    });
  };

  // change CSS based on selection
  str1 = "datebutton";
  str2 = i;
  var a = str1.concat(str2);
  var color = document.getElementById(a);
  color.className = 'selected';

  str3 = "orderdate";
  var hide = document.getElementById(str3);
  hide.className = 'hide';

  str4 = "count-output";
  var count = document.getElementById(str4);
  count.className = 'show';

  if (i != 1) {
      a = "datebutton1";
      var color = document.getElementById(a);
      color.className = 'active';
    };

  for (var j = 2; j < 16; j++) {
    if (j != i) {
      str1 = "datebutton";
      str2 = j;
      var a = str1.concat(str2);
      var color = document.getElementById(a);
      color.className = 'normal';
    };
  };
}


// display of times
function timeFunction (n) {
  str1 = "timebutton";
  str2 = n;
  var a = str1.concat(str2);
  var color = document.getElementById(a);
  color.className = 'selected';

  for (var j = 1; j < 15; j++) {
    if (j != n) {
      str1 = "timebutton";
      str2 = j;
      var a = str1.concat(str2);
      var color = document.getElementById(a);
      color.className = 'normal';
    };
  };

  document.getElementById("selectedtime").value = n;
}


// show if other date (not in table) is selected
function moreFunction () {

  $('#time-output').load('blank.html');

  str1 = "orderdate";
  var hide = document.getElementById(str1);
  hide.className = 'show';

  a = "datebutton1";
  var color = document.getElementById(a);
  color.className = 'active';

  for (var j = 2; j < 16; j++) {
    str1 = "datebutton";
    str2 = j;
    var a = str1.concat(str2);
    var color = document.getElementById(a);
    color.className = 'normal';
  };

};

// handle if other date (not in table) is selected
function moreRefresh (i) {
  var datum = document.getElementById("moreDatum").value;

  if (i = 1) {
    //change time values
    $.ajax({
      url: "tools/get-time.php",
      data:'datum='+datum,
      type: "POST",
      success:function(data){
        $('#time-output').html(data);
      }
    });
  };
  if (i = 2) {
    var length = document.getElementById("projectLength").value;
    //show number of trucks
    $.ajax({
      url: "tools/get-truck.php",
      data: {datum:datum,length:length},
      type: "POST",
      success:function(data){
        $('#truck-output').html(data);
      }
    });
  };

    str4 = "count-output";
    var count = document.getElementById(str4);
    count.className = 'show';
}

// show/hide forwarders if delivery is selected
function deliveryFunction () {
  str2 = "deliveryAgent";
  var hide2 = document.getElementById(str2);
  hide2.className = 'col-md-3 show';

};

function deliveryFunction2 () {
  str2 = "deliveryAgent";
  var hide2 = document.getElementById(str2);
  hide2.className = 'col-md-3 hide';

  document.getElementById("licence").value = "";
};

// insert licence number of forwarder
function forwarderFunction (id) {

  //change licence values
  $.ajax({
    url: "tools/get-licence.php",
    data:'id='+id,
    type: "POST",
    success:function(data){
      $('#licence').val(data);
    }
  });
}

// different fields for Hungary vs abroad
function countryFunction () {

  var country = document.getElementById("country1").value;

  if (country > 0) {
    var hide = document.getElementById("plz-group");
    hide.className = 'form-group hide';

    document.getElementById("address_input").innerHTML = '<input type="text" class="form-control" id="deliveryaddress" name="deliveryaddress" placeholder="Cím">';
    document.getElementById("plz_input2").innerHTML = '<input class="form-control" type="text" name="plz" id="plz_input" placeholder="Irányítószám / Város" />';

  };
  
  if (country == 0) {
    var hide = document.getElementById("plz-group");
    hide.className = 'form-group show';

    document.getElementById("address_input").innerHTML = '<input type="text" class="form-control" id="deliveryaddress" name="deliveryaddress" placeholder="Útca" required>';
    document.getElementById("plz_input2").innerHTML = '<input class="form-control" type="text" name="plz" id="plz_input" placeholder="Irányítószám / Város" required />';

  };
};

function countryFunction2 () {

  var country = document.getElementById("country1").value;

  if (country > 0) {
    var hide = document.getElementById("plz-group");
    hide.className = 'form-group hide';
  };
  
  if (country == 0) {
    var hide = document.getElementById("plz-group");
    hide.className = 'form-group show';

  };
};


// insert customer name as invoice name
function invoiceRefresh () {
  var customer_id = document.getElementById("customer_id").value;

  //change time values
  $.ajax({
    url: "tools/get-customer.php",
    data:'customer_id='+customer_id,
    type: "POST",
    success:function(data){
      $('#invoicename').val(data);
    }
  });
}

// insert delivery name and address for invoice data
function invoiceRefresh2 () {
  var deliveryname = document.getElementById("deliveryname").value;
  var deliveryaddress = document.getElementById("deliveryaddress").value;

  document.getElementById("invoicename").value = deliveryname;
  document.getElementById("invoiceaddress").value = deliveryaddress;
}


// buttons for quick note inserts
function noteRefresh () {
  var note = document.getElementById("note").value;
  var note2 = document.getElementById("note2").value;
  
  if (note2 == "") {
    var newnote2 = note;
  } else {
    var newnote2 = note2 + " | " + note;
  };

  document.getElementById("note2").value = newnote2;
}


function noteRefresh2 (i) {
  var note = document.getElementById("note").value;
  var note2 = document.getElementById("note2").value;

  if (i == 1) {
    var add = "még visz";
  } else if (i == 2) {
    var add = "folytatás";
  };

  if (note == "") {
    var newnote = add;
  } else {
    var newnote = note + " | " + add;
  };

  if (note2 == "") {
    var newnote2 = add;
  } else {
    var newnote2 = note2 + " | " + add;
  };

  document.getElementById("note").value = newnote;
  document.getElementById("note2").value = newnote2;
}

// insert new customer into database
function insertData() {
  var customer_name=$("#customer_name").val();
  var contactperson=$("#contactperson2").val();
  var plz=$("#customer_plz2").val();
  var city=$("#customer_city2").val();
  var street=$("#customer_street2").val();
  var phone=$("#telephone2").val();
  var email=$("#email2").val();

  var delivery=$("input[name='delivery_standard']:checked").val();
  var payment=$("input[name='payment_standard']:checked").val();
  var country=$("#country").val();
  var area=$("#area").val();
 
  // AJAX code to send data to php file.
  $.ajax({
      type: "POST",
      url: "tools/insert-data.php",
      data: {customer_name:customer_name,contactperson:contactperson,plz:plz,city:city,street:street,phone:phone,email:email,delivery:delivery,payment:payment,country:country,area:area},
      dataType: "JSON",
      success: function(result) {
        $("#message").html(result[0]);
        $("p").addClass("alert alert-success");
        $('#newModal').modal('hide');
        $("#customer_id").val(result[2]);
        $("#customer_input").val(result[1]);

        document.getElementById("customer-group-1").className = 'form-group show';
        $("#customer_plz").val(result[5]);
        $("#customer_city").val(result[6]);

        document.getElementById("customer-group-2").className = 'form-group show';
        $("#customer_street").val(result[4]);

        document.getElementById("customer-group-3").className = 'form-group show';
        $("#contactperson").val(result[3]);

        document.getElementById("customer-group-4").className = 'form-group show';
        $("#telephone").val(result[7]);

        document.getElementById("customer-group-5").className = 'form-group show';
        $("#email").val(result[8]);

        var delivery = result[9];  

        if (delivery == 1) {
          a = "delivery1";
          var field = document.getElementById(a);
          field.checked = true;

          str2 = "deliveryAgent";
          var hide2 = document.getElementById(str2);
          hide2.className = 'col-md-3 hide';
        };

        if (delivery == 2) {
          a = "delivery2";
          var field = document.getElementById(a);
          field.checked = true;

          str2 = "deliveryAgent";
          var hide2 = document.getElementById(str2);
          hide2.className = 'col-md-3 show';
        };

        var payment = result[10];

        if (payment == 1) {
          a = "payment1";
          var field = document.getElementById(a);
          field.checked = true;
        };

        if (payment == 2) {
          a = "payment2";
          var field = document.getElementById(a);
          field.checked = true;
          };
      },
      error: function(err) {
      console.log('Error:', err);
      alert(err);
      }
  });

}


$(function() {
    $("#customer_input").autocomplete({
        source: "tools/search.php",
        select: function( event, ui ) {
          $("#customer_id").val(ui.item.id);

          var group1 = document.getElementById("customer-group-1");
          group1.className = 'form-group show';
          $("#customer_plz").val(ui.item.plz);
          $("#customer_city").val(ui.item.city);

          var group2 = document.getElementById("customer-group-2");
          group2.className = 'form-group show';
          $("#customer_street").val(ui.item.street);

          var group3 = document.getElementById("customer-group-3");
          group3.className = 'form-group show';
          $("#contactperson").val(ui.item.contactperson);

          var group4 = document.getElementById("customer-group-4");
          group4.className = 'form-group show';
          $("#telephone").val(ui.item.phone);

          var group5 = document.getElementById("customer-group-5");
          group5.className = 'form-group show';
          $("#email").val(ui.item.email);

          $("#licence").val(ui.item.licence);

          var delivery = ui.item.delivery;  

          if (delivery == 1) {
            a = "delivery1";
            var field = document.getElementById(a);
            field.checked = true;

            str2 = "deliveryAgent";
            var hide2 = document.getElementById(str2);
            hide2.className = 'col-md-3 hide';
          };

          if (delivery == 2) {
            a = "delivery2";
            var field = document.getElementById(a);
            field.checked = true;

            str2 = "deliveryAgent";
            var hide2 = document.getElementById(str2);
            hide2.className = 'col-md-3 show';
          };

          var payment = ui.item.payment;

          if (payment == 1) {
            a = "payment1";
            var field = document.getElementById(a);
            field.checked = true;
          };

          if (payment == 2) {
            a = "payment2";
            var field = document.getElementById(a);
            field.checked = true;
            };

          // special request for Oazis
          if (ui.item.id == 12) {
            $("#note").html("Oazis");
          }
          
        }
    });
});


// autocomplete customer
$(function() {
    $("#customer_input2").autocomplete({
        source: "tools/search.php",
        select: function( event, ui ) {
          $("#customer_id").val(ui.item.id);  
          /*
          var group1 = document.getElementById("customer-group-1");
          group1.className = 'form-group show';
          $("#customer_plz").val(ui.item.plz);
          $("#customer_city").val(ui.item.city);

          var group2 = document.getElementById("customer-group-2");
          group2.className = 'form-group show';
          $("#customer_street").val(ui.item.street);

          var group3 = document.getElementById("customer-group-3");
          group3.className = 'form-group show';
          $("#contactperson").val(ui.item.contactperson);

          var group4 = document.getElementById("customer-group-4");
          group4.className = 'form-group show';
          $("#telephone").val(ui.item.phone);

          var group5 = document.getElementById("customer-group-5");
          group5.className = 'form-group show';
          $("#email").val(ui.item.email);       
          */
        }
    });
});

// autocomplete plz
$(function() {
    $("#plz_input").autocomplete({
        source: "tools/search_plz.php",
        select: function( event, ui ) {
          $("#city_id").val(ui.item.id);
        }
    });
});

$(function() {
  $(document).on('focus','.plz_input2',function(){
    order = $(this).data('id');

    $(this).autocomplete({
        source: "tools/search_plz.php",
        select: function( event, ui ) {
          //$("#city_id_" + order).val(ui.item.id);
        }
    });
  });
});


// update field list according to type2
function fieldRefresh () {
  var type2 = document.forms["myForm"]["type2"].value;

  $.ajax({
    url: "tools/get-fieldoptions.php",
    data: 'type2='+type2,
    type: "POST",
    success:function(data){
      $('#field_select').html(data);
    }
  });

}

// update truck calculation
function updateCalculation3 () {
  var amount = document.getElementById("amount").value;
  var length = document.getElementById("length").value;
  var cooling = document.getElementById("cooling");

  var pipes = Math.ceil(amount / length / 1.2);

  if (cooling.checked == true){
    var trucks = Math.ceil(pipes / 38);
    var standard = 38;
  } else {
    var trucks = Math.ceil(pipes / 48);
    var standard = 48;
  }

  if (amount > 0 && length > 0) {
    document.getElementById("calculation-output").innerHTML = "<br>" + pipes + " tekercs<br>" + standard + " tekercs / kamion<br><b>kb. " + trucks + " kamion</b>";
  };
}

// change duration of project
function changeLength () {

  var length = document.getElementById("projectLength").value;
  var datum = document.getElementById("selecteddate").value;
  //change time values
  $.ajax({
    url: "tools/get-truck.php",
    data: {datum:datum,length:length},
    type: "POST",
    success:function(data){
      $('#truck-output').html(data);
    }
  });

  var newcount = length * 3;

  document.getElementById("count-output").innerHTML = (newcount + " kamion tervezett");
  document.getElementById("truckcount").value = newcount;
}


// change trucks per day
function changeTruck (j, t) {

  str1 = "truckdatum_";
  str2 = j;
  var a = str1.concat(str2);

  str3 = "truckpics_";
  str4 = j;
  var b = str3.concat(str4);

  var truckcount = document.getElementById("truckcount").value;

  var length = document.getElementById(a).value;

  if (t == 1) {
    if (length > 1) {
      var newlength = parseInt(length) - 1;
      var check = 1;
      var newcount = parseInt(truckcount) - 1;
    };
  }
  if (t == 2) {
    var newlength = parseInt(length) + 1;
    var check = 1;
    var newcount = parseInt(truckcount) + 1;
  };

  if (check == 1) {
    var trucks = '<img src="../img/truck.png" class="truck">';

    for (var i = 1; i < newlength; i++) {
      trucks += '<img src="../img/truck.png" class="truck">';
    };

    document.getElementById(b).innerHTML = trucks;
    document.getElementById(a).value = newlength;

    document.getElementById("count-output").innerHTML = (newcount + " kamion tervezett");
    document.getElementById("truckcount").value = newcount;
  };
}


////////////////////////
// today, plan, plan2 //
////////////////////////


// drag and drop tables for cutting plan
$(document).ready(function() {
    // Initialise the table
    $("#table-1").tableDnD({
      onDragClass: "myDragClass",

      onDrop: function() {
        var parameter = $.tableDnD.serialize();
        
        $.ajax({
            url: "tools/update_rang.php",
            type: "POST",
            data:{parameter:parameter} ,
            //dataType: "JSON",
            success: function(data) {
              location.reload();
            },
            error: function(err) {
            console.log('Error:', err);
            alert(err);
            }
        });   
      }
    });

     // Initialise the table
    $("#table-2").tableDnD({
      onDragClass: "myDragClass",

      onDrop: function() {
        var parameter = $.tableDnD.serialize();
        
        $.ajax({
            url: "tools/update_rang.php",
            type: "POST",
            data:{parameter:parameter} ,
            //dataType: "JSON",
            success: function(data) {
              location.reload();
            },
            error: function(err) {
            console.log('Error:', err);
            alert(err);
            }
        });   
      }
    });

    $("#myTable").tablesorter({
      sortList: [[5,1]]
    }); 
});


// change planneddate to next/previous date
function changeDay(id, type) {

  $.ajax({
    url: "tools/change-day.php",
    data: {id:id,type:type},
    type: "POST",
    success:function(data){
      location.reload();
    }
  });
}


// toggle functions for changing the field
function toggle(j) {    // team 1
  var checkAll = document.getElementById("checkAll");
  
  if (checkAll.checked == true) {
    for(var i=1;i<j;i++) {
    str1 = "editField";
    str2 = i;
    var a = str1.concat(str2);
    var field = document.getElementById(a);
    field.checked = true;
    }
  }
  else {
    for(var i=1;i<j;i++) {
    str1 = "editField";
    str2 = i;
    var a = str1.concat(str2);
    var field = document.getElementById(a);
    field.checked = false;
    }
  } 
}

function toggle2(j, k) {    // team 2
  var checkAll = document.getElementById("checkAll2");
  
  if (checkAll.checked == true) {
    for(var i=k;i<j;i++) {
    str1 = "editField";
    str2 = i;
    var a = str1.concat(str2);
    var field = document.getElementById(a);
    field.checked = true;
    }
  }
  else {
    for(var i=k;i<j;i++) {
    str1 = "editField";
    str2 = i;
    var a = str1.concat(str2);
    var field = document.getElementById(a);
    field.checked = false;
    }
  } 
}

function untoggle() {     // team 1
  var checkAll = document.getElementById("checkAll");
  checkAll.checked = false; 
}

function untoggle2() {    // team 2
  var checkAll = document.getElementById("checkAll2");
  checkAll.checked = false; 
}


// change team
function teamFunction(id, team) {

  $.ajax({
    url: "tools/change-team.php",
    data: {id:id,team:team},
    type: "POST",
    success:function(data){
      location.reload();
    }
  });

  // ?? script does not always work without this alert
  alert("ok");
}


/////////////
// amounts //
/////////////

// change maximum possible amounts
function amountsFunction(n, timespan) {
  str1 = "amountsdate_";
  str2 = n;
  var c = str1.concat(str2);
  var datum = document.getElementById(c).value;

  str3 = "amounts_";
  var d = str3.concat(str2);
  var amount = document.getElementById(d).value;

  //alert(datum + " / " + n + " / " + amount + " / " + timespan);

  $.ajax({
    url: "tools/change-amounts.php",
    data: {datum:datum,amount:amount,timespan:timespan,n:n},
    type: "POST",
    success:function(data){
      $("#" + d).addClass("form-control inserted");
      location.reload();
    }
  });

}


/////////////
// history //
/////////////

// show orders of right date
function historyRefresh (i) {
  if (i == 1) {
    var datum = document.getElementById("historyDatum").value;
  } else if (i == 2) {
    var datum = document.getElementById("changePrevious").value;
  } else if (i == 3) {
    var datum = document.getElementById("changeNext").value;
  } else if (i == 4) {
    var datum = document.getElementById("changeYesterday").value;
  } else if (i == 5) {
    var datum = document.getElementById("changeToday").value;
  }

  window.location.href = "https://turfgrass.site/history.php?datum=" + datum;
}

// show orders of right date
function historyRefresh2 (i) {
  if (i == 1) {
    var datum = document.getElementById("historyDatum").value;
  } else if (i == 2) {
    var datum = document.getElementById("changePrevious").value;
  } else if (i == 3) {
    var datum = document.getElementById("changeNext").value;
  } else if (i == 4) {
    var datum = document.getElementById("changeYesterday").value;
  } else if (i == 5) {
    var datum = document.getElementById("changeToday").value;
  }

  window.location.href = "http://program.turfgrass.at/history.php?datum=" + datum;
}


//////////////
// customer //
//////////////

// insert right dates when different buttons are clicked
function lastYear() {
  var startdate = document.getElementById("lastYearStart").value;
  var enddate = document.getElementById("lastYearEnd").value;

  document.getElementById("fromDatum").value = startdate;
  document.getElementById("toDatum").value = enddate;
}

function thisYear() {
  var startdate = document.getElementById("thisYearStart").value;
  var enddate = document.getElementById("thisYearEnd").value;

  document.getElementById("fromDatum").value = startdate;
  document.getElementById("toDatum").value = enddate;
}

function lastMonth() {
  var startdate = document.getElementById("lastMonthStart").value;
  var enddate = document.getElementById("lastMonthEnd").value;

  document.getElementById("fromDatum").value = startdate;
  document.getElementById("toDatum").value = enddate;
}

function thisMonth() {
  var startdate = document.getElementById("thisMonthStart").value;
  var enddate = document.getElementById("thisMonthEnd").value;

  document.getElementById("fromDatum").value = startdate;
  document.getElementById("toDatum").value = enddate;
}


/////////////
// project //
/////////////

// change the date of a truck
function truckDatumFunction(id, t) {
  str4 = "oldDatum_";
  str5 = t;
  var c = str4.concat(str5);
  var olddatum = document.getElementById(c).value;

  str6 = "truckDatum_";
  var d = str6.concat(str5);
  var newdatum = document.getElementById(d).value;

  e = "projectStartDatum";
  var projectstart = document.getElementById(e).value;

  // check if date is before project start (except for first truck)
  if (t > 1) {
    if (newdatum > projectstart) {
      var check = 1;
    } else {
      alert("Nem sikerült. Dátum a projekt kezdése elött.");
    };
  } else {
    var check = 1;
  };
    
  if (check == 1) {
    $.ajax({
      url: "tools/change-datum.php",
      data: {id:id,olddatum:olddatum,newdatum:newdatum,type:t},
      type: "POST",
      success:function(data){
        $("#" + d).addClass("inserted");
        location.reload();
      }
    });
  };
}

// add/remove trucks on a day
function changeTruck2 (id, t, c) {

  str1 = "totalTrucks_";
  str2 = t;
  var a = str1.concat(str2);
  var total = document.getElementById(a).value;

  str3 = "fixTrucks_";
  str4 = t;
  var b = str3.concat(str4);
  var fix = document.getElementById(b).value;

  str5 = "truckDatum_";
  str6 = t;
  var d = str5.concat(str6);
  var datum = document.getElementById(d).value;

  if (c == 1) {

    if (total > fix) {
      $.ajax({
        url: "tools/change-truck.php",
        data: {datum:datum,project:id,type:c},
        type: "POST",
        success:function(data){
          location.reload();
        }
      });
    };
  };
  
  if (c == 2) {
    $.ajax({
      url: "tools/change-truck.php",
      data: {datum:datum,project:id,type:c},
      type: "POST",
      success:function(data){
        location.reload();
      }
    });
  };
}

// add day to project
function addDayFunction (id) {
  var datum = document.getElementById("addDay").value;
  //change time values
  $.ajax({
    url: "tools/add-day.php",
    data: {datum:datum,project:id},
    type: "POST",
    success:function(data){
      location.reload();
    }
  });
}

// change expected time for a truck
function truckTimeFunction(id) {
  str4 = "plannedtime";
  str5 = id;
  var c = str4.concat(str5);
  var time = document.getElementById(c).value;

  $.ajax({
    url: "tools/change-time2.php",
    data: {id:id,time:time},
    type: "POST",
    success:function(data){
      $("#" + c).addClass("form-control inserted");
    }
  });
}

// finish truck
function truckLeave(id) {
  $.ajax({
    url: "tools/truck-leave.php",
    data: {id:id},
    type: "POST",
    success:function(data){
      location.reload();
    }
  });

}

// change project status
function projectStatus(id, status) {
  $.ajax({
    url: "tools/project-status.php",
    data: {id:id, status:status},
    type: "POST",
    success:function(data){
      location.reload();
    }
  });

}

// delete project
function deleteProject(id) {
   var x = confirm("Biztos?");
    if (x) {
      //delete id2
      $.ajax({
        url: "tools/delete-project.php",
        data:'id='+id,
        type: "POST",
        success:function(data){
          document.location = 'project.php';
        }
      });
    } else {
      return false;
    };
}

// calculate m2 per trucks (delivery note)
function updateCalculation (i) {
  str1 = i;
  str2 = "pipes";
  var b = str2.concat(str1);
  var pipes = document.getElementById(b).value;

  str3 = "length";
  var c = str3.concat(str1);
  var length = document.getElementById(c).value;

  var newamount = Math.ceil(pipes * length * 1.2);

  str4 = "calculation";
  var d = str4.concat(str1);
  document.getElementById(d).innerHTML = pipes + " db <b>x</b> " + length + " m <b>x</b> 1.2 m =<br><b>" + newamount + " m&sup2;</b>";

  str6 = "finalamount";
  var f = str6.concat(str1);
  var btn = document.getElementById(f);
  btn.value = newamount;           
}

// calculate m2 per trucks (edit truck)
function updateCalculation2 (i) {
  str1 = i;
  str2 = "pipes2_";
  var b = str2.concat(str1);
  var pipes = document.getElementById(b).value;

  str3 = "truck_length";
  var c = str3.concat(str1);
  var length = document.getElementById(c).value;

  var newamount = Math.ceil(pipes * length * 1.2);

  str4 = "calculation2_";
  var d = str4.concat(str1);
  document.getElementById(d).innerHTML = pipes + " db <b>x</b> " + length + " m <b>x</b> 1.2 m =<br><b>" + newamount + " m&sup2;</b>";

  str6 = "finalamount2_";
  var f = str6.concat(str1);
  var btn = document.getElementById(f);
  btn.value = newamount;
}


//////////////////////
// Cutting, Loading //
//////////////////////


function moveFunction(id, type) {

  $.ajax({
    url: "tools/move-order.php",
    data: {id:id,type:type},
    type: "POST",
    success:function(data){
      location.reload();
    }
  });
}

// add new finished pallet
function palletFunction(field, team, type, amount, view, day, points) {
  var pallet_div = "pallet_" + points;

  $.ajax({
    url: "tools/add-pallet.php",
    data: {field:field,team:team,type:type,amount:amount,day:day},
    type: "POST",
    success:function(data){
      $('#pallets_finish').html(view);
      $('#' + pallet_div).html('<img src="../img/point.png" style="height: 20px;">');
      location.reload();
    } 
  });
}

// make table rows clickable with links
jQuery("tr.clickable").click(function() {
  var field = jQuery(this).attr("id");
  var type = jQuery(this).attr("id2");
  var href = "https://turfgrass.site/cutting2.php?field=" + field + "&type=" + type;
  window.location = href;
});

// make table rows clickable with links
jQuery("tr.clickable2").click(function() {
  var order = jQuery(this).attr("id");
  var href = "https://turfgrass.site/loading.php?order=" + order;
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


////////////////
// Production //
////////////////

// insert full area of field (_add.php)
function completeFunction (size) {
  var e = document.getElementById("optionb");
  if(e.style.display == 'none')
    e.style.display = 'block';
  else
    e.style.display = 'none';

  var fixinput = document.getElementById("fixinput");
  if(fixinput.value != size)
    fixinput.value = size;
  else
    fixinput.value = '0';

  var color = document.getElementById("changebutton");
  if(color.className == 'btn btn-complete')
    color.className = 'btn btn-active';
  else
    color.className = 'btn btn-complete';

  var complete = document.getElementById("complete_check");
  if(complete.value == "0")
    complete.value = "1";
  else
    complete.value = '0';
}

// insert full area of field (_production_out.php)
function completeFunction2 (size, i) {
  
  str1 = "optionb";
  str2 = i;
  var a = str1.concat(str2);
  var e = document.getElementById(a);
  if(e.style.display == 'none')
    e.style.display = 'block';
  else
    e.style.display = 'none';

  str3 = "fixinput";
  var b = str3.concat(str2);
  var fixinput = document.getElementById(b);
  if(fixinput.value != size)
    fixinput.value = size;
  else
    fixinput.value = '0';

  str4 = "changebutton";
  var c = str4.concat(str2);
  var color = document.getElementById(c);
  if(color.className == 'btn btn-complete')
    color.className = 'btn btn-active';
  else
    color.className = 'btn btn-complete';

  str5 = "complete_check";
  var d = str5.concat(str2);
  var complete = document.getElementById(d);
  if(complete.value == "0")
    complete.value = "1";
  else
    complete.value = '0';
}


// start cutting on a field
function startCutting(id) {
  var startCutting = document.getElementById("startCutting");
  
  if (startCutting.checked == true) {
    var value = 1;
  }
  else {
    var value = 0;
  }

  $.ajax({
    url: "tools/change-cutting.php",
    data: {id:id,value:value},
    type: "POST",
    success:function(data){
    }
  });
}



//////////////////////
// form validations //
//////////////////////


function validateForm() {
    var x = document.forms["myForm"]["complete"].value;
    if (x == 0) {
        alert("Írja be a befejezett ha-t!");
        return false;
    }
}

function validateForm2(i, old) {
    str1 = "myForm";
    str2 = i;
    var a = str1.concat(str2);
    var x = document.forms[a]["complete"].value;
    if (x == 0) {
        alert("Írja be a befejezett ha-t!");
        return false;
    }
    if (x == old) {
        alert("Írja be az új befejezett ha-t!");
        return false;
    }
}

// validate new order
function validateForm3() {
    var x = document.forms["myForm"]["date"].value;
    if (x == 0) {
        alert("Válassz ki egy napot!");
        return false;
    }

    var y = document.forms["myForm"]["time"].value;
    if (y == 0) {
        alert("Válassz ki egy időpontot!");
        return false;
    }

    // check capacity
    var type1 = document.forms["myForm"]["type1"].value;
    var availability_total = document.getElementById("availability_total").value;
    var availability_1 = document.getElementById("availability_1").value;
    var timepoint = document.getElementById("selectedtime").value;
    var amount = document.forms["myForm"]["amount"].value;

    var difference1 = parseInt(availability_total) - parseInt(amount);
    var difference2 = parseInt(availability_1) - parseInt(amount);

    if (type1 < 4) {
      if (difference1 < 0) {
          alert("Ez a nap már megtelt!");
          return false;
      } else if (timepoint < 6 && difference2 < 0) {
          alert("Ez az időpont már megtelt!");
          return false;
      };
    };


    var z = document.forms["myForm"]["customer_id"].value;
    if (z == 0) {
        alert("Válassz ki egy vevőt, aki létezik! Vagy létesíts egy új vevőt!");
        return false;
    }

    var v = document.forms["myForm"]["city_id"].value;
    var f = document.forms["myForm"]["country1"].value;
    var p = document.forms["myForm"]["project"].value;
    
    if (v == 0 && f == 0 && p == 0) {
        alert("Válassz ki egy várost, aki létezik! Vagy javíts ki az országot!");
        return false;
    }

    var a = document.forms["myForm"]["amount"].value;
    if (a == 0) {
        alert("Ne felejts el a mennyiséget!");
        return false;
    }

    //check if field is mediterran (has to be refreshed after seeding!!!)
    var b = document.forms["myForm"]["type2"].value;
    var c = document.forms["myForm"]["field"].value;
    if (b == 2) {
        if (!(c == 34 || c == 38)) {
          
          alert("A választott területen nincs Mediterran.");
          return false;
        };
    }

    if (c == 0) {
      alert("Ne felejts el a területet!");
      return false;
    };
}

// validate order edit
function validateForm4() {

    var x = document.forms["myForm"]["date"].value;
    if (x == 0) {
        alert("Válassz ki egy napot!");
        return false;
    }

    var y = document.forms["myForm"]["time"].value;
    if (y == 0) {
        alert("Válassz ki egy időpontot!");
        return false;
    }

    // check capacity
    var availability_total = document.getElementById("availability_total").value;
    var availability_1 = document.getElementById("availability_1").value;
    var timepoint = document.getElementById("selectedtime").value;
    var amount = document.forms["myForm"]["amount"].value;

    var difference1 = parseInt(availability_total) - parseInt(amount);
    var difference2 = parseInt(availability_1) - parseInt(amount);

    if (difference1 < 0) {
        alert("Ez a nap már megtelt!");
        return false;
    } else if (timepoint < 6 && difference2 < 0) {
        alert("Ez az időpont már megtelt!");
        return false;
    };

    var a = document.forms["myForm"]["amount"].value;
    if (a == 0) {
        alert("Ne felejts el a mennyiséget!");
        return false;
    }

}



//////////////////
// out of order //
//////////////////

/*
// changed planned time in today, plan
function timechangeFunction(id) {
  str4 = "plannedtime";
  str5 = id;
  var c = str4.concat(str5);
  var time = document.getElementById(c).value;

  $.ajax({
    url: "tools/change-time.php",
    data: {id:id,time:time},
    type: "POST",
    success:function(data){
      $("#" + c).addClass("form-control inserted");
    }
  });
}


// set all orders from one customer within the selected time frame to paid (NOT working)
function allPaid(customer) {
    var from = document.getElementById("from").value;
    var to = document.getElementById("to").value;

    $.ajax({
    url: "tools/all-paid.php",
    data: {id:customer,from:from,to:to},
    type: "POST",
    success:function(data){
      location.reload();
    }
  });

}
*/


////////////////////////////////////////
////////////////////////////////////////






function dateEditFunction () {
  
  str2 = "editDiv";
  var show1 = document.getElementById(str2);
  show1.className = 'row modal_row show';
};



function updateSize (id) {
  var remain = document.getElementById("remaining_size").value;

  $.ajax({
      url: "tools/update-size.php",
      data: {id:id,remain:remain},
      type: "POST",
      success:function(data){
        location.reload();
      }
    });

}










function amountCalculation (i, mode) {
  
  str1 = "amount2_";
  str2 = i;
  var a = str1.concat(str2);

  str3 = "amount3_";
  str4 = i;
  var b = str3.concat(str4);

  str5 = "amount_total_";
  str6 = i;
  var c = str5.concat(str6);

  str7 = "amount_totalb_";
  str8 = i;
  var d = str7.concat(str8);

  var total = document.getElementById(c).value;
  
  if (mode == 1) {
    document.getElementById(d).value = total.toString().replace(/(\d)(?=(\d{3})+(?!\d))/g, '$1.');
    document.getElementById(a).value = total.toString().replace(/(\d)(?=(\d{3})+(?!\d))/g, '$1.');
  } 

  if (mode == 2) {
    document.getElementById(d).value = total.toString().replace(/(\d)(?=(\d{3})+(?!\d))/g, '$1.');
    var total_half = total / 2;
    document.getElementById(a).value = total_half.toString().replace(/(\d)(?=(\d{3})+(?!\d))/g, '$1.');
    document.getElementById(b).value = total_half.toString().replace(/(\d)(?=(\d{3})+(?!\d))/g, '$1.');
  } 

  if (mode == 3) {
    document.getElementById(d).value = total.toString().replace(/(\d)(?=(\d{3})+(?!\d))/g, '$1.');
    var total_half = total / 2;
    document.getElementById(a).value = total_half.toString().replace(/(\d)(?=(\d{3})+(?!\d))/g, '$1.');
    document.getElementById(b).value = total_half.toString().replace(/(\d)(?=(\d{3})+(?!\d))/g, '$1.');
  } 
}

function calculationFunction (amount, i) {
  str2 = i;
  str3 = "finalamount";
  var b = str3.concat(str2);
  var btn = document.getElementById(b);
  btn.value = amount;
}

function calculationFunction2 (amount, i) {
  str2 = i;
  str3 = "finalamount2_";
  var b = str3.concat(str2);
  var btn = document.getElementById(b);
  btn.value = amount;
}



</script>

<script src="js/lightbox.js"></script>

</body>

</html>