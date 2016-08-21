<?php 
	require_once 'main.class.php';

	class chatProcedure extends Main {
		private $UserEmail 		=	NULL;
		private $FriendEmail	=	NULL;
		private $FileName 		=	NULL;
		private $NewMessages	=	NULL;
		private $path			=	'/georgegkas';
		
		function __construct($FriendEmail) {
			parent::__construct();
            $this->UserEmail = $this->getUserEmail();
            $this->FriendEmail = $FriendEmail;
            $this->setDataFileName();
		}

		public function getMessages($LastMsgId) {
			$this->NewMessages = array();
			$loc = '../lib/data/'.$this->FileName;

			if (!file_exists($loc)) {
				array_push($this->NewMessages, "Error");
				return $this->NewMessages;
			}

			$File = file($loc);
			for ($i=0; $i < count($File); $i++) {
		        $msg = explode("<!@#@>", $File[$i]);
		        if ($msg[0] > $LastMsgId) {
		            array_push($this->NewMessages, $msg);
		            $LastMsgId++;
		        }
		    }

		    array_push($this->NewMessages, $LastMsgId);

		    return $this->NewMessages;


		}

		public function checkMessagingStatus() {
			$FriendisOnline = $this->getFriendChatStatus(); # See if the User's Friend is online
			$iWantToChat = $this->getUserChatPreferences(); # See if User has added his Friend for his chat list
			if ($this->checkChatPair($iWantToChat, $FriendisOnline)) {
				return $this->setReturnState(null, true);
			} 
			else {
				return $this->setReturnState(null, false);
			}
		}

		public function createChatDataFile() {
			$File = fopen('../lib/data/'.$this->FileName, "a+");
		    if (!$File) {
		    	return $this->setReturnState('Could not create Chat Data file.');
		    }
		    else {
		    	fclose($File);
		    	return $this->setReturnState($this->FileName, true);
		    }
		}

		public function destroyChatDataFile() {
			if (file_exists("../lib/data/".$this->FileName)) {

		        $file_to_delete = array_map('unlink', glob("../lib/data/".$this->FileName));

		    	if (!$file_to_delete) {
		    		return $this->setReturnState('Could not delete Chat Data file.');
		    	}
		    	else { 
		    		return $this->setReturnState(null, true);
		    	}
		    }
		    return $this->setReturnState(null, true);
		}

		public function sendMessage($Message, $PostTime) {
			$linesOfMessages = $this->countLinesOfMessages();
			$AvatarLink = $this->getUserAvatarLink();
			$write_msg = $linesOfMessages."<!@#@>".$this->UserEmail."<!@#@>".$PostTime."<!@#@>".$Message."<!@#@>". $AvatarLink."\n";
			$File = fopen('../lib/data/'.$this->FileName, "a+");
			$fwrite = fwrite($File, $write_msg);
			if ($write_msg === false ) {
				fclose($File);
		        return $this->setReturnState('Failed to send your message.', true);
		    }
		    else  {
		    	fclose($File);
		    	return $this->setReturnState('MsgSent', true);
		    }
		    
		}

		private function setDataFileName() {
			if (strcmp($this->UserEmail, $this->FriendEmail) < 0) {
		        $this->FileName = $this->UserEmail.'-'.$this->FriendEmail.'.txt';
		    }
		    else {
		        $this->FileName = $this->FriendEmail.'-'.$this->UserEmail.'.txt';
		    }
		}

		private function countLinesOfMessages() {
			$File = fopen('../lib/data/'.$this->FileName, "a+");
			$linecount = 0;
		    while(!feof($File)){
		      $line = fgets($File);
		      $linecount++;
		    }
		    fclose($File);
		    return $linecount;
		}

		private function getUserAvatarLink() {
			$host  = $_SERVER['HTTP_HOST'];
    		$link = "http://".$host.$this->path."/lib/profile_avatars/".$this->UserEmail.'.jpg';
    		return $link;
		}
		private function getFriendChatStatus() {
			$STH = "SELECT OnlineFriends FROM Users WHERE UserEmail = '".$this->UserEmail."'";
		    $OnlineFriends = $this->DHB->query($STH)->fetchAll();
		    $OnlineFriends =  explode(';', $OnlineFriends[0]['OnlineFriends']);
		    return $OnlineFriends;
		}

		private function getUserChatPreferences() {
			$STH = "SELECT FriendsYWantTtalkTo FROM Users WHERE UserEmail = '".$this->UserEmail."'";
		    $IaddThem = $this->DHB->query($STH)->fetchAll();
		    $IaddThem =  explode(';', $IaddThem[0]['FriendsYWantTtalkTo']);
		    return $IaddThem;
		}

		private function checkChatPair($UserList, $OnlineList) {
			if (in_array($this->FriendEmail, $OnlineList) && in_array($this->FriendEmail, $UserList)) {
		    	return true;
		    }
		    else {
		    	return false;
		    }
		}


	}

?>