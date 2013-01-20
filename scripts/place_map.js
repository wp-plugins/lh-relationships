function bounds_handler(var1) {

for (var i = 0; i < var1.results.bindings.length; i++) { 

var myLatlng = new google.maps.LatLng(var1.results.bindings[i].lat.value, var1.results.bindings[i].lng.value);

start_marker = new google.maps.Marker({
 position: myLatlng,
title: var1.results.bindings[i].title.value,
map: map
            });

google.maps.event.addListener(start_marker, "click", function() {
    window.location = var1.results.bindings[1].s.value;
});



}


}


function loadjscssfile(filename, filetype){
 if (filetype=="js"){ //if filename is a external JavaScript file
  var fileref=document.createElement('script')
  fileref.setAttribute("type","text/javascript")
  fileref.setAttribute("src", filename)
 }
 else if (filetype=="css"){ //if filename is an external CSS file
  var fileref=document.createElement("link")
  fileref.setAttribute("rel", "stylesheet")
  fileref.setAttribute("type", "text/css")
  fileref.setAttribute("href", filename)
fileref.setAttribute("media", "print")
 }
 if (typeof fileref!="undefined")
  document.getElementsByTagName("head")[0].appendChild(fileref)
}



function getElements() {

var x=document.getElementsByTagName("article");

return x[0].getAttribute('itemid');

 }




function json_handler(var1) {


expected = var1;

}

function final_test(){

var divArray = document.getElementById('map_canvas');

divArray.style.width = '400px';
divArray.style.height = '400px';



var myLatlng = new google.maps.LatLng(expected.results.bindings[0].lat.value, expected.results.bindings[0].lng.value);

        var myOptions = {
          center: myLatlng,
          zoom: 8,
          mapTypeId: google.maps.MapTypeId.ROADMAP
        };


map = new google.maps.Map(document.getElementById("map_canvas"),
            myOptions);

start_marker = new google.maps.Marker({
 position: myLatlng,
title: 'Drag Me',
map: map
            });

google.maps.event.addListener(map, "idle", function() {
    

bar1 = 'http://shawfactor.com/wp-content/plugins/lh-tools/?query=';



var sparqle_query = 'SELECT * WHERE { ?s <http://rdfs.org/sioc/ns#topic> ?o . ?s <http://purl.org/dc/elements/1.1/title> ?title . ?o <http://www.w3.org/1999/02/22-rdf-syntax-ns#type> <http://dbpedia.org/ontology/place> . ?o <http://www.w3.org/2003/01/geo/wgs84_pos#lat> ?lat . ?o <http://www.w3.org/2003/01/geo/wgs84_pos#long> ?lng . FILTER (?lat >= $sw_latitude) . FILTER (?lat <= $ne_latitude) . FILTER (?lng >= $sw_longitude) . FILTER (?lng <= $ne_longitude)}';

var sparqle_query = sparqle_query.replace("$sw_latitude", map.getBounds().getSouthWest().lat());
var sparqle_query = sparqle_query.replace("$ne_latitude", map.getBounds().getNorthEast().lat());
var sparqle_query = sparqle_query.replace("$sw_longitude", map.getBounds().getSouthWest().lng());
var sparqle_query = sparqle_query.replace("$ne_longitude", map.getBounds().getNorthEast().lng());

var sparqle_query = encodeURIComponent(sparqle_query);

bar2 = '&output=json&callback=bounds_handler';

bar = bar1 + sparqle_query + bar2;

loadjscssfile(bar, 'js');


});


}


function action_JSON(url) {

JIT.startChain().
loadOnce(url, function(){ return (typeof(expected) != "undefined") ?  true : false;}).
onComplete(function() {

if (typeof(expected.results.bindings[0]) != "undefined"){



loadjscssfile('http://maps.google.com/maps/api/js?sensor=true&callback=final_test', 'js');



}

}



); 


}


subject = getElements();

endpoint = 'http://shawfactor.com/wp-content/plugins/lh-tools/?output=json&callback=json_handler&query=';

sparqle_query = 'SELECT * WHERE { <$subject_var> <http://rdfs.org/sioc/ns#topic> ?o . ?o <http://www.w3.org/1999/02/22-rdf-syntax-ns#type> <http://dbpedia.org/ontology/place> . ?o <http://www.w3.org/2003/01/geo/wgs84_pos#lat> ?lat . ?o <http://www.w3.org/2003/01/geo/wgs84_pos#long> ?lng }';

sparqle_query = sparqle_query.replace("$subject_var", subject);

sparqle_query = encodeURIComponent(sparqle_query);

foo = endpoint + sparqle_query;


action_JSON(foo);