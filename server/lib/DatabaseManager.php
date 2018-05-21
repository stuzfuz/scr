<?php

class DatabaseManager
{
    private static $__connection;   // $connection   das __ heißt private

    public static function getConnection()
    {
        if (!isset(self::$__connection)) {
            try {
                $s = "mysql:host=localhost;dbname=" . ApplicationConfig::$databaseName . ";charset=utf8";
                self::$__connection = new \PDO($s, ApplicationConfig::$databaseUsername, ApplicationConfig::$databasePassword);
            } catch (PDOException $e) {
                \Logger::logError("Fatal Error - could not connect to Database", $e->getMessage());
                readfile('client/static/500.html');
                exit();
            }
        }
        if (self::$__connection  == null) {
            \Logger::logError("Fatal Error - could not connect to Database", $e->getMessage());
        }
        return self::$__connection;
    }

    public static function query($connection, $query, $parameters = array())
    {
        // echo "query   querystring = " .$query . "<br>";
        $statement = $connection->prepare($query);
        $i = 1;

        $connection->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

        foreach ($parameters as $param) {
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
        } catch (PDOException $e) {
            \Logger::logDebug("Fatal Error in 'query' - could not exeute query: $query", $e->getMessage());
            \Logger::logError("Fatal Error in 'query' - could not exeute query", $e->getMessage());
            readfile('client/static/500.html');
            exit();
        }

        // \Logger::logDebugPrintR($statement->debugDumpParams(), "statement - debugDUmpParams");
        return $statement;
    }

    public static function fetchObject($cursor)
    {
        return $cursor->fetchObject();
    }

    public static function fetchAssoz($cursor)
    {
        return $cursor->fetch();
    }

    public static function closeConnection()
    {
        self::$__connection == null;
    }

    public static function getUserByUserName(string $username)
    {
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

    public static function getUserById(string $id)
    {
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

    public static function getChannelsForUser($userid)
    {
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

    public static function getAllChannels()
    {
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

    public static function insertUser(string $username, string $password, string $firstname, string $lastname)
    {
        $con = self::getConnection();
        $con->beginTransaction();
        try {
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

    public static function getMessagesForUser(int $userid, string $channelname)
    {
        $sqlMessages = "\n";
        $sqlMessages .= "SELECT channel.id AS channel_id,  channel.name AS channelname, channel.description AS channeldescription,                  \n";
        $sqlMessages .= "message.id AS messageid, message.txt AS messagetxt, message.created_at AS messagecreatedat,                    \n ";
        $sqlMessages .= "message_flag.unread AS unread, message_flag.important AS messageimportant,                     \n ";
        $sqlMessages .= "user.username                       \n ";
        $sqlMessages .= "FROM channel                    \n";
        $sqlMessages .= "LEFT JOIN message ON (message.channel_id = channel.id)                    \n ";
        $sqlMessages .= "LEFT JOIN message_flag ON (message_flag.message_id = message.id AND message_flag.user_id = ?)                     \n";
        $sqlMessages .= "LEFT JOIN user ON (user.id = message.user_id)                      \n " ;
        $sqlMessages .= "WHERE channel.name = ? AND channel.deleted = 0   AND message.deleted = 0                 \n ";
        $sqlMessages .= "ORDER BY message.created_at ASC                     \n";

        $sqlParams[] = $userid;
        $sqlParams[] = $channelname;
        $con = self::getConnection();
        $res = self::query($con, $sqlMessages, $sqlParams);

        // \Logger::logDebugPrintR("'getMessagesForUser()'  [" . __LINE__ . "] res = ", $res);
        // \Logger::logDebugPrintR("'getMessagesForUser()'  [" . __LINE__ . "]  rowCount  = ", $res->rowCount() );

        $messages = [];
        $channelId = "";
        $channelName = "";
        $channelDescription = "";
        
        // convert to messages array
        if ($res->rowCount() == 0) {
            \Logger::logDebugPrintR("'getMessagesForUser()'  [" . __LINE__ . "]  well no message - thats ok   ", "");
        } else {
            while ($msg = \DatabaseManager::fetchAssoz($res)) {
                $date = new DateTime();
                $date->setTimestamp($msg["messagecreatedat"]);
                $msg["date"] = $date->format('Y-m-d');
                $msg["time"] = $date->format('H:i');
                $messages[] = $msg;
                $channelId = $msg["channel_id"];
                $channelName = $msg["channelname"];
                $channelDescription = $msg["channeldescription"];
            }
        }
        // \Logger::logDebugPrintR("'getMessagesForUser()'  [" . __LINE__ . "]  messages   = ", $messages );

        $orderedMessages = [];
        $orderedMessages["hasimportantmessages"] = false;
        $orderedMessages["hasmessages"] = false;
        $orderedMessages["channelid"] = $channelId;
        $orderedMessages["channelname"] = $channelName;
        $orderedMessages["channeldescription"] = $channelDescription;

        foreach ($messages as $msg) {
            // ????
            if ($msg["messageid"] == null) {
                continue;
            }

            // \Logger::logDebugPrintR("'getMessagesForUser()'  [" . __LINE__ . "]  after if ($msg[messageid] == null)     ", "" );

            $found = false;
            if ($msg["messageimportant"]) {
                $orderedMessages["importantmessages"][] = $msg;
                $orderedMessages["hasimportantmessages"] = true;
            } else {
                $orderedMessages["messages"][] = $msg;
                $orderedMessages["hasmessages"] = true;
            }
        }

        \Logger::logDebugPrintR("'getMessagesForUser()'  [" . __LINE__ . "]  messages for the user    = ", $orderedMessages);
        self::closeConnection();
        return $orderedMessages;
    }

    public static function existsChannel(string $channelname): bool
    {
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

    public static function insertChannel(int $userid, string $channelname)
    {
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

    public static function insertMessage(int $userid, int $channelid, string $txt)
    {
        $users = self::getUsersForChannel($channelid);

        $con = self::getConnection();
        $con->beginTransaction();
        $msgId = false;
        try {
            // insert new message name
            $sql = "INSERT INTO message (user_id, channel_id, txt)";
            $sql .= " VALUES (?, ?, ?)";
            self::query($con, $sql, array($userid, $channelid, $txt));

            $msgId = $con->lastInsertid();

            // assign the new message to all members of the channel as unread and not important
            $sqlAssignMessage = "INSERT INTO message_flag (message_id, user_id, important, unread) VALUES";
            $paramsAssignMessage = [];
            $sqlArr = [];
            $s = "(?, ?, FALSE, TRUE)";

            \Logger::logDebugPrintR("DatabaseManager::insertMessage() insert User <-> channelid relation  sqlAssignMessage = ", $sqlAssignMessage);

            foreach ($users as $u) {
                $sqlArr[] = $s;
                $paramsAssignMessage[] = $msgId;
                $paramsAssignMessage[] = $u["userid"];
            }
            $sqlAssignMessage .= " " . implode(",", $sqlArr);

            \Logger::logDebug("DatabaseManager::insertMessage() insert User <-> channel relation  sqlAssignMessage = $sqlAssignMessage ", "");
            \Logger::logDebugPrintR("DatabaseManager::insertMessage() insert User <-> channel relation  paramsAssignMessage = ", $paramsAssignMessage);

            // insert channel <-> users relation
            self::query($con, $sqlAssignMessage, $paramsAssignMessage);
            // $res = $con->lastInsertid();

            $con->commit();

            $res = true;
        } catch (Exception $e) {
            $con->rollBack();
            $msgId = false;
        }
        self::closeConnection();
        return $msgId;
    }

    public static function getUsersForChannel($channelid)
    {
        $con = self::getConnection();

        // get all user ids who have access to the channel
        $sql = "\n";
        $sql .= "SELECT user_id AS userid                      \n";
        $sql .= "FROM ref_user_channel                                        \n  ";
        $sql .= "WHERE channel_id = ?   ";
          
        $con = self::getConnection();
        $tmp = self::query($con, $sql, array($channelid));
 
        $users = array();
        while ($u = \DatabaseManager::fetchAssoz($tmp)) {
            $users[]= $u;
        }
        return $users;
    }

    public static function getChannelIdForName(string $channelname)
    {
        $con = self::getConnection();
        $sql = "SELECT id FROM channel WHERE name = ?";
            
        $res = self::query($con, $sql, array($channelname));
        $res = \DatabaseManager::fetchAssoz($res);
        $id = $res["id"];

        self::closeConnection();
        return $id;
    }
    
    public static function markMessageImportant(int $userid, int $messageid) 
    {
        $con = self::getConnection();
        $con->beginTransaction();
        try {
            $sql = "UPDATE message_flag SET important = TRUE WHERE message_id = ? AND user_id = ?";
            
            self::query($con, $sql, array($messageid, $userid));

            $con->commit();
        } catch (Exception $e) {
            $con->rollBack();
            $userid = false;
        }
        self::closeConnection();
        return true;
    }

    public static function markMessageNotImportant(int $userid, int $messageid) 
    {
        $con = self::getConnection();
        $con->beginTransaction();
        try {
            $sql = "UPDATE message_flag SET important = FALSE WHERE message_id = ? AND user_id = ?";
            
            self::query($con, $sql, array($messageid, $userid));

            $con->commit();
        } catch (Exception $e) {
            $con->rollBack();
            $userid = false;
        }
        self::closeConnection();
        return true;
    }
}
