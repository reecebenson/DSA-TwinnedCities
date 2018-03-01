<?php
    require_once('sys/core.php');

    
    // Get data from database
    $woeid = "28218";
    $statement = $db->prepare("SELECT * FROM `city` WHERE `woeid` = ? LIMIT 1");
    $statement->bindParam(1, $woeid);
    $statement->execute();

    // Get row
    $row = $statement->fetch(PDO::FETCH_ASSOC);

    if($row) {
        
        $poi = array();

        // Get data from database
        $statement = $db->prepare("SELECT * FROM `places` WHERE `city_id` = ?");
        $statement->bindParam(1, $woeid);
        $statement->execute();

        // Fetch all data
        foreach($statement->fetchAll() as $res)
        {
            // Setup Place
            $place = array(
                "name" => $res['name'],
                "desc" => $res['description'],
                "www" => $res['www'],
                "phone" => $res['phone'],
                "address" => $res['address'],
                "image" => $res['img_source']
            );

            // Add Place to POI Array
            $poi[$res['name']] = $place;
        }
        $row['poi'] = $poi;
    } else {
        die('no row');
    }

    print_r($row);


    echo "print json encoding<br/><br/>";
    die(json_encode($row));
?>