function createMarker(pos, title, thelink) {

var image = 'http://labs.google.com/ridefinder/images/mm_20_red.png';

    var marker = new google.maps.Marker({       
        position: pos, 
      	map: map,  
        title: title,
icon: image
     
    }); 

    google.maps.event.addListener(marker, 'click', function() { 
          window.location = thelink;
    }); 
    return marker;  
}


function bounds_handler(var1) {

for (var i = 0; i < var1.results.bindings.length; i++) {

var myLatlng = new google.maps.LatLng(var1.results.bindings[i].lat.value, var1.results.bindings[i].lng.value);

createMarker(myLatlng, var1.results.bindings[i].title.value, var1.results.bindings[i].s.value)



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


var txt = document.createTextNode("View nearbye articles");
var heading  = document.createElement('h2');
heading.appendChild(txt);
divArray.parentNode.insertBefore(heading,divArray);

var otherspan  = document.createElement('span');

var artimg  = document.createElement('img');
artimg.setAttribute('src', 'http://maps.google.com/mapfiles/ms/icons/red-dot.png');
var txt = document.createTextNode("article location");
otherspan.appendChild(artimg);
otherspan.appendChild(txt);

var artimg  = document.createElement('img');
artimg.setAttribute('src', 'http://labs.google.com/ridefinder/images/mm_20_red.png');
var txt = document.createTextNode("other articles");
otherspan.appendChild(artimg);
otherspan.appendChild(txt);

divArray.parentNode.insertBefore(otherspan,divArray.nextSibling);




divArray.style.width = '300px';
divArray.style.height = '300px';



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
title: 'Article Location',
map: map
});

google.maps.event.addListener(map, "idle", function() {
    

bar1 = 'http://shawfactor.com/wp-content/plugins/lh-tools/?query=';



var sparqle_query = 'SELECT ?s ?title ?lat ?lng COUNT(?tag) AS ?tags WHERE {<$subject> <http://rdfs.org/sioc/ns#topic> ?tag . ?s <http://rdfs.org/sioc/ns#topic> ?tag . ?s <http://rdfs.org/sioc/ns#topic> ?o . ?s <http://purl.org/dc/elements/1.1/title> ?title . ?o <http://www.w3.org/1999/02/22-rdf-syntax-ns#type> <http://dbpedia.org/ontology/place> . ?o <http://www.w3.org/2003/01/geo/wgs84_pos#lat> ?lat . ?o <http://www.w3.org/2003/01/geo/wgs84_pos#long> ?lng . FILTER (?lat >= $sw_latitude) . FILTER (?lat <= $ne_latitude) . FILTER (?lng >= $sw_longitude) . FILTER (?lng <= $ne_longitude) . FILTER(str(?s)!="$subject") } GROUP BY ?s order by desc(?tags) LIMIT 10 OFFSET 0';


var sparqle_query = sparqle_query.replace("$sw_latitude", map.getBounds().getSouthWest().lat());
var sparqle_query = sparqle_query.replace("$ne_latitude", map.getBounds().getNorthEast().lat());
var sparqle_query = sparqle_query.replace("$sw_longitude", map.getBounds().getSouthWest().lng());
var sparqle_query = sparqle_query.replace("$ne_longitude", map.getBounds().getNorthEast().lng());
var sparqle_query = sparqle_query.replace("$subject", subject);
var sparqle_query = sparqle_query.replace("$subject", subject);

var sparqle_query = encodeURIComponent(sparqle_query);

bar2 = '&output=json&callback=bounds_handler';

bar = bar1 + sparqle_query + bar2;

loadjscssfile(bar, 'js');


});


}


function action_JSON(url) {

document.getElementById('map_canvas').innerHTML = url;

LazyLoad.js(url, function () {

if (typeof(expected) != "undefined"){

if (typeof(expected.results.bindings[0]) != "undefined"){

loadjscssfile('http://maps.google.com/maps/api/js?sensor=true&callback=final_test', 'js');

}

}

});


}


subject = getElements();

endpoint = 'http://shawfactor.com/wp-content/plugins/lh-tools/?output=json&callback=json_handler&query=';

sparqle_query = 'SELECT * WHERE { <$subject_var> <http://rdfs.org/sioc/ns#topic> ?o . ?o <http://www.w3.org/1999/02/22-rdf-syntax-ns#type> <http://dbpedia.org/ontology/place> . ?o <http://www.w3.org/2003/01/geo/wgs84_pos#lat> ?lat . ?o <http://www.w3.org/2003/01/geo/wgs84_pos#long> ?lng }';

sparqle_query = sparqle_query.replace("$subject_var", subject);

sparqle_query = encodeURIComponent(sparqle_query);

foo = endpoint + sparqle_query;


action_JSON(foo);