
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
	
	setTimeout(getInfo, 3000);
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
