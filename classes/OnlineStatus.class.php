<?php 
	require_once 'main.class.php';
	
	class OnlineStatus extends Main {
		private $UserEmail 			=	NULL;
		private $FriendEmail		=	NULL;
		private $UserOnlineStatus	=	NULL;
		
		function __construct() {
            parent::__construct();
            $this->UserEmail = $this->getUserEmail();
        }

        public function getFriendsStatus() {
        	try {
		        $STH = $this->DHB->prepare("SELECT OnlineFriends FROM Users WHERE UserEmail = :user_email");
		        $STH->bindParam(':user_email', $this->UserEmail);
		        $STH->execute();

		        $row = $STH->fetchAll();

		        $friendsOnline = explode(';', $row[0]['OnlineFriends']);
		        return $friendsOnline;

		    } catch (PDOException $e) {
		        file_put_contents('../PDOErrors.txt', $e->getMessage(), FILE_APPEND);
		        $this->setReturnState("Something went wrong. Please Try again later.");
		    }
        }

        public function changeUserStatus($FriendEmail, $Status) {
        	$this->FriendEmail = $FriendEmail;
        	$this->UserOnlineStatus = $Status;

        	try {
		        if ($this->UserOnlineStatus == 'offline') {
		         	if ($this->setOfflineStatus()) {
		         		 return $this->setReturnState("offline", true);
		         	}
		         	else {
		         		 return $this->setReturnState("Something went wrong. Please Try again later.");
		         	}		            
		        }
		        else {
		            if ($this->setOnlineStatus()) {
		         		 return $this->setReturnState("online", true);
		         	}
		         	else {
		         		 return $this->setReturnState("Something went wrong. Please Try again later.");
		         	}	

		        }
		            
		    } catch (PDOException $e) {
		        file_put_contents('../PDOErrors.txt', $e->getMessage(), FILE_APPEND);
		        $this->setReturnState("Something went wrong. Please Try again later.");
		    }
        }

        private function setOfflineStatus() {
		    $e = ';'.$this->FriendEmail.';';
		    $m = ';'.$this->UserEmail.';';
		    try {
		    	$STH1 = $this->DHB->prepare("UPDATE Users SET FriendsYWantTtalkTo = REPLACE(`FriendsYWantTtalkTo`, :email, ';') WHERE UserEmail = :my_email");
			    $STH1->bindParam(':email', $e);
			    $STH1->bindParam(':my_email', $this->UserEmail);
			    $STH1->execute();


			    $STH2 = $this->DHB->prepare("UPDATE Users SET OnlineFriends = REPLACE(`OnlineFriends`, :my_email , ';' ) WHERE UserEmail = :email");
			    $STH2->bindParam(':email', $this->FriendEmail);
			    $STH2->bindParam(':my_email', $m);
			    $STH2->execute();

			    if ($STH1 && $STH2) {
			    	return $this->setReturnState(null, true);
			    }
			    else {
			    	return $this->setReturnState(null);
			    }

		    } catch (PDOException $e) {
		        file_put_contents('../PDOErrors.txt', $e->getMessage(), FILE_APPEND);
		        return $this->setReturnState(null);
		    }
		    
        }

        private function setOnlineStatus() {
		    $e = $this->FriendEmail.';';
		    $m = $this->UserEmail.';';
		    try {
	            $STH1 = $this->DHB->prepare("UPDATE Users SET FriendsYWantTtalkTo = concat(`FriendsYWantTtalkTo`, :email) WHERE UserEmail = :my_email");
	            $STH1->bindParam(':email', $e);
	            $STH1->bindParam(':my_email', $this->UserEmail);
	            $STH1->execute();

	            $STH2 = $this->DHB->prepare("UPDATE Users SET OnlineFriends = concat(`OnlineFriends`, :my_email) WHERE UserEmail = :email");
	            $STH2->bindParam(':email', $this->FriendEmail);
	            $STH2->bindParam(':my_email', $m);
	            $STH2->execute();


			    if ($STH1 && $STH2) {
			    	return $this->setReturnState(null, true);
			    }
			    else {
			    	return $this->setReturnState(null);
			    }

		    } catch (PDOException $e) {
		        file_put_contents('../PDOErrors.txt', $e->getMessage(), FILE_APPEND);
		        return $this->setReturnState(null);
		    }
		    
        }	# END setOnlineStatus()

	}	# END OnlineStatus CLASS

?>