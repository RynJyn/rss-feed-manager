<?php

require_once("DatabaseHelper.php");

/**
 * Handles various operations relating to the rss_feeds table
 * General improvement: All functions should contain some error handling
 */
class RSSTable
{
    private $db;
    /**
     * Upon creation of the object, creates a connection to the database
     */
    function __construct()
    {
        $this->db = new DatabaseHelper();
        $this->db->connect();
    }

    /**
     * Creates the rss_feeds table 
     */
    public function createRSSTable()
    {
        $sql = "CREATE TABLE `rss_feeds` (`feed_id` INT UNSIGNED NOT NULL AUTO_INCREMENT , `feed_title` VARCHAR(70) NOT NULL , `feed_url` TEXT NOT NULL , PRIMARY KEY (`feed_id`)) ENGINE = InnoDB;";
        $this->db->runQuery($sql);
    }

    /**
     * Gets a list of all of the available feeds in the table
     * @return array<array> - An array of associative arrays containing data retrieved from the database 
     */
    public function getFeeds()
    {
        $sql = "SELECT * FROM `rss_feeds`";
        return $this->db->runQuery($sql);
    }

    /**
     * Queries the database to insert a new row into the database using data provided by the user
     * @param array $data - An array of inputs provided by the user
     * @return array - Returns an array containing the user-provided data and the ID of the newly inserted row
     */
    public function addFeed($data)
    {
        $sql = "INSERT INTO `rss_feeds` (`feed_title`, `feed_url`) VALUES (?,?)";
        $res = $this->db->runQuery($sql, $data);
        array_push($data, $res);
        return $data;
    }

    /**
     * Queries the database to update an existing record pertaining to an ID provided by the user
     * Improvement: Rather than relying on user input for the return, it should retrieve the data from the database and send it back
     * @param array $data - An array of inputs provided by the user
     * @return array - Returns the user input
     */
    public function editFeed($data)
    {
        $sql = "UPDATE `rss_feeds` SET `feed_title` = ?, `feed_url` = ? WHERE `feed_id` = ?";
        $res = $this->db->runQuery($sql, $data);
        return $data;
    }

    /**
     * Queries the database to delete an existing record pertaining to an ID provided by the user
     * @param array $data - An array of inputs provided by the user
     * @return boolean - Returns whether the transaction was successful or not
     */
    public function deleteFeed($data)
    {
        $sql = "DELETE FROM `rss_feeds` WHERE `feed_id` = ?";
        return $this->db->runQuery($sql, $data);
    }
}

?>