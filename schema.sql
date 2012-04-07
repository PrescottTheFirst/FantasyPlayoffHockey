USE FantasyPlayoffHockey;

CREATE TABLE USERS (
	userid integer AUTO_INCREMENT,
	username varchar(20),
	password varchar(50),
	realname varchar(50),
	PRIMARY KEY (userid) 
);

CREATE TABLE MATCHUPS (
	matchid integer AUTO_INCREMENT,
	year varchar(4),
	round integer,
	conference varchar(10),
	top_seed_rank integer,
	top_seed_team varchar(30),
	bottom_seed_rank integer,
	bottom_seed_team varchar(30),
	PRIMARY KEY (matchid)
);

CREATE TABLE PICKS (
	userid integer REFERENCES USERS(userid),
	matchid integer REFERENCES MATCHUPS(matchid),
	pick varchar(30),
	games integer,
	goal_diff integer,
	points integer,
	PRIMARY KEY (userid, matchid)
);

CREATE TABLE RESULTS (
	matchid integer REFERENCES MATCHUPS(matchid),
	winner varchar(30),
	games integer,
	goal_diff integer,
	PRIMARY KEY (matchid)
);

CREATE TABLE GAMES (
	matchid integer REFERENCES MATCHUPS(matchid),
	date date,
	game_number integer,
	top_seed_score integer,
	bottom_seed_score integer,
	PRIMARY KEY (matchid, game_number)
);
