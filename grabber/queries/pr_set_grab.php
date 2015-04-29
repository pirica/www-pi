<cfstoredproc procedure="pr_set_grab" datasource="#connDefault#">
	<cfprocparam value="#id_grab#" cfsqltype="CF_SQL_INTEGER">
	<cfprocparam value="#grab_description#" cfsqltype="CF_SQL_VARCHAR">
	<cfprocparam value="#grab_url#" cfsqltype="CF_SQL_VARCHAR">
	<cfprocparam value="#grab_path#" cfsqltype="CF_SQL_VARCHAR">
	<cfprocparam value="#grab_filename#" cfsqltype="CF_SQL_VARCHAR">
	<cfprocparam value="#grab_timeout#" cfsqltype="CF_SQL_INTEGER">
	<cfprocparam value="#grab_autogenerate_data#" cfsqltype="CF_SQL_INTEGER">
	<cfprocparam value="#grab_max_grabbers#" cfsqltype="CF_SQL_INTEGER">
	
</cfstoredproc>