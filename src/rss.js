/***
 * Retrieves RSS from a provided URL and parses it into XML elements ready for manipulation
 */
class RSSParser {
    #getFeedUrl = "http://localhost/api/get_feed.php";
    #url;

    /***
     * @param {string} url - The URL to fetch the RSS feed from
     */
    constructor(url) {
        this.#url = url;
    }

    /***
     * Gets the URL the parser has been set to fetch
     */
    getURL() {
        return this.#url;
    }

    /***
     * Fetches the RSS content from the relevant API endpoint
     * @returns {string} - Response text from the fetch request
     */
    async fetchFeed() {
        let data = await fetch(this.#getFeedUrl + "?url=" + this.#url)
            .then(res => res.text());

        return data;
    }

    /***
     * Parses the RSS into DOM-compatible elements
     * @param {string} data - A string of XML to be parsed
     * @returns {HTMLElement[]}
     */
    parseXML(data) {
        let parser = new DOMParser;
        let parsedElements = parser.parseFromString(data, "text/xml").querySelectorAll('item');
        return parsedElements;
    }
}