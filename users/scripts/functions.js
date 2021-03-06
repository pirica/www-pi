
var 
	settingsForm = false,
	actionsForm = false,
	profileAppsForm = false,
	profileAppActionsForm = false
;

$().ready(function(){
	
	settingsForm = $('.settings-form').length > 0;
	actionsForm = $('.actions-form').length > 0;
	profileAppsForm = $('.profile-apps-form').length > 0;
	profileAppActionsForm = $('.profile-app-actions-form').length > 0;
	
	if(settingsForm){
		$('.settings-form input[type=text]').focusout(function() {
			updateSetting(this, $(this).val());
		});
		$('.settings-form input[type=checkbox]').change(function() {
			updateSetting(this, ($(this).is(":checked") ? 1 : 0));
		});
		$('.settings-form select').change(function() {
			updateSetting(this, $(this).find('option:selected').val());
		});
		
	}
	
	if(actionsForm){
		$('.actions-form input[type=text]').focusout(function() {
			updateAction(this, $(this).val());
		});
		$('.actions-form input[type=checkbox]').change(function() {
			updateAction(this, ($(this).is(":checked") ? 1 : 0));
		});
		$('.actions-form select').change(function() {
			updateAction(this, $(this).find('option:selected').val());
		});
		
	}
	
	if(profileAppsForm){
		$('.profile-apps-form input[type=text]').focusout(function() {
			updateProfileApp(this, $(this).val());
		});
		$('.profile-apps-form input[type=checkbox]').change(function() {
			updateProfileApp(this, ($(this).is(":checked") ? 1 : 0));
		});
		$('.profile-apps-form select').change(function() {
			updateProfileApp(this, $(this).find('option:selected').val());
		});
		
	}
	
	if(profileAppActionsForm){
		$('.profile-app-actions-form input[type=text]').focusout(function() {
			updateProfileAppAction(this, $(this).val());
		});
		$('.profile-app-actions-form input[type=checkbox]').change(function() {
			updateProfileAppAction(this, ($(this).is(":checked") ? 1 : 0));
		});
		$('.profile-app-actions-form select').change(function() {
			updateProfileAppAction(this, $(this).find('option:selected').val());
		});
		
	}
});

function updateSetting(el, val){
	var 
		id_app = $(el).parents('.tab-pane').attr('id').replace('app', ''),
		code = $(el).data('code'),
		edittype = $(el).data('edittype')
	;
	$.ajax({
		url: 'index.php?action=do_setsetting' + 
				'&id_app=' + id_app + 
				'&code=' + code + 
				'&value=' + val + 
				'&edittype=' + edittype + 
			'',
		type: 'GET',
		cache: false,
		dataType: 'json',
		error: function(xhr, status, error) {
			//location.href = ...
		},
		success: function(data, textStatus, jqXHR){
			
		}
	});
}

function updateAction(el, val){
	var 
		id_app = $(el).parents('.tab-pane').attr('id').replace('app', ''),
		code = $(el).data('code'),
		field = $(el).data('field')
	;
	$.ajax({
		url: 'index.php?action=do_setaction' + 
				'&id_app=' + id_app + 
				'&code=' + code + 
				'&field=' + field + 
				'&value=' + val + 
			'',
		type: 'GET',
		cache: false,
		dataType: 'json',
		error: function(xhr, status, error) {
			//location.href = ...
		},
		success: function(data, textStatus, jqXHR){
			
		}
	});
}

function updateProfileApp(el, val){
	var 
		id_profile = $(el).parents('.tab-pane').attr('id').replace('profile', ''),
		id_app = $(el).data('id_app'),
		field = $(el).data('field')
	;
	$.ajax({
		url: 'index.php?action=do_setprofileapp' + 
				'&id_profile=' + id_profile + 
				'&id_app=' + id_app + 
				'&field=' + field + 
				'&value=' + val + 
			'',
		type: 'GET',
		cache: false,
		dataType: 'json',
		error: function(xhr, status, error) {
			//location.href = ...
		},
		success: function(data, textStatus, jqXHR){
			
		}
	});
}

function updateProfileAppAction(el, val){
	var 
		id_profile = $(el).parents('.tab-pane').attr('id').replace('profile', ''),
		id_app_action = $(el).data('id_app_action'),
		field = $(el).data('field')
	;
	$.ajax({
		url: 'index.php?action=do_setprofileappaction' + 
				'&id_profile=' + id_profile + 
				'&id_app_action=' + id_app_action + 
				'&field=' + field + 
				'&value=' + val + 
			'',
		type: 'GET',
		cache: false,
		dataType: 'json',
		error: function(xhr, status, error) {
			//location.href = ...
		},
		success: function(data, textStatus, jqXHR){
			
		}
	});
}
