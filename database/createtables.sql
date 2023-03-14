DROP DATABASE IF EXISTS projectsampledata;
CREATE DATABASE projectsampledata;
USE projectsampledata;

-- Drop any if already present
drop table if exists Thumbsup CASCADE;
drop table if exists Answer CASCADE;
drop table if exists Question CASCADE;
drop table if exists Users CASCADE;
drop table if exists UserStatus CASCADE;
drop table if exists Topic CASCADE;
drop table if exists Category CASCADE;
drop table if exists Award CASCADE;

-- Create table and insert sample data
create table UserStatus(
	sname varchar(10) primary key,
    spoints integer not null
);

insert into UserStatus(sname, spoints) values ('basic', 0);
insert into UserStatus(sname, spoints) values ('advanced', 300);
insert into UserStatus(sname, spoints) values ('expert', 1000);

create table Users(
	uid integer primary key auto_increment,
	username varchar(100) not null,
	email varchar(50) not null,
	upassword varchar(20) not null,
	city varchar(30),
	state varchar(2),
	country varchar(30),
	uprofile varchar(1000),
	upoints integer not null default 0,
    ustatus varchar(10) not null default 'basic',
    foreign key (ustatus) references UserStatus(sname)
);

insert into Users(username, email, upassword, city, state, country, uprofile) values ('Quasarleoi', 'quasarleoi@gmail.com', '76kB^X^9Ux', 'New York', 'NY', 'US', 'Amateur astronomer');
insert into Users(username, email, upassword, city, state, country) values ('Algolvenus', 'algolvenus@gmail.com', '1e%SUG&1Un', 'Brooklyn', 'NY', 'US');
insert into Users(username, email, upassword, city, state, country) values ('Magnetar', 'magnetar@gmail.com', '!42V4XPmj&', 'Chicago', 'IL', 'US');
insert into Users(username, email, upassword, city, state, country) values ('Saturnmir', 'saturnmir@gmail.com', 'Y%RwqW9hc6', 'San Francisco', 'CA', 'US');
insert into Users(username, email, upassword, city, state, country) values ('Sergue2mir', 'sergue2mir@gmail.com', 'K&d9wcX&R9', 'Houston', 'TX', 'US');
insert into Users(username, email, upassword, city, state, country) values ('Suninsight', 'suninsight@gmail.com', 'n#T#B2bjV&', 'San Antonio', 'TX', 'US');
insert into Users(username, email, upassword, city, state, country) values ('Haumeajuno', 'haumeajuno@gmail.com', 'QsW#S4@%DU', 'Champaign', 'IL', 'US');
insert into Users(username, email, upassword, city, state, country) values ('Vegacomets', 'vegacomets@gmail.com', 'NdLCnNa7b%', 'San Jose', 'CA', 'US');
insert into Users(username, email, upassword, city, state, country) values ('Nixdeneb', 'nixdeneb@gmail.com', 'LRaS6W4%uv', 'Urbana', 'IL', 'US');
insert into Users(username, email, upassword, city, state, country) values ('Junosunnix', 'junosunnix@gmail.com', 'qV5f2Y5qN&', 'Jersey City', 'NJ', 'US');

create table Category(
	cid integer primary key auto_increment,
	cname varchar(30) not null
);

insert into Category(cname) values ('Science and Technology');
insert into Category(cname) values ('Culture and History');

create table Topic(
	tid integer primary key auto_increment,
    cid integer not null,
	tname varchar(30) not null,
    foreign key (cid) references Category(cid)
);

insert into Topic(cid, tname) values (1, 'Progamming');
insert into Topic(cid, tname) values (1, 'Database');
insert into Topic(cid, tname) values (1, 'Networking');
insert into Topic(cid, tname) values (1, 'Algorithm');
insert into Topic(cid, tname) values (1, 'Electrical Engineering');
insert into Topic(cid, tname) values (2, 'Asian History');
insert into Topic(cid, tname) values (2, 'European History');
insert into Topic(cid, tname) values (2, 'African History');
insert into Topic(cid, tname) values (2, 'American history');

create table Question(
	qid integer primary key auto_increment,
    quid integer not null,
    tid integer not null,
    qtitle varchar(200) not null,
    qbody text not null,
    qdate date default(current_date),
    qtime time default(current_time),
    isResolved integer default 0,
    foreign key (quid) references Users(uid),
    foreign key (tid) references Topic(tid),
	check (isResolved in (0, 1))
);

insert into Question(quid, tid, qtitle, qbody, qdate, qtime) values (1, 2, 'Is there a Boolean data type in Microsoft SQL Server?', 
	'Is there a Boolean data type in Microsoft SQL Server like there is in MySQL? If not, what is the alternative in MS SQL Server?', 
    '2022-03-02', '09:30:23');
insert into Question(quid, tid, qtitle, qbody, qdate, qtime) values (2, 2, 'What is the difference between "INNER JOIN" and "OUTER JOIN" in SQL?', 
	'What is the difference between "INNER JOIN" and "OUTER JOIN"? Also how do LEFT JOIN, RIGHT JOIN and FULL JOIN fit in?',
    '2022-03-03', '10:39:22');
insert into Question(quid, tid, qtitle, qbody, qdate, qtime) values (3, 1, 'What are the differences between a HashMap and a Hashtable in Java?', 
	'What are the differences between a HashMap and a Hashtable in Java? Which is more efficient for non-threaded applications?',
    '2022-03-07', '22:23:34');
insert into Question(quid, tid, qtitle, qbody, qdate, qtime) values (7, 1, 'How to reuse html for footer?', 
	"I see multiple answers about creating php files in order to reuse headers/footers, but nothing specific enough. I can't seem to get it to work. What exactly would the php file look like and what exactly would my html file look like (given the code as it currently is, below)? do I have to convert all my html files to php files in order to use the php include line?",
    '2022-05-09', '17:16:32');
insert into Question(quid, tid, qtitle, qbody, qdate, qtime) values (7, 1, 'How to fix Headers already sent error in PHP?', 
	"When running my script, I am getting several errors like this: Warning: Cannot modify header information - headers already sent by (output started at /some/file.php:12) in /some/file.php on line 23. What could be the reason for this? And how to fix it?",
    '2022-05-12', '12:16:17');   
    

-- for keyword search
alter table Question
add fulltext(qtitle);

alter table Question
add fulltext(qbody);

create table Answer(
	aid integer primary key auto_increment,
    qid integer not null,
    auid integer not null,
    abody text not null,
    adate date default(current_date),
    atime time default(current_time),
    isBestAns integer default 0,
    foreign key (qid) references Question(qid),
    foreign key (auid) references Users(uid),
    check (isBestAns in (0, 1))
);

insert into Answer(qid, auid, abody, adate, atime, isBestAns) values (1, 3, 
	"You could use the BIT datatype to represent boolean data. A BIT field's value is either 1, 0, or null.", 
    '2022-03-07', '16:23:26', 1);
insert into Answer(qid, auid, abody, adate, atime, isBestAns) values (1, 4, 
	"Alternatively, you could use the strings 'true' and 'false' in place of 1 or 0", 
    '2022-03-04', '17:25:27', 0);
insert into Answer(qid, auid, abody, adate, atime, isBestAns) values (1, 5, 
	"There is boolean data type in SQL Server. Its values can be TRUE, FALSE or UNKNOWN. However, the boolean data type is only the result of a boolean expression.", 
	'2022-03-05', '19:37:12', 0);
insert into Answer(qid, auid, abody, adate, atime, isBestAns) values (2, 5, 
	"An inner join retrieve the matched rows only.
    Whereas an outer join retrieve the matched rows from one table and all rows in other table. The result depends on which one you are using:
    Left: Matched rows in the right table and all rows in the left table;
    Right: Matched rows in the left table and all rows in the right table; or
    Full: All rows in all tables. It doesn't matter if there is a match or not.", 
	'2022-03-12', '23:52:37', 0);
insert into Answer(qid, auid, abody, adate, atime, isBestAns) values (2, 6, 
	"Joins are used to combine the data from two tables, with the result being a new, temporary table. 
    Joins are performed based on something called a predicate, which specifies the condition to use in order to perform a join. 
    The difference between an inner join and an outer join is that an inner join will return only the rows that actually match based on the join predicate.", 
	'2022-03-10', '22:45:34', 0);
insert into Answer(qid, auid, abody, adate, atime, isBestAns) values (2, 7, 
	"Assuming you're joining on columns with no duplicates, which is a very common case:
    An inner join of A and B gives the result of A intersect B. An outer join of A and B gives the results of A union B.
    A left outer join will give all rows in A, plus any common rows in B.
    A right outer join will give all rows in B, plus any common rows in A.
    A full outer join will give you the union of A and B, i.e. all the rows in A and all the rows in B. If something in A doesn't have a corresponding datum in B, then the B portion is null, and vice versa.", 
	'2022-03-17', '12:27:12', 1);
insert into Answer(qid, auid, abody, adate, atime, isBestAns) values (3, 7, 
	"There are several differences between HashMap and Hashtable in Java:
    Hashtable is synchronized, whereas HashMap is not. This makes HashMap better for non-threaded applications, as unsynchronized Objects typically perform better than synchronized ones.
    Hashtable does not allow null keys or values. HashMap allows one null key and any number of null values.
    One of HashMap's subclasses is LinkedHashMap, so in the event that you'd want predictable iteration order (which is insertion order by default), you could easily swap out the HashMap for a LinkedHashMap. This wouldn't be as easy if you were using Hashtable.
    Since synchronization is not an issue for you, I'd recommend HashMap. If synchronization becomes an issue, you may also look at ConcurrentHashMap.", 
	'2022-03-22', '07:42:17', 1);
insert into Answer(qid, auid, abody, adate, atime, isBestAns) values (3, 9, 
	"Note, that a lot of the answers state that Hashtable is synchronized. In practice this buys you very little. 
    The synchronization is on the accessor/mutator methods will stop two threads adding or removing from the map concurrently, but in the real world, you will often need additional synchronization.", 
	'2022-03-27', '21:17:52', 0); 
insert into Answer(qid, auid, abody, adate, atime, isBestAns) values (3, 10, 
	"Hashtable is considered legacy code. There's nothing about Hashtable that can't be done using HashMap or derivations of HashMap, so for new code, I don't see any justification for going back to Hashtable.", 
	'2022-03-28', '23:17:26', 0);
insert into Answer(qid, auid, abody, adate, atime, isBestAns) values (4, 3, 
	"You could place your footer HTML in a separate file, and then use javascript to load the HTML content of that file, and append it to a div in your page.",
    '2022-05-11', '22:13:50', 0);
insert into Answer(qid, auid, abody, adate, atime, isBestAns) values (5, 10, 
	"Functions that send/modify HTTP headers must be invoked before any output is made. Otherwise the call fails.",
    '2022-05-12', '23:30:12', 1);
insert into Answer(qid, auid, abody, adate, atime, isBestAns) values (5, 2, 
	"It is because of this line: printf('Hi %s,</br/>', $name); You should not print/echo anything before sending the headers.",
    '2022-05-12', '23:30:12', 0);

create table Thumbsup(
	aid integer not null,
    tuid integer not null,
    tdate date default(current_date),
    ttime time default(current_time),
    primary key (aid, tuid),
    foreign key (aid) references Answer(aid),
    foreign key (tuid) references Users(uid)
);

insert into Thumbsup values (1, 1, '2022-03-09', '22:45:12');
insert into Thumbsup values (1, 8, '2022-03-12', '22:23:17');
insert into Thumbsup values (1, 9, '2022-03-15', "10:22:39");
insert into Thumbsup values (2, 3, '2022-03-07', '14:25:37');
insert into Thumbsup values (4, 1, '2022-03-16', '17:52:23');
insert into Thumbsup values (5, 2, '2022-03-12', '12:34:25');
insert into Thumbsup values (6, 1, '2022-03-22', '10:27:34');
insert into Thumbsup values (6, 2, '2022-03-23', '07:23:56');
insert into Thumbsup values (6, 3, '2022-03-25', '09:57:12');
insert into Thumbsup values (6, 4, '2022-03-26', '21:42:14');
insert into Thumbsup values (6, 5, '2022-03-26', '05:17:29');
insert into Thumbsup values (6, 6, '2022-03-29', '16:23:37');
insert into Thumbsup values (6, 9, '2022-03-31', '06:34:12');
insert into Thumbsup values (6, 10, '2022-04-02', '23:58:45');
insert into Thumbsup values (7, 3, '2022-04-03', '18:54:16');
insert into Thumbsup values (7, 5, '2022-04-05', '20:52:34');
insert into Thumbsup values (7, 6, '2022-04-09', '19:29:54');

create table Award(
	aname varchar(10) primary key,
    apoints integer
);

insert into Award values ('base', 10);
insert into Award values ('thumbsup', 30);
insert into Award values ('bestanswer',150);
