<?php
if ($_SERVER['REQUEST_METHOD'] === "DELETE" && isset($_GET['id'])) {
    require_once("../RSSTable.php");
    $rss_table = new RSSTable();
    $rss_table->deleteFeed(
        array(
            $_GET['id']
        )
    );

    http_response_code(204);
} else {
    http_response_code(405);
}
?>