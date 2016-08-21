<?php
	require_once 'main.class.php';

	/**
	* 
	*/
	class Meeting extends Main {
		private $FriendEmail	=	NULL;
		private $UserEmail		=	NULL;

		private $FirstEmail		=	NULL;
		private $SecondEmail	=	NULL;

		private $Time			=	NULL;

		function __construct($FriendEmail) {
            parent::__construct();
            $this->UserEmail = $this->getUserEmail();
            $this->FriendEmail = $FriendEmail;
			$this->setMeetingId();
        }

		public function getMeeting() {
			return $this->get();
		}

		public function getuncheckedMeetings() {

			try {
				$STH = $this->DHB->prepare("SELECT * FROM Meetings WHERE FirstPerson = :email");
				$STH->bindParam(':email', $this->UserEmail);
				$STH->execute();

				$res = $STH->fetchAll();

				$uncheckedMeetings = array();

				foreach ($res as $user) {
					if ($user['CheckedF'] == 0) {
						array_push($uncheckedMeetings, $user['SecondPerson']);
					}
				}

				$STH = $this->DHB->prepare("SELECT * FROM Meetings WHERE SecondPerson = :email");
				$STH->bindParam(':email', $this->UserEmail);
				$STH->execute();

				$res = $STH->fetchAll();

				foreach ($res as $user) {
					if ($user['CheckedS'] == 0) {
						array_push($uncheckedMeetings, $user['FirstPerson']);
					}
				}


				return $uncheckedMeetings;

			 } catch (PDOException $e) {
		        file_put_contents('../PDOErrors.txt', $e->getMessage(), FILE_APPEND);
		        return $this->setReturnState(null);
		    }
		}
		
		public function setNewDate($Time) {
			$this->Time = $Time;
			return $this->setNew();
		}

		public function updateCurrent($Time) {
			# NOT IMPLEMENTED YET
			$this->Time = $Time;
		}
		
		public function deleteCurrent() {
			return $this->doDelete();
		}

		public function getConfirmState() {
			return $this->checkConfirmState();
		}

		public function confirmDate() {
			return $this->doConfirm();
		}

		private function setMeetingId() {
			if (strcmp($this->UserEmail, $this->FriendEmail) < 0) {
		        $this->FirstEmail = $this->UserEmail;
		        $this->SecondEmail = $this->FriendEmail;
		    }
		    else {
		        $this->FirstEmail = $this->FriendEmail;
		        $this->SecondEmail = $this->UserEmail;
		    }
		}


		private function doDelete() {
			try {
		        $STH = $this->DHB->prepare("UPDATE `Meetings` SET `State` = 0, `PreviousDateTime` = `DateTime`,`CheckedF` = 0,`CheckedS` = 0, `DateTime` = '' WHERE `FirstPerson` = :first AND `SecondPerson` = :second");
		        $STH->bindParam(':first', $this->FirstEmail);
		        $STH->bindParam(':second', $this->SecondEmail);
		        $STH->execute();

		        return $this->checkReturnState($STH);

		    } catch (PDOException $e) {
		        file_put_contents('../PDOErrors.txt', $e->getMessage(), FILE_APPEND);
		        return $this->setReturnState(null);
		    }
		}

		private function checkConfirmState() {
			try {
		        $STH = $this->DHB->prepare("SELECT `Confirmed` FROM `Meetings` WHERE `FirstPerson` = :first AND `SecondPerson` = :second");
		        $STH->bindParam(':first', $this->FirstEmail);
		        $STH->bindParam(':second', $this->SecondEmail);
		        $STH->execute();

		        $ar =  $STH->fetch();
		        $res = $ar['Confirmed'];

		        if ($res == 0) {
		        	return $this->setReturnState("0", true);
		        }
		        else {
		        	return $this->setReturnState("1", true);
		        }
		        

		    } catch (PDOException $e) {
		        file_put_contents('../PDOErrors.txt', $e->getMessage(), FILE_APPEND);
		        return $this->setReturnState(null);
		    }
		}

		private function doConfirm() {
			try {
		        $STH = $this->DHB->prepare("UPDATE Meetings SET `Confirmed` = 1, `CheckedF` = 0,`CheckedS` = 0 WHERE `FirstPerson` = :first AND `SecondPerson` = :second");
		        $STH->bindParam(':first', $this->FirstEmail);
		        $STH->bindParam(':second', $this->SecondEmail);
		        $STH->execute();

		        return $this->checkReturnState($STH);
		   
		    } catch (PDOException $e) {
		        file_put_contents('../PDOErrors.txt', $e->getMessage(), FILE_APPEND);
		        return $this->setReturnState(null);
		    }
		}

		private function setNew() {
			try {
				$STH = $this->DHB->prepare("SELECT * FROM Meetings WHERE `FirstPerson` = :first AND `SecondPerson` = :second");
		        $STH->bindParam(':first', $this->FirstEmail);
		        $STH->bindParam(':second', $this->SecondEmail);
		        $STH->execute();

		        $res = $STH->fetch(PDO::FETCH_ASSOC);

				if ($res) {
					# Meeting informations already registered
					$STH = $this->DHB->prepare("UPDATE `Meetings` SET `State` = 1, `DateTime` = :time, `Creator` = :my_email, `Confirmed` = 0, `State` = 1, `CheckedF` = 0, `CheckedS` = 0 WHERE `FirstPerson` = :first AND `SecondPerson` = :second");
			        $STH->bindParam(':first', $this->FirstEmail);
			        $STH->bindParam(':second', $this->SecondEmail);
			        $STH->bindParam(':time', $this->Time);
			        $STH->bindParam(':my_email', $this->UserEmail);
			        $STH->execute();
				}
				else {
					$STH = $this->DHB->prepare("INSERT INTO Meetings(FirstPerson, SecondPerson, DateTime, Creator) values(:first, :second, :time, :my_email)");
			        $STH->bindParam(':first', $this->FirstEmail);
			        $STH->bindParam(':second', $this->SecondEmail);
			        $STH->bindParam(':time', $this->Time);
			        $STH->bindParam(':my_email', $this->UserEmail);
			        $STH->execute(); 
				}
	

		        return $this->checkReturnState($STH);

		    } catch (PDOException $e) {
		        file_put_contents('../PDOErrors.txt', $e->getMessage(), FILE_APPEND);
		        return $this->setReturnState(null);
		    }
		}

		private function get() {
			try {
		        $STH = $this->DHB->prepare("SELECT * FROM Meetings WHERE `FirstPerson` = :first AND `SecondPerson` = :second");
		        $STH->bindParam(':first', $this->FirstEmail);
		        $STH->bindParam(':second', $this->SecondEmail);
		        $STH->execute();

		        $MeetingInfo = $STH->fetchAll();
		        $results = array();

		        if (!$MeetingInfo) {
		        	$results['MeetingExist'] = false;

		            return $results;
		        }
		        else {

		        	if ($this->FirstEmail == $this->UserEmail) {
		        		$STH = $this->DHB->prepare("UPDATE Meetings SET `CheckedF` = 1 WHERE `FirstPerson` = :first AND `SecondPerson` = :second");
				        $STH->bindParam(':first', $this->FirstEmail);
				        $STH->bindParam(':second', $this->SecondEmail);
				        $STH->execute();

		        	}
		        	else {
		        		$STH = $this->DHB->prepare("UPDATE Meetings SET `CheckedS` = 1 WHERE `FirstPerson` = :first AND `SecondPerson` = :second");
				        $STH->bindParam(':first', $this->FirstEmail);
				        $STH->bindParam(':second', $this->SecondEmail);
				        $STH->execute();
		        	}



		        	if ($MeetingInfo[0]['State'] == 0) {
		        		# Meeting has been canceled
		        		$results['MeetingExist'] = 'canceled';
		        		$results['canceledDate'] = $MeetingInfo[0]['PreviousDateTime'];
		        	}
		        	else {
		        		$results['MeetingExist'] = 'yes';
		        	}
		        	
		        	$results['DateTime'] = $MeetingInfo[0]['DateTime'];

		        	
		        	# $results['Checked'] = true;
		        	$results['Creator'] = $MeetingInfo[0]['Creator'];
		            $results['Confirmed'] = $MeetingInfo[0]['Confirmed'];
		        	$results['friend'] = $this->FriendEmail;

		            
		        }
		        return $results;
		        

		    } catch (PDOException $e) {
		        file_put_contents('../PDOErrors.txt', $e->getMessage(), FILE_APPEND);
		        return $this->setReturnState(null);
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

	}	# END Meeting CLASS
?>