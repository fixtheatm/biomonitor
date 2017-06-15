<!-- Start of common_single_map.blade.php -->
<script type="text/javascript" src="http://maps.google.com/maps/api/js?sensor=true"></script>
<script type="text/javascript" src="/js/ui/jquery.ui.map.js"></script>

<script type="text/javascript">
/*global google */
/*jslint browser, devel */
(function () {
    "use strict";
    var myLatLng = {lat: {{ $bioreactor->latitude }}, lng: {{ $bioreactor->longitude }}};

    var map = new google.maps.Map(document.getElementById("map_canvas"), {
        center: myLatLng,
        mapTypeId: google.maps.MapTypeId.ROADMAP,
        zoom: 5
    });
    var marker = new google.maps.Marker({
        map: map,
        position: myLatLng,
        title: "BioReactor"
    });
}());// anonymous function()
</script>
<!-- End of common_single_map.blade.php -->
