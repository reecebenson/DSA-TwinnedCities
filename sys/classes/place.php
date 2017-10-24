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
    }

?>