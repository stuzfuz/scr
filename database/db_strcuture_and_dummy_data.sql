DROP DATABASE fh_2018_scm4_S1610307036;
CREATE DATABASE fh_2018_scm4_S1610307036  CHARACTER SET utf8 COLLATE utf8_general_ci;

USE fh_2018_scm4_S1610307036;

CREATE TABLE user (
	id INT(11) NOT NULL AUTO_INCREMENT,
	username VARCHAR(255) NOT NULL,
	firstname VARCHAR(255) NOT NULL,
	lastname VARCHAR(255) NOT NULL,
	created_at INT(14) DEFAULT UNIX_TIMESTAMP(NOW()), 
	password VARCHAR(255) NOT NULL, 
	deleted TINYINT(1) NOT NULL DEFAULT FALSE,
	isadmin TINYINT(1) NOT NULL DEFAULT FALSE,
	PRIMARY KEY (id),
	UNIQUE KEY (username) 
) ENGINE=InnoDB AUTO_INCREMENT=1 CHARSET=utf8;

CREATE TABLE channel (
	id INT(11) NOT NULL AUTO_INCREMENT,
	name VARCHAR(255) NOT NULL,
	description VARCHAR(255) NOT NULL,
	created_at INT(14) DEFAULT UNIX_TIMESTAMP(NOW()), 
	created_by_user_id INT(11) NOT NULL,
	deleted TINYINT(1) NOT NULL DEFAULT FALSE, 
	PRIMARY KEY (id)
) ENGINE=InnoDB AUTO_INCREMENT=1 CHARSET=utf8;

--  contraint created_by_user_id -> user.id
ALTER TABLE channel
ADD CONSTRAINT channel_created_by FOREIGN KEY (created_by_user_id) REFERENCES user (id) ON DELETE CASCADE ON UPDATE CASCADE;

CREATE TABLE ref_user_channel (
	id INT(11) NOT NULL AUTO_INCREMENT,
	user_id INT(11) NOT NULL,
	channel_id INT(11) NOT NULL,
	created_at INT(14) DEFAULT UNIX_TIMESTAMP(NOW()), 
	deleted TINYINT(1) NOT NULL DEFAULT FALSE,
	PRIMARY KEY (id),
	UNIQUE KEY (user_id, channel_id)
) ENGINE=InnoDB AUTO_INCREMENT=1 CHARSET=utf8; 
ALTER TABLE ref_user_channel
ADD CONSTRAINT ref_user_channel_user_id FOREIGN KEY (user_id) REFERENCES user (id) ON DELETE CASCADE ON UPDATE CASCADE;
ALTER TABLE ref_user_channel
ADD CONSTRAINT ref_user_channel_channel_id FOREIGN KEY (channel_id) REFERENCES channel (id) ON DELETE CASCADE ON UPDATE CASCADE;




CREATE TABLE message (
	id INT(11) NOT NULL AUTO_INCREMENT,
	user_id INT(11) NOT NULL,
	channel_id INT(11) NOT NULL,
	txt VARCHAR(1000) NOT NULL, 
	created_at INT(14) DEFAULT UNIX_TIMESTAMP(NOW()), 
	deleted TINYINT(1) NOT NULL DEFAULT FALSE,
	PRIMARY KEY (id)
) ENGINE=InnoDB AUTO_INCREMENT=1 CHARSET=utf8;

ALTER TABLE message
ADD CONSTRAINT message_user_id FOREIGN KEY (user_id) REFERENCES user (id) ON DELETE CASCADE ON UPDATE CASCADE;

ALTER TABLE message
ADD CONSTRAINT message_channel_id FOREIGN KEY (channel_id) REFERENCES channel (id) ON DELETE CASCADE ON UPDATE CASCADE;


-- combined key (user_id, message_id
CREATE TABLE message_flag (
	id INT(11) NOT NULL AUTO_INCREMENT,
	user_id INT(11) NOT NULL,
	message_id INT(11) NOT NULL,
	important TINYINT(1) NOT NULL,
	unread TINYINT(1) NOT NULL,
	created_at INT(14) DEFAULT UNIX_TIMESTAMP(NOW()), 
	PRIMARY KEY (id),
	UNIQUE KEY (user_id, message_id)
) ENGINE=InnoDB AUTO_INCREMENT=1 CHARSET=utf8;
-- TODO: contraint user_id -> user.id
-- TODO: contraint message_id -> message.id
ALTER TABLE message_flag
ADD CONSTRAINT message_flag_user_id FOREIGN KEY (user_id) REFERENCES user (id) ON DELETE CASCADE ON UPDATE CASCADE;
ALTER TABLE message_flag
ADD CONSTRAINT message_flag_message_id FOREIGN KEY (message_id) REFERENCES message (id) ON DELETE CASCADE ON UPDATE CASCADE;






-- combined key (user_id, message_id
CREATE TABLE route (
	id INT(11) NOT NULL AUTO_INCREMENT,
	route VARCHAR(500) NOT NULL, 
	type VARCHAR(20) NOT NULL, 
	headertemplate VARCHAR(300)  NULL, 
	contenttemplate VARCHAR(300) NULL, 
	footertemplate VARCHAR(300)  NULL, 
	routeparam VARCHAR(300)  NULL, 
	controller VARCHAR(300) NOT NULL, 
	controllername VARCHAR(300) NOT NULL, 
	verb VARCHAR(30) NOT NULL, 
	PRIMARY KEY (id),
	UNIQUE KEY (route, verb)
) ENGINE=InnoDB AUTO_INCREMENT=1 CHARSET=utf8;


INSERT INTO route (route, type, headertemplate, contenttemplate ,footertemplate, controller, controllername, verb)
VALUES ('/', 'PAGE', 'client/header.html', 'server/components/main/content.html', 'client/footer.html', 'server/components/main/', 'MainController', 'GET');

INSERT INTO route (route, type, headertemplate,contenttemplate ,	footertemplate, controller, controllername, verb)
VALUES ('/login' ,'PAGE', NULL, 'server/components/login/login.html', NULL, 'server/components/login/', 'LoginController', 'GET');


INSERT INTO route (route, type, headertemplate, contenttemplate,	footertemplate, controller, controllername, verb)
VALUES ('/register' ,'PAGE', NULL, 'server/components/register/register.html', NULL, 'server/components/register/', 'RegisterController', 'GET');

INSERT INTO route (route, type, headertemplate, contenttemplate,	footertemplate, controller, controllername, verb)
VALUES ('/registerstep2' ,'API', NULL, 'server/components/register/register.html', NULL, 'server/components/register/', 'RegisterStep2PostController', 'POST');

INSERT INTO route (route, type, headertemplate, contenttemplate,	footertemplate, controller, controllername, verb)
VALUES ('/registerstep2' ,'PAGE', NULL, 'server/components/register/register.html', NULL, 'server/components/register/', 'RegisterStep2GetController', 'GET');


INSERT INTO route (route, type, headertemplate, contenttemplate,	footertemplate, controller, controllername, verb)
VALUES ('/api/checkusername' ,'API', NULL, NULL, NULL, 'server/components/register/', 'CheckusernameController', 'POST');

INSERT INTO route (route, type, headertemplate, contenttemplate,	footertemplate, controller, controllername, verb)
VALUES ('/api/savechannels' ,'API', NULL, NULL, NULL, 'server/components/register/', 'SavechannelsController', 'POST');



INSERT INTO route (route, type, headertemplate,contenttemplate ,	footertemplate, routeparam, controller, controllername, verb)
VALUES ('/channel' ,'PAGE', 'client/header.html', 'server/components/channel/content.html', 'client/footer.html', 'channelname', 'server/components/channel/', 'ChannelController', 'GET');

INSERT INTO route (route, type, headertemplate,contenttemplate ,	footertemplate, routeparam, controller, controllername, verb)
VALUES ('/api/login' ,'API', NULL, NULL, NULL, NULL, 'server/components/login/', 'LoginApiController', 'POST');


INSERT INTO route (route, type, headertemplate,contenttemplate ,	footertemplate, routeparam, controller, controllername, verb)
VALUES ('/api/signout' ,'API', NULL, NULL, NULL, NULL, 'server/components/signout/',  'SignoutController', 'GET');


INSERT INTO route (route, type, headertemplate,contenttemplate ,	footertemplate, routeparam, controller, controllername, verb)
VALUES ('/newchannel' ,'PAGE', 'client/header.html', 'server/components/newchannel/newchannel.html', 'client/footer.html', NULL, 'server/components/newchannel/', 'NewChannelController', 'GET');


INSERT INTO route (route, type, headertemplate,contenttemplate ,	footertemplate, routeparam, controller, controllername, verb)
VALUES ('/api/newchannel' ,'API', NULL, NULL, NULL, NULL, 'server/components/newchannel/', 'NewChannelApiController', 'POST');


INSERT INTO route (route, type, headertemplate,contenttemplate ,	footertemplate, routeparam, controller, controllername, verb)
VALUES ('/api/newmessage' ,'API', NULL, NULL, NULL, NULL, 'server/components/newmessage/', 'NewMessageApiController', 'POST');


INSERT INTO route (route, type, headertemplate,contenttemplate ,	footertemplate, routeparam, controller, controllername, verb)
VALUES ('/api/markmessageimportant' ,'API', NULL, NULL, NULL, NULL, 'server/components/message/', 'MessageStatusImportantApiController', 'POST');



INSERT INTO route (route, type, headertemplate,contenttemplate ,	footertemplate, routeparam, controller, controllername, verb)
VALUES ('/api/markmessagenotimportant' ,'API', NULL, NULL, NULL, NULL, 'server/components/message/', 'MessageStatusNotImportantApiController', 'POST');


INSERT INTO route (route, type, headertemplate,contenttemplate ,	footertemplate, routeparam, controller, controllername, verb)
VALUES ('/api/deletemessage' ,'API', NULL, NULL, NULL, NULL, 'server/components/message/', 'MessageDeleteApiController', 'POST');

INSERT INTO route (route, type, headertemplate,contenttemplate ,	footertemplate, routeparam, controller, controllername, verb)
VALUES ('/api/editmessage' ,'API', NULL, NULL, NULL, NULL, 'server/components/message/', 'MessageEditApiController', 'POST');


INSERT INTO route (route, type, headertemplate,contenttemplate ,	footertemplate, routeparam, controller, controllername, verb)
VALUES ('/api/editmessageallowed' ,'API', NULL, NULL, NULL, NULL, 'server/components/message/', 'MessageEditAllowedApiController', 'POST');


INSERT INTO route (route, type, headertemplate,contenttemplate ,	footertemplate, routeparam, controller, controllername, verb)
VALUES ('/api/markmessagesasread' ,'API', NULL, NULL, NULL, NULL, 'server/components/message/', 'MessageMarkAsReadApiController', 'POST');






INSERT INTO user(username, firstname, lastname, password, isadmin) 
VALUES ('admin', 'admin', 'admin', '68be59da0cf353ae74ee8db8b005454b515e1a22', TRUE);

	
INSERT INTO user(username, firstname, lastname, password) 
VALUES ('goofy', 'goofy', 'goofy', '890e854c233a481206176f00f52c1b33b8fa0ff7');


-- nur damit ein channel beim registrieren angezeigt wird
INSERT INTO channel (name, created_by_user_id, created_at, description) 
VALUES ( 'money', 1, UNIX_TIMESTAMP(NOW())-500000, "All things money related");

INSERT INTO channel (name, created_by_user_id, created_at, description) 
VALUES ( 'SCR', 1, UNIX_TIMESTAMP(NOW())-600000, "All the things about SCR");


-- message from userid 2  - channel 1
INSERT INTO message (user_id, channel_id, txt, created_at) 
VALUES (2, 1, "first posting ch. 'money''", UNIX_TIMESTAMP(NOW())-700000);

-- message from userid 1 - channel  1
INSERT INTO message (user_id, channel_id, txt, created_at) 
VALUES (1, 1, "second  posting ch 'money'", UNIX_TIMESTAMP(NOW())-60000);



-- message flags
INSERT INTO message_flag (user_id, message_id, important, unread) 
VALUES (2, 1, TRUE, FALSE);

INSERT INTO message_flag (user_id, message_id, important, unread) 
VALUES (1, 1, FALSE, TRUE);

INSERT INTO message_flag (user_id, message_id, important, unread) 
VALUES (1, 2, TRUE, FALSE);

INSERT INTO message_flag (user_id, message_id, important, unread) 
VALUES (2, 2, FALSE, TRUE);





INSERT INTO ref_user_channel (user_id, channel_id) 
VALUES (1, 1);

INSERT INTO ref_user_channel (user_id, channel_id) 
VALUES (1, 2);

INSERT INTO ref_user_channel (user_id, channel_id) 
VALUES (2, 1);
