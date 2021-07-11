(function($) {
 
  /*
  *  render_map
  *
  *  This function will render a Google Map onto the selected jQuery element
  *
  *  @type function
  *  @date 8/11/2013
  *  @since 4.3.0
  *
  *  @param $el (jQuery element)
  *  @return n/a
  */
   
  function render_map( $el ) {
   
     // var
     var $markers = $el.find('.marker');
     
     // vars
     var args = {
       zoom : 14, 
       maxZoom: 20,
       zoomOnClick: false,
       disableDefaultUI: true,
       center: new google.maps.LatLng(48.864716,2.349014),
       mapTypeId : google.maps.MapTypeId.ROADMAP,
      styles: [{"featureType":"water","elementType":"geometry","stylers":[{"color":"#e9e9e9"},{"lightness":17}]},{"featureType":"landscape","elementType":"geometry","stylers":[{"color":"#f5f5f5"},{"lightness":20}]},{"featureType":"road.highway","elementType":"geometry.fill","stylers":[{"color":"#ffffff"},{"lightness":17}]},{"featureType":"road.highway","elementType":"geometry.stroke","stylers":[{"color":"#ffffff"},{"lightness":29},{"weight":0.2}]},{"featureType":"road.arterial","elementType":"geometry","stylers":[{"color":"#ffffff"},{"lightness":18}]},{"featureType":"road.local","elementType":"geometry","stylers":[{"color":"#ffffff"},{"lightness":16}]},{"featureType":"poi","elementType":"geometry","stylers":[{"color":"#f5f5f5"},{"lightness":21}]},{"featureType":"poi.park","elementType":"geometry","stylers":[{"color":"#dedede"},{"lightness":21}]},{"elementType":"labels.text.stroke","stylers":[{"visibility":"on"},{"color":"#ffffff"},{"lightness":16}]},{"elementType":"labels.text.fill","stylers":[{"saturation":36},{"color":"#333333"},{"lightness":40}]},{"elementType":"labels.icon","stylers":[{"visibility":"off"}]},{"featureType":"transit","elementType":"geometry","stylers":[{"color":"#f2f2f2"},{"lightness":19}]},{"featureType":"administrative","elementType":"geometry.fill","stylers":[{"color":"#fefefe"},{"lightness":20}]},{"featureType":"administrative","elementType":"geometry.stroke","stylers":[{"color":"#fefefe"},{"lightness":17},{"weight":1.2}]}]
     };
  
     
     // create map          
     var map = new google.maps.Map( $el[0], args);
     
     // add a markers reference
     map.markers = [];
	  
     
     // add markers
     $markers.each(function(){
     
         add_marker( $(this), map );
     
     });
     
     markerCluster( map.markers, map )

     // center map
     center_map( map );
  
     //var markerCluster = new MarkerClusterer(map, markers, options);
    }
     
  // create info window outside of each - then tell that singular infowindow to swap content based on click
  var infowindow = new google.maps.InfoWindow({
  content     : '' 
  });
  
  /*
  *  add_marker
  *
  *  This function will add a marker to the selected Google Map
  *
  *  @type    function
  *  @date    8/11/2013
  *  @since   4.3.0
  *
  *  @param   $marker (jQuery element)
  *  @param   map (Google Map object)
  *  @return  n/a
  */
  
  function add_marker( $marker, map ) {
  
  // var
  var latlng = new google.maps.LatLng( $marker.attr('data-lat'), $marker.attr('data-lng') );
  
 
  
  // create marker
  
  var marker = new google.maps.Marker({
      position    : latlng,
      map         : map,
     // title:"P1220214 8.JPG",
     markercard:$marker.html(),
      icon: "/wp-content/themes/colibrity-wp-theme-b4/images/geo3.png",
  });
  /*
 var marker = new google.maps.Circle({
   position:latlng,
   center: latlng,
   map:map,
   radius: 100, //радиус метров
   strokeColor: "#B40404",
   strokeOpacity: 0.6,
   strokeWeight: 2,
   fillColor: "#B40404",
   fillOpacity: 0.6
 });
 */
  // add to array
  map.markers.push( marker );
  
  // if marker contains HTML, add it to an infoWindow
  if( $marker.html() )
  {
  
      // show info window when marker is clicked & close other markers
      google.maps.event.addListener(marker, 'click', function() {
          //swap content of that singular infowindow
                  infowindow.setContent($marker.html());
                  infowindow.open(map, marker);
      });
  
      // close info window when map is clicked
           google.maps.event.addListener(map, 'click', function(event) {
              if (infowindow) {
                  infowindow.close(); }
              }); 
  
  }
  
  }
  
     
    /*
    *  center_map
    *
    *  This function will center the map, showing all markers attached to this map
    *
    *  @type function
    *  @date 8/11/2013
    *  @since 4.3.0
    *
    *  @param map (Google Map object)
    *  @return n/a
    */
     
    function center_map( map ) {
     
     // vars
     var bounds = new google.maps.LatLngBounds();
     
     // loop through all markers and create bounds
     $.each( map.markers, function( i, marker ){
     
     var latlng = new google.maps.LatLng( marker.position.lat(), marker.position.lng() );
     
     bounds.extend( latlng );
     
     });
     // only 1 marker?
     if (map.markers.length < 1 ) {
      // set center of map
      map.setCenter(new google.maps.LatLng(48.864716,2.349014));
    //map.setCenter({lat: -34, lng: 151}); 
      map.setZoom(10);
    }
     else if( map.markers.length == 1 )
     {
     // set center of map
         map.setCenter( bounds.getCenter() );
         map.setZoom( 14 );
     }
     else
     {
     // fit to bounds
     map.fitBounds( bounds );
 
     }
     


    }
    var clusterStyles = [
      {
        textColor: 'white',
        url: '/wp-content/themes/colibrity-wp-theme-b4/images/geo1.png',
        height: 40,
        width: 30
      },
     {
        textColor: 'white',
        url: '/wp-content/themes/colibrity-wp-theme-b4/images/geo1.png',
        height: 40,
        width: 30
      },
     {
        textColor: 'white',
        url: '/wp-content/themes/colibrity-wp-theme-b4/images/geo1.png',
        height: 40,
        width: 30
      }
    ];
    var options_markerclusterer = {
      gridSize: 20,
      maxZoom: 20,
      styles: clusterStyles,
      zoomOnClick: false,
     // imagePath:  '/wp-content/colibrity-theme-b4/images/geo2.png?raw=true'
    //  imagePath:  'https://github.com/googlemaps/js-markerclustererplus/blob/main/images/m3.png?raw=true'
  };
    function markerCluster(markers, map, $marker) {
      var markerCluster = new MarkerClusterer(map, markers,options_markerclusterer);
     


      google.maps.event.addListener(markerCluster, 'clusterclick', function(cluster) {

        var markers = cluster.getMarkers();
      
       var array = [];
    
        var num = 0;
		  let arrayString = '';
      
        for(i = 0; i < markers.length; i++) {
         
            num++;
      //      array.push(markers[i].getTitle());
            array.push(markers[i].markercard);
        }
		  array.forEach((item, i) => {
			  arrayString += item.toString();
		  });
        if (map.getZoom() <= markerCluster.getMaxZoom()) {
           infowindow.setContent("<div class='map-arrow'></div><div class='map-arrow'></div>"+arrayString);
// 			infowindow.setContent("<div class='map-arrow'></div><div class='map-arrow'></div>");
           infowindow.setPosition(cluster.getCenter());
           infowindow.open(map);
        }  
		  
		  let cartMap, mapArrow;
			setTimeout(() => {
				cartMap = document.querySelectorAll('.map-col__views-map');
				mapArrow = document.querySelectorAll('.map-arrow');
				let imageActive = 0;
				cartMap.forEach((item, i) => {
					if (i != imageActive) {
						item.style.display = 'none';
					};					
				});
				
				mapArrow.forEach((item, i) => {
					item.addEventListener('click', () => {
						if (i === 0 & imageActive != 0) {
							imageActive = imageActive - 1;
						} else if (i === 1 & imageActive < cartMap.length-1) {
							imageActive = imageActive + 1;				
						} else if (i === 1 & imageActive == cartMap.length-1) {
							imageActive = 0;
						} else {
							imageActive = cartMap.length-1;
						}
						
						cartMap.forEach((item, i) => {
								item.style.display = 'none';									
						});
						
						cartMap[imageActive].style.display = 'flex';
						
					});
				});
					
				


			}, 10);    
			
      
        
      });
      

    }
     
    $(document).ready(function(){
     
     $('.acf-map').each(function(){

    

     render_map( $(this) );
     
     });
     
    }); 
  
  })(jQuery);