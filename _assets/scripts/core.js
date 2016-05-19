
$().ready(function(){
	// load status info into footer
	getInfo();
	
	// create input masks for all relevant input fields
	if($.isFunction($.fn.inputmask))
	{
		formatInputs();
	}

	
	$('#btnSaveTableeditor').click(validateTableEditorForm);
	
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

	
function validateTableEditorForm(event)
{
	event.stopPropagation();
	event.preventDefault();
	
	var oForm = $('#frmTableeditor');
	
	if(validateForm(oForm) == true)
	{
		// submit data to DB
		oForm.submit();
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



// =========	Form validation functions	=========

var rAmount = /^\-?\d+([,.]\d{1,2})?$/;
var rDate   = "[0-9-/]{10,10}";
var rEmail  = "^[a-zA-Z0-9-_\+~]+(\.[a-zA-Z0-9-_\+~]+)*@([a-zA-Z_0-9-]+\.)+[a-zA-Z]{2,7}$";
var rNumber = /^\-?\d+$/;
var rPhone  = "\\+?[0-9.\\-\\(\\) ]+";

// create input masks for all relevant input fields
function formatInputs()
{
	// dates
	$("input[data-type='date']").inputmask("dd-mm-yyyy");

	// text only
	$("input[data-type='textOnly']").inputmask({
		'mask': 'a',
		'greedy': false,
		'repeat': '*'
	});

	// phone numbers
	$("input[data-type='phone']").inputmask('Regex', {regex: rPhone});
}

// validate a complete form
function validateForm(oForm)
{

	// remove all errors
	oForm.find('.error').removeClass('error');
	oForm.find('.success').removeClass('success');
	clearErrors();
	
	// validation
	var bValid = validateRequired(oForm);

	if(validateAmount(oForm) === false)
	{
		bValid = false;
	}
	if(validateNumber(oForm) === false)
	{
		bValid = false;
	}
	if(validateEmail(oForm) === false)
	{
		bValid = false;
	}
	if(validateDates(oForm) === false)
	{
		bValid = false;
	}
	if(validatePhone(oForm) === false)
	{
		bValid = false;
	}

	return bValid;
}


function clearErrors()
{
	// remove alerts
	$('.popupAlert').alert('close');
}

// check all required fields are filled out
function validateRequired(oForm)
{
	var bValid = true;

	// loop through form elements that are required
	oForm.find('input[type!="radio"][required], textarea[required], select[required]').each(function()
	{
		// check if empty (and visible) (val == null is for select2 components)
		if(($(this).val() == "" || $(this).val() == null) && $(this).is(':visible'))
		{
			$(this).closest('.control-group').addClass('error');
			bValid = false;
		}
		else
		{
			$(this).closest('.control-group').addClass('success');
		}
	});

	// set feedback
	if(bValid == false)
	{
		showAlert('error', tErrRequired, false);
	}

	return bValid;
}

function validateAmount(oForm)
{
	var bValid   = true;
	var oAmounts = oForm.find('input[data-type="amount"]');
	var iAmount  = 0;

	// loop through inputs fields and validate
	oAmounts.each(function()
	{
		iValue = $(this).val().replace('-', '');
		iValue = parseFloat($(this).val());
		if(!rAmount.test(iValue))
		{
			$(this).closest('.control-group').addClass('error').removeClass('success');
			bValid = false;
		}
		else
		{
			$(this).closest('.control-group').addClass('success');
		}
	});

	return bValid;
}

function validateNumber(oForm)
{
	var bValid   = true;
	var oAmounts = oForm.find('input[type="number"]');
	var iAmount  = 0;

	// loop through inputs fields and validate
	oAmounts.each(function()
	{
		iValue = $(this).val().replace('-', '');
		iValue = parseFloat($(this).val());
		if(!rAmount.test(iValue))
		{
			$(this).closest('.control-group').addClass('error').removeClass('success');
			bValid = false;
		}
		else
		{
			$(this).closest('.control-group').addClass('success');
		}
	});

	return bValid;
}

// check if dates are valid
// this function assumes the input mask already covered the correct date
// it only checks if only numbers are filled in
function validateDates(oForm)
{
	var bValid = true;
	var oDates = oForm.find('input[data-type="date"],input[type="date"]');

	// loop through email address input fields
	oDates.each(function()
	{
		// check if filled in
		if($(this).val() != '')
		{
			if(!validateDate($(this).val()) || $(this).closest('.control-group').hasClass('error'))
			{
				$(this).closest('.control-group').addClass('error').removeClass('success');
				bValid = false;
			}
			else
			{
				$(this).closest('.control-group').addClass('success');
			}
		}
	});

	// set feedback
	if(bValid == false)
	{
		showAlert('error', tErrDate, false);
	}

	return bValid
}

function validateDate(sDate)
{
	return RegExp(rDate).test(sDate)
}

// regex check for an e-mail field
function validateEmail(oForm)
{
	var bValid  = true;
	var oEmails = oForm.find('input[data-type="email"],input[type=email]');

	// loop through email address input fields
	oEmails.each(function()
	{
		var sEmail = $(this).val().trim();
		if(sEmail != '')
		{
			if(RegExp(rEmail).test(sEmail) == false || $(this).closest('.control-group').hasClass('error'))
			{
				$(this).closest('.control-group').addClass('error').removeClass('success');
				bValid = false;
			}
			else
			{
				// valid, when trimmed
				$(this).val(sEmail);
				$(this).closest('.control-group').addClass('success');
			}
		}
		else
		{
			// empty when trimmed
			$(this).val(sEmail);
		}
	});

	// set feedback
	if(bValid == false)
	{
		showAlert('error', tErrEmail, false);
	}

	return bValid;
}

// validate a phone number
// this function only checks the characters used, not the actual format
function validatePhone(oForm)
{
	var bValid  = true;
	var oPhones = oForm.find('input[data-type="phone"]');

	// loop through email address input fields
	oPhones.each(function()
	{
		// check if filled in
		if($(this).val() != '')
		{
			if(RegExp(rPhone).test($(this).val()) == false || $(this).closest('.control-group').hasClass('error'))
			{
				$(this).closest('.control-group').addClass('error').removeClass('success');
				bValid = false;
			}
			else
			{
				$(this).closest('.control-group').addClass('success');
			}
		}
	});

	// set feedback
	if(bValid == false)
	{
		showAlert('error', tErrPhone, false);
	}

	return bValid;
}

// validate a select box
function validateSelect(oForm)
{
	var bValid  = true;
	var oSelect = oForm.find('select:not(.multi-select)');

	// loop through all select boxes
	oSelect.each(function()
	{
		// check if an option is selected
		if($(this).val() === null)
		{
			$(this).closest('.control-group, .form-group').addClass('error has-error').removeClass('success');
			bValid = false;
		}
		else
		{
			$(this).closest('.control-group, .form-group').addClass('success').removeClass('error has-error');
		}
	});

	return bValid;
}


// show popup for an error that occured during processing
function showAlert(sType, sMessage, bUnhandled, timeout)
{
	// set translation for unhandled errors
	if(bUnhandled === true)
	{
		var sMessage = tErrorAlertMessage;
	}

	// set alert title
	var sTitle = '';
	if(sType === 'error')
	{
		sTitle = tTitleError;
		sType  = "error alert-danger";
	}
	else if(sType === 'warning')
	{
		sTitle = tTitleWarning;
	}
	else if(sType === 'info')
	{
		sTitle = tTitleInfo;
	}

	// wrap in error element
	var oError = $(wrapElement('<span>' + sTitle + '</span>' + sMessage, 'div'))
				.addClass('popupAlert fade alert alert-' + sType)
				.prepend('<a href="#" class="close" data-dismiss="alert">x</a>');

	// output to browser
	$('.alertContainer').append(oError);
	setTimeout(function(){oError.addClass('in')}, 100);
	if(typeof timeout != 'undefined')
	{
		setTimeout(function(){oError.remove()}, timeout);
	}
}
