-- (1) Write a query that computes for each user their current status based on their answers and your own chosen criteria for defining the status.
with BasePoints(uid, bpoints) as (
	select auid, count(*) * (select apoints from Award where aname = 'base')
    from Answer
    group by auid
),
ThumbsupPoints(uid, tpoints) as (
	select auid, count(*) * (select apoints from Award where aname = 'thumbsup')
	from Thumbsup natural join Answer
    group by auid
),
BestAnsPoints(uid, epoints) as (
	select auid, count(*) * (select apoints from Award where aname = 'bestanswer')
    from Answer
    where isBestAns = 1
    group by auid
),
Upoints(uid, newupoints) as (
	select uid, coalesce(bpoints, 0) + coalesce(tpoints, 0) + coalesce(epoints, 0)
    from BasePoints natural left outer join ThumbsupPoints natural left outer join BestAnsPoints
)
select uid, coalesce(newupoints, 0) as upoints, (case 
	when newupoints is null then 'basic'
	when newupoints < (select spoints from UserStatus where sname = 'advanced') then 'basic'
    when newupoints >= (select spoints from UserStatus where sname = 'advanced') and newupoints < (select spoints from UserStatus where sname = 'expert') then 'advanced'
    else 'expert'
    end) as ustatus
from Users natural left outer join Upoints
order by uid;


-- update user upoints and ustatus
drop procedure if exists updateUserStatus;
delimiter //
create procedure updateUserStatus()
begin
	declare done integer default false;

    declare Cursor_uid integer;
    declare Cursor_upoints integer;
    declare Cursor_ustatus varchar(10);

    declare curs Cursor for
		(with BasePoints(uid, bpoints) as (
			select auid, count(*) * (select apoints from Award where aname = 'base')
			from Answer
			group by auid
		),
        ThumbsupPoints(uid, tpoints) as (
			select auid, count(*) * (select apoints from Award where aname = 'thumbsup')
			from Thumbsup natural join Answer
			group by auid),
		BestAnsPoints(uid, epoints) as (
			select auid, count(*) * (select apoints from Award where aname = 'bestanswer')
			from Answer
			where isBestAns = 1
			group by auid),
		Upoints(uid, newupoints) as (
			select uid, coalesce(bpoints, 0) + coalesce(tpoints, 0) + coalesce(epoints, 0)
			from BasePoints natural left outer join ThumbsupPoints natural left outer join BestAnsPoints)
		select uid, coalesce(newupoints, 0) as upoints, (case
									when newupoints is null then 'basic'
									when newupoints < (select spoints from UserStatus where sname = 'advanced') then 'basic'
									when newupoints >= (select spoints from UserStatus where sname = 'advanced') and newupoints < (select spoints from UserStatus where sname = 'expert') then 'advanced'
									else 'expert'
									end) as ustatus
		from Users natural left outer join Upoints);

    declare continue handler for not found set done = true;

open curs;
loop_thru_user: loop
		fetch curs into Cursor_uid, Cursor_upoints, Cursor_ustatus;

        if done then
			leave loop_thru_user;
end if;

update Users
set upoints = Cursor_upoints, ustatus = Cursor_ustatus
where uid = Cursor_uid;

end loop;
close curs;
end; //
delimiter ;
call updateUserStatus();


-- (2) For a given question (say identified by an ID), output all answers to the question in chronological order from first to last.
select abody, adate, atime, isBestAns
from Answer
where qid = 3
order by adate, atime;

-- (3) For each topic in the topic hierarchy, output the number of questions posted and total number of answers posted within that topic.
select tname, count(distinct qid) as QuestionCount, count(aid) as AnswerCount
from Answer natural join Question natural join Topic
group by tname;

-- (4) Given a keyword query, output all questions that match the query and that fall into a particular topic, sorted from highest to lowest relevance.
-- Sample keyword: 'JOIN'
alter table Question
add fulltext(qtitle);

alter table Question
add fulltext(qbody);

set @keyword := 'sql join';
select qid, qtitle, qbody, match(qtitle) against(@keyword in natural language mode) * 1.2 + match(qbody) against(@keyword in natural language mode) as relevance
from Question
where match(qtitle) against(@keyword in natural language mode) or match(qbody) against(@keyword in natural language mode)
order by relevance desc;


set @keyword := 'sql join';
SELECT qid, qtitle, qdate, qtime, quid, username, 
	MATCH(qtitle) AGAINST(@keyword IN NATURAL LANGUAGE MODE) * 1.2 + MATCH(qbody) AGAINST(@keyword IN NATURAL LANGUAGE MODE) as relevance
FROM Question, Users 
WHERE quid = uid
ORDER BY relevance DESC;