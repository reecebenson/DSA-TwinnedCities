<?php
	/* Reece Benson */
	/* BSc Comp Sci */
    require_once('../sys/core.php');

    // > Variables
    $resp = array();

    if(!isset($_POST)) {
        $resp['status'] = 500;
        $resp['recv'] = array();
        $resp['no_post'] = true;
        die(json_encode($resp, JSON_PRETTY_PRINT));
    }

    // > Setup our response
    $resp['status'] = 200;
    $resp['recv'] = $_POST;

    // > Request our places
    $nameOfPlace = $_POST['name'];
    $resp['resp_json'] = Places::queryPlaces($nameOfPlace)['query'];
    $resp['resp_html'] = '<div style="text-align: left !important;">';

    // > Filter our response into HTML
    for($i = 0; $i < $resp['resp_json']['count']; $i++)
    {
        $builtStr = '<div class="content"><div class="row"><div class="col-3">test</div><div class="col-9">test</div></div></div>';

        // > Append to our HTML response
        $resp['resp_html'] .= $builtStr;
    }

    // > Complete our HTML response
    $resp['resp_html'] .= "</div>";

    // > Echo our response as JSON
    die(json_encode($resp, JSON_PRETTY_PRINT));
?>