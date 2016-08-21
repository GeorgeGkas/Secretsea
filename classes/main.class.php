<?php 

    # The symbolic constants of databse connection
    require_once 'config.php';

    class Main {
        protected $DHB  =   NULL;

        function __construct() {
            try {
                # DHB : Database Handle
                $this->DHB = new PDO("mysql:host=".DB_HOST.";dbname=".DB_DATABSE, DB_USER, DB_PASSWORD);
                $this->DHB->setAttribute( PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION );
            }
            catch(PDOException $e) {
                file_put_contents('../PDOErrors.txt', $e->getMessage(), FILE_APPEND);
            }
        }

        protected function getUserEmail() {
            return $_SESSION['login'];
        }

        protected function setReturnState($msg, $state = false) {
            return array('State' => $state, 'Msg' => $msg);
        }

        public function isLogin() {
            if($this->UserIsLogin()) {
                return true;
            }
            else {
                return false;
            }
        }


        private function UserIsLogin() {
            if (isset($_SESSION['login'])) {
                return true;
            }
            else {
                return false;
            }
        }   # END UserIsLogin()
    }   # END Main CLASS
?>