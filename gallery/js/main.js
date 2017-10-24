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
var cPlacesData = $("#contentPlacesData");
var cWarning = $("#warning");
var cWarningTxt = $("#warningMsg");

const displayWarning = (errText) => {
    cWarningTxt.html(errText);
    cWarning.slideDown();
}

const hideElements = () => {
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
    cPlacesData.html("Searching for places related to '<em>" + curSearch + "</em>'...<br/><br/><img src=\"" + imgLoader + "\" alt=\"Loading...\">");
    cPlaces.slideDown();

    // > Log (debug)
    if(debug) console.log("Searching for: " + curSearch);

    $.ajax({
        method: 'POST',
        dataType: 'json',
        url: './data/fetchPlaces.php',
        data: { name: curSearch },
        error: () => {
            hideElements();
            return displayWarning("There was an error fetching your request!");
        },
        success: (result) => {
            cPlacesData.html(result['resp_html']);
        }
    });
};

base.ready(() => {
    // > Allocate our 'click' for our search form
    eSearch.on('click', findData);
    eForm.on('input', 'input', hideElements);

    // > Override what happens when the user submits the form
    eForm.submit(e => {
        // > Deny our form from submitting when the user presses the Return key
        // or older browsers supporting buttons as Submit buttons
        e.preventDefault();

        // > Perform what clicking 'eSearch' would do
        findData();
    });

    // > Ready and loaded!
    console.log("Ready!");
});