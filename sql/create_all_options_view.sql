drop view all_options;

create view all_options as
select 
  a.item_id, 
  a.option_id,
  a.option_label as option_label, 
  b.option_id    as parent_id, 
  b.option_label as parent_option
from
  survey_role_options a
  left join survey_role_options b on (b.item_id=a.item_id and b.option_id=a.require_option_id)