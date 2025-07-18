<!doctype html>
<html>
  <head>
    <link rel="stylesheet" type="text/css" href="./map.css" />
    <script type="module" src="./index.js"></script>
     <title>Add Map</title>
  </head>
  <body>
    <h3>My Google Maps Demo</h3>
    <!--The div element for the map -->
    <div id="map"></div>
    
    <script>
  (g=>{var h,a,k,p="The Google Maps JavaScript API",c="google",l="importLibrary",q="__ib__",m=document,b=window;b=b[c]||(b[c]={});var d=b.maps||(b.maps={}),r=new Set,e=new URLSearchParams,u=()=>h||(h=new Promise(async(f,n)=>{await (a=m.createElement("script"));e.set("libraries",[...r]+"");for(k in g)e.set(k.replace(/[A-Z]/g,t=>"_"+t[0].toLowerCase()),g[k]);e.set("callback",c+".maps."+q);a.src=`https://maps.${c}apis.com/maps/api/js?`+e;d[q]=f;a.onerror=()=>h=n(Error(p+" could not load."));a.nonce=m.querySelector("script[nonce]")?.nonce||"";m.head.append(a)}));d[l]?console.warn(p+" only loads once. Ignoring:",g):d[l]=(f,...n)=>r.add(f)&&u().then(()=>d[l](f,...n))})({
    key: "AIzaSyBsdto0bY3WfiIHL_JVxqiyAEFRphHOZ0g",
    v: "weekly",
    // Use the 'v' parameter to indicate the version to use (weekly, beta, alpha, etc.).
    // Add other bootstrap parameters as needed, using camel case.
  });

  // Initialize and add the map
let map;
async function initMap(): Promise<void> {
  // The location of Necochea
  const position = { lat: -38.5545, lng: -58.73961 };

  // Request needed libraries.
  //@ts-ignore
  const { Map } = await google.maps.importLibrary("maps") as google.maps.MapsLibrary;
  const { AdvancedMarkerElement } = await google.maps.importLibrary("marker") as google.maps.MarkerLibrary;

  // The map, centered at Necochea
  map = new Map(
    document.getElementById('map') as HTMLElement,
    {
      zoom: 4,
      center: position,
      mapId: 'DEMO_MAP_ID',
    }
  );

  // The marker, positioned at Necochea
  const marker = new AdvancedMarkerElement({
    map: map,
    position: position,
    title: 'Necochea'
  });
}

initMap();


</script>


  </body>
    </main>
</body>

<?php include("footer"); ?>
<!-- <!DOCTYPE html>
<html>
  <head>
    <title>Simple Marker</title>
    The callback parameter is required, so we use console.debug as a noop 
    <script async src="https://maps.googleapis.com/maps/api/js?key=YOUR_API_KEY_HERE&callback=console.debug&libraries=maps,marker&v=beta">
    </script>
    <link rel="stylesheet" href="./style.css"/>
  </head>
  <body>
    <gmp-map center="-38.55433654785156,-58.739620208740234" zoom="14" map-id="DEMO_MAP_ID">
      <gmp-advanced-marker position="-38.55433654785156,-58.739620208740234" title="My location"></gmp-advanced-marker>
    </gmp-map>
  </body>
</html> -->