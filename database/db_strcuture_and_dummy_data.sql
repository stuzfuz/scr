DROP DATABASE fh_2018_scm4_S1610307036;
CREATE DATABASE fh_2018_scm4_S1610307036  CHARACTER SET utf8 COLLATE utf8_general_ci;

USE fh_2018_scm4_S1610307036;

CREATE TABLE user (
	id INT(11) NOT NULL AUTO_INCREMENT,
	username VARCHAR(255) NOT NULL,
	email VARCHAR(255) NOT NULL,
	firstname VARCHAR(255) NOT NULL,
	lastname VARCHAR(255) NOT NULL,
	created_at INT(14) NOT NULL, 
	password VARCHAR(255) NOT NULL, 
	deleted TINYINT(1) NOT NULL, 
	
	PRIMARY KEY (id)
) ENGINE=InnoDB AUTO_INCREMENT=1 CHARSET=utf8;

CREATE TABLE channel (
	id INT(11) NOT NULL AUTO_INCREMENT,
	name VARCHAR(255) NOT NULL,
	created_at INT(14) NOT NULL, 
	created_by_user_id INT(11) NOT NULL,
	deleted TINYINT(1) NOT NULL, 
	PRIMARY KEY (id)
) ENGINE=InnoDB AUTO_INCREMENT=1 CHARSET=utf8;

--  contraint created_by_user_id -> user.id
ALTER TABLE channel
ADD CONSTRAINT channel_created_by FOREIGN KEY (created_by_user_id) REFERENCES user (id) ON DELETE CASCADE ON UPDATE CASCADE;


-- TODO: which user has access to which channel ..?  WHY DID I DO THIS?
-- CREATE TABLE user_channel (
-- 	id INT(11) NOT NULL AUTO_INCREMENT,
-- 	user_id INT(11) NOT NULL,
-- 	channel_id INT(11) NOT NULL
-- 	created_at INT(14) NOT NULL, 
-- 	deleted TINYINT(1) NOT NULL, 
-- 	PRIMARY KEY (id),
-- ) ENGINE=InnoDB AUTO_INCREMENT=1 CHARSET=utf8;
-- TODO: contraint user_id -> user.id
-- TODO: contraint channel_id -> channel.id

--  contraint created_by_user_id -> user.id


CREATE TABLE message (
	id INT(11) NOT NULL AUTO_INCREMENT,
	user_id INT(11) NOT NULL,
	channel_id INT(11) NOT NULL,
	txt VARCHAR(1000) NOT NULL, 
	created_at INT(14) NOT NULL, 
	deleted TINYINT(1) NOT NULL, 
	PRIMARY KEY (id)
) ENGINE=InnoDB AUTO_INCREMENT=1 CHARSET=utf8;

-- TODO: contraint user_id -> user.id
-- TODO: contraint channel_id -> channel.id
ALTER TABLE message
ADD CONSTRAINT message_created_by_user_id FOREIGN KEY (user_id) REFERENCES user (id) ON DELETE CASCADE ON UPDATE CASCADE;
ALTER TABLE message
ADD CONSTRAINT message_belongs_to_channel FOREIGN KEY (channel_id) REFERENCES channel (id) ON DELETE CASCADE ON UPDATE CASCADE;






-- combined key (user_id, message_id
CREATE TABLE message_flags (
	id INT(11) NOT NULL AUTO_INCREMENT,
	user_id INT(11) NOT NULL,
	message_id INT(11) NOT NULL,
	created_at INT(14) NOT NULL, 
	important TINYINT(1) NOT NULL,
	PRIMARY KEY (id, user_id)
) ENGINE=InnoDB AUTO_INCREMENT=1 CHARSET=utf8;
-- TODO: contraint user_id -> user.id
-- TODO: contraint message_id -> message.id
ALTER TABLE message_flags
ADD CONSTRAINT message_flag_created_by_user FOREIGN KEY (user_id) REFERENCES user (id) ON DELETE CASCADE ON UPDATE CASCADE;
ALTER TABLE message_flags
ADD CONSTRAINT message_id_flagged FOREIGN KEY (message_id) REFERENCES message (id) ON DELETE CASCADE ON UPDATE CASCADE;




-- INSERT INTO categories VALUES (1, 'Mobile & Wireless Computing');
-- INSERT INTO categories VALUES (2, 'Functional Programming');
-- INSERT INTO categories VALUES (3, 'C / C++');
-- INSERT INTO categories VALUES (4, '<< New Publications >>');

-- INSERT INTO books VALUES (1, 1, 'Hello, Android: Introducing Google''s Mobile Development Platform', 'Ed Burnette', '9781934356562', 19.97);
-- INSERT INTO books VALUES (2, 1, 'Android Wireless Application Development', 'Shane Conder, Lauren Darcey', '0321743016', 31.22);
-- INSERT INTO books VALUES (5, 1, 'Professional Flash Mobile Development', 'Richard Wagner', '0470620072', 19.90);
-- INSERT INTO books VALUES (7, 1, 'Mobile Web Design For Dummies', 'Janine Warner, David LaFontaine', '9780470560969', 16.32);
-- INSERT INTO books VALUES (11, 2, 'Introduction to Functional Programming using Haskell', 'Richard Bird', '9780134843469', 74.75);
-- INSERT INTO books VALUES (12, 2, 'Scripting (Attacks) for Beginners - <script type="text/javascript">alert(''All your base are belong to us!'');</script>', 'John Doe', '1234567890', 9.99);
-- INSERT INTO books VALUES (14, 2, 'Expert F# (Expert''s Voice in .NET)', 'Antonio Cisternino, Adam Granicz, Don Syme', '9781590598504', 47.64);
-- INSERT INTO books VALUES (16, 3, 'C Programming Language (2nd Edition)', 'Brian W. Kernighan, Dennis M. Ritchie', '0131103628', 48.36);
-- INSERT INTO books VALUES (27, 3, 'C++ Primer Plus (5th Edition)', 'Stephan Prata', ' 9780672326974', 36.94);
-- INSERT INTO books VALUES (29, 3, 'The C++ Programming Language', 'Bjarne Stroustrup', '0201700735', 67.49);
	
INSERT INTO user VALUES (1, 'admin', 'admin@poormansslack.at', 'admin', 'admin',UNIX_TIMESTAMP(NOW())-343434, SHA1('admin'), 0);





-- combined key (user_id, message_id
CREATE TABLE route (
	id INT(11) NOT NULL AUTO_INCREMENT,
	route VARCHAR(1000) NOT NULL, 
	type VARCHAR(20) NOT NULL, 
	headertemplate VARCHAR(300)  NULL, 
	contenttemplate VARCHAR(300) NULL, 
	footertemplate VARCHAR(300)  NULL, 
	routeparam VARCHAR(300)  NULL, 
	controller VARCHAR(300) NOT NULL, 
	controllername VARCHAR(300) NOT NULL, 
	verb VARCHAR(30) NOT NULL, 
	PRIMARY KEY (id),
	UNIQUE KEY (route)
) ENGINE=InnoDB AUTO_INCREMENT=1 CHARSET=utf8;


INSERT INTO route (route, type, headertemplate, contenttemplate ,footertemplate, controller, controllername, verb)
VALUES ('/', 'PAGE', 'client/header.html', 'server/components/main/content.html', 'client/footer.html', 'server/components/main/', 'MainController', 'GET');

INSERT INTO route (route, type, headertemplate,contenttemplate ,	footertemplate, controller, controllername, verb)
VALUES ('/login' ,'PAGE', NULL, 'server/components/login/login.html', NULL, 'server/components/login/', 'LoginController', 'GET');


INSERT INTO route (route, type, headertemplate, contenttemplate,	footertemplate, controller, controllername, verb)
VALUES ('/register' ,'PAGE', NULL, 'server/components/register/register.html', NULL, 'server/components/register/', 'RegisterController', 'GET');


INSERT INTO route (route, type, headertemplate,contenttemplate ,	footertemplate, routeparam, controller, controllername, verb)
VALUES ('/messages' ,'PAGE', 'client/header.html', 'server/components/messages/content.html', 'client/footer.html', 'channelname', 'server/components/messages/', 'MessagesController', 'GET');


INSERT INTO route (route, type, headertemplate,contenttemplate ,	footertemplate, routeparam, controller, controllername, verb)
VALUES ('/dev' ,'PAGE', 'client/header.html', 'server/components/dev/content.html', 'client/footer.html', NULL, 'server/components/dev/', 'DevController', 'GET');



INSERT INTO route (route, type, headertemplate,contenttemplate ,	footertemplate, routeparam, controller, controllername, verb)
VALUES ('/api/login' ,'API', NULL, NULL, NULL, NULL, 'server/components/login/', 'LoginApiController', 'POST');






INSERT INTO user (username, email, firstname, lastname, created_at, password, deleted) 
VALUES ( 'donaldduck', 'donald@poormansslack.at', 'donald', 'duck', 	UNIX_TIMESTAMP(NOW())-444444, SHA1('donald'), 0);


INSERT INTO user (username, email, firstname, lastname, created_at, password, deleted) 
VALUES ( 'dagobertdduck', 'dagobert@poormansslack.at', 'dagobert', 'duck', 	UNIX_TIMESTAMP(NOW())-5555555, SHA1('dagobert'), 0);


INSERT INTO user (username, email, firstname, lastname, created_at, password, deleted) 
VALUES ( 'daisyduck', 'daisy@poormansslack.at', 'daisy', 'duck', 	UNIX_TIMESTAMP(NOW())-6666666, SHA1('dais'), 0);


INSERT INTO user (username, email, firstname, lastname, created_at, password, deleted) 
VALUES ( 'goofy', 'goofy@poormansslack.at', 'goofy', 'duck', 	UNIX_TIMESTAMP(NOW())-777777, SHA1('goofy'), 0);




INSERT INTO channel (name, created_at, created_by_user_id, deleted) 
VALUES ( 'money',UNIX_TIMESTAMP(NOW())-787777, 3, 0);


INSERT INTO channel (name, created_at, created_by_user_id, deleted) 
VALUES ( 'bad luck', UNIX_TIMESTAMP(NOW())-717777, 2, 0);


INSERT INTO channel (name, created_at, created_by_user_id, deleted) 
VALUES ( 'dogs', UNIX_TIMESTAMP(NOW())-177777, 4, 0 );
 



INSERT INTO message (user_id, channel_id, txt, created_at, deleted) 
VALUES (3, 1,  'channel owner: dagobert, message by @dagobertduck, channel name is "money"', UNIX_TIMESTAMP(NOW())-888888,  0 );

INSERT INTO message (user_id, channel_id, txt, created_at, deleted) 
VALUES (2, 1,  'channel owner: dagobert, message by @donaldduck, channel name is "money"', UNIX_TIMESTAMP(NOW())-9834,  0 );

INSERT INTO message (user_id, channel_id, txt, created_at, deleted) 
VALUES (4, 1,  'channel owner: dagobert, message by @daisyduck, channel name is "money"', UNIX_TIMESTAMP(NOW())-12345,  0 );


INSERT INTO message (user_id, channel_id, txt, created_at, deleted) 
VALUES (4, 2,  'channel owner: donald, message by @daisyduck, channel name is "bad luck"', UNIX_TIMESTAMP(NOW())-393434,  0 );


INSERT INTO message (user_id, channel_id, txt, created_at, deleted) 
VALUES (5, 2,  'channel owner: donald, message by @goofy, channel name is "bad luck"', UNIX_TIMESTAMP(NOW())-243434,  0 );
