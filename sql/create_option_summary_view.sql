create view option_summary as
select 
 item_id,
 option_id,
 concat (
   if(parent_id is null, "" , concat(parent_option,": ")),
   option_label
 ) as option_label
 from 
all_options;