<!DOCTYPE html>
<html>

<head>

  <meta charset="UTF-8">

  <title>CodePen - Sky gradient based on time 2</title>


  <script>
    window.console = window.console || function(t) {};
   // window.open = function(){ console.log('window.open is disabled.'); };
  //  window.print = function(){ console.log('window.print is disabled.'); };
    // Support hover state for mobile.
    if (false) {
      window.ontouchstart = function(){};
    }
  </script>

  
<link href='http://fonts.googleapis.com/css?family=Nixie+One|Yanone+Kaffeesatz:400,200,700,300' rel='stylesheet' type='text/css'>
<script type="text/javascript" src="scripts/skycons.js"></script>
	<script type="text/javascript" src="http://suncalc.net/scripts/suncalc.js"></script>


  <!--<script src='http://assets.codepen.io/assets/libs/fullpage/jquery_and_jqueryui-f854fb17d00ce0affeccceb6506e478e.js'></script>-->
  
  <!--
  <script src="../_assets/scripts/jquery/jquery-1.4.2.bis.min.js"></script>
  
  <script src="../_assets/scripts/jquery-ui-1.11.3/jquery-ui.min.js"></script>
  -->
  
  <script src="../_assets/scripts/jquery/jquery-1.10.2.min.js"></script>
  
  <script src="../_assets/scripts/jquery-ui-1.11.3/jquery-ui.min.js"></script>
  <link rel='stylesheet' href='http://ajax.googleapis.com/ajax/libs/jqueryui/1.11.1/themes/smoothness/jquery-ui.css'>
  
  <!--<script src="../_assets/scripts/jquery-color/jquery.color.js"></script>-->
  
<link href='styles/weather.css' rel='stylesheet' type='text/css'>
  
  <script src='http://momentjs.com/downloads/moment.min.js'></script>

  <!--script src="http://assets.codepen.io/assets/common/stopExecutionOnTimeout-6c99970ade81e43be51fa877be0f7600.js"></script-->

  <script>
 var local = 1;
 var lang = 'nl';
 var apikey = '74fe9f2731af7ec3a45c5e767a86883d';
 var city = 'Geel,be';
var defaultLocation = {coords:{latitude:51.2,longitude:5}};
   
  // http://suncalc.net/

// props
var d = moment(); 
var h = updateTime();
var inx = -1; 

var skykonMap = {
	'01d' : Skycons.CLEAR_DAY,
	'01n' : Skycons.CLEAR_NIGHT,
	'02d' : Skycons.PARTLY_CLOUDY_DAY,
	'02n ' : Skycons.PARTLY_CLOUDY_NIGHT,
	'03d' : Skycons.CLOUDY, // scattered clouds
  '03n' : Skycons.CLOUDY,
  '04d' : Skycons.CLOUDY, // broken clouds
  '04n' : Skycons.CLOUDY, 
	'09d' : Skycons.RAIN, // shower rain
  '09n' : Skycons.RAIN,
  '10d' : Skycons.RAIN, // rain
  '10n' : Skycons.RAIN,
	'11d' : Skycons.SLEET, // thunderstorm
  '11n' : Skycons.SLEET, // thunderstorm
	'13d' : Skycons.SNOW,
  '13n' : Skycons.SNOW,
	'50d' : Skycons.FOG, // mist
  '50n' : Skycons.FOG,
  'wind' : Skycons.WIND 
};

var skycons = new Skycons({"color": "white"}); 
var skyconIcon = '01d';


// gradient colors from http://cdpn.io/rDEAl
/* icon:
01d.png 	01n.png 	clear sky
02d.png 	02n.png 	few clouds
03d.png 	03n.png 	scattered clouds
04d.png 	04n.png 	broken clouds
09d.png 	09n.png 	shower rain
10d.png 	10n.png 	rain
11d.png 	11n.png 	thunderstorm
13d.png 	13n.png 	snow
50d.png 	50n.png 	mist 
*/
var grads = {
	'01': [//clear sky
	  [{color:"00000c",position:0},{color:"00000c",position:0}],
	  [{color:"020111",position:85},{color:"191621",position:100}],
	  [{color:"020111",position:60},{color:"20202c",position:100}],
	  [{color:"020111",position:10},{color:"3a3a52",position:100}],
	  [{color:"20202c",position:0},{color:"515175",position:100}],
	  [{color:"40405c",position:0},{color:"6f71aa",position:80},{color:"8a76ab",position:100}],
	  [{color:"4a4969",position:0},{color:"7072ab",position:50},{color:"cd82a0",position:100}],
	  [{color:"757abf",position:0},{color:"8583be",position:60},{color:"eab0d1",position:100}],
	  [{color:"82addb",position:0},{color:"ebb2b1",position:100}],
	  [{color:"94c5f8",position:1},{color:"a6e6ff",position:70},{color:"b1b5ea",position:100}],
	  [{color:"b7eaff",position:0},{color:"94dfff",position:100}],
	  [{color:"9be2fe",position:0},{color:"67d1fb",position:100}],
	  [{color:"90dffe",position:0},{color:"38a3d1",position:100}],
	  [{color:"57c1eb",position:0},{color:"246fa8",position:100}],
	  [{color:"2d91c2",position:0},{color:"1e528e",position:100}],
	  [{color:"2473ab",position:0},{color:"1e528e",position:70},{color:"5b7983",position:100}],
	  [{color:"1e528e",position:0},{color:"265889",position:50},{color:"9da671",position:100}],
	  [{color:"1e528e",position:0},{color:"728a7c",position:50},{color:"e9ce5d",position:100}],
	  [{color:"154277",position:0},{color:"576e71",position:30},{color:"e1c45e",position:70},{color:"b26339",position:100}],
	  [{color:"163C52",position:0},{color:"4F4F47",position:30},{color:"C5752D",position:60},{color:"B7490F",position:80},{color:"2F1107",position:100}],
	  [{color:"071B26",position:0},{color:"071B26",position:30},{color:"8A3B12",position:80},{color:"240E03",position:100}],
	  [{color:"010A10",position:30},{color:"59230B",position:80},{color:"2F1107",position:100}],
	  [{color:"090401",position:50},{color:"4B1D06",position:100}],
	  [{color:"00000c",position:80},{color:"150800",position:100}]
	],
	'02': [//few clouds
	  [{color:"00000c",position:0},{color:"00000c",position:0}],
	  [{color:"020111",position:85},{color:"191621",position:100}],
	  [{color:"020111",position:60},{color:"20202c",position:100}],
	  [{color:"020111",position:10},{color:"3a3a52",position:100}],
	  [{color:"20202c",position:0},{color:"515175",position:100}],
	  [{color:"40405c",position:0},{color:"6f71aa",position:80},{color:"8a76ab",position:100}],
	  [{color:"4a4969",position:0},{color:"7072ab",position:50},{color:"cd82a0",position:100}],
	  [{color:"757abf",position:0},{color:"8583be",position:60},{color:"eab0d1",position:100}],
	  [{color:"82addb",position:0},{color:"ebb2b1",position:100}],
	  [{color:"94c5f8",position:1},{color:"a6e6ff",position:70},{color:"b1b5ea",position:100}],
	  [{color:"b7eaff",position:0},{color:"94dfff",position:100}],
	  [{color:"9be2fe",position:0},{color:"67d1fb",position:100}],
	  [{color:"90dffe",position:0},{color:"38a3d1",position:100}],
	  [{color:"57c1eb",position:0},{color:"246fa8",position:100}],
	  [{color:"2d91c2",position:0},{color:"1e528e",position:100}],
	  [{color:"2473ab",position:0},{color:"1e528e",position:70},{color:"5b7983",position:100}],
	  [{color:"1e528e",position:0},{color:"265889",position:50},{color:"9da671",position:100}],
	  [{color:"1e528e",position:0},{color:"728a7c",position:50},{color:"e9ce5d",position:100}],
	  [{color:"154277",position:0},{color:"576e71",position:30},{color:"e1c45e",position:70},{color:"b26339",position:100}],
	  [{color:"163C52",position:0},{color:"4F4F47",position:30},{color:"C5752D",position:60},{color:"B7490F",position:80},{color:"2F1107",position:100}],
	  [{color:"071B26",position:0},{color:"071B26",position:30},{color:"8A3B12",position:80},{color:"240E03",position:100}],
	  [{color:"010A10",position:30},{color:"59230B",position:80},{color:"2F1107",position:100}],
	  [{color:"090401",position:50},{color:"4B1D06",position:100}],
	  [{color:"00000c",position:80},{color:"150800",position:100}]
	],
	'03': [//scattered clouds
	  [{color:"00000c",position:0},{color:"00000c",position:0}],
	  [{color:"020111",position:85},{color:"191621",position:100}],
	  [{color:"020111",position:60},{color:"20202c",position:100}],
	  [{color:"020111",position:10},{color:"3a3a52",position:100}],
	  [{color:"20202c",position:0},{color:"515175",position:100}],
	  [{color:"40405c",position:0},{color:"6f71aa",position:80},{color:"8a76ab",position:100}],
	  [{color:"4a4969",position:0},{color:"7072ab",position:50},{color:"cd82a0",position:100}],
	  [{color:"757abf",position:0},{color:"8583be",position:60},{color:"eab0d1",position:100}],
	  [{color:"82addb",position:0},{color:"ebb2b1",position:100}],
	  [{color:"94c5f8",position:1},{color:"a6e6ff",position:70},{color:"b1b5ea",position:100}],
	  [{color:"b7eaff",position:0},{color:"94dfff",position:100}],
	  [{color:"9be2fe",position:0},{color:"67d1fb",position:100}],
	  [{color:"90dffe",position:0},{color:"38a3d1",position:100}],
	  [{color:"57c1eb",position:0},{color:"246fa8",position:100}],
	  [{color:"2d91c2",position:0},{color:"1e528e",position:100}],
	  [{color:"2473ab",position:0},{color:"1e528e",position:70},{color:"5b7983",position:100}],
	  [{color:"1e528e",position:0},{color:"265889",position:50},{color:"9da671",position:100}],
	  [{color:"1e528e",position:0},{color:"728a7c",position:50},{color:"e9ce5d",position:100}],
	  [{color:"154277",position:0},{color:"576e71",position:30},{color:"e1c45e",position:70},{color:"b26339",position:100}],
	  [{color:"163C52",position:0},{color:"4F4F47",position:30},{color:"C5752D",position:60},{color:"B7490F",position:80},{color:"2F1107",position:100}],
	  [{color:"071B26",position:0},{color:"071B26",position:30},{color:"8A3B12",position:80},{color:"240E03",position:100}],
	  [{color:"010A10",position:30},{color:"59230B",position:80},{color:"2F1107",position:100}],
	  [{color:"090401",position:50},{color:"4B1D06",position:100}],
	  [{color:"00000c",position:80},{color:"150800",position:100}]
	],
	'04': [//broken clouds
	  [{color:"00000c",position:0},{color:"00000c",position:0}],
	  [{color:"020111",position:85},{color:"191621",position:100}],
	  [{color:"020111",position:60},{color:"20202c",position:100}],
	  [{color:"020111",position:10},{color:"3a3a52",position:100}],
	  [{color:"20202c",position:0},{color:"515175",position:100}],
	  [{color:"40405c",position:0},{color:"6f71aa",position:80},{color:"8a76ab",position:100}],
	  [{color:"4a4969",position:0},{color:"7072ab",position:50},{color:"cd82a0",position:100}],
	  [{color:"757abf",position:0},{color:"8583be",position:60},{color:"eab0d1",position:100}],
	  [{color:"82addb",position:0},{color:"ebb2b1",position:100}],
	  [{color:"94c5f8",position:1},{color:"a6e6ff",position:70},{color:"b1b5ea",position:100}],
	  [{color:"b7eaff",position:0},{color:"94dfff",position:100}],
	  [{color:"9be2fe",position:0},{color:"67d1fb",position:100}],
	  [{color:"90dffe",position:0},{color:"38a3d1",position:100}],
	  [{color:"57c1eb",position:0},{color:"246fa8",position:100}],
	  [{color:"2d91c2",position:0},{color:"1e528e",position:100}],
	  [{color:"2473ab",position:0},{color:"1e528e",position:70},{color:"5b7983",position:100}],
	  [{color:"1e528e",position:0},{color:"265889",position:50},{color:"9da671",position:100}],
	  [{color:"1e528e",position:0},{color:"728a7c",position:50},{color:"e9ce5d",position:100}],
	  [{color:"154277",position:0},{color:"576e71",position:30},{color:"e1c45e",position:70},{color:"b26339",position:100}],
	  [{color:"163C52",position:0},{color:"4F4F47",position:30},{color:"C5752D",position:60},{color:"B7490F",position:80},{color:"2F1107",position:100}],
	  [{color:"071B26",position:0},{color:"071B26",position:30},{color:"8A3B12",position:80},{color:"240E03",position:100}],
	  [{color:"010A10",position:30},{color:"59230B",position:80},{color:"2F1107",position:100}],
	  [{color:"090401",position:50},{color:"4B1D06",position:100}],
	  [{color:"00000c",position:80},{color:"150800",position:100}]
	],
	'09': [//shower rain
	  [{color:"00000c",position:0},{color:"00000c",position:0}],
	  [{color:"020111",position:85},{color:"191621",position:100}],
	  [{color:"020111",position:60},{color:"20202c",position:100}],
	  [{color:"020111",position:10},{color:"3a3a52",position:100}],
	  [{color:"20202c",position:0},{color:"515175",position:100}],
	  [{color:"40405c",position:0},{color:"6f71aa",position:80},{color:"8a76ab",position:100}],
	  [{color:"4a4969",position:0},{color:"7072ab",position:50},{color:"cd82a0",position:100}],
	  [{color:"757abf",position:0},{color:"8583be",position:60},{color:"eab0d1",position:100}],
	  [{color:"82addb",position:0},{color:"ebb2b1",position:100}],
	  [{color:"94c5f8",position:1},{color:"a6e6ff",position:70},{color:"b1b5ea",position:100}],
	  [{color:"b7eaff",position:0},{color:"94dfff",position:100}],
	  [{color:"9be2fe",position:0},{color:"67d1fb",position:100}],
	  [{color:"90dffe",position:0},{color:"38a3d1",position:100}],
	  [{color:"57c1eb",position:0},{color:"246fa8",position:100}],
	  [{color:"2d91c2",position:0},{color:"1e528e",position:100}],
	  [{color:"2473ab",position:0},{color:"1e528e",position:70},{color:"5b7983",position:100}],
	  [{color:"1e528e",position:0},{color:"265889",position:50},{color:"9da671",position:100}],
	  [{color:"1e528e",position:0},{color:"728a7c",position:50},{color:"e9ce5d",position:100}],
	  [{color:"154277",position:0},{color:"576e71",position:30},{color:"e1c45e",position:70},{color:"b26339",position:100}],
	  [{color:"163C52",position:0},{color:"4F4F47",position:30},{color:"C5752D",position:60},{color:"B7490F",position:80},{color:"2F1107",position:100}],
	  [{color:"071B26",position:0},{color:"071B26",position:30},{color:"8A3B12",position:80},{color:"240E03",position:100}],
	  [{color:"010A10",position:30},{color:"59230B",position:80},{color:"2F1107",position:100}],
	  [{color:"090401",position:50},{color:"4B1D06",position:100}],
	  [{color:"00000c",position:80},{color:"150800",position:100}]
	],
	'10': [//rain
	  [{color:"00000c",position:0},{color:"00000c",position:0}],
	  [{color:"020111",position:85},{color:"191621",position:100}],
	  [{color:"020111",position:60},{color:"20202c",position:100}],
	  [{color:"020111",position:10},{color:"3a3a52",position:100}],
	  [{color:"20202c",position:0},{color:"515175",position:100}],
	  [{color:"40405c",position:0},{color:"6f71aa",position:80},{color:"8a76ab",position:100}],
	  [{color:"4a4969",position:0},{color:"7072ab",position:50},{color:"cd82a0",position:100}],
	  [{color:"757abf",position:0},{color:"8583be",position:60},{color:"eab0d1",position:100}],
	  [{color:"82addb",position:0},{color:"ebb2b1",position:100}],
	  [{color:"94c5f8",position:1},{color:"a6e6ff",position:70},{color:"b1b5ea",position:100}],
	  [{color:"b7eaff",position:0},{color:"94dfff",position:100}],
	  [{color:"9be2fe",position:0},{color:"67d1fb",position:100}],
	  [{color:"90dffe",position:0},{color:"38a3d1",position:100}],
	  [{color:"57c1eb",position:0},{color:"246fa8",position:100}],
	  [{color:"2d91c2",position:0},{color:"1e528e",position:100}],
	  [{color:"2473ab",position:0},{color:"1e528e",position:70},{color:"5b7983",position:100}],
	  [{color:"1e528e",position:0},{color:"265889",position:50},{color:"9da671",position:100}],
	  [{color:"1e528e",position:0},{color:"728a7c",position:50},{color:"e9ce5d",position:100}],
	  [{color:"154277",position:0},{color:"576e71",position:30},{color:"e1c45e",position:70},{color:"b26339",position:100}],
	  [{color:"163C52",position:0},{color:"4F4F47",position:30},{color:"C5752D",position:60},{color:"B7490F",position:80},{color:"2F1107",position:100}],
	  [{color:"071B26",position:0},{color:"071B26",position:30},{color:"8A3B12",position:80},{color:"240E03",position:100}],
	  [{color:"010A10",position:30},{color:"59230B",position:80},{color:"2F1107",position:100}],
	  [{color:"090401",position:50},{color:"4B1D06",position:100}],
	  [{color:"00000c",position:80},{color:"150800",position:100}]
	],
	'11': [//thunderstorm
	  [{color:"00000c",position:0},{color:"00000c",position:0}],
	  [{color:"020111",position:85},{color:"191621",position:100}],
	  [{color:"020111",position:60},{color:"20202c",position:100}],
	  [{color:"020111",position:10},{color:"3a3a52",position:100}],
	  [{color:"20202c",position:0},{color:"515175",position:100}],
	  [{color:"40405c",position:0},{color:"6f71aa",position:80},{color:"8a76ab",position:100}],
	  [{color:"4a4969",position:0},{color:"7072ab",position:50},{color:"cd82a0",position:100}],
	  [{color:"757abf",position:0},{color:"8583be",position:60},{color:"eab0d1",position:100}],
	  [{color:"82addb",position:0},{color:"ebb2b1",position:100}],
	  [{color:"94c5f8",position:1},{color:"a6e6ff",position:70},{color:"b1b5ea",position:100}],
	  [{color:"b7eaff",position:0},{color:"94dfff",position:100}],
	  [{color:"9be2fe",position:0},{color:"67d1fb",position:100}],
	  [{color:"90dffe",position:0},{color:"38a3d1",position:100}],
	  [{color:"57c1eb",position:0},{color:"246fa8",position:100}],
	  [{color:"2d91c2",position:0},{color:"1e528e",position:100}],
	  [{color:"2473ab",position:0},{color:"1e528e",position:70},{color:"5b7983",position:100}],
	  [{color:"1e528e",position:0},{color:"265889",position:50},{color:"9da671",position:100}],
	  [{color:"1e528e",position:0},{color:"728a7c",position:50},{color:"e9ce5d",position:100}],
	  [{color:"154277",position:0},{color:"576e71",position:30},{color:"e1c45e",position:70},{color:"b26339",position:100}],
	  [{color:"163C52",position:0},{color:"4F4F47",position:30},{color:"C5752D",position:60},{color:"B7490F",position:80},{color:"2F1107",position:100}],
	  [{color:"071B26",position:0},{color:"071B26",position:30},{color:"8A3B12",position:80},{color:"240E03",position:100}],
	  [{color:"010A10",position:30},{color:"59230B",position:80},{color:"2F1107",position:100}],
	  [{color:"090401",position:50},{color:"4B1D06",position:100}],
	  [{color:"00000c",position:80},{color:"150800",position:100}]
	],
	'13': [//snow
	  [{color:"00000c",position:0},{color:"00000c",position:0}],
	  [{color:"020111",position:85},{color:"191621",position:100}],
	  [{color:"020111",position:60},{color:"20202c",position:100}],
	  [{color:"020111",position:10},{color:"3a3a52",position:100}],
	  [{color:"20202c",position:0},{color:"515175",position:100}],
	  [{color:"40405c",position:0},{color:"6f71aa",position:80},{color:"8a76ab",position:100}],
	  [{color:"4a4969",position:0},{color:"7072ab",position:50},{color:"cd82a0",position:100}],
	  [{color:"757abf",position:0},{color:"8583be",position:60},{color:"eab0d1",position:100}],
	  [{color:"82addb",position:0},{color:"ebb2b1",position:100}],
	  [{color:"94c5f8",position:1},{color:"a6e6ff",position:70},{color:"b1b5ea",position:100}],
	  [{color:"b7eaff",position:0},{color:"94dfff",position:100}],
	  [{color:"9be2fe",position:0},{color:"67d1fb",position:100}],
	  [{color:"90dffe",position:0},{color:"38a3d1",position:100}],
	  [{color:"57c1eb",position:0},{color:"246fa8",position:100}],
	  [{color:"2d91c2",position:0},{color:"1e528e",position:100}],
	  [{color:"2473ab",position:0},{color:"1e528e",position:70},{color:"5b7983",position:100}],
	  [{color:"1e528e",position:0},{color:"265889",position:50},{color:"9da671",position:100}],
	  [{color:"1e528e",position:0},{color:"728a7c",position:50},{color:"e9ce5d",position:100}],
	  [{color:"154277",position:0},{color:"576e71",position:30},{color:"e1c45e",position:70},{color:"b26339",position:100}],
	  [{color:"163C52",position:0},{color:"4F4F47",position:30},{color:"C5752D",position:60},{color:"B7490F",position:80},{color:"2F1107",position:100}],
	  [{color:"071B26",position:0},{color:"071B26",position:30},{color:"8A3B12",position:80},{color:"240E03",position:100}],
	  [{color:"010A10",position:30},{color:"59230B",position:80},{color:"2F1107",position:100}],
	  [{color:"090401",position:50},{color:"4B1D06",position:100}],
	  [{color:"00000c",position:80},{color:"150800",position:100}]
	],
	'50': [//mist
	  [{color:"00000c",position:0},{color:"00000c",position:0}],
	  [{color:"020111",position:85},{color:"191621",position:100}],
	  [{color:"020111",position:60},{color:"20202c",position:100}],
	  [{color:"020111",position:10},{color:"3a3a52",position:100}],
	  [{color:"20202c",position:0},{color:"515175",position:100}],
	  [{color:"40405c",position:0},{color:"6f71aa",position:80},{color:"8a76ab",position:100}],
	  [{color:"4a4969",position:0},{color:"7072ab",position:50},{color:"cd82a0",position:100}],
	  [{color:"757abf",position:0},{color:"8583be",position:60},{color:"eab0d1",position:100}],
	  [{color:"82addb",position:0},{color:"ebb2b1",position:100}],
	  [{color:"94c5f8",position:1},{color:"a6e6ff",position:70},{color:"b1b5ea",position:100}],
	  [{color:"b7eaff",position:0},{color:"94dfff",position:100}],
	  [{color:"9be2fe",position:0},{color:"67d1fb",position:100}],
	  [{color:"90dffe",position:0},{color:"38a3d1",position:100}],
	  [{color:"57c1eb",position:0},{color:"246fa8",position:100}],
	  [{color:"2d91c2",position:0},{color:"1e528e",position:100}],
	  [{color:"2473ab",position:0},{color:"1e528e",position:70},{color:"5b7983",position:100}],
	  [{color:"1e528e",position:0},{color:"265889",position:50},{color:"9da671",position:100}],
	  [{color:"1e528e",position:0},{color:"728a7c",position:50},{color:"e9ce5d",position:100}],
	  [{color:"154277",position:0},{color:"576e71",position:30},{color:"e1c45e",position:70},{color:"b26339",position:100}],
	  [{color:"163C52",position:0},{color:"4F4F47",position:30},{color:"C5752D",position:60},{color:"B7490F",position:80},{color:"2F1107",position:100}],
	  [{color:"071B26",position:0},{color:"071B26",position:30},{color:"8A3B12",position:80},{color:"240E03",position:100}],
	  [{color:"010A10",position:30},{color:"59230B",position:80},{color:"2F1107",position:100}],
	  [{color:"090401",position:50},{color:"4B1D06",position:100}],
	  [{color:"00000c",position:80},{color:"150800",position:100}]
	]
};

// "linear-gradient(to bottom, #020111 85%,#191621 100%)"
// {color:"20202c",position:0},{color:"515175",position:100}
function toCSSGradient(data)
{ 
  var css = "linear-gradient(to bottom, ";
  var len = data.length;

  for (var i=0;i<len;i++)
  { 
     var item = data[i];
     css+= " #" + item.color + " " + item.position + "%";
     if ( i<len-1 ) css += ",";
  }
  return css + ")"; 
}

function updateTime()
{
  d = moment(); 
  d.local(); 
  return d.hours();  
}

function updateBasedOnNow()
{ 
  setCSSGradientByIndex(updateTime()); 
}

function setCSSGradientByIndex(nInx)
{  
  if ( nInx != inx ) 
  {
    inx = nInx;
    var data = grads[skyconIcon.replace(/n|d/, '')][inx]; 
    if ( data == null ) return;

    // convert data to gradient
    var css = toCSSGradient(data);

    // update the background
    $("#grad").css("background", css);
    $("#grad").css("background-image:", "-webkit-" + css);
    $("#grad").css("background-image:", "-moz-" + css);
    $("#grad").css("background-image:", "" + css);
	
	/*$("#grad").animate({
            backgroundColor: jQuery.Color(css)
    }, 1500 );*/
	
    // reset the slider
    $( "#slider" ).slider( "option", "value", (inx/24)*100 );
    updateWeatherIcon(skyconIcon);
    // possible to change the foreground color on background change
    //$("#gradInfo").css("color", "#fff");
  }

  // always set time
  d.hours(inx);

  // update visible
  $("#time").html(d.format('h:mm'));
  //$("#time").html(d.format('h:mm[<span id="timeOfDay">]a[</span>]'));
  $("#date").html(d.format('MMMM Do YYYY'));

  // update in console
  console.log(d.format('MMMM Do YYYY h:mm:ss a'));
}

function getLocation()
{
  if (local == 0 && navigator.geolocation) 
  {
            var timeoutVal = 10 * 1000 * 1000;
            navigator.geolocation.getCurrentPosition(
                    showLocation,
                    function(){showLocation(defaultLocation);},
                    { enableHighAccuracy: true, timeout: timeoutVal, maximumAge: 0 }
            );
  }
  else 
  { 
     showLocation(defaultLocation);
    // alert("Geolocation is not supported by this browser");
  } 
}

function showLocation(position)
{
  console.log(position);
  getSunInfo(position.coords.latitude,position.coords.longitude);
  getWeather(position.coords.latitude,position.coords.longitude,showWeather);
}

function kelvinToCDegrees(kelvin)
{
   //return Math.round((1.8 * (kelvin - 273 ) + 32));
   return Math.round(kelvin - 273);
}

function showWeather(response)
{

  var newline = '<br>&nbsp&nbsp';
  var icon = response["weather"][0]["icon"];
  updateWeatherIcon(icon);
  var iconImg = '<img align="middle" width="50" height="50"  src="http://openweathermap.org/img/w/' + icon + '.png" >';
  var locationName = response.name;
  var lat = response.coord.lat;
  var lon = response.coord.lon;
  var weatherDesc = response.weather[0].description;
  var fDegrees = kelvinToCDegrees(response.main.temp) + "&#186;";

  var result = ''
  + iconImg
  + "  " + weatherDesc + newline
  + locationName + ": " + 
  + lat + ', ' + lon + newline
  ;

  $("#temp").html(fDegrees);
  $("#weather").html(result); 

  console.log(response);
  console.log(result); 
}

function getWeather(lat,lon,callback)
{
  var api = "http://api.openweathermap.org/data/2.5/weather";
  api += "?lang=" + lang; 
  api += "&APPID=" + apikey; 
  if(local == 1 && city != ''){
	api += "&q=" + city; 
  }
  else {
	  api += "&lat=" + lat;
	  api += "&lon=" + lon; 
  }

  //$("#weatherId").html("Loading Weather Info...");
  $.ajax({
    url: api,
    dataType: 'jsonp',
    success:callback
    }); 
}

function getSunInfo(lat,lng)
{
  var data = new Date(); 
  var di = SunCalc.getDayInfo(data, lat, lng);
  var sunrisePos = SunCalc.getSunPosition(di.sunrise.start, lat, lng);
  var sunsetPos = SunCalc.getSunPosition(di.sunset.end, lat, lng);
  var sR = moment(di.sunrise.start);
  var sS = moment(di.sunset.end);
  var daylightHours = sS.diff (sR, 'hours');
  console.log("getDayInfo", di);
  console.log("daylightHours", daylightHours); 
}

function updateWeatherIcon(icon)
{
  if ( icon == undefined ) return;
 // var h = d.hour();
  if (inx < 5 || inx > 18)
  {
    skyconIcon = icon.replace("d","n");
  } 
  else 
  {
    skyconIcon = icon.replace("n","d");
  }

  console.log(skyconIcon + "  " + h);
  skycons.set(document.getElementById("skycon"), skykonMap[skyconIcon] || Skycons.CLEAR_DAY);
skycons.play();
}


function toggleFullScreen(event) {
  if (!document.fullscreenElement &&    // alternative standard method
      !document.mozFullScreenElement && !document.webkitFullscreenElement && !document.msFullscreenElement ) {  // current working methods
    if (document.documentElement.requestFullscreen) {
      document.documentElement.requestFullscreen();
    } else if (document.documentElement.msRequestFullscreen) {
      document.documentElement.msRequestFullscreen();
    } else if (document.documentElement.mozRequestFullScreen) {
      document.documentElement.mozRequestFullScreen();
    } else if (document.documentElement.webkitRequestFullscreen) {
      document.documentElement.webkitRequestFullscreen(Element.ALLOW_KEYBOARD_INPUT);
    }
  } else {
    if (document.exitFullscreen) {
      document.exitFullscreen();
    } else if (document.msExitFullscreen) {
      document.msExitFullscreen();
    } else if (document.mozCancelFullScreen) {
      document.mozCancelFullScreen();
    } else if (document.webkitExitFullscreen) {
      document.webkitExitFullscreen();
    }
  }
}


$().ready(function(){

	// generate the slider
	$( "#slider" ).slider({
	  slide: function( event, ui )
	  {  
		var per = ui.value == 0 ? 0 : ui.value/100;
		var nInx = Math.round((grads[skyconIcon.replace(/n|d/, '')].length-1) * per);
		if ( nInx != inx ) 
		{ 
		  setCSSGradientByIndex(nInx);
		}
	  } 
	}); 

	setCSSGradientByIndex(h);
	getLocation();

	// update every minute
	var interval = setInterval(function(){updateBasedOnNow();},60 * 1000);
	var interval2 = setInterval(function(){getLocation();},60 * 60 * 1000);
	// update onClick
	$("#gradInfo").click(function() {
	  updateBasedOnNow();
	  getLocation();
	});
	
	$('#fullscreentoggler').click(toggleFullScreen);

});




  </script>
</head>

<body>


<div id="slider"></div>
<div id="grad">
  <div id="gradInfo">
    <div id="time"></div>
    <div id="date"></div>
    <canvas id="skycon" width="128" height="128"></canvas>
    <div id="temp"></div>

  </div>
  <div id="weather"></div>
</div> 

<div id="fullscreentoggler"></div>

</body>

</html>
