<?php
    class login_dao {
        static $_instance;

        private function __construct() {
        }

        public static function getInstance() {
            if(!(self::$_instance instanceof self)){
                self::$_instance = new self();
            }
            return self::$_instance;
        }

        public function select_userReg($db, $username){

			$sql = "SELECT u.username
                    FROM `user` u
                    WHERE u.username = '$username/local'";

            $stmt = $db->ejecutar($sql);
            return $db->listar_object($stmt);
        }

        public function select_email($db, $email){

			$sql = "SELECT u.email
                    FROM `user` u
                    WHERE u.email = '$email/local'";

            $stmt = $db->ejecutar($sql);
            return $db->listar_object($stmt);
        } 

        public function insert_user($db, $uid, $username, $password, $email, $avatar) {

            $sql = "INSERT INTO `user`(`uid`, `username`, `password`, `email`, `type_user`, `avatar`, `isActive`) 
                    VALUES ('$uid','$username/local','$password','$email/local','client','$avatar','false')";

            return $stmt = $db->ejecutar($sql);
        }

        public function select_social_login($db, $uid) {

			$sql = "SELECT u.uid
                        FROM `user` u
                        WHERE u.uid = '$uid'";

            $stmt = $db->ejecutar($sql);
            return $db->listar_object($stmt);
        }

        public function insert_social_login($db, $uid, $username, $email, $avatar, $provider) {

            $sql = "INSERT INTO `user`(`uid`, `username`, `email`, `type_user`, `avatar`, `isActive`) 
                    VALUES ('$uid','$username/$provider','$email/$provider','client','$avatar','true')";

            return $stmt = $db->ejecutar($sql);
        }

        public function select_verify_email($db, $token_email){

			$sql = "SELECT u.token_email
                        FROM `user` u
                        WHERE u.token_email = '$token_email'";

            $stmt = $db->ejecutar($sql);
            return $db->listar_object($stmt);
        }

        public function update_verify_email($db, $uid){

            $sql = "UPDATE `user` u
                        SET u.isActive = 'true'
                        WHERE u.uid = '$uid'";

            return $stmt = $db->ejecutar($sql);
        }

        public function delete_verify_email($db, $uid){

            $sql = "DELETE
                        FROM `user` u
                        WHERE u.uid = '$uid'";

            return $stmt = $db->ejecutar($sql);
        }

        public function select_recover_email($db, $email){

			$sql = "SELECT u.email
                        FROM `user` u
                        WHERE u.email = '$email/local' AND u.password NOT LIKE ('')";

            $stmt = $db->ejecutar($sql);
            return $db->listar_object($stmt);
        }

        public function update_recover_email($db, $email, $token_email){

			$sql = "UPDATE `user` u
                        SET u.token_email = '$token_email', u.isActive = 'false'
                        WHERE u.email = '$email/local'";

            return $stmt = $db->ejecutar($sql);
        }

        public function update_new_passwoord($db, $token_email, $password){

            $sql = "UPDATE `user` u
                        SET u.password = '$password', u.isActive = 'true', u.token_email = ''
                        WHERE u.token_email = '$token_email'";

            return $stmt = $db->ejecutar($sql);
        }

        public function select_userLogin($db, $username){

			$sql = "SELECT u.uid, u.username, u.password, u.isActive
                        FROM `user` u
                        WHERE u.username = '$username/local'";

            $stmt = $db->ejecutar($sql);
            return $db->listar_object($stmt);
        }

        // SIMPLIFICAR LOS DOS ÚLTIMOS DAOS EN UNO ???

        public function select_data_user($db, $uid){

            $sql = "SELECT u.username, u.password, u.email, u.type_user, u.avatar
                    FROM `user` u
                    WHERE u.uid = '$uid'";

            $stmt = $db->ejecutar($sql);
            return $db->listar_object($stmt);
        }
    }

?>