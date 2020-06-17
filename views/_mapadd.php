<?php 
//////////////////////////////////////////////
// Map to select field for adding operation //
//////////////////////////////////////////////

$_SESSION['mode'] = $_GET['mode'];

include('views/_header'.$header.'.php');

?>

 </div>

  <div id="map" class="center-block"></div>

    <script>

      function initMap() {
        var map = new google.maps.Map(document.getElementById('map'), {
          zoom: 15,
          center: {lat: 47.3354, lng: 19.269368},
          mapTypeId: 'satellite'
        });


        //// FIELDS

/*
       /// Temetö
        // Define the LatLng coordinates for the polygon's path.
        var temetoCoords = [
          // egész temetö !!!!!
          //{lat: 47.3335, lng: 19.2609}, 
          //{lat: 47.3359, lng: 19.2576},
          //{lat: 47.3382, lng: 19.2606},
          //{lat: 47.3361, lng: 19.2638}
          ////
          {lat: 47.3335, lng: 19.2609}, //lower left
          {lat: 47.3359, lng: 19.2576},
          {lat: 47.3364, lng: 19.2581},
          {lat: 47.3343, lng: 19.2615}
        ];

        // Construct the polygon.
        var temeto = new google.maps.Polygon({
          paths: temetoCoords,
          strokeColor: '#DF7401',
          strokeOpacity: 0.9,
          strokeWeight: 2,
          fillColor: '#DF7401',
          fillOpacity: 0.7,
        });
        temeto.setMap(map);

        //insert label as image
        var temetoLabel = new google.maps.Marker({
          position: new google.maps.LatLng(47.335646, 19.260779),
          icon: "/img/temeto.png",
          map: map
        });

        //add hyperlink to polygon
        google.maps.event.addListener(temeto, 'click', function () {
        window.location.href = 'https://turfgrass.site/add.php?field=24';
        });
        
        /*
        //add hyperlinks to label
        google.maps.event.addListener(temetoLabel, 'click', function () {
        window.location.href = 'https://turfgrass.site/add.php?field=24';
        });
  */

        /// Temetö fiatal 2019
        // Define the LatLng coordinates for the polygon's path.
        var temeto2Coords = [
          {lat: 47.3382, lng: 19.2606}, // upper right
          {lat: 47.3361, lng: 19.2638},
          {lat: 47.3343, lng: 19.2615},
          {lat: 47.336453, lng: 19.258195}
        ];

        // Construct the polygon.
        var temeto2 = new google.maps.Polygon({
          paths: temeto2Coords,
          strokeColor: '#FAAC58',
          strokeOpacity: 0.9,
          strokeWeight: 2,
          fillColor: '#FAAC58',
          fillOpacity: 0.7,
        });
        temeto2.setMap(map);

        
        //insert label as image
        var temeto2Label = new google.maps.Marker({
          position: new google.maps.LatLng(47.335646, 19.260779),
          icon: "/img/temeto.png",
          map: map
        });

        //add hyperlink to polygon
        google.maps.event.addListener(temeto2, 'click', function () {
        window.location.href = 'https://turfgrass.site/add.php?field=39';
        });
        
        
        //add hyperlinks to label
        google.maps.event.addListener(temeto2Label, 'click', function () {
        window.location.href = 'https://turfgrass.site/add.php?field=39';
        });


/*
        /// Hullamos bal
        // Define the LatLng coordinates for the polygon's path.
        var hullamosCoords = [
          {lat: 47.334922, lng: 19.2744399},
          {lat: 47.335721, lng: 19.273401},
          {lat: 47.339536, lng: 19.278182},
          {lat: 47.338809, lng: 19.279276}
        ];

        // Construct the polygon.
        var hullamos = new google.maps.Polygon({
          paths: hullamosCoords,
          strokeColor: '#DF7401',
          strokeOpacity: 0.9,
          strokeWeight: 2,
          fillColor: '#DF7401',
          fillOpacity: 0.5,
        });
        hullamos.setMap(map);

        //insert label as image
        var hullamosLabel = new google.maps.Marker({
          position: new google.maps.LatLng(47.336708, 19.275374),
          icon: "/img/hullamos_bal.png",
          map: map
        });

        //add hyperlink to polygon
        google.maps.event.addListener(hullamos, 'click', function () {
        window.location.href = 'https://turfgrass.site/add.php?field=31';
        });
        
        //add hyperlinks to label
        google.maps.event.addListener(hullamosLabel, 'click', function () {
        window.location.href = 'https://turfgrass.site/add.php?field=31';
        });

*/
        /// Hullamos jobb (mediteran)
        // Define the LatLng coordinates for the polygon's path.
        var hullamos2Coords = [
          {lat: 47.335318, lng: 19.275153},
          {lat: 47.334588, lng: 19.276087},
          {lat: 47.337985, lng: 19.280306},
          {lat: 47.338646, lng: 19.279254}
        ];

        // Construct the polygon.
        var hullamos2 = new google.maps.Polygon({
          paths: hullamos2Coords,
          strokeColor: '#04B404',
          strokeOpacity: 0.9,
          strokeWeight: 2,
          fillColor: '#04B404',
          fillOpacity: 0.7,
        });
        hullamos2.setMap(map);

        //insert label as image
        var hullamos2Label = new google.maps.Marker({
          position: new google.maps.LatLng(47.335840, 19.277435),
          icon: "/img/hullamos_jobb.png",
          map: map
        });

        //add hyperlink to polygon
        google.maps.event.addListener(hullamos2, 'click', function () {
        window.location.href = 'https://turfgrass.site/add.php?field=34';
        });
        
        //add hyperlinks to label
        google.maps.event.addListener(hullamos2Label, 'click', function () {
        window.location.href = 'https://turfgrass.site/add.php?field=34';
        });


        /*
        /// Hullamos bal rövid
        // Define the LatLng coordinates for the polygon's path.
        var hullamos3Coords = [
          {lat: 47.337465, lng: 19.277689},
          {lat: 47.338205, lng: 19.276578},
          {lat: 47.339566, lng: 19.278285},
          {lat: 47.338859, lng: 19.279435}
        ];

        // Construct the polygon.
        var hullamos3 = new google.maps.Polygon({
          paths: hullamos3Coords,
          strokeColor: '#04B404',
          strokeOpacity: 0.9,
          strokeWeight: 2,
          fillColor: '#04B404',
          fillOpacity: 0.7,
        });
        hullamos3.setMap(map);

        //insert label as image
        var hullamos3Label = new google.maps.Marker({
          position: new google.maps.LatLng(47.338401, 19.278063),
          icon: "/img/hullamos_rovid.png",
          map: map
        });

        //add hyperlink to polygon
        google.maps.event.addListener(hullamos3, 'click', function () {
        window.location.href = 'https://turfgrass.site/add.php?field=7';
        });
        
        //add hyperlinks to label
        google.maps.event.addListener(hullamos3Label, 'click', function () {
        window.location.href = 'https://turfgrass.site/add.php?field=7';
        });
*/

    
        /// Gabonas bal
        // Define the LatLng coordinates for the polygon's path.
        var gabonasCoords = [
          {lat: 47.330763, lng: 19.264813}, //rechts oben, UZS
          {lat: 47.329409, lng: 19.267699},
          {lat: 47.331360, lng: 19.270943},   
          {lat: 47.333273, lng: 19.268108}
        ];

        // Construct the polygon.
        var gabonas = new google.maps.Polygon({
          paths: gabonasCoords,
          strokeColor: '#FAAC58',
          strokeOpacity: 0.9,
          strokeWeight: 2,
          fillColor: '#FAAC58',
          fillOpacity: 0.7,
        });
        gabonas.setMap(map);

        //insert label as image
        var gabonasLabel = new google.maps.Marker({
          position: new google.maps.LatLng(47.330742, 19.268178),
          icon: "/img/gabonas.png",
          map: map
        });

        //add hyperlink to polygon
        google.maps.event.addListener(gabonas, 'click', function () {
        window.location.href = 'https://turfgrass.site/add.php?field=36';
        });
        
        //add hyperlinks to label
        google.maps.event.addListener(gabonasLabel, 'click', function () {
        window.location.href = 'https://turfgrass.site/add.php?field=36';
        });
/*
        /// Gabonas (kis rész csarnoknal)
        // Define the LatLng coordinates for the polygon's path.
        var gabonasCoords = [
          {lat: 47.333845, lng: 19.266058},
          {lat: 47.334505, lng: 19.265100},
          {lat: 47.335008, lng: 19.265698},
          {lat: 47.334281, lng: 19.266726},
        ];

        // Construct the polygon.
        var gabonas = new google.maps.Polygon({
          paths: gabonasCoords,
          strokeColor: '#013ADF',
          strokeOpacity: 0.9,
          strokeWeight: 2,
          fillColor: '#013ADF',
          fillOpacity: 0.7,
        });
        gabonas.setMap(map);

        //insert label as image
        var gabonasLabel = new google.maps.Marker({
          position: new google.maps.LatLng(47.334170, 19.265985),
          icon: "/img/gabonas.png",
          map: map
        });

        //add hyperlink to polygon
        google.maps.event.addListener(gabonas, 'click', function () {
        window.location.href = 'https://turfgrass.site/add.php?field=6';
        });
        
        //add hyperlinks to label
        google.maps.event.addListener(gabonasLabel, 'click', function () {
        window.location.href = 'https://turfgrass.site/add.php?field=6';
        });
*/
        
        /// Kettes kut
        // Define the LatLng coordinates for the polygon's path.
        var ketkutCoords = [
          {lat: 47.329516, lng: 19.273542},
          {lat: 47.328190, lng: 19.275381},
          {lat: 47.324613, lng: 19.269391},
          {lat: 47.325914, lng: 19.267956}
        ];

        // Construct the polygon.
        var ketkut = new google.maps.Polygon({
          paths: ketkutCoords,
          strokeColor: '#FAAC58',
          strokeOpacity: 0.9,
          strokeWeight: 2,
          fillColor: '#FAAC58',
          fillOpacity: 0.7,
        });
        ketkut.setMap(map);

        //insert label as image
        var ketkutLabel = new google.maps.Marker({
          position: new google.maps.LatLng(47.326867, 19.270985),
          icon: "/img/ketkut.png",
          map: map
        });

        //add hyperlink to polygon
        google.maps.event.addListener(ketkut, 'click', function () {
        window.location.href = 'https://turfgrass.site/add.php?field=40';
        });
        
        //add hyperlinks to label
        google.maps.event.addListener(ketkutLabel, 'click', function () {
        window.location.href = 'https://turfgrass.site/add.php?field=40';
        });
        

        /// Uj kut 1
        // Define the LatLng coordinates for the polygon's path.
        var ujkutCoords = [
          {lat: 47.326372, lng: 19.272481},
          {lat: 47.328196, lng: 19.275401},
          {lat: 47.327664, lng: 19.276065},
          {lat: 47.325930, lng: 19.273148}
        ];

        // Construct the polygon.
        var ujkut = new google.maps.Polygon({
          paths: ujkutCoords,
          strokeColor: '#FAAC58',
          strokeOpacity: 0.9,
          strokeWeight: 2,
          fillColor: '#FAAC58',
          fillOpacity: 0.7,
        });
        ujkut.setMap(map);

        //insert label as image
        var ujkutLabel = new google.maps.Marker({
          position: new google.maps.LatLng(47.326855, 19.274452),
          icon: "/img/ujkut.png",
          map: map
        });

        //add hyperlink to polygon
        google.maps.event.addListener(ujkut, 'click', function () {
        window.location.href = 'https://turfgrass.site/add.php?field=37';
        });
        
        //add hyperlinks to label
        google.maps.event.addListener(ujkutLabel, 'click', function () {
        window.location.href = 'https://turfgrass.site/add.php?field=37';
        });


        /// Uj kut 2
        // Define the LatLng coordinates for the polygon's path.
        var ujkutCoords = [
          {lat: 47.325488, lng: 19.271029},
          {lat: 47.324531, lng: 19.272288},
          {lat: 47.323623, lng: 19.270754},
          {lat: 47.324577, lng: 19.269427}
        ];

        // Construct the polygon.
        var ujkut = new google.maps.Polygon({
          paths: ujkutCoords,
          strokeColor: '#FAAC58',
          strokeOpacity: 0.9,
          strokeWeight: 2,
          fillColor: '#FAAC58',
          fillOpacity: 0.7,
        });
        ujkut.setMap(map);

        //insert label as image
        var ujkutLabel = new google.maps.Marker({
          position: new google.maps.LatLng(47.324416, 19.270640),
          icon: "/img/ujkut.png",
          map: map
        });

        //add hyperlink to polygon
        google.maps.event.addListener(ujkut, 'click', function () {
        window.location.href = 'https://turfgrass.site/add.php?field=37';
        });
        
        //add hyperlinks to label
        google.maps.event.addListener(ujkutLabel, 'click', function () {
        window.location.href = 'https://turfgrass.site/add.php?field=37';
        });



        /// Golfos
        // Define the LatLng coordinates for the polygon's path.
        var golfosCoords = [
          {lat: 47.329179, lng: 19.267519},
          {lat: 47.331288, lng: 19.271026},
          {lat: 47.330252, lng: 19.272424},
          {lat: 47.328516, lng: 19.269685},
          {lat: 47.328784, lng: 19.269184},
          {lat: 47.328370, lng: 19.268567}
        ];

        // Construct the polygon.
        var golfos = new google.maps.Polygon({
          paths: golfosCoords,
          strokeColor: '#DF7401',
          strokeOpacity: 0.9,
          strokeWeight: 2,
          fillColor: '#DF7401',
          fillOpacity: 0.7,
        });
        golfos.setMap(map);

        //insert label as image
        var golfosLabel = new google.maps.Marker({
          position: new google.maps.LatLng(47.329352, 19.270033),
          icon: "/img/golfos.png",
          map: map
        });

        //add hyperlink to polygon
        google.maps.event.addListener(golfos, 'click', function () {
        window.location.href = 'https://turfgrass.site/add.php?field=26';
        });
        
        //add hyperlinks to label
        google.maps.event.addListener(golfosLabel, 'click', function () {
        window.location.href = 'https://turfgrass.site/add.php?field=26';
        });


/*
        /// Rozsos
        // Define the LatLng coordinates for the polygon's path.
        var rozsosCoords = [
          {lat: 47.323266, lng: 19.271102},
          {lat: 47.326875, lng: 19.277193},
          {lat: 47.326439, lng: 19.277804},
          {lat: 47.322777, lng: 19.271704},
        ];

        // Construct the polygon.
        var rozsos = new google.maps.Polygon({
          paths: rozsosCoords,
          strokeColor: '#FAAC58',
          strokeOpacity: 0.9,
          strokeWeight: 2,
          fillColor: '#FAAC58',
          fillOpacity: 0.7,
        });
        rozsos.setMap(map);

        //insert label as image
        var rozsosLabel = new google.maps.Marker({
          position: new google.maps.LatLng(47.323645, 19.273171),
          icon: "/img/rozsos.png",
          map: map
        });

        //add hyperlink to polygon
        google.maps.event.addListener(rozsos, 'click', function () {
        window.location.href = 'https://turfgrass.site/add.php?field=43';
        });
        
        //add hyperlinks to label
        google.maps.event.addListener(rozsosLabel, 'click', function () {
        window.location.href = 'https://turfgrass.site/add.php?field=43';
        });
*/
        
        /// Tóföld
        // Define the LatLng coordinates for the polygon's path.
        var tofoldCoords = [
          {lat: 47.331313, lng: 19.271219},
          {lat: 47.332207, lng: 19.272473},
          //{lat: 47.332681, lng: 19.273229},
          //{lat: 47.331616, lng: 19.274727},
          //{lat: 47.331017, lng: 19.273817},
          {lat: 47.329045, lng: 19.276579},
          {lat: 47.328297, lng: 19.275453},
        ];

        // Construct the polygon.
        var tofold = new google.maps.Polygon({
          paths: tofoldCoords,
          strokeColor: '#FAAC58',
          strokeOpacity: 0.9,
          strokeWeight: 2,
          fillColor: '#FAAC58',
          fillOpacity: 0.7,
        });
        tofold.setMap(map);

        //insert label as image
        var tofoldLabel = new google.maps.Marker({
          position: new google.maps.LatLng(47.331079, 19.272749),
          icon: "/img/tofold.png",
          map: map
        });

        //add hyperlink to polygon
        google.maps.event.addListener(tofold, 'click', function () {
        window.location.href = 'https://turfgrass.site/add.php?field=42';
        });
        
        //add hyperlinks to label
        google.maps.event.addListener(tofoldLabel, 'click', function () {
        window.location.href = 'https://turfgrass.site/add.php?field=42';
        });
        

        /*
        /// Tóföld tavaszi 2017 (összehajtott a Tóföld lucernaval)
        // Define the LatLng coordinates for the polygon's path.
        var tofold2Coords = [
          {lat: 47.332665, lng: 19.273277},
          {lat: 47.332896, lng: 19.273678},
          {lat: 47.329891, lng: 19.277963},
          {lat: 47.329643, lng: 19.277462},
        ];

        // Construct the polygon.
        var tofold2 = new google.maps.Polygon({
          paths: tofold2Coords,
          strokeColor: '#0B6121',
          strokeOpacity: 0.9,
          strokeWeight: 2,
          fillColor: '#0B6121',
          fillOpacity: 0.7,
        });
        tofold2.setMap(map);

        //insert label as image
        var tofold2Label = new google.maps.Marker({
          position: new google.maps.LatLng(47.330349, 19.276861),
          icon: "/img/tofold2.png",
          map: map
        });

        //add hyperlink to polygon
        google.maps.event.addListener(tofold2, 'click', function () {
        window.location.href = 'https://turfgrass.site/add.php?field=29';
        });
        
        //add hyperlinks to label
        google.maps.event.addListener(tofold2Label, 'click', function () {
        window.location.href = 'https://turfgrass.site/add.php?field=29';
        });
        */

/*
        /// Tóföld tavaszi 2018  (lucerna)
        // Define the LatLng coordinates for the polygon's path.
        
        var tofold2Coords = [
          {lat: 47.331017, lng: 19.273817},
          {lat: 47.331616, lng: 19.274727},
          {lat: 47.332665, lng: 19.273277},
          {lat: 47.333439, lng: 19.274102},
          {lat: 47.331254, lng: 19.277184},
          {lat: 47.331246, lng: 19.277213},
          {lat: 47.330850, lng: 19.276668},
          {lat: 47.329872, lng: 19.277865},
          {lat: 47.329079, lng: 19.276555},
        ];

        // Construct the polygon.
        var tofold2 = new google.maps.Polygon({
          paths: tofold2Coords,
          strokeColor: '#04B404',
          strokeOpacity: 0.9,
          strokeWeight: 2,
          fillColor: '#0B6121',
          fillOpacity: 0.7,
        });
        tofold2.setMap(map);

        //insert label as image
        var tofold2Label = new google.maps.Marker({
          position: new google.maps.LatLng(47.330606, 19.276566),
          icon: "/img/tofold3.png",
          map: map
        });

        //add hyperlink to polygon
        google.maps.event.addListener(tofold2, 'click', function () {
        window.location.href = 'https://turfgrass.site/add.php?field=29';
        });
       
        //add hyperlinks to label
        google.maps.event.addListener(tofold2Label, 'click', function () {
        window.location.href = 'https://turfgrass.site/add.php?field=29';
        });
*/
        /*
          /// Tóföld lucerna
        // Define the LatLng coordinates for the polygon's path.
        var tofold3Coords = [
          {lat: 47.332665, lng: 19.273277},
          {lat: 47.333439, lng: 19.274102},
          {lat: 47.331254, lng: 19.277184},
          {lat: 47.331246, lng: 19.277213},
          {lat: 47.330850, lng: 19.276668},
          {lat: 47.329899, lng: 19.277958},
          {lat: 47.329643, lng: 19.277462},
        ];

        // Construct the polygon.
        var tofold3 = new google.maps.Polygon({
          paths: tofold3Coords,
          strokeColor: '#0B6121',
          strokeOpacity: 0.9,
          strokeWeight: 2,
          fillColor: '#0B6121',
          fillOpacity: 0.7,
        });
        tofold3.setMap(map);

        //insert label as image
        var tofold3Label = new google.maps.Marker({
          position: new google.maps.LatLng(47.331688, 19.275665),
          icon: "/img/tofold3.png",
          map: map
        });

        //add hyperlink to polygon
        google.maps.event.addListener(tofold3, 'click', function () {
        window.location.href = 'https://turfgrass.site/add.php?field=29';
        });
        
        //add hyperlinks to label
        google.maps.event.addListener(tofold3Label, 'click', function () {
        window.location.href = 'https://turfgrass.site/add.php?field=29';
        });

  */
   
        /// Tanyaföld 1
        // Define the LatLng coordinates for the polygon's path.
        var tanjafoldCoords = [
          {lat: 47.345234, lng: 19.259995},
          {lat: 47.348241, lng: 19.264010},
          {lat: 47.346248, lng: 19.267063},
          {lat: 47.343258, lng: 19.263251},
        ];

        // Construct the polygon.
        var tanjafold = new google.maps.Polygon({
          paths: tanjafoldCoords,
          strokeColor: '#FAAC58',
          strokeOpacity: 0.9,
          strokeWeight: 2,
          fillColor: '#FAAC58',
          fillOpacity: 0.7,
        });
        tanjafold.setMap(map);

        //insert label as image
        var tanyafoldLabel = new google.maps.Marker({
          position: new google.maps.LatLng(47.345558, 19.263999),
          icon: "/img/tanyafold1.png",
          map: map
        });

        //add hyperlink to polygon
        google.maps.event.addListener(tanjafold, 'click', function () {
        window.location.href = 'https://turfgrass.site/add.php?field=41';
        });

        //add hyperlinks to label
        google.maps.event.addListener(tanyafoldLabel, 'click', function () {
        window.location.href = 'https://turfgrass.site/add.php?field=41';
        });
      

        /// Tanjaföld 2
        // Define the LatLng coordinates for the polygon's path.
        var tanyafoldCoords = [
          {lat: 47.343258, lng: 19.263251},
          {lat: 47.346248, lng: 19.267063},
          {lat: 47.344196, lng: 19.271289},
          {lat: 47.340296, lng: 19.268071},
        ];

        // Construct the polygon.
        var tanyafold = new google.maps.Polygon({
          paths: tanyafoldCoords,
          strokeColor: '#FAAC58',
          strokeOpacity: 0.9,
          strokeWeight: 2,
          fillColor: '#FAAC58',
          fillOpacity: 0.7,
        });
        tanyafold.setMap(map);

        //insert label as image
        var tanyafoldLabel = new google.maps.Marker({
          position: new google.maps.LatLng(47.342483, 19.267208),
          icon: "/img/tanyafold2.png",
          map: map
        });

        //add hyperlink to polygon
        google.maps.event.addListener(tanyafold, 'click', function () {
        window.location.href = 'https://turfgrass.site/add.php?field=33';
        });
        
        //add hyperlinks to label
        google.maps.event.addListener(tanyafoldLabel, 'click', function () {
        window.location.href = 'https://turfgrass.site/add.php?field=33';
        });

        /*
        /// Tanjaföld lucerna
        // Define the LatLng coordinates for the polygon's path.
        var tanjafoldCoords = [
          {lat: 47.343299, lng: 19.263186},
          {lat: 47.346262, lng: 19.267144},
          {lat: 47.344218, lng: 19.271259},
          {lat: 47.340277, lng: 19.268201},
        ];

        // Construct the polygon.
        var tanjafold = new google.maps.Polygon({
          paths: tanjafoldCoords,
          strokeColor: '#E6E6E6',
          strokeOpacity: 0.9,
          strokeWeight: 2,
          fillColor: '#E6E6E6',
          fillOpacity: 0.7,
        });
        tanjafold.setMap(map);

        //insert label as image
        var tanjafoldLabel = new google.maps.Marker({
          position: new google.maps.LatLng(47.342859, 19.268390),
          icon: "/img/tanjafold2.png",
          map: map
        });

        //add hyperlink to polygon
        google.maps.event.addListener(tanjafold, 'click', function () {
        window.location.href = 'https://turfgrass.site/add.php?field=19';
        });
        
        //add hyperlinks to label
        google.maps.event.addListener(tanjafoldLabel, 'click', function () {
        window.location.href = 'https://turfgrass.site/add.php?field=19';
        });
*/        

        /// Gabonas jobb (csarnok mellett)
        // Define the LatLng coordinates for the polygon's path.
        var gabonas2Coords = [
          {lat: 47.334290, lng: 19.266668}, //rechts oben, Straße
          {lat: 47.333273, lng: 19.268108}, //UZS
          {lat: 47.330763, lng: 19.264813},
          {lat: 47.331590, lng: 19.263437},
        ];

        // Construct the polygon.
        var gabonas2 = new google.maps.Polygon({
          paths: gabonas2Coords,
          strokeColor: '#DF7401',
          strokeOpacity: 0.9,
          strokeWeight: 2,
          fillColor: '#DF7401',
          fillOpacity: 0.7,
        });
        gabonas2.setMap(map);

        //insert label as image
        var gabonas2Label = new google.maps.Marker({
          position: new google.maps.LatLng(47.332306, 19.265914),
          icon: "/img/gabonas_jobb.png",
          map: map
        });

        //add hyperlink to polygon
        google.maps.event.addListener(gabonas2, 'click', function () {
        window.location.href = 'https://turfgrass.site/add.php?field=32';
        });
        
        //add hyperlinks to label
        google.maps.event.addListener(gabonas2Label, 'click', function () {
        window.location.href = 'https://turfgrass.site/add.php?field=32';
        });

/*
        /// Dunkel
        // Define the LatLng coordinates for the polygon's path.
        var dunkelCoords = [
          {lat: 47.328468, lng: 19.269686},
          {lat: 47.329734, lng: 19.271821},
          {lat: 47.329119, lng: 19.272916},
          {lat: 47.327729, lng: 19.270581},
        ];

        // Construct the polygon.
        var dunkel = new google.maps.Polygon({
          paths: dunkelCoords,
          strokeColor: '#8A0808',
          strokeOpacity: 0.9,
          strokeWeight: 2,
          fillColor: '#8A0808',
          fillOpacity: 0.7,
        });
        dunkel.setMap(map);

        //insert label as image
        var dunkelLabel = new google.maps.Marker({
          position: new google.maps.LatLng(47.328601, 19.271300),
          icon: "/img/dunkel.png",
          map: map
        });

        //add hyperlink to polygon
        google.maps.event.addListener(dunkel, 'click', function () {
        window.location.href = 'https://turfgrass.site/add.php?field=28';
        });
        
        //add hyperlinks to label
        google.maps.event.addListener(dunkelLabel, 'click', function () {
        window.location.href = 'https://turfgrass.site/add.php?field=28';
        });
*/
/*
        /// Rozsos jobb
        // Define the LatLng coordinates for the polygon's path.
        var rozsosfiatalCoords = [
          {lat: 47.327654, lng: 19.276089},
          {lat: 47.326882, lng: 19.277150},
          {lat: 47.323305, lng: 19.271047},
          {lat: 47.323625, lng: 19.270786},
          {lat: 47.324567, lng: 19.272333},
          {lat: 47.325538, lng: 19.271105},
          {lat: 47.326396, lng: 19.272457},
          {lat: 47.325843, lng: 19.273158},
        ];

        // Construct the polygon.
        var rozsosfiatal = new google.maps.Polygon({
          paths: rozsosfiatalCoords,
          strokeColor: '#DF7401',
          strokeOpacity: 0.9,
          strokeWeight: 2,
          fillColor: '#DF7401',
          fillOpacity: 0.7,
        });
        rozsosfiatal.setMap(map);

        //insert label as image
        var rozsosfiatalLabel = new google.maps.Marker({
          position: new google.maps.LatLng(47.325073, 19.273264),
          icon: "/img/rozsos_jobb.png",
          map: map
        });

        //add hyperlink to polygon
        google.maps.event.addListener(rozsosfiatal, 'click', function () {
        window.location.href = 'https://turfgrass.site/add.php?field=30';
        });
        
        //add hyperlinks to label
        google.maps.event.addListener(rozsosfiatalLabel, 'click', function () {
        window.location.href = 'https://turfgrass.site/add.php?field=30';
        });
*/
        /*
        /// Mediterran jobb 1
        // Define the LatLng coordinates for the polygon's path.
        var medregiCoords = [
          {lat: 47.328257, lng: 19.275485},
          {lat: 47.328640, lng: 19.276112},
          {lat: 47.326440, lng: 19.279208},
          {lat: 47.326024, lng: 19.278587},
        ];

        // Construct the polygon.
        var medregi = new google.maps.Polygon({
          paths: medregiCoords,
          strokeColor: '#04B404',
          strokeOpacity: 0.9,
          strokeWeight: 2,
          fillColor: '#04B404',
          fillOpacity: 0.7,
        });
        medregi.setMap(map);

        //insert label as image
        var medregiLabel = new google.maps.Marker({
          position: new google.maps.LatLng(47.326453, 19.278247),
          icon: "/img/med_jobb.png",
          map: map
        });

        //add hyperlink to polygon
        google.maps.event.addListener(medregi, 'click', function () {
        window.location.href = 'https://turfgrass.site/add.php?field=21';
        });
        
        //add hyperlinks to label
        google.maps.event.addListener(medregiLabel, 'click', function () {
        window.location.href = 'https://turfgrass.site/add.php?field=21';
        });


        /// Mediterran jobb 2
        // Define the LatLng coordinates for the polygon's path.
        var medregiCoords = [
          {lat: 47.328884, lng: 19.276485},
          {lat: 47.329317, lng: 19.277125},
          {lat: 47.327193, lng: 19.280188},
          {lat: 47.326760, lng: 19.279599},
        ];

        // Construct the polygon.
        var medregi = new google.maps.Polygon({
          paths: medregiCoords,
          strokeColor: '#04B404',
          strokeOpacity: 0.9,
          strokeWeight: 2,
          fillColor: '#04B404',
          fillOpacity: 0.7,
        });
        medregi.setMap(map);

        //insert label as image
        var medregiLabel = new google.maps.Marker({
          position: new google.maps.LatLng(47.327236, 19.279302),
          icon: "/img/med_jobb.png",
          map: map
        });

        //add hyperlink to polygon
        google.maps.event.addListener(medregi, 'click', function () {
        window.location.href = 'https://turfgrass.site/add.php?field=21';
        });
        
        //add hyperlinks to label
        google.maps.event.addListener(medregiLabel, 'click', function () {
        window.location.href = 'https://turfgrass.site/add.php?field=21';
        });
        */


        /// Mediterran bal 1
        // Define the LatLng coordinates for the polygon's path.
        var medCoords = [
          {lat: 47.328210, lng: 19.275535},
          {lat: 47.329416, lng: 19.277315},
          {lat: 47.327286, lng: 19.280403},
          {lat: 47.326031, lng: 19.278549},
        ];

        // Construct the polygon.
        var med = new google.maps.Polygon({
          paths: medCoords,
          strokeColor: '#04B404',
          strokeOpacity: 0.9,
          strokeWeight: 2,
          fillColor: '#04B404',
          fillOpacity: 0.7,
        });
        med.setMap(map);

        //insert label as image
        var medLabel = new google.maps.Marker({
          position: new google.maps.LatLng(47.327439, 19.278550),
          icon: "/img/med.png",
          map: map
        });

        //add hyperlink to polygon
        google.maps.event.addListener(med, 'click', function () {
        window.location.href = 'https://turfgrass.site/add.php?field=38';
        });
        
        //add hyperlinks to label
        google.maps.event.addListener(medLabel, 'click', function () {
        window.location.href = 'https://turfgrass.site/add.php?field=38';
        });

        /*
        /// Mediterran bal 2
        // Define the LatLng coordinates for the polygon's path.
        var medCoords = [
          {lat: 47.329317, lng: 19.277125},
          {lat: 47.329481, lng: 19.277365},
          {lat: 47.327328, lng: 19.280437},
          {lat: 47.327193, lng: 19.280188},
        ];

        // Construct the polygon.
        var med = new google.maps.Polygon({
          paths: medCoords,
          strokeColor: '#0B6121',
          strokeOpacity: 0.9,
          strokeWeight: 2,
          fillColor: '#0B6121',
          fillOpacity: 0.7,
        });
        med.setMap(map);

        //insert label as image
        var medLabel = new google.maps.Marker({
          position: new google.maps.LatLng(47.328691, 19.278083),
          icon: "/img/med_bal.png",
          map: map
        });

        //add hyperlink to polygon
        google.maps.event.addListener(med, 'click', function () {
        window.location.href = 'https://turfgrass.site/add.php?field=18';
        });
        
        //add hyperlinks to label
        google.maps.event.addListener(medLabel, 'click', function () {
        window.location.href = 'https://turfgrass.site/add.php?field=18';
        });
        */


        /// Jónásföld
        // Define the LatLng coordinates for the polygon's path.
        var jonasCoords = [
          {lat: 47.342773, lng: 19.270608}, // northern corner, clockwise
          {lat: 47.338883, lng: 19.277203},
          {lat: 47.335847, lng: 19.273262},
          {lat: 47.339646, lng: 19.268162},
        ];

        // Construct the polygon.
        var jonas = new google.maps.Polygon({
          paths: jonasCoords,
          strokeColor: '#FAAC58',
          strokeOpacity: 0.9,
          strokeWeight: 2,
          fillColor: '#FAAC58',
          fillOpacity: 0.7,
        });
        jonas.setMap(map);

        //insert label as image
        var jonasLabel = new google.maps.Marker({
          position: new google.maps.LatLng(47.339442, 19.272413),
          icon: "/img/jonas.png",
          map: map
        });

        //add hyperlink to polygon
        google.maps.event.addListener(jonas, 'click', function () {
        window.location.href = 'https://turfgrass.site/add.php?field=35';
        });
        
        //add hyperlinks to label
        google.maps.event.addListener(jonasLabel, 'click', function () {
        window.location.href = 'https://turfgrass.site/add.php?field=35';
        });

        /// Next field
    
    }


    </script>

    <script async defer
      src="https://maps.googleapis.com/maps/api/js?key=AIzaSyCrVyRGBuSl5ICq5zPeou3zYIKEFhYQZnA&callback=initMap">
    </script>

<br><br><br><br><br>


