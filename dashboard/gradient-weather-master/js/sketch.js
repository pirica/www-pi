var Y_AXIS = 1;
var PartlyCloudy1, PartlyCloudy2, Sunny1, Sunny2, Cloudy1, Cloudy2, Snowing1, Snowing2, Rain1, Rain2, Fog1, Fog2, Moreclouds1, Moreclouds2;
var weather;
var loaded;
var main;
var name;
var list;

function setup() {
  createCanvas(windowWidth, windowHeight);


    if(geoPosition.init()){  // Geolocation Initialisation
            geoPosition.getCurrentPosition(success_callback,error_callback,{enableHighAccuracy:true});
    }else{
            // You cannot use Geolocation in this device
    }
    geoPositionSimulator.init(); 

    // p : geolocation object
    function success_callback(p){
        // p.latitude : latitude value
        // p.longitude : longitude value
        var address = "http://api.openweathermap.org/data/2.5/find?lat=" + p.coords.latitude + "&lon=" + p.coords.longitude + "&cnt=1&units=imperial";
        loadJSON( address, function(data){
            var w = data.list[0];
            loaded = true;
            weather = w.weather;
            main = w.main;
            name = w.name;
            createDiv( floor(main.temp) + '&deg;').addClass('temperature');
            createDiv( weather[0].description).addClass('weather');
            createDiv( name).addClass('city');
            print(weather);
            print(main);
            print(name);
            print(weather[0].id)
        });
    }

    function error_callback(p){
        // p.message : error message
        loadJSON('http://api.openweathermap.org/data/2.5/weather?q=San%20Francisco,CA&units=imperial', function(w){
            loaded = true;
            weather = w.weather;
            main = w.main;
            name = w.name;
            createDiv( floor(main.temp) + '&deg;').addClass('temperature');
            createDiv( weather[0].description).addClass('weather');
            createDiv( name).addClass('city');
            print(weather);
            print(main);
            print(name);
            print(weather[0].id)
        });
    }

  PartlyCloudy1 = color(218,226,248);
  PartlyCloudy2 = color(214,164,164);

  Sunny1 = color(255,78,80);
  Sunny2 = color(249,212,35);

  Cloudy1 = color(236,233,230);
  Cloudy2 = color(255,255,255);

  Snowing1 = color(230,218,218);
  Snowing2 = color(39,64,70);

  Rain1 = color(97,97,97);
  Rain2 = color(155,197,195);

  Haze1 = color(117,127,154);
  Haze2 = color(215,221,232); 

  Fog1 = color(255,255,255);
  Fog2 = color(236,233,230);

  Thunder1 = color(35,37,38);
  Thunder2 = color(65,67,69);

  Moreclouds1 = color(117,127,154);
  Moreclouds2 = color(215,221,232);




  Fewclouds1 = color();
  Fewclouds2 = color();     

  // Request the data from openweathermap
  // loadJSON('http://api.openweathermap.org/data/2.5/weather?q=San%20Francisco,CA&units=imperial', function(w){
  //       loaded = true;
  //       weather = w.weather;
  //       main = w.main;
  //       name = w.name;
  //       createDiv( floor(main.temp) + '&deg;').addClass('temperature');
  //       createDiv( weather[0].description).addClass('wind');
  //       createDiv( name).addClass('wind');
  //       print(weather);
  //       print(main);
  //       print(name);
  //       print(weather[0].id)
  //   });
}

function draw() {
  if(loaded){

  	

    if( weather[0].id >= 200 && weather[0].id <= 232 ) setGradient(0, 0, width, height, Thunder1, Thunder2, 1); 
    if( weather[0].id >= 300 && weather[0].id <= 331 ) setGradient(0, 0, width, height, Rain1, Rain2, 1);
    if( weather[0].id >= 500 && weather[0].id <= 531 ) setGradient(0, 0, width, height, Rain1, Rain2, 1);
    if( weather[0].id >= 600 && weather[0].id <= 622 ) setGradient(0, 0, width, height, Snowing1, Snowing2, 1);
    if( weather[0].id >= 701 && weather[0].id <= 731 ) setGradient(0, 0, width, height, Haze1, Haze2, 1);
    if( weather[0].id >= 741 && weather[0].id <= 781 ) setGradient(0, 0, width, height, Fog1, Fog2, 1);
    if( weather[0].id == 800 ) setGradient(0, 0, width, height, Sunny1, Sunny2, 1);
    if( weather[0].id == 801 ) setGradient(0, 0, width, height, PartlyCloudy2, PartlyCloudy1, 1);
    if( weather[0].id >= 802 && weather[0].id <= 804 ) setGradient(0, 0, width, height, Moreclouds1, Moreclouds2, 1);

  }
}

function setGradient(x, y, w, h, c1, c2, axis) {
  for (var i = y; i <= y+h; i++) {
      var inter = map(i, y, y+h, 0, 1);
      var c = lerpColor(c1, c2, inter);
      stroke(c);
      line(x, i, x+w, i);
    }
}




// if (weather[0].id >= 200 && weather[0].id <= 232) {
//   setGradient(0, 0, width, height, Sunny1, Sunny2, 1);
// } else if( weather[0].id >= 300 && weather[0].id <= 331 ) { 
//   setGradient(0, 0, width, height, Rain1, Rain2, 1);
// } else if( weather[0].id >= 500 && weather[0].id <= 531 ) {
// setGradient(0, 0, width, height, Rain1, Rain2, 1);
// } else if( weather[0].id >= 600 && weather[0].id <= 622 ) {
// setGradient(0, 0, width, height, Snowing1, Snowing2, 1);
// }

// if ((weather[0].id >= 200) || (weather[0].id <= 232)) {
//       setGradient(0, 0, width, height, Sunny1, Sunny2, 1);
//     } else if( (weather[0].id >= 300) || (weather[0].id <= 331)) { 
//       setGradient(0, 0, width, height, Rain1, Rain2, 1);
//     } else if( (weather[0].id >= 500) || (weather[0].id <= 531)) {
//     setGradient(0, 0, width, height, Rain1, Rain2, 1);
//     } else if( (weather[0].id >= 600) || (weather[0].id <= 622)) {
//     setGradient(0, 0, width, height, Snowing1, Snowing2, 1);
//     }

// if( weather[0].id >= 200 || weather[0].id <= 232 ) setGradient(0, 0, width, height, Sunny1, Sunny2, 1);
// if( weather[0].id >= 300 || weather[0].id <= 331 ) setGradient(0, 0, width, height, Rain1, Rain2, 1);
// if( weather[0].id >= 500 || weather[0].id <= 531 ) setGradient(0, 0, width, height, Rain1, Rain2, 1);
// if( weather[0].id >= 600 || weather[0].id <= 622 ) setGradient(0, 0, width, height, Snowing1, Snowing2, 1);

// if( weather[0].id >= 200 && weather[0].id <= 232 ) setGradient(0, 0, width, height, Sunny1, Sunny2, 1);
// if( weather[0].id >= 300 && weather[0].id <= 331 ) setGradient(0, 0, width, height, Rain1, Rain2, 1);
// if( weather[0].id >= 500 && weather[0].id <= 531 ) setGradient(0, 0, width, height, Rain1, Rain2, 1);
// if( weather[0].id >= 600 && weather[0].id <= 622 ) setGradient(0, 0, width, height, Snowing1, Snowing2, 1);