drop view groups_overview;

create view groups_overview as
select
group_index,
ifnull(label,"(unnamed)") as group_label,
if(collapsible=1,"YES","NO") as collapsible,
if(comment=1, "YES","NO") as comment,
ifnull(comment_qualifier,"") comment_qualifier
from survey_groups
where
  year=2017;