<?php
/**
 * Controls all operations that require interaction with a database
 */
class DatabaseHelper
{
    private $config;
    private $db;
    private $query;

    /**
     * Sets the database config according to environment variables
     * Improvement: Use proper environment variables and allow to use any config
     */
    function __construct()
    {
        //Ensure the correct file is accessed regardless of how deep we are in our directory structure
        $CONFIG_DIR = $_SERVER['DOCUMENT_ROOT'] . "/../";
        require_once($CONFIG_DIR . "db.php");

        $this->config = $db_credentials;
    }

    /**
     * Creates a connection to our database
     * Improvement: Return whether the connection was successful
     */
    public function connect()
    {
        $this->db = new mysqli(
            $this->config['server'],
            $this->config['usr'],
            $this->config['pwd'],
            $this->config['db']
        );
    }

    /**
     * Checks if the connection to the database is still active
     * @return boolean - Returns whether the connection is active
     */
    public function isConnected()
    {
        return $this->db->ping();
    }

    /**
     * Handles our queries and deals with prepared statements if needed. Additionally will determine parameter types for prepared statements.
     * Improvement: Split this into different functions depending on the query type.
     * @param string $statement - The SQL query to run
     * @param array|boolean $values - An array of values to bind to a prepared statement. Set to false to skip this step.
     * @return int|boolean|array - Returns different values depending on the type of statement ran. SELECT queries will return an array of results, INSERT will return the ID of a newly added row, and UPDATE/DELETE will return true/false. 
     */
    public function runQuery($statement, $values = false)
    {
        if (!$this->isConnected()) {
            $this->connect();
        }

        $this->query = $this->db->prepare($statement);

        if ($values) {
            $types = "";
            foreach ($values as $value) {
                switch (gettype($value)) {
                    case "string":
                        $types .= "s";
                        break;
                    case "integer":
                        $types .= "i";
                        break;
                    case "double":
                        $types .= "d";
                        break;
                }
            }
            $this->query->bind_param($types, ...$values);
        }

        $this->query->execute();

        if ($this->db->insert_id) {
            return $this->db->insert_id;
        }

        $res = $this->query->get_result();
        if (gettype($res) == "boolean") {
            return $res;
        } else {
            return $res->fetch_all(MYSQLI_ASSOC);
        }
    }

    /**
     * Allows to manually disconnect from the database
     */
    public function x()
    {
        if ($this->isConnected()) {
            $this->db->close();
        }
    }

    function __destruct()
    {
        //Disconnect from the database
        $this->x();
    }
}

?>