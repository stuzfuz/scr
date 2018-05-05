<?php

class DatabaseManager {

    private static $__connection;   // $connection   das __ heißt private

    public static function getConnection() {
        
        if (!isset(self::$__connection)) {
            // echo "connecting to database";
            try {
                $s = "mysql:host=localhost;dbname=" . ApplicationConfig::$databaseName . ";charset=utf8";
                self::$__connection = new \PDO($s, ApplicationConfig::$databaseUsername, ApplicationConfig::$databasePassword);
                // echo 'Connected to database';
            }
            catch(PDOException $e)
            {
                \Logger::logError("Fatal Error - could not connect to Database" , $e->getMessage());
                readfile('static/500.html');
                exit();
            }
        }
        // var_dump(self::$__connection);
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

        // \Util::my_var_dump($statement->debugDumpParams(), "statement - debugDUmpParams");
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
        $sql = "SELECT channel.id, name    ";
        $sql .= "FROM channel ";
        $sql .= "LEFT JOIN ref_user_channel ON (ref_user_channel.channel_id = channel.id)   "; 
        $sql .= "WHERE ref_user_channel.user_id = ? AND channel.deleted = FALSE   ";

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
            $password = hash('sha1', $password);

            $sql = "INSERT INTO user (username, password, firstname, lastname)";
            $sql .= " VALUES (?, ?, ?, ?)";
            
            self::query($con, $sql, array($username, $password, $firstname, $lastname));

            $userid = $con->lastInsertid();

            $con->commit();
        } catch (Exception $e) {
            $con->rollBack();
            $userid = null;
        }
        self::closeConnection();
        return $userid;
    }

    public static function assignUserChannelsTopicsMessages(int $userid, array $channels) {
        \Logger::logDebugPrintR("'assignUserChannelsTopicsMessages()'  [" . __LINE__ . "   userid = $userid,  channels", $channels);
        
        // if there are no channels -> do nothing 
        if (!is_array($channels)) { return true; }
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
        \Logger::logDebugPrintR("'assignUserChannelsTopicsMessages()'  [" . __LINE__ . "] assign channels to userid sql = ", $sqlAssignChannels);
        \Logger::logDebugPrintR("'assignUserChannelsTopicsMessages()'  [" . __LINE__ . "] paramsAssignChannels ", $paramsAssignChannels);

        $con = self::getConnection();
        $con->beginTransaction();
        try {
            // assign userid to all the channel ids, so the user has access to the channel
            self::query($con, $sqlAssignChannels, $paramsAssignChannels);
            $lastId = $con->lastInsertid();

            // TODO: Insert user_id, topic_id for all channels and topics to the "topic_flags" table: unread, not important 

            // TODO: Insert user_id, message_id for all channels and messages to the  "message_flags" table: unread, not important 
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
        \Logger::logDebugPrintR("'getTopicsAndMessagesForUser()'  [" . __LINE__ . "   userid = $userid,  channelname: $channelname", "");

        // $sql = "SELECT channel.id AS channelid, channel.name AS channelname, ";
        // $sql .= "topic.id AS topicid, topic.title AS topictitle, topic.description AS topicdescription, topic.created_at AS topiccreatedat, ";
        // $sql .= "message.id AS messageid, message.txt AS messagetxt, message.created_at AS messagecreatedat,  ";
        // $sql .= "topic_flag.unread AS topicunread, topic_flag.important AS topicimportant,    ";
        // $sql .= "message_flag.unread AS messageunread, message_flag.important AS messageimportant   "; 
        // $sql .= "FROM channel ";
        // $sql .= "LEFT JOIN topic ON (topic.channel_id = channel.id)  ";
        // $sql .= "LEFT JOIN message ON (message.topic_id = topic.id)  ";
        // $sql .= "LEFT JOIN message_flag ON (message_flag.message_id = message.id AND message_flag.user_id = ?)  ";
        // $sql .= "LEFT JOIN topic_flag ON (topic_flag.topic_id = topic.id AND topic_flag.user_id = ?)    " ;
        // $sql .= "WHERE channel.name = ? AND channel.deleted = 0    ";
        // $sql .= "ORDER BY topic.created_at,   message.created_at   ";

        $sqlTopics = "SELECT channel.id AS channelid, channel.name AS channelname, ";
        $sqlTopics .= "topic.id AS topicid, topic.title AS topictitle, topic.description AS topicdescription, topic.created_at AS topiccreatedat, ";
        $sqlTopics .= "topic_flag.unread AS topicunread, topic_flag.important AS topicimportant,   ";
        $sqlTopics .= "user.username     ";
        $sqlTopics .= "FROM channel ";
        $sqlTopics .= "LEFT JOIN topic ON (topic.channel_id = channel.id)  ";
        $sqlTopics .= "LEFT JOIN topic_flag ON (topic_flag.topic_id = topic.id AND topic_flag.user_id = ?)    " ;
        $sqlTopics .= "LEFT JOIN user ON (user.id = topic.user_id)    " ;
        $sqlTopics .= "WHERE channel.name = ? AND channel.deleted = 0    ";
        $sqlTopics .= "ORDER BY topic.created_at  ";

        $sqlParams[] = $userid;
        $sqlParams[] = $channelname;

        \Logger::logDebugPrintR("'getTopicsAndMessagesForUser()'  [" . __LINE__ . "] get topics and message = ", $sqlTopics);
        \Logger::logDebugPrintR("'getTopicsAndMessagesForUser()'  [" . __LINE__ . "] sqlParams ", $sqlParams);

        $con = self::getConnection();
        $res = self::query($con, $sqlTopics, $sqlParams);

        \Logger::logDebugPrintR("'getTopicsAndMessagesForUser()'  [" . __LINE__ . "] res = ", $res);
        \Logger::logDebugPrintR("'getTopicsAndMessagesForUser()'  [" . __LINE__ . "]  rowCount  = ", $res->rowCount() );

        $topics = [];
        if ($res->rowCount() == 0) {
            $topics["hastopics"] = 0; 
            $topics["hasimportanttopics"] = 0; 
        } else {
            while ($tmp = \DatabaseManager::fetchAssoz($res)) {
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
        \Logger::logDebugPrintR("'getTopicsAndMessagesForUser()'  [" . __LINE__ . "]  topics  = ", $topics);    

        $sqlMessages = "SELECT topic.id AS topicid, ";
        $sqlMessages .= "message.id AS messageid, message.txt AS messagetxt, message.created_at AS messagecreatedat,  ";
        $sqlMessages .= "message_flag.unread AS messageunread, message_flag.important AS messageimportant,   "; 
        $sqlMessages .= "user.username     ";
        $sqlMessages .= "FROM channel ";
        $sqlMessages .= "LEFT JOIN topic ON (topic.channel_id = channel.id)  ";
        $sqlMessages .= "LEFT JOIN message ON (message.topic_id = topic.id)  ";
        $sqlMessages .= "LEFT JOIN message_flag ON (message_flag.message_id = message.id AND message_flag.user_id = ?)  ";
        $sqlMessages .= "LEFT JOIN user ON (user.id = message.user_id)    " ;
        $sqlMessages .= "WHERE channel.name = ? AND channel.deleted = 0  ";
        $sqlMessages .= "ORDER BY message.created_at   ";

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
        \Logger::logDebugPrintR("'getTopicsAndMessagesForUser()'  [" . __LINE__ . "]  messages   = ", $messages );

        foreach($messages as $msg) {
            $topicid = $msg["topicid"];

            if ($msg["messageid"] == null) { continue; }

            \Logger::logDebugPrintR("'getTopicsAndMessagesForUser()'  [" . __LINE__ . "]  after if ($msg[messageid] == null     ", "" );

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
            if (isset($topics["topics"])) {
                foreach($topics["topics"] as $key => $t) {
                    // $topics["topics"][$key]["hasimportantmessages"] = 0;
                    // $topics["topics"][$key]["hasmessages"] = 0;

                    if ($t["topicid"] == $topicid) {
                        if ($msg["messageimportant"]) {
                            $topics["topics"][$key]["importantmessages"][] = $msg;
                            $topics["topics"][$key]["hasimportantmessages"] = true;
                        } else {
                            $topics["topics"][$key]["messages"][] = $msg;
                            $topics["topics"][$key]["hasmessages"] = true;
                        }
                    }
                }
            }
        }

        \Logger::logDebugPrintR("'getTopicsAndMessagesForUser()'  [" . __LINE__ . "]  topics with messages   = ", $topics );

        self::closeConnection();
        return $topics;
    }
}
