
$().ready(function(){
	getInfo();
});

function getInfo()
{
	$.ajax({
		url: '/_core/index.php?action=status',
		type: 'GET',
		cache: false,
		//dataType: 'json',
		error: function(xhr, status, error) {
			//location.href = ...
		},
		success: function(data, textStatus, jqXHR){
			$('.bottombar').html(data);
		}
	});
	
	if(desktop == 1)
	{
		setTimeout(getInfo, 30000);
	}
}


// =========	Core functions	=========

function secondsToTimeRange(value)
{
	var intervals = [
		{label: 'sec', value: 1.0},
		{label: 'min', value: 60.0},
		{label: 'hour', value: 60.0 * 60.0},
		{label: 'day', value: 24.0 * 60.0 * 60.0},
		//{label: 'week', value: 7.0 * 24.0 * 60.0 * 60.0},
		{label: 'month', value: 30.0 * 24.0 * 60.0 * 60.0},
		{label: 'year', value: 12.0 * 30.0 * 24.0 * 60.0 * 60.0}
	];
	var ret = '';
	
	for(var i = intervals.length-1; i >= 0; i--)
	{
		if(value * 1.0 >= intervals[i].value ){
			ret += Math.floor((value * 1.0) / intervals[i].value) + intervals[i].label + (value == 1 ? '' : 's') + ' ';
			value = value * 1.0 % intervals[i].value;
		}
	}
	
	return ret;
}

function minutesToTimeRange(value)
{
	return secondsToTimeRange(value * 60);
}


function timeRangeToSeconds(value)
{
	var ret = 'todo';
	
	return ret;
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
