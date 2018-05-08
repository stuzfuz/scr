<?php

class DatabaseManager {

    private static $__connection;   // $connection   das __ heißt private

    public static function getConnection() {
        
        if (!isset(self::$__connection)) {
            try {
                $s = "mysql:host=localhost;dbname=" . ApplicationConfig::$databaseName . ";charset=utf8";
                self::$__connection = new \PDO($s, ApplicationConfig::$databaseUsername, ApplicationConfig::$databasePassword);
            }
            catch(PDOException $e)
            {
                \Logger::logError("Fatal Error - could not connect to Database" , $e->getMessage());
                readfile('static/500.html');
                exit();
            }
        }
        if (self::$__connection  == null) {
            \Logger::logError("Fatal Error - could not connect to Database" , $e->getMessage());
        }
        return self::$__connection;
    }

    public static function query($connection, $query, $parameters = array()) {
        // echo "query   querystring = " .$query . "<br>";
        $statement = $connection->prepare($query);
        $i = 1; 

        $connection->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

        foreach($parameters as $param) {
            if (is_int($param)) {
                $statement->bindValue($i, $param, \PDO::PARAM_INT);

            }
            if (is_string($param)) {
                $statement->bindValue($i, $param, \PDO::PARAM_STR);
            }
            $i++;
        }

        \Logger::logQuery($query, $parameters);

        try {
            $statement->execute();
        }
        catch(PDOException $e)
        {
            \Logger::logDebug("Fatal Error in 'query' - could not exeute query: $query", $e->getMessage());
            \Logger::logError("Fatal Error in 'query' - could not exeute query" , $e->getMessage());
            readfile('static/500.html');
            exit();
        }

        // \Logger::logDebugPrintR($statement->debugDumpParams(), "statement - debugDUmpParams");
        return $statement; 
    }

    public static function fetchObject($cursor) {
        return $cursor->fetchObject();
    }

    public static function fetchAssoz($cursor) {
        return $cursor->fetch();
    }

    public static function closeConnection() {
        self::$__connection == null; 
    }

    public static function getUserByUserName(string $username) {
        $user = null; 
        $con = self::getConnection();
        
        $sql = "SELECT id, username, password FROM user WHERE username = ?";

        $res = self::query($con, $sql, array($username));
        if ($u = self::fetchObject($res)) {
            $user = new User($u->id, $u->username, $u->password);
        }
        self::closeConnection();
        return $user;
    }

    public static function getUserById(string $id) {
        $user = null; 
        $con = self::getConnection();
        
        $sql = "SELECT id, username, password FROM user WHERE id = ?";

        $res = self::query($con, $sql, array($id));
        if ($u = self::fetchObject($res)) {
            $user = new User($u->id, $u->username, $u->password);
        }
        self::closeConnection();
        return $user;
    }    

    public static function getChannelsForUser($userid) {
        $sql ="\n";
        $sql .= "SELECT channel.id, name                                 \n ";
        $sql .= "FROM channel                                 \n ";
        $sql .= "LEFT JOIN ref_user_channel ON (ref_user_channel.channel_id = channel.id)                                 \n   "; 
        $sql .= "WHERE ref_user_channel.user_id = ? AND channel.deleted = FALSE                                 \n   ";
        $sql .= "ORDER BY name                                \n   ";

        $res = \DatabaseManager::query(self::getConnection(), $sql, array($userid));

        $channels = array();
        $data = array(); 
        if ($res->rowCount() == 0) {
            $data["channelsfound"] = 0; 
        } else {
            while ($channel = \DatabaseManager::fetchAssoz($res)) {
                $channel["nameasurl"] = urlencode($channel["name"]);
                $channels[] = $channel; 
            }
            
            $data["channelsfound"] = true; 
            $data["channels"] = $channels; 
        }
        return $data; 
    }

    public static function getAllChannels() {
        // TODO: only those for the user who is logged in
        $sql = "SELECT id as channelid, name FROM channel WHERE deleted = 0 ORDER BY name";
        $res = \DatabaseManager::query(self::getConnection(), $sql, array());

        $channels = array();
        $data = array(); 
        if ($res->rowCount() == 0) {
            $data["channelsfound"] = 0; 
        } else {
            // echo "<br><br> adding channels to array ... <br>";
            while ($channel = \DatabaseManager::fetchAssoz($res)) {
                // \Util::my_var_dump($channel, "MessagesController channel  = ");
                $channels[] = $channel; 
            }
            
            $data["channelsfound"] = true; 
            $data["channels"] = $channels; 
        }
        return $data; 
    }

    public static function insertUser(string $username, string $password, string $firstname, string $lastname) {
        $con = self::getConnection();
        $con->beginTransaction();
        try {
            // TODO: change back to hash('sha1', "$username|$password")
            $password = hash('sha1', "$username|$password");

            $sql = "INSERT INTO user (username, password, firstname, lastname)";
            $sql .= " VALUES (?, ?, ?, ?)";
            
            self::query($con, $sql, array($username, $password, $firstname, $lastname));

            $userid = $con->lastInsertid();

            $con->commit();
        } catch (Exception $e) {
            $con->rollBack();
            $userid = false;
        }
        self::closeConnection();
        return $userid;
    }

    public static function assignUserChannelsTopicsMessages(int $userid, array $channels) {
        // \Logger::logDebugPrintR("'assignUserChannelsTopicsMessages()'  [" . __LINE__ . "   userid = $userid,  channels", $channels);

        // if there are no channels -> do nothing 
        if (!is_array($channels)) { return true; }
        
        // get all topic ids for all the  channels  
        $sql = "\n";
        $sql .= "SELECT topic.id AS topicid                      \n";
        $sql .= "FROM topic                                        \n  ";
        $sql .= "WHERE channel_id IN (";
        
        $sql2 = "\n";
        $sql2 .= ") AND topic.deleted = FALSE;                                    \n      ";
        
        $params = [];
        $s = "?";
        foreach($channels as $c) {
            $sqlArr[] = $s;
            $params[] = $c;
        } 
        $sqlTopics = $sql . " " . implode(",", $sqlArr) . $sql2;

        \Logger::logDebugPrintR("'assignUserChannelsTopicsMessages()'  [" . __LINE__ . "] sqlTopics = ", $sqlTopics);
        \Logger::logDebugPrintR("'assignUserChannelsTopicsMessages()'  [" . __LINE__ . "] params = ", $params);
        \Logger::logDebugPrintR("'assignUserChannelsTopicsMessages()'  [" . __LINE__ . "] sqlArr = ", $sqlArr);

        $con = self::getConnection();

        $topics = self::query($con, $sqlTopics, $params);


        // get all message ids for all the  topics  
        $sql = "\n";
        $sql .= "SELECT topic.id AS topicid, channel_id,                \n";
        $sql .= "message.id AS messageid                \n";
        $sql .= "FROM topic                                        \n  ";
        $sql .= "LEFT JOIN message ON (message.topic_id = topic.id)                                        \n  ";
        $sql .= "WHERE channel_id IN (";
        
        $sql2 = "\n";
        $sql2 .= ") AND  topic.deleted = FALSE AND message.deleted = FALSE;                                    \n      ";
        $sqlMessages = $sql . " " . implode(",", $sqlArr) . $sql2;

        // \Logger::logDebugPrintR("'assignUserChannelsTopicsMessages()'  [" . __LINE__ . "] sqlMessages = ", $sqlMessages);
        // \Logger::logDebugPrintR("'assignUserChannelsTopicsMessages()'  [" . __LINE__ . "] params = ", $params);
        // \Logger::logDebugPrintR("'assignUserChannelsTopicsMessages()'  [" . __LINE__ . "] sqlArr = ", $sqlArr);

        $messages = self::query($con, $sqlMessages, $params);

        // \Logger::logDebugPrintR("'assignUserChannelsTopicsMessages()'  [" . __LINE__ . "] topics = ", $topics);
        // \Logger::logDebugPrintR("'assignUserChannelsTopicsMessages()'  [" . __LINE__ . "] messages = ", $messages);
        
        // prepare sql Queries - assign channels to user (or is it vice versa?)
        $sqlAssignChannels = "INSERT INTO ref_user_channel (user_id, channel_id) VALUES";
        $paramsAssignChannels = [];
        $sqlArr = [];
        $s = "(?, ?)";
        foreach($channels as $c) {
            $sqlArr[] = $s;
            $paramsAssignChannels[] = $userid;
            $paramsAssignChannels[] = $c;
        } 
        $sqlAssignChannels .= " " . implode(",", $sqlArr);

        // \Logger::logDebugPrintR("'assignUserChannelsTopicsMessages()'  [" . __LINE__ . "] assign channels to userid sql = ", $sqlAssignChannels);
        // \Logger::logDebugPrintR("'assignUserChannelsTopicsMessages()'  [" . __LINE__ . "] paramsAssignChannels ", $paramsAssignChannels);


        // mark all topics/messages as unread and not important
        $sqlMarkTopics = "\n";
        $sqlMarkTopics = "INSERT INTO topic_flag (topic_id, user_id, important, unread) VALUES                   ";
        $s = "(?, ?, FALSE, TRUE)";
        $sqlArr = [];
        $paramsMarkTopics = [];
        foreach($topics as $t) {
            $sqlArr[] = $s;
            $paramsMarkTopics[] = $t["topicid"];
            $paramsMarkTopics[] = $userid;
        } 
        $sqlMarkTopics = $sqlMarkTopics . " " . implode(",", $sqlArr) ."\n";

        $sqlMarkMessages = "\n";
        $sqlMarkMessages = "INSERT INTO message_flag (message_id, user_id, important, unread) VALUES                   ";
        $s = "(?, ?, FALSE, TRUE)";
        $sqlArr = [];
        $paramsMarkMessages= [];
        foreach($messages as $m) {
            $sqlArr[] = $s;
            $paramsMarkMessages[] = $m["messageid"];
            $paramsMarkMessages[] = $userid;
        } 
        $sqlMarkMessages = $sqlMarkMessages . " " . implode(",", $sqlArr) ."\n";


        // \Logger::logDebug("'assignUserChannelsTopicsMessages()'  [" . __LINE__ . "] sqlMarkTopics    ", $sqlMarkTopics);
        // \Logger::logDebug("'assignUserChannelsTopicsMessages()'  [" . __LINE__ . "] sqlMarkMessages     ", $sqlMarkMessages);

        
        $con->beginTransaction();
        try {
            // assign userid to all the channel ids, so the user has access to the channel
            self::query($con, $sqlAssignChannels, $paramsAssignChannels);
            $lastId = $con->lastInsertid();

            // add user <-> topic.id to table "topic_flag"
            self::query($con, $sqlMarkTopics, $paramsMarkTopics);
            $lastId = $con->lastInsertid();

            // add user <-> message.id to table "message_flag"
            self::query($con, $sqlMarkMessages, $paramsMarkMessages);
            $lastId = $con->lastInsertid();

            $con->commit();
        } catch (Exception $e) {
            \Logger::logError("'assignUserChannelsTopicsMessages()'  [" . __LINE__ . "] could not insert into table 'ref_user_channel' ", $e->getMessage());
            \Logger::logDebug("'assignUserChannelsTopicsMessages()'  [" . __LINE__ . "] could not insert into table 'ref_user_channel' ", $e->getMessage());

            $con->rollBack();
            return false; 
        }
        self::closeConnection();
        return true;
    }

    public static function getTopicsAndMessagesForUser(int $userid, string $channelname) {
        // \Logger::logDebugPrintR("'getTopicsAndMessagesForUser()'  [" . __LINE__ . "   userid = $userid,  channelname: $channelname", "");

        $sqlTopics ="\n";
        $sqlTopics .= "SELECT channel.id AS channelid, channel.name AS channelname,                               \n";
        $sqlTopics .= "topic.id AS topicid, topic.title AS topictitle, topic.description AS topicdescription, topic.created_at AS topiccreatedat,                              \n ";
        $sqlTopics .= "topic_flag.unread AS unread, topic_flag.important AS topicimportant,                                \n ";
        $sqlTopics .= "user.username                                  \n ";
        $sqlTopics .= "FROM channel                               \n";
        $sqlTopics .= "LEFT JOIN topic ON (topic.channel_id = channel.id)                               \n ";
        $sqlTopics .= "LEFT JOIN topic_flag ON (topic_flag.topic_id = topic.id AND topic_flag.user_id = ?)                                  \n" ;
        $sqlTopics .= "LEFT JOIN user ON (user.id = topic.user_id)                                 \n " ;
        $sqlTopics .= "WHERE channel.name = ? AND channel.deleted = 0                                  \n";
        $sqlTopics .= "ORDER BY topic.created_at DESC                              \n ";

        $sqlParams[] = $userid;
        $sqlParams[] = $channelname;

        // \Logger::logDebugPrintR("'getTopicsAndMessagesForUser()'  [" . __LINE__ . "] get topics and message = ", $sqlTopics);
        // \Logger::logDebugPrintR("'getTopicsAndMessagesForUser()'  [" . __LINE__ . "] sqlParams ", $sqlParams);

        $con = self::getConnection();
        $res = self::query($con, $sqlTopics, $sqlParams);

        // \Logger::logDebugPrintR("'getTopicsAndMessagesForUser()'  [" . __LINE__ . "] res = ", $res);
        // \Logger::logDebugPrintR("'getTopicsAndMessagesForUser()'  [" . __LINE__ . "]  rowCount  = ", $res->rowCount() );

        $topics = [];
        if ($res->rowCount() == 0) {
            $topics["hastopics"] = 0; 
            $topics["hasimportanttopics"] = 0; 
        } else {
            while ($tmp = \DatabaseManager::fetchAssoz($res)) {
                if ($tmp["topicid"] == null) { continue; }
                $tmp["hasmessages"] = 0;
                $tmp["hasimportantmessages"] = 0;

                $date = new DateTime();
                $date->setTimestamp($tmp["topiccreatedat"]);
                $tmp["date"] = $date->format('Y-m-d');
                $tmp["time"] = $date->format('H:i');

                $topicid = $tmp["topicid"];
                if ($tmp["topicimportant"]) {
                    $topics["importanttopic"][] = $tmp;
                } else {
                    $topics["topic"][] = $tmp;
                }
            }
            $topics["hastopics"] = isset($topics["topic"]) ? 1 : 0; 
            $topics["hasimportanttopics"] = isset($topics["importanttopic"]) ? 1 : 0; 
        }
        // \Logger::logDebugPrintR("'getTopicsAndMessagesForUser()'  [" . __LINE__ . "]  topics  = ", $topics);    

        $sqlMessages = "\n";
        $sqlMessages .= "SELECT topic.id AS topicid,                    \n";
        $sqlMessages .= "message.id AS messageid, message.txt AS messagetxt, message.created_at AS messagecreatedat,                    \n ";
        $sqlMessages .= "message_flag.unread AS unread, message_flag.important AS messageimportant,                     \n "; 
        $sqlMessages .= "user.username                       \n ";
        $sqlMessages .= "FROM channel                    \n";
        $sqlMessages .= "LEFT JOIN topic ON (topic.channel_id = channel.id)                    \n ";
        $sqlMessages .= "LEFT JOIN message ON (message.topic_id = topic.id)                    \n ";
        $sqlMessages .= "LEFT JOIN message_flag ON (message_flag.message_id = message.id AND message_flag.user_id = ?)                     \n";
        $sqlMessages .= "LEFT JOIN user ON (user.id = message.user_id)                      \n " ;
        $sqlMessages .= "WHERE channel.name = ? AND channel.deleted = 0                    \n ";
        $sqlMessages .= "ORDER BY message.created_at DESC                     \n";

        $res = self::query($con, $sqlMessages, $sqlParams);

        // \Logger::logDebugPrintR("'getTopicsAndMessagesForUser()'  [" . __LINE__ . "] res = ", $res);
        // \Logger::logDebugPrintR("'getTopicsAndMessagesForUser()'  [" . __LINE__ . "]  rowCount  = ", $res->rowCount() );

        $messages = [];
        // convert to messages array
        if ($res->rowCount() == 0) {
            \Logger::logDebugPrintR("'getTopicsAndMessagesForUser()'  [" . __LINE__ . "]  well no message - thats ok   ", "");
        } else {
            while ($msg = \DatabaseManager::fetchAssoz($res)) {
                $date = new DateTime();
                $date->setTimestamp($msg["messagecreatedat"]);
                $msg["date"] = $date->format('Y-m-d');
                $msg["time"] = $date->format('H:i');
                $messages[] = $msg; 
            }
        }
        // \Logger::logDebugPrintR("'getTopicsAndMessagesForUser()'  [" . __LINE__ . "]  messages   = ", $messages );

        foreach($messages as $msg) {
            $topicid = $msg["topicid"];

            if ($msg["messageid"] == null) { continue; }

            // \Logger::logDebugPrintR("'getTopicsAndMessagesForUser()'  [" . __LINE__ . "]  after if ($msg[messageid] == null)     ", "" );

            $found = false; 
            if (isset($topics["importanttopic"])) {
                foreach($topics["importanttopic"] as $key => $t) {
                    // $topics["importanttopic"][$key]["hasimportantmessages"] = 0;
                    // $topics["importanttopic"][$key]["hasmessages"] = 0;

                    if ($t["topicid"] == $topicid) {
                        if ($msg["messageimportant"]) {
                            $topics["importanttopic"][$key]["importantmessages"][] = $msg;
                            $topics["importanttopic"][$key]["hasimportantmessages"] = true;
                        } else {
                            $topics["importanttopic"][$key]["messages"][] = $msg;
                            $topics["importanttopic"][$key]["hasmessages"] = true;
                        }
                        $found = true;
                        break;
                    }
                }
            }
            if (isset($topics["topic"])) {
                foreach($topics["topic"] as $key => $t) {
                    // $topics["topics"][$key]["hasimportantmessages"] = 0;
                    // $topics["topics"][$key]["hasmessages"] = 0;

                    if ($t["topicid"] == $topicid) {
                        if ($msg["messageimportant"]) {
                            $topics["topic"][$key]["importantmessages"][] = $msg;
                            $topics["topic"][$key]["hasimportantmessages"] = true;
                        } else {
                            $topics["topic"][$key]["messages"][] = $msg;
                            $topics["topic"][$key]["hasmessages"] = true;
                        }
                    }
                }
            }
        }

        // \Logger::logDebugPrintR("'getTopicsAndMessagesForUser()'  [" . __LINE__ . "]  topics with messages   = ", $topics );

        self::closeConnection();
        return $topics;
    }

    public static function existsChannel(string $channelname): bool {
        $con = self::getConnection();
        $channelId = false;            
        $sql = "SELECT id FROM  channel WHERE  name = ?";
        $res = self::query($con, $sql, array($channelname));
        
        $found = true; 
        if ($res->rowCount() == 0) {
            $found = false; 
             
        }
        self::closeConnection();
        return $found;
    }

    public static function insertChannel(int $userid, string $channelname) {
        $con = self::getConnection();
        $con->beginTransaction();
        $channelId = false;
        try {
            // insert new channel name
            $sql = "INSERT INTO channel (name, created_by_user_id)";
            $sql .= " VALUES (?, ?)";
            
            self::query($con, $sql, array($channelname, $userid));

            $channelId = $con->lastInsertid();

            // assign channel to user_id
            $sql = "INSERT INTO ref_user_channel (user_id, channel_id)";
            $sql .= " VALUES (?, ?)";
            
            self::query($con, $sql, array($userid, $channelId));
            $con->lastInsertid();

            $con->commit();
        } catch (Exception $e) {
            $con->rollBack();
            $userid = false;
        }
        self::closeConnection();
        return $channelId;
    }

    public static function insertMessage(int $userid, int $topicid, string $txt) {
        // $con = self::getConnection();
        // $con->beginTransaction();
        // $msgId = false;
        // try {
        //     // insert new channel name
        //     $sql = "INSERT INTO message (user_id, topic_id, txt)";
        //     $sql .= " VALUES (?, ?, ?)";
            
        //     self::query($con, $sql, array($userid, $topicid, $txt));

        //     $msgId = $con->lastInsertid();

        //     $con->commit();
        // } catch (Exception $e) {
        //     $con->rollBack();
        //     $msgId = false;
        // }
        // self::closeConnection();
        return $msgId;
    }

    public static function insertTopic(int $userid, int $channelid, string $title, string $description) {
        $con = self::getConnection();

        // get all user ids for the channel  
        $sql = "\n";
        $sql .= "SELECT user_id AS userid                      \n";
        $sql .= "FROM topic                                        \n  ";
        $sql .= "WHERE channel_id = ?   ";          // AND (user_id <>  ?)
         
        $con = self::getConnection();
        $tmp = self::query($con, $sql, array($channelid));    // ,  $userid

        $users = array();
        while ($u = \DatabaseManager::fetchAssoz($tmp)) {
            $users[]= $u; 
        }

        // begin transaction and insert values 
        $con->beginTransaction();
        try {
            // insert new topic
            $topicId = -1;
            $sql = "INSERT INTO topic (user_id, channel_id, title, description)";
            $sql .= " VALUES (?, ?, ?, ?)";
            self::query($con, $sql, array($userid, $channelid, $title, $description));
            $topicId = $con->lastInsertid();

            \Logger::logDebug("DatabaseManager::insertTopic() new topicId = $topicId ", "");

             // assign the new message to all members of the channel as unread and not important
            $sqlAssignTopic = "INSERT INTO topic_flag (topic_id, user_id, important, unread) VALUES";
            $paramsAssignTopic = [];
            $sqlArr = [];
            $s = "(?, ?, FALSE, TRUE)";

            \Logger::logDebugPrintR("DatabaseManager::insertTopic() insert User <-> topic relation  paramsAssignTopic = ", $paramsAssignTopic);


            foreach($users as $u) {
                $sqlArr[] = $s;
                $paramsAssignTopic[] = $topicId;
                $paramsAssignTopic[] = $u["userid"];
            } 
            $sqlAssignTopic .= " " . implode(",", $sqlArr);

            \Logger::logDebug("DatabaseManager::insertTopic() insert User <-> topic relation  sqlAssignTopic = $sqlAssignTopic ", "");
            \Logger::logDebugPrintR("DatabaseManager::insertTopic() insert User <-> topic relation  paramsAssignTopic = ", $paramsAssignTopic);
            
            // insert topic <-> users relation
            self::query($con, $sqlAssignTopic, $paramsAssignTopic);
            $res = $con->lastInsertid();

            $con->commit();

            $res = true;
        } catch (Exception $e) {
            $con->rollBack();
            $res = false;
        }

        // die("insert messages");
        self::closeConnection();

        return $res;
    }

    public static function getChannelIdForName(string $channelname) {
        $con = self::getConnection();
        $sql = "SELECT id FROM channel WHERE name = ?";
            
        $res = self::query($con, $sql, array($channelname));
        $res = \DatabaseManager::fetchAssoz($res);
        $id = $res["id"];

        self::closeConnection();
        return $id;
    }
}
