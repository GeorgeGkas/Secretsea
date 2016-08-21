<?php
    require_once 'main.class.php';
    require_once 'bcrypt.php';

    class userAuth extends Main {

        private $L_UserEmail    =   NULL;
        private $L_UserPass     =   NULL;

        private $R_UserEmail    =   NULL;
        private $R_UserPass1    =   NULL;
        private $R_UserPass2    =   NULL;
        private $ReCaptcha      =   NULL;

        private $LogoutEmails   =   NULL;

        private $path           =   '/georgegkas';

        /*public function showEmail() {
            return $this->getUserEmail();
        }*/


        public function Login($UserEmail, $UserPass) {
            $this->L_UserEmail = $UserEmail;
            $this->L_UserPass = $UserPass;

            if (!$this->exist_LoginEmail()) {
                return $this->setReturnState('User email does not exist in our databases.');
            }

            if (!$this->correct_LoginPass()) {
                return $this->setReturnState('Password is wrong.');
            }

            return $this->doLogin();
            
        }

        public function Logout($emails) {
            $this->LogoutEmails = json_decode(stripslashes($emails));
            if(!$this->RemoveOnlineState()) {
                return $this->setReturnState('We had problems connected to our databases. Please try again later');
            }
            $this->clearUserEmail();
            $indexUrl = $this->redirectToIndex();
            return $this->setReturnState($indexUrl, true);
        }

        public function Register($UserEmail, $UserPass1, $UserPass2, $ReCaptcha) {
            $this->R_UserEmail = $UserEmail;
            $this->R_UserPass1 = $UserPass1;
            $this->R_UserPass2 = $UserPass2;
            $this->ReCaptcha = $ReCaptcha;


            if (!$this->valid_Captcha()) {
                return $this->setReturnState('Please verify yourself as human.');
            }

            if (!$this->valid_RegisterEmail()) {
                return $this->setReturnState('Only letters and nubers are allowed for email. Don\'t use special symbols or email domain names.');
            }

            # MOVED TO FRONT END
            # if (!$this->valid_RegisterPass()) {
            #     return $this->setReturnState('Passwords do not match.');
            # } 

            if ($this->exist_RegisterEmail()) {
                return $this->setReturnState('Email already registered.');
            }

            return $this->doRegister();

        }



        private function exist_LoginEmail() {
            try {
                $STH = $this->DHB->prepare("SELECT UserEmail FROM Users WHERE UserEmail = :user_email");
                $STH->bindParam(':user_email', $this->L_UserEmail);
                $STH->execute();

                $UserExists = $STH->fetchAll();

                if (!$UserExists) {
                    return false;
                } 
                else {
                    return true;
                }
            }
            catch(PDOException $e) {
                file_put_contents('PDOErrors.txt', $e->getMessage(), FILE_APPEND);
                return false;
            }
        }

        private function correct_LoginPass() {
            try {
                $STH = $this->DHB->prepare("SELECT UserPass FROM Users WHERE UserEmail = :user_email");
                $STH->bindParam(':user_email', $this->L_UserEmail);
                $STH->execute();

                $UserInfo = $STH->fetchAll();

                $VerifiedPassword = password_verify($this->L_UserPass, $UserInfo[0]['UserPass']);

                # Check if password is good
                if ($VerifiedPassword) {
                    return true;
                }
                else {
                    return false;
                }
            }
            catch(PDOException $e) {
                file_put_contents('PDOErrors.txt', $e->getMessage(), FILE_APPEND);
                return false;
            }
        }

        private function valid_Captcha() {
            # FIRST WE CHECK IF THE FORM WAS POSTED BY A HUMAN
            if ($this->ReCaptcha == NULL) {
                return false;
            }

            # HAS THE USER BEEN AUTHORIAZED BY GOOGLE ?
            $response = file_get_contents("https://www.google.com/recaptcha/api/siteverify?secret=6Lc6mxcTAAAAAABnITaUtxp3pbH_xUf8fEtj_f7p&response=".$this->ReCaptcha."&remoteip=".$_SERVER['REMOTE_ADDR']);
            if($response.success == false) {
                return false;
            }

            return true;
        }

        private function valid_RegisterEmail() {
            # CHECK IF EMAIL CONSISTS ONLY a-z A-Z 0-9 characters
            if (!ctype_alnum($this->R_UserEmail)) {
                return false;
            }

            return true;
        }

        private function valid_RegisterPass() {
            if ($this->R_UserPass1 == $this->R_UserPass2) {
                return true;
            }

            return false;
        }

        private function exist_RegisterEmail() {
            $this->R_UserEmail = $this->R_UserEmail.'@secretsea.com';
            try {
                $STH = $this->DHB->prepare("SELECT * FROM Users WHERE UserEmail = :user_email");
                $STH->bindParam(':user_email', $this->R_UserEmail);
                $STH->execute();
                
                $UsernameExist = $STH->rowCount();
                if($UsernameExist <= 0) {
                    return false;
                }
                else {
                     return true;
                }
                
                
            }
            catch(PDOException $e) {
                file_put_contents('../lib/PDOErrors.txt', $e->getMessage(), FILE_APPEND);
                return false;
            }
        }

        private function doRegister() {
            $hashedPassword = password_hash($this->R_UserPass1, PASSWORD_BCRYPT, array("cost" => 13));                

            $STH = $this->DHB->prepare("INSERT INTO Users(UserEmail, UserPass, Contacts, OnlineFriends, FriendsYWantTtalkTo) values(:user_email, :user_pass, ';', ';', ';')");
            $STH->bindParam(':user_email', $this->R_UserEmail);
            $STH->bindParam(':user_pass', $hashedPassword);
            $STH->execute(); 

            if (!$STH) {
                return $this->setReturnState('We could not process your order. Please try again later.');
            }

            return $this->setReturnState(null, true);
        }

        private function doLogin() {
            $this->setUserEmail($this->L_UserEmail);
            return $this->setReturnState(null, true);
        }

        private function setUserEmail($email) {
            $_SESSION['login'] = $email;
        }

        private function clearUserEmail() {
            unset($_SESSION['login']); 
            session_destroy();
        }

        private function redirectToIndex() {
            $host  = $_SERVER['HTTP_HOST'];
            $link = "http://".$host.$this->path."/index.php";
            return $link;
        }


        private function RemoveOnlineState() {
            $MyEmail = ';'.$this->getUserEmail().';';

            foreach ($this->LogoutEmails as $person) {
                try {
                    $STH = $this->DHB->prepare("UPDATE `Users` SET `OnlineFriends`=REPLACE(`OnlineFriends`, :my_email, ';') WHERE `UserEmail` = :friend_email");
                    $STH->bindParam(':my_email', $MyEmail);
                    $STH->bindParam(':friend_email', $person);
                    $STH->execute();

                    if (!$STH) {
                      file_put_contents('../PDOErrors.txt', $STH->errorInfo(), FILE_APPEND);
                      return false;
                    }

                } catch (PDOException $e) {
                    file_put_contents('../PDOErrors.txt', $e->getMessage(), FILE_APPEND);
                    return false;
                }
            }

            try {
                $MyEmail = $this->getUserEmail();
                $STH = $this->DHB->prepare("UPDATE `Users` SET `FriendsYWantTtalkTo`= ';' WHERE `UserEmail` = :my_email");
                $STH->bindParam(':my_email', $MyEmail);
                $STH->execute();

                if (!$STH) {
                  file_put_contents('../PDOErrors.txt', $STH->errorInfo(), FILE_APPEND);
                  return false;
                }

            } catch (PDOException $e) {
                file_put_contents('../PDOErrors.txt', $e->getMessage(), FILE_APPEND);
                return false;
            }

            return true;
        } # END OF RemoveOnlineState()

    } # END OF UserAuth CLASS
?>