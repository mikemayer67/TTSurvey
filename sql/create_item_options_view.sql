drop view item_options;
create view item_options as
select
  a.item_id,
  a.option_label option_1,
  ifnull(b.option_label,"") option_2,
  ifnull(c.option_label,"") option_3,
  ifnull(d.option_label,"") option_4,
  ifnull(e.option_label,"") option_5,
  ifnull(f.option_label,"") option_6,
  ifnull(g.option_label,"") option_7,
  ifnull(h.option_label,"") option_8
from
  option_summary a
  left join option_summary b on (b.item_id=a.item_id and b.option_id = 2)
  left join option_summary c on (c.item_id=a.item_id and c.option_id = 3)
  left join option_summary d on (d.item_id=a.item_id and d.option_id = 4)
  left join option_summary e on (e.item_id=a.item_id and e.option_id = 5)
  left join option_summary f on (f.item_id=a.item_id and f.option_id = 6)
  left join option_summary g on (g.item_id=a.item_id and g.option_id = 7)
  left join option_summary h on (h.item_id=a.item_id and h.option_id = 8)
where
  a.option_id = 1