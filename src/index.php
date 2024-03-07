<?php 
ini_set('display_errors', 1);
?>
<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>RSS Feed Application</title>
    <script type="text/javascript" src="rss.js"></script>
    <script type="text/javascript" src="index.js"></script>
    <link rel="stylesheet" type="text/css" href="index.css"/>
</head>
<body>
    <noscript>This application requires JavaScript.</noscript>
    <main>
        <h1>RSS Feed Selector</h1>
        <select id="select-feed">
            <option disabled selected value="">Select the feed you'd like to fetch</option>
            <?php 
            include_once("RSSTable.php");
            $rss_table = new RSSTable();
            $feeds = $rss_table->getFeeds();
            //Improvement: Could assign each retrieved feed item into its own Feed class 
            if($feeds)
            {
                foreach($feeds as $feed)
                {
                    echo '<option data-url="'. $feed['feed_url'] . '" value=' . $feed['feed_id'] . '>' . $feed['feed_title'] . '</option>';
                }
            }
            ?>
        </select>
        <button id="fetch-feed-btn" disabled type="button">Fetch</button>
        <button id="edit-feed-btn" disabled type="button">Edit</button>
        <button id="add-feed-btn" type="button">Add New Feed</button>

        <dialog id="edit-feed" closed>
            <form id="edit-feed-form">
                <label>Title: <input name="feed-title" id="feed-title" type="text" minlength="1"/></label>
                <label>URL: <input name="feed-url" id="feed-url" type="text" minlength="1"/></label>
                <input type="hidden" name="feed-id" id="feed-id" name="feed-id"/>
                <input id="save-changes-btn" type="submit" value="Save"/>
                <input id="delete-feed-btn" type="submit" disabled value="Delete"/>
            </form>
            <button id="close-modal-btn" type="button">Cancel</button>
        </dialog>

        <div id="feed-content"></div>
    </main>
</body>
</html>