<?php
    class home_model {

        private $bll;
        static $_instance;
        
        function __construct() {
            $this -> bll = home_bll::getInstance();
        }

        public static function getInstance() {
            if (!(self::$_instance instanceof self)) {
                self::$_instance = new self();
            }
            return self::$_instance;
        }

        public function get_carouselTouristcat() {
            return $this -> bll -> get_carouselTouristcat_BLL();
        }

        public function get_carouselType() {
            return $this -> bll -> get_carouselType_BLL();
        }

        public function get_carouselCategory() {
            // return 'hola car type';
            return $this -> bll -> get_carouselCategory_BLL();
        }

        public function get_carouselOperation() {
            return $this -> bll -> get_carouselOperation_BLL();
        }

        public function get_carouselCity() {
            return $this -> bll -> get_carouselCity_BLL();
        }

        public function get_carouselLastsearch($filters) {
            return $this -> bll -> get_carouselLastsearch_BLL($filters);
        }

        public function get_carouselMostvisited() {
            return $this -> bll -> get_carouselMostvisited_BLL();
        }

        public function get_carouselRecomendations() {
            return $this -> bll -> get_carouselRecomendations_BLL();
        }
    }
?>