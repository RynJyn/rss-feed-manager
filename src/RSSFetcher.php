<?php 

class RSSFetcher 
{
    function __construct($url)
    {
        $this->url = $url;
        $this->directory = $_SERVER['DOCUMENT_ROOT'] . "/rss_files/";
        if(!is_dir($this->directory))
        {
            mkdir($this->directory);
        }
    }

    public function getContents()
    {
        $parsedURL = parse_url($this->url);
        $fileToCheck = $this->directory . $parsedURL['host'] . ".xml";
        if(!is_file($fileToCheck))
        {
            $contents = file_get_contents($this->url);

            file_put_contents($fileToCheck, $contents);

            return $contents;
        }
      
        return file_get_contents($fileToCheck);
    }
}

?>