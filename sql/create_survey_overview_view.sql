drop view survey_overview;

create view survey_overview as
select
    s.group_index as group_index,
    if(g.label is null,"(unnamed)", g.label) as group_label,
    s.order_index as group_order,
    s.item_id,
    concat(a.item_type, if(a.label_type is null, "", concat(":",a.label_type))) as item_type,
    a.survey_label,
    a.advanced_label_options as 'label_options',
    a.summary_label,
    a.can_be_anonymous as 'anon',
	ifnull(b.option_1,"") as option_1,
	b.option_2,
	b.option_3,
	b.option_4,
	b.option_5,
	b.option_6,
	b.option_7,
	b.option_8,
	if(c.qualification_option is null, "",
	   concat( "option_", c.qualification_option, ": ", c.qualification_hint )
	) as open_option_hint
from 
    survey s,
	survey_groups g,
	item_summary a
	left join item_options b on(a.item_id=b.item_id)
	left join survey_role_qualifiers c on (c.item_id=a.item_id)
where 
    s.year=2017 and 
    g.group_index = s.group_index and
	g.year=s.year and
    a.item_id=s.item_id
;

select * 
from survey_overview
order by group_index,group_order;