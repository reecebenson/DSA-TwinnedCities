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

    // > Filter our response into HTML
    $resp['resp_html'] = '<thead>
        <tr class="content" style="font-weight: bold; text-align: left;">
            <th>Name</th>
            <th>Type</th>
            <th>District/County</th>
            <th>Province/State</th>
            <th>Country</th>
            <th>WOEID</th>
        </tr>
    </thead>';

    $resp['resp_html'] .= "<tbody>";
    for($i = 0; $i < $resp['resp_json']['count']; $i++)
    {
        $place = Places::formatPlace($resp['resp_json']['results']['place'][$i]);
        $resp['places'][$i] = $place;

        // > Build our string
        $builtStr = '<tr class="content" style="text-align: left;" data-woeid="' . $place['woe_id'] . '">
            <td>' . $place['name'] . '</td>
            <td>' . $place['type'] . '</td>
            <td>' . $place['county']['name'] . '</td>
            <td>' . $place['state']['name'] . '</td>
            <td>' . $place['country']['name'] . '</td>
            <td>' . $place['woe_id'] . '</td>
        </tr>';

        // > Append to our HTML response
        $resp['resp_html'] .= $builtStr;
    }

    // > Complete our HTML repsonse
    $resp['resp_html'] .= "</tbody>";

    // > Echo our response as JSON
    die(json_encode($resp, JSON_PRETTY_PRINT));
?>