<?php

/*

select distinct relative_directory from t_file where active = 0


delete inactive files
mark parent dirs of these to be reindexed

??? delete inactive directories with no files (including inactive)



update t_directory set date_last_checked = null
where  relative_directory in (
select distinct relative_directory from t_file where active = 0
);

delete from t_file where active = 0 
and date_deleted < now() - interval 3 month
;

delete from t_file where active = 0 
and date_deleted is null
;


*/
?>