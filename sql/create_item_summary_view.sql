drop view item_summary;

create view item_summary as
select 
  i.group_index,
  i.order_index,
  i.item_id,
  i.item_type,
  ifnull(l.type,"") as label_type,
  ifnull(l.value,ifnull(i.label,"")) as survey_label,
  ifnull(
    concat(
    "indent=",l.level,
    if(l.bold=0, "", "; bold"),
    if(l.italic=0, "", "; italic"),
    if(isnull(l.size),"",concat("; size=",l.size))), "") as advanced_label_options,
  if(i.anonymous=0,"","X") as can_be_anonymous,
  ifnull(i.summary_label,"") as summary_label
from
  item_overview i
  left join survey_labels l on (l.item_id = i.item_id)
;

select * from item_summary;
