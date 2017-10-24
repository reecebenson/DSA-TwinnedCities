// > Variables
var $ = jQuery;
var base = $(document);
var debug = true;

// > Data Variables
var imgLoader = "./gallery/img/load.gif";
var curSearch = undefined;

// > Elements
var eForm = $('form[name="fForm"]');
var eSearch = $("#fSearch");
var eInput = $("#fPlace");

// > Content Elements
var cPlaces = $("#contentPlaces");
var cPlacesTitle = $("#contentPlacesTitle");
var cPlacesData = $("#contentPlacesData");
var cPlaceData = $("#placeData");
var cWarning = $("#warning");
var cWarningTxt = $("#warningMsg");

const displayWarning = (errText) => {
    cWarningTxt.html(errText);
    cWarning.slideDown();
}

const hideElements = () => {
    if(curSearch === eInput.val()) return;
    cWarning.slideUp();
    cPlaces.slideUp();
}

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
        context: $("#contentPlacesData > tbody > tr"),
        url: './data/fetchPlaces.php',
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
    console.log(row.data()['woeid']);
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