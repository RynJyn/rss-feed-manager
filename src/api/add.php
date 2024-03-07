<?php
if ($_SERVER['REQUEST_METHOD'] == "POST" && isset($_POST)) {
    if (isset($_POST['feed-title'])) {
        $titleLength = strlen($_POST['feed-title']);
        if ($titleLength == 0 || $titleLength > 70) {
            http_response_code(400);
        }
    }

    if (isset($_POST['feed-url'])) {
        if (strlen($_POST['feed-url']) == 0) {
            http_response_code(400);
        }
    }

    require_once("../RSSTable.php");
    $rss_table = new RSSTable();
    $res = $rss_table->addFeed(
        array(
            $_POST['feed-title'],
            $_POST['feed-url']
        )
    );

    $keys = array('feed_title', 'feed_url', 'feed_id');
    $res = array_combine($keys, $res);
    echo json_encode($res);
    http_response_code(201);
} else {
    http_response_code(405);
}
?>