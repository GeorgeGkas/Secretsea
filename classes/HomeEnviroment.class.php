<?php
	require_once 'main.class.php';
	require_once 'rot.function.php';

    class HomeEnviroment extends Main {
    	private $UserEmail					=	NULL;
    	private $UserAvatar					=	NULL;
    	private $Contacts					=	NULL;
    	private $Username 					=	NULL;
    	private $FriendsIWantToTalk			=	NULL;
    	private $FriendsReadyToChatWithMe	=	NULL;
    	private $FriendsAvatar				=	NULL;

    	private $path 						=	'/georgegkas';
    	private $filePath					=	NULL;


    	public function prepareEnviromentVariables() {
    		$this->UserEmail = $this->getUserEmail();

    		if ($this->UserEmail == NULL) {
    			return false;
    		}

    		$this->UserAvatar = $this->getUserAvatar();
    		$this->Username = $this->getUsername();
    		$this->Contacts = $this->getContactList();

    		$this->removeJankValues();

    		if ($this->Contacts == NULL) {
    			return false;
    		}

    		$this->FriendsIWantToTalk = $this->getMyOnlineFriendsPreferences();
    		$this->FriendsReadyToChatWithMe = $this->getFriendsOnlineList();
    		$this->FriendsAvatar = $this->getFriendsAvatar();

    		$Info = array(
    			'UserEmail' => $this->UserEmail,
    			'UserAvatar' => $this->UserAvatar, 
    			'Username' => $this->Username,
    			'Contacts' => $this->Contacts, 
    			'FriendsIWantToTalk' => $this->FriendsIWantToTalk, 
    			'FriendsReadyToChatWithMe' => $this->FriendsReadyToChatWithMe,
    			'FriendsAvatar' => $this->FriendsAvatar,
    		);

    		$this->setJSONFileCookieName();

    		$this->writeJSONfile($Info);
    	}


    	private function getUserAvatar() {
    		try {
		        $STH = $this->DHB->prepare("SELECT UserAvatar FROM Users WHERE UserEmail = :user_email");
		        $STH->bindParam(':user_email', $this->UserEmail);
		        $STH->execute();

		        $UserInfo = $STH->fetchAll();

		        if (!$UserInfo) {
		          return false;
		        }
		        

				return $UserInfo[0]['UserAvatar'];


		    } catch (PDOException $e) {
		        file_put_contents('PDOErrors.txt', $e->getMessage(), FILE_APPEND);
		        return false;
		    }
    	}

    	private function getUsername() {
    		try {
		        $STH = $this->DHB->prepare("SELECT UserName FROM Users WHERE UserEmail = :user_email");
		        $STH->bindParam(':user_email', $this->UserEmail);
		        $STH->execute();

		        $username = $STH->fetchAll();

		        if (!$username) {
		          return false;
		        }
		        

				return $username[0]['UserName'];


		    } catch (PDOException $e) {
		        file_put_contents('PDOErrors.txt', $e->getMessage(), FILE_APPEND);
		        return false;
		    }
    	}

    	private function getContactList() {
    		try {
		        $STH = $this->DHB->prepare("SELECT Contacts FROM Users WHERE UserEmail = :user_email");
		        $STH->bindParam(':user_email', $this->UserEmail);
		        $STH->execute();

		        $contacts = $STH->fetchAll();

		        if (!$contacts) {
		          return false;
		        }
		        

				return explode(';', $contacts[0]['Contacts']);


		    } catch (PDOException $e) {
		        file_put_contents('PDOErrors.txt', $e->getMessage(), FILE_APPEND);
		        return false;
		    }
    	}

    	private function getMyOnlineFriendsPreferences() {
    		try {
		        $STH = $this->DHB->prepare("SELECT FriendsYWantTtalkTo FROM Users WHERE UserEmail = :user_email");
		        $STH->bindParam(':user_email', $this->UserEmail);
		        $STH->execute();

		        $Myonlinefriends = $STH->fetchAll();

		        if (!$Myonlinefriends) {
		          return false;
		        }
		        

				return explode(';', $Myonlinefriends[0]['FriendsYWantTtalkTo']);


		    } catch (PDOException $e) {
		        file_put_contents('PDOErrors.txt', $e->getMessage(), FILE_APPEND);
		        return false;
		    }
    	}

    	private function getFriendsOnlineList() {
    		try {
		        $STH = $this->DHB->prepare("SELECT OnlineFriends FROM Users WHERE UserEmail = :user_email");
		        $STH->bindParam(':user_email', $this->UserEmail);
		        $STH->execute();

		        $onlinefriends = $STH->fetchAll();

		        if (!$onlinefriends) {
		          return false;
		        }
		        

				return explode(';', $onlinefriends[0]['OnlineFriends']);


		    } catch (PDOException $e) {
		        file_put_contents('PDOErrors.txt', $e->getMessage(), FILE_APPEND);
		        return false;
		    }
    	}

    	private function removeJankValues() {
    		unset($this->Contacts[0]);
    		$this->Contacts = array_values($this->Contacts);
    	}

    	private function writeJSONfile($INFORMATIONS) {
    		$targetPath = $_SERVER['DOCUMENT_ROOT'].$this->path."/json/HV-".$this->filePath.".json";
    		$fp = fopen($targetPath, 'w');
		    fwrite($fp, json_encode($INFORMATIONS));
		    fclose($fp);
    	}

    	private function setJSONFileCookieName() {
    		$arr = explode("@", $this->UserEmail, 2);
			$name = $arr[0];
    		setcookie('fp', str_rot($name), time() + 90, '/');
    		$this->filePath = str_rot($name);
    	}

    	private function getFriendsAvatar() {
    		$friends_Avatar = array();

    		# IF THE PERSON HAS ADDED YOU IN HIS/HER CONTACTS
		    # THEN SHOW THEIR PROFILE IMAGE TO OUR USER
		    try {
		        foreach ($this->Contacts as $person) {           

		            $STH = $this->DHB->prepare("SELECT Contacts FROM Users WHERE UserEmail = :user_email");
		            $STH->bindParam(':user_email', $person);
		            $STH->execute();

		            # GET CONTACTS FROM OF OUR FRIEND
		            $fetch = $STH->fetchAll();

		            if (!empty($fetch) && is_array($fetch)) {
		                $tmp = explode(';', $fetch[0]['Contacts']);

		                # THE PERSON HAS ADDED OUR EMAIL TO THEIR CONTACTS
		                if (in_array($this->UserEmail, $tmp)) {
		                    $STH = $this->DHB->prepare("SELECT UserAvatar FROM Users WHERE UserEmail = :user_email");
		                    $STH->bindParam(':user_email', $person);
		                    $STH->execute();

		                    $img = $STH->fetchAll();

		                    # GET THE PERSON AVATAR IMAGE
		                    $friends_Avatar[$person] = $img[0]['UserAvatar'];
		                }
		            }
		        }

		        return $friends_Avatar;

		    } catch (PDOException $e) {
		        file_put_contents('PDOErrors.txt', $e->getMessage(), FILE_APPEND);
		       return false;
		    }
    	} # END getFriendsAvatar()

    } # END HomeEnviroment CLASS
?>