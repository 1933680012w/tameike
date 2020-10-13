var map_markers = [];

function map() {
    var map = L.map('map').setView([mapPlaces[mapPlaces.length - 1].lat, mapPlaces[mapPlaces.length - 1].lon], 12);
    L.tileLayer('http://{s}.tile.osm.org/{z}/{x}/{y}.png', { attribution: '&copy; <a href="http://osm.org/copyright">OpenStreetMap</a> contributors' }).addTo(map);

    for (var i_for = 0; i_for < mapPlaces.length; i_for++) {
        var color = ''
        if (mapPlaces[i_for].waterLevel < mapPlaces[i_for].overflowLevel) {
            color = 'green'
        }
        else {
            color = 'red'
        }

        var greenIcon = new L.Icon({
            iconUrl: '../img/marker-icon-2x-' + color + '.png',
            shadowUrl: '../img/marker-shadow.png',
            iconSize: [25, 41],
            iconAnchor: [12, 41],
            popupAnchor: [1, -34],
            shadowSize: [41, 41]
        });

        map_markers[i_for] = L.marker([mapPlaces[i_for].lat, mapPlaces[i_for].lon], { icon: greenIcon }).addTo(map).bindPopup(mapPlaces[i_for].name).openPopup().on('click', function (e) { mapICONclickEvent(e); });
        map_markers[i_for].code = mapPlaces[i_for].id;
        map_markers[i_for].place_name = mapPlaces[i_for].name;
        place_name = mapPlaces[i_for].name;

        var elements = document.getElementsByClassName("leaflet-popup-content");
        elements[0].innerHTML += '</br><a href="./index.php?id=' + mapPlaces[i_for].id + '">詳細</a></br>' +
            'N[' + mapPlaces[i_for].waterLevel + ']</br>' +
            '1[' + mapPlaces[i_for].pre_1 + ']</br>' +
            '2[' + mapPlaces[i_for].pre_2 + ']</br>';
    }
}

function mapICONclickEvent(e) {
    mapICONView(e.target.code);
}

function mapICONView(code) {
    var elements = document.getElementsByClassName("leaflet-popup-content");

    elements[0].innerHTML += '</br><a href="./index.php?id=' + mapPlaces[code - 1].id + '">詳細</a></br>' +
        'N[' + mapPlaces[code - 1].waterLevel + ']</br>' +
        '1[' + mapPlaces[code - 1].pre_1 + ']</br>' +
        '2[' + mapPlaces[code - 1].pre_2 + ']</br>';
}

var load_event = function () {
    window.onload = function () {
        var map_height = 440;
        document.getElementById('map').style.height = map_height + "px";
        map();
    };

}

var $script = $('#tameikeMap');
var mapPlaces = JSON.parse($script.attr('data-param'));
console.log(mapPlaces);

load_event();
