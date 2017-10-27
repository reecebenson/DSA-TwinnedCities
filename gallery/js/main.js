/**
 * Default Variables
 * 
 * @param Object
 * @param Object
 * @param Bool
 */
var $ = jQuery;
var base = $(document);
var debug = true;

/**
 * Data Variables
 * 
 * @param {*} imgLoader URL for the loading image
 * @param {*} curSearch 
 */
var imgLoader = "./gallery/img/load.gif";
var curSearch = undefined;

/**
 * Elements
 * 
 * @param {*} eForm      The form HTML element
 * @param {*} eSearch    The form Search button
 * @param {*} eInput     The input field
 */
var eForm = $('form[name="fForm"]');
var eSearch = $("#fSearch");
var eInput = $("#fPlace");

/**
 * Content Elements
 * 
 * @param {*} cPlaces        DIV for Places
 * @param {*} cPlacesTitle   DIV for Place Title
 * @param {*} cPlacesData    DIV for Places Data
 * @param {*} cWarning       DIV for display warnings
 * @param {*} cWarningTxt    SPAN for warning text
 * @param {*} cPlaceData     DIV for Place Data (information)
 * @param {*} cPlaceWeather  DIV for Place Weather (information)
 * @param {*} cPlaceTweets   DIV for Place Tweets (information)
 */
var cPlaces = $("#contentPlaces");
var cPlacesTitle = $("#contentPlacesTitle");
var cPlacesData = $("#contentPlacesData");
var cWarning = $("#warning");
var cWarningTxt = $("#warningMsg");
var cPlaceData = $("#placeData");
var cPlaceWeather = $("#placeWeather");
var cPlaceTweets = $("#placeTweets");

/**
 * Display Warning to User through an alert
 * @param {*} errText The Error text to show to the user
 */
const displayWarning = (errText) => {
    cWarningTxt.html(errText);
    cWarning.slideDown();
}

/**
 * Hide the warning element and the current places
 * - Used when the user starts typing again
 */
const hideElements = () => {
    if(curSearch === eInput.val()) return;
    cWarning.slideUp();
    cPlaces.slideUp();
}

/**
 * Start the process for requesting data for what the user
 * has inputted. Calls are done via AJAX and data is posted
 * to `/data/fetchPlaces.php` to grab the place data.
 * 
 * Each place is then assigned an onClick event which fires
 * the `loadTableRowData()` function. Further docs about this
 * are provided above and within that function.
 * 
 * findData() grabs the User's input and stores it into curSearch
 * @param {String} curSearch  Stores the current search string (input from user)
 * 
 * //TODO
 */
const findData = () => {
    // > Update our data variables
    curSearch = eInput.val();

    // > Validate our value and return if we don't have a valid term
    if (/^\s+|\s+$/g.test(curSearch) || !curSearch)
        return displayWarning("Please enter a valid search term!");

    // > Slide our content into view
    cPlacesData.html("<br/>Searching for places related to '<em>" + curSearch + "</em>'...<br/><br/><img src=\"" + imgLoader + "\" alt=\"Loading...\"><br/><br/>");
    cPlaces.slideDown();

    // > Log (debug)
    if(debug) console.log("Searching for: " + curSearch);

    $.ajax({
        method: 'POST',
        dataType: 'json',
        url: './data/fetchPlaces.php',
        async: false,
        timeout: 30000,
        data: { name: curSearch },
        error: () => {
            hideElements();
            return displayWarning("There was an error fetching your request!");
        },
        success: (result) => {
            cPlacesTitle.html("Places <small>(" + result['resp_json']['count'] + " matches for '" + curSearch + "')</small>");
            cPlacesData.html(result['resp_html']);
            $("#contentPlacesData > tbody > tr").click(function() {
                loadTableRowData($(this));
            });
        }
    });
};

const loadTableRowData = (row) => {
    console.log(row.data()["woeid"]);

    // > Start Process
    cPlaceData.html("<br/>Looking up data...</br><br/><img src=\"" + imgLoader + "\" alt=\"Loading...\"><br/><br/>");
    cPlaceWeather.html("<br/>Looking up data...</br><br/><img src=\"" + imgLoader + "\" alt=\"Loading...\"><br/><br/>");
    cPlaceTweets.html("<br/>Looking up data...</br><br/><img src=\"" + imgLoader + "\" alt=\"Loading...\"><br/><br/>");

    // > Processes
    $.ajax({
        method: 'POST',
        dataType: 'json',
        url: './data/fetchPlace.php',
        async: false,
        timeout: 30000,
        data: { woeid: row.data()["woeid"] },
        error: () => {
            cPlaceData.html("<br/>There was an issue trying to fetch the data.<br/>");
        },
        success: (result) => {
            // - Data
            cPlaceData.html(result['resp_details']);

            // - Weather
            cPlaceWeather.html(result['resp_weather']);

            // - Tweets
            cPlaceTweets.html(result['resp_tweets']);
        }
    });
};

base.ready(() => {
    // > Allocate our 'click' for our search form
    eSearch.on('click', () => { hideElements(); findData(); });
    eForm.on('input', 'input', hideElements);

    // > Override what happens when the user submits the form
    eForm.submit(e => {
        // > Deny our form from submitting when the user presses the Return key
        // or older browsers supporting buttons as Submit buttons
        e.preventDefault();

        // > Perform what clicking 'eSearch' would do
        hideElements();
        findData();
    });

    // > Ready and loaded!
    console.log("Ready!");
});