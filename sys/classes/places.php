<?php

    class Places {
        private static $yahooBase = "http://query.yahooapis.com/v1/public/yql";
        
        public static function query($qry) {
            // > Build URL Request
            $args = array(
                'q' => $qry,
                'format' => "json"
            );
            $qryUrl = self::$yahooBase . "?" . http_build_query($args);

            // > Request from URL
            $data = json_decode(file_get_contents($qryUrl), true);
            return $data;
        }

        public static function queryPlaces($place) {
            return self::query("SELECT * FROM geo.places WHERE text = \"" . $place . "\"");
        }

        public static function formatPlace($data) {
            // > Setup our Place
            $place = array(
                "name" => $data['name'],
                "woe_id" => $data['woeid'],
                "type" => $data['placeTypeName']['content'],
                "country" => array(
                    "name" => $data['country']['content'],
                    "woe_id" => $data['country']['woeid'],
                    "classifier" => $data['country']['code']
                )
            );

            // > Check for extra data (region of country/state, county, city, town, postcode, central lat/long, timezone)
            // -> Region/State
            if(!is_null($data['admin1'])) {
                // > Add to our Place
                $place['state'] = array(
                    "name" => $data['admin1']['content'],
                    "woe_id" => $data['admin1']['woeid']
                );
            } else $place['state'] = array("name" => "<em>undefined</em>", "woe_id" => 0);

            // -> County/Area/LGA
            if(!is_null($data['admin2'])) {
                $place['county'] = array(
                    "name" => $data['admin2']['content'],
                    "woe_id" => $data['admin2']['woeid']
                );
            } else $place['county'] = array("name" => "<em>undefined</em>", "woe_id" => 0);

            // -> Postcode / ZIP
            if(!is_null($data['postal'])) {
                $place['postcode'] = array(
                    "name" => $data['postal']['content'],
                    "woe_id" => $data['postal']['woeid'],
                    "classifier" => $data['postal']['type']
                );
            } else $place['postcode'] = array("name" => "<em>undefined</em>", "woe_id" => 0, "classifier" => null);

            // -> Central Lat/Long
            if(!is_null($data['centroid'])) {
                $place['location'] = array(
                    "latitude" => $data['centroid']['latitude'],
                    "longitude" => $data['centroid']['longitude']
                );
            } else $place['location'] = array("latitude" => null, "longitude" => null);

            // -> Time Zone
            if(!is_null($data['timezone'])) {
                $place['timezone'] = array(
                    "name" => $data['timezone']['content'],
                    "woe_id" => $data['timezone']['woeid']
                );
            } else $place['timezone'] = array("name" => "<em>undefined</em>", "woe_id" => 0);

            // > Return our place
            return $place;
        }
    }

?>