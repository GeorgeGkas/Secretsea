<?php
	require_once 'main.class.php';
	
	class Friendships extends Main {
		private $UserEmail 		=	NULL;
		private $FriendEmail	=	NULL;
		private $MembersEmail	=	NULL;

		function __construct($FriendEmail) {
            parent::__construct();
            $this->UserEmail = $this->getUserEmail();
            $this->FriendEmail = $FriendEmail;
        }

        public function searchEmail() {
        	$this->MembersEmail = array();

        	$this->getMembersEmail();

        	if (in_array($this->FriendEmail, $this->MembersEmail)) {
        		$FriendUsername = $this->getFriendUsername();
        		$returnRes = array( 'found' => $FriendUsername, 'msg' => "We found results");
      			return $returnRes;
        	}
        	else {
        		$returnRes =  array( 'found' => null, 'msg' => "We couldn't find results");
        		return $returnRes;
        	}
        }

        public function addFriend() {
        	$this->MembersEmail = array();

        	$this->getMembersEmail();

        	if (in_array($this->FriendEmail, $this->MembersEmail)) {
        		return $this->addContact();
        	}
        	else {
        		return $this->setReturnState(null, false);
        	}

        }

        public function removeFriend() {
        	$this->FriendEmail = ';'.$this->FriendEmail.';';
        	return $this->deleteContact();
        }

        public function getUsername() {
        	$FriendUsername = $this->getFriendUsername();
        	if ($FriendUsername == NULL) {
        		return $this->FriendEmail;
        	}
        	else {
        		return $this->getFriendUsername();
        	}
        	
        }

        private function getMembersEmail() {
        	$STH = "SELECT UserEmail FROM Users";
        	$this->MembersEmail = $this->DHB->query($STH)->fetchAll(PDO::FETCH_COLUMN);
        }

        private function addContact() {
        	try {
		        $STH = $this->DHB->prepare("UPDATE `Users` SET `Contacts` = concat(`Contacts`, :email, ';') WHERE `UserEmail` = :UserEmail");
		        $STH->bindParam(':email', $this->FriendEmail);
		        $STH->bindParam(':UserEmail', $this->UserEmail);
		        $STH->execute();

		        return $this->checkReturnState($STH);
		      
		    } catch (PDOException $e) {
		        file_put_contents('../PDOErrors.txt', $e->getMessage(), FILE_APPEND);
		        return $this->setReturnState(null, false);
		    }
        }

        private function deleteContact() {
        	try {
		        $STH = $this->DHB->prepare("UPDATE `Users` SET `Contacts` = REPLACE(`Contacts`, :email, ';') WHERE `UserEmail` = :UserEmail");
		        $STH->bindParam(':email', $this->FriendEmail);
		        $STH->bindParam(':UserEmail', $this->UserEmail);
		        $STH->execute();

		        return $this->checkReturnState($STH);
		        

		    } catch (PDOException $e) {
		        file_put_contents('../PDOErrors.txt', $e->getMessage(), FILE_APPEND);
		        return $this->setReturnState(null, false);
		    }
        }

        private function getFriendUsername() {
        	try {
		        $STH = $this->DHB->prepare("SELECT UserName FROM Users WHERE UserEmail = :email");
		        $STH->bindParam(':email', $this->FriendEmail);
		        $STH->execute();

		        $Username = $STH->fetchAll();
		        return $Username[0]['UserName'];
		        

		    } catch (PDOException $e) {
		        file_put_contents('../PDOErrors.txt', $e->getMessage(), FILE_APPEND);
		        return $this->setReturnState(null, false);
		    }
        }

        private function checkReturnState($STH) {
			if ($STH) {
		        return $this->setReturnState(null, true);
		    }
		    else {
		    	return $this->setReturnState(null);
		    }
		}	# END checkReturnState()
		
	}	# END Friendships CLASS

?>