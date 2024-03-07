<?php 
if($_SERVER['REQUEST_METHOD'] === "GET" && isSet($_GET['url']))
{
    require_once("../RSSFetcher.php");
    $fetcher = new RSSFetcher($_GET['url']);
    $feed = $fetcher->getContents();

    header('Content-Type: text/xml' );
    echo $feed;
}
?>