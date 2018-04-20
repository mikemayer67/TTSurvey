drop view item_overview;

create view item_overview as
select 
  s.group_index,
  s.order_index,
  s.item_id,
  i.item_type,
  i.label,
  i.summary_label,
  i.anonymous
from
  survey s,
  survey_items i
where
  i.item_id=s.item_id and
  s.year = 2017;
  
select * from item_overview;