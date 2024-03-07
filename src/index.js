(
    function () {
        let currentFeed = false;
        const apiURL = "http://localhost/api/"

        window.onload = function () {
            addEventListeners();
        }

        /*********
         * 
         * UI
         * 
         ********/

        function addEventListeners() {
            const feedSelect = document.getElementById('select-feed');
            const fetchBtn = document.getElementById('fetch-feed-btn');
            const editBtn = document.getElementById('edit-feed-btn');
            const addBtn = document.getElementById('add-feed-btn');
            const closeModalBtn = document.getElementById('close-modal-btn');

            // Allow user to edit feed record or fetch content when selecting a feed from the dropdown
            feedSelect.addEventListener('change', function (e) {
                let selectedOption = this.children[this.selectedIndex];
                currentFeed = {
                    id: selectedOption.value,
                    title: selectedOption.textContent,
                    url: selectedOption.dataset.url,
                    selectedIndex: this.selectedIndex
                };

                editBtn.disabled = false;
                fetchBtn.disabled = false;
            });

            fetchBtn.addEventListener('click', async function (e) {
                if (currentFeed) {
                    let rssFetcher = new RSSParser(currentFeed.url);
                    let data = await rssFetcher.fetchFeed();
                    let elements = rssFetcher.parseXML(data);
                    displayRSSContent(elements);
                }
            });

            editBtn.addEventListener('click', function (e) {
                if (currentFeed) {
                    showRSSModal(currentFeed);
                }
            });

            addBtn.addEventListener('click', function (e) {
                showRSSModal();
            });

            closeModalBtn.addEventListener('click', function (e) {
                closeModal();
            });
        }

        /***
         * Generates parsed RSS/XML into a usable HTML element and appends it to a container.
         * @param {HTMLElement[]} elements - An array of item elements that will then be queried to retrieve relevant content
         */
        function displayRSSContent(elements) {
            let feedContent = document.getElementById('feed-content');

            if (feedContent.childElementCount > 0) {
                feedContent.innerHTML = "";
            }

            for (let element of elements) {
                let newArticle = document.createElement('article');
                newArticle.innerHTML = `<h2>${element.querySelector('title').textContent}</h2>
                <a href=${element.querySelector('link').textContent}>${element.querySelector('link').textContent}</a>
                <p>${element.querySelector('description').textContent}</p>`;

                document.getElementById('feed-content').appendChild(newArticle);
            }
        }

        /***
         * Appends a new option element to the RSS feed select element using data from the provided object
         * @param {Object} newContent - The feed information to include in the option element
         * @param {string} newContent.feed_url - The URL of the new feed 
         * @param {string} newContent.feed_title - The title of the new feed
         * @param {number} newContent.feed_id - The ID of the new record
         */
        function addNewFeedItem(newContent){
            let selectElement = document.getElementById('select-feed');

            let newOption = document.createElement('option');
                newOption.setAttribute('data-url', newContent.feed_url);
                newOption.value = newContent.feed_id;
                newOption.textContent = newContent.feed_title;
            selectElement.appendChild(newOption);
        }

        /***
         * Updates attributes of an existing option element at a specified index of the select element, using data from the provided object
         * @param {number} selectedIndex - The index of the option element that is being updated
         * @param {Object} newContent - The feed information to include in the option element
         * @param {string} newContent.feed_url - The updated URL 
         * @param {string} newContent.feed_title - The updated title 
         */
        function updateFeedItem(selectedIndex, newContent){
            let selectElement = document.getElementById('select-feed');
            let elementToChange = selectElement.children[selectedIndex];

            elementToChange.setAttribute('data-url', newContent.feed_url);
            elementToChange.textContent = newContent.feed_title;
        }

        /***
         * Deletes an option element at a specified index of the select element, then resets the UI accordingly
         * @param {number} selectedIndex - The index of the option element that is being removed
         */
        function deleteFeedItem(selectedIndex){
            let selectElement = document.getElementById('select-feed');
            let elementToChange = selectElement.children[selectedIndex];

            selectElement.removeChild(elementToChange);
            selectElement.selectedIndex = 0;
            currentFeed = false;
            document.getElementById('fetch-feed-btn').disabled = true;
            document.getElementById('edit-feed-btn').disabled = true;
        }

        /*********
         * 
         * SERVER
         * 
         ********/

        /***
         * Sends form data to the relative API endpoint for adding a new record or altering an existing one
         * @param {?number} selectedIndex - The ID of the feed that is being altered
         */
        function submitChanges(selectedIndex) {
            let dataToSend = new FormData(document.getElementById('edit-feed-form'));

            let api = selectedIndex ? "edit.php" : "add.php";
            let method = "POST";

            fetch(apiURL + api, {
                body: dataToSend,
                method: method
            })
                .then(data => data.json())
                .then(function (data) {
                    if(selectedIndex){
                        updateFeedItem(selectedIndex, data);
                    }
                    else {
                        addNewFeedItem(data);
                    }
                    closeModal();
                });
        }

        /***
         * Sends the ID of the chosen RSS feed to the server to delete the feed, then updates the select element to reflect the changes pending successful deletion.
         * @param {number} selectedIndex - The index of the option element that'll subsequently be removed from our select element
         */
        function deleteFeed(selectedIndex) {
            let id = document.getElementById('feed-id').value;

            fetch(apiURL + "delete.php?id=" + id, {
                method: "DELETE"
            })
                .then(function () {
                    deleteFeedItem(selectedIndex);
                    closeModal();
                });
        }

        /*********
         * 
         * MODAL
         * 
         ********/

        /***
         * Displays a modal allowing the user to edit an existing feed or add a new one.
         * @param {?Object} content - If content is provided, this function will render additional content to aid with editing an existing feed.
         * @param {string} content.title - The title of an RSS feed
         * @param {string} content.url - The URL of an RSS feed
         * @param {number} content.id - The ID of an RSS feed
         */
        function showRSSModal(content = null) {
            //Content is only provided when an item is being edited, so let's control which items are rendered here
            if (content) {
                const deleteFeedBtn = document.getElementById('delete-feed-btn');

                deleteFeedBtn.disabled = false;
                deleteFeedBtn.addEventListener('click', function (e) {
                    e.preventDefault();
                    deleteFeed(content.selectedIndex);
                });

                let feedTitleInput = document.getElementById('feed-title');
                let feedURLInput = document.getElementById('feed-url');
                let feedIDInput = document.getElementById('feed-id');
                
                feedTitleInput.value = content.title;
                feedTitleInput.defaultValue = content.title;
                feedURLInput.value = content.url;
                feedURLInput.defaultValue = content.url;
                feedIDInput.value = content.id;
                feedIDInput.defaultValue = content.id;
            }

            document.getElementById('save-changes-btn').addEventListener('click', function (e) {
                e.preventDefault();
                submitChanges(content?.selectedIndex);
            });

            document.getElementById('edit-feed').showModal();
        }

        /***
         * Resets the necessary form inputs and by cloning buttons, removes event listeners set up in displayRSSModal()
         */
        function clearModal() {
            let inputs = document.querySelectorAll('#edit-feed-form input:not([type=submit])');

            for (let input of inputs) {
                input.value = "";
                input.defaultValue = "";
            }

            const deleteFeedBtn = document.getElementById('delete-feed-btn');
            deleteFeedBtn.disabled = true;

            const newDeleteBtn = deleteFeedBtn.cloneNode();
            deleteFeedBtn.replaceWith(newDeleteBtn);

            const saveBtn = document.getElementById('save-changes-btn');
            const newSaveBtn = saveBtn.cloneNode();
            saveBtn.replaceWith(newSaveBtn);
        }

        /***
         * Clears the modal first and then closes it
         */
        function closeModal() {
            clearModal();
            document.getElementById('edit-feed').close();
        }
    })();