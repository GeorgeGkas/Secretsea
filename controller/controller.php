<?php 
	session_start();
	require_once '../classes/userAuth.class.php';
	require_once '../classes/updateProfile.class.php';
	require_once '../classes/Meeting.class.php';
	require_once '../classes/Friendships.class.php';
	require_once '../classes/OnlineStatus.class.php';
	require_once '../classes/chatProcedure.class.php';


	$whatProcedure = $_POST['f']; # f stands for the desirable procedure to execute 

	switch ($whatProcedure) {
		case 'a':
			# signIn
			$USER = new userAuth();
			$login = $USER->Login($_POST['Lemail'], $_POST['LPass']);
			if ($login['State'] == true) {
            	echo "yes";
	        }
	        else {
	            echo $login['Msg'];
	        }

			break;

		case 'b':
			# signUp
			$USER = new userAuth();
			$register = $USER->Register($_POST['Remail'], $_POST['RPass1'], $_POST['RPass2'], $_POST['g-recaptcha-response']);
	        if ($register['State'] == true) {
	             echo 'We have register your email to our databases.';
	        }
	        else {
	            echo $register['Msg'];
	        }

			break;


		case 'c':
			# signOut
			$USER = new userAuth();
			$logout = $USER->Logout($_POST['emails']);
	        echo $logout['Msg'];

			break;

		case 'd':
			# Update Profile Info
			$USER = new updateProfile();
			$update_info = $USER->updateProfileInfo($_POST['username'], $_POST['pass']);
			echo json_encode($update_info);

			break;

		case 'e':
			# Clear Avatar Image
			$USER = new updateProfile();
			$clearImg = $USER->clearAvatarImage();
			echo $clearImg['Msg'];

			break;

		case 'f':
			# Set Avatar Image
			$USER = new updateProfile();
			$Avatar = $_FILES['image'];
			$setImg = $USER->setAvatarImage($_FILES['image']);
			echo $setImg['Msg'];

			break;

		case 'g':
			# See if a meeting with person $_POST['FriendEmail'] exist
			$Meeting = new Meeting($_POST['FriendEmail']);
			$meeting = $Meeting->getMeeting();
			echo json_encode($meeting);
			break;

		case 'h':
			# Set new meeting with person $_POST['FriendEmail']
			$Meeting = new Meeting($_POST['FriendEmail']);
			$meeting = $Meeting->setNewDate($_POST['time']);
			if ($meeting['State'] == true) {
				echo "done";
			}
			else {
				echo "bad";
			}
			
			break;

		case 'i':
			# Update current meeting with person $_POST['FriendEmail']
			$Meeting = new Meeting($_POST['FriendEmail']);
			# Update Method wasn't implemented in this state
			$meeting = $Meeting->updateCurrent($_POST['time']);
			if ($meeting['State'] == true) {
				echo "done";
			}
			else {
				echo "bad";
			}
			
			break;

		case 'j':
			# Delete existing meeting with person $_POST['FriendEmail']
			$Meeting = new Meeting($_POST['FriendEmail']);
			$meeting = $Meeting->deleteCurrent();
			if ($meeting['State'] == true) {
				echo "done";
			}
			else {
				echo "bad";
			}

			break;

		case 'k':
			# See if the other person confirmed the meeting date
			$Meeting = new Meeting($_POST['FriendEmail']);
			$meeting = $Meeting->getConfirmState();
			if ($meeting['Msg'] == "1") {
				echo "1";
			}
			else {
				echo "0";
			}

			
			break;

		case 'l':
			# If the meeting was created from the other person
			# we can confirm it
			$Meeting = new Meeting($_POST['FriendEmail']);
			$meeting = $Meeting->confirmDate();
			if ($meeting['State'] == true) {
				echo "done";
			}
			else {
				echo "bad";
			}

			break;

		case 'm':
			# Add the passed $_POST['FriendEmail'] in your contacts
			$Friendship = new Friendships($_POST['FriendEmail']);
			$friend = $Friendship->addFriend();
			if ($friend['State'] == true) {
				$returnRes = array( 'found' => true, 'msg' => "User added ".$_POST['FriendEmail']." to your frined list.");
               	echo json_encode($returnRes);
			}
			else {
				$returnRes = array( 'found' => false, 'msg' => "Error adding person to your record.");
                echo json_encode($returnRes);
			}

			break;


		case 'n':
			# Delete the passed $_POST['FriendEmail'] from your contacts
			$Friendship = new Friendships($_POST['FriendEmail']);
			$friend = $Friendship->removeFriend();
			if ($friend['State'] == true) {
				echo "User ".$_POST['FriendEmail']." removed from your contacts.";
			}
			else {
				echo "Could not remove person ".$_POST['FriendEmail'];
			}

			break;

		case 'o':
			# Delete the passed $_POST['FriendEmail'] from your contacts
			$Friendship = new Friendships($_POST['FriendEmail']);
			$friend = $Friendship->searchEmail();
			echo json_encode($friend);

			break;

		case 'p':
			# Get Friend Username if exists
			# else return the email
			$Friendship = new Friendships($_POST['FriendEmail']);
			$friend = $Friendship->getUsername();
			echo $friend;

			break;

		case 'q':
			# Change User Online Status (offline/online)
			$OnlineStatus = new OnlineStatus();
			$status = $OnlineStatus->changeUserStatus($_POST['FriendEmail'], $_POST['UserStatus']);
			echo $status['Msg'];

			break;

		case 'r':
			# Get list of all User's friends who are online
			$OnlineStatus = new OnlineStatus();
			$status = $OnlineStatus->getFriendsStatus();
			echo json_encode($status);

			break;

		case 's':
			# Create Chat Message Data File
			$Chat = new chatProcedure($_POST['FriendEmail']);
			$file = $Chat->createChatDataFile();
			echo json_encode($file);

			break;

		case 't':
			# Delete Chat Message Data File
			$Chat = new chatProcedure($_POST['FriendEmail']);
			$file = $Chat->destroyChatDataFile();
			echo json_encode($file);

			break;

		case 'u':
			# Push Message to Data File
			$Chat = new chatProcedure($_POST['FriendEmail']);
			$Msg = $Chat->sendMessage($_POST['Message'], $_POST['PostTime']);
			echo json_encode($Msg);

			break;
		
		case 'v':
			# Check if the other person is keep chatting or has left the chat room
			$Chat = new chatProcedure($_POST['FriendEmail']);
			$MsgStatus = $Chat->checkMessagingStatus();
			echo json_encode($MsgStatus);

			break;

		
		case 'w':
			# Get new messages from Data File
			$Chat = new chatProcedure($_POST['FriendEmail']);
			$NewMsg = $Chat->getMessages($_POST['LastMsgId']);
			echo json_encode($NewMsg);

			break;

		case 'x':
			# Get new messages from Data File
			$Meeting = new Meeting(NULL);
			$NewM = $Meeting->getuncheckedMeetings();
			echo json_encode($NewM);

			break;
		

		default:
			break;
	}

?>