<?php
    require_once 'main.class.php';
    require_once 'bcrypt.php';

    /**
    * 
    */
    class updateProfile extends Main {
    	private $UserName	=	NULL;
    	private $UserPass	=	NULL;
    	private $UserEmail	=	NULL;
    	private $UserAvatar	=	NULL;
    	private $path 		=	'/georgegkas';

    	private $ImageFileErrors = array(
    				1 => 'php.ini max file size exceeded', 
                	2 => 'html form max file size exceeded', 
                	3 => 'file upload was only partial', 
                	4 => 'no file was attached'
        		);

    	public function updateProfileInfo($Name, $Pass) {
    		$this->UserName = $Name;
    		$this->UserPass = $Pass;
    		$this->UserEmail = $this->getUserEmail();
    		$Info = array();

    		if ($this->UserPass != NULL) {
    			$Info['Pass'] = $this->doPasswordUpdate();
    		}

    		if ($this->UserName != NULL) {
    			$Info['Username'] = $this->doUsernameUpdate();
    		}

    		return $Info;
    	}

    	public function clearAvatarImage() {
    		$this->UserEmail = $this->getUserEmail();

    		$Info = $this->doAvatarClear();
    		return $Info;
    	}

    	public function setAvatarImage($Avatar) {
    		$this->UserAvatar = $Avatar;
    		$this->UserEmail = $this->getUserEmail();		
    		
    		$isGoodFile = $this->checkImageRequirements();
    		if (!$isGoodFile['State']) {
    			return $isGoodFile;
    		}

    		if ($this->isFileExist()) {
    			$this->deleteExistingImage();
    		}

    		$isUploaded = $this->uploadImageFile();
    		if (!$isUploaded['State']) {
    			return $isUploaded;
    		}

    		$updateDatabase = $this->updateDatabaseAvatarUrl();
    		return $updateDatabase;

    	}

    	private function doPasswordUpdate() {
    		try {
	    		$hash = password_hash($this->UserPass, PASSWORD_BCRYPT, array("cost" => 13)); 

	    		$STH = $this->DHB->prepare("UPDATE `Users` SET `UserPass` = :user_pass WHERE `UserEmail` = :user_email");
	            $STH->bindParam(':user_pass', $hash);
	            $STH->bindParam(':user_email', $this->UserEmail);
	            $STH->execute();

	            if ($STH) {
	                return $this->setReturnState("Your password updated successfully.", true);
	            }
	            else {
	                return $this->setReturnState("We could not update your password. Please try again later.");
	            }
	        }
	         catch(PDOException $e) {
                file_put_contents('../PDOErrors.txt', $e->getMessage(), FILE_APPEND);
                return $this->setReturnState("We could not update your password. Please try again later.");
            }

    	}

    	private function doUsernameUpdate() {
    		try {
	    		$STH = $this->DHB->prepare("UPDATE `Users` SET `UserName` = :user_name WHERE `UserEmail` = :user_email");
	            $STH->bindParam(':user_name', $this->UserName);
	            $STH->bindParam(':user_email', $this->UserEmail);
	            $STH->execute();

	            if ($STH) {
	                return $this->setReturnState("Your username updated successfully.", true);
	            }
	            else {
	                return $this->setReturnState("We could not update your username. Please try again later.");
	            }
	        }
	         catch(PDOException $e) {
                file_put_contents('../PDOErrors.txt', $e->getMessage(), FILE_APPEND);
                return $this->setReturnState("We could not update your username. Please try again later.");
            }
    	}

    	private function doAvatarClear() {
    		try {
		        $STH = $this->DHB->prepare("UPDATE `Users` SET `UserAvatar` =  '' WHERE `UserEmail` = :my_email");
		        $STH->bindParam(':my_email', $this->UserEmail);
		        $STH->execute();

		        if ($STH) {
			    	return $this->setReturnState("Avatar image deleted successfully", true);
			    }
			    else {
			    	return $this->setReturnState("Could not clear avatar image from your profile");
			    }
		        

		    } catch (PDOException $e) {
		        file_put_contents('../PDOErrors.txt', $e->getMessage(), FILE_APPEND);
		        return $this->setReturnState("Could not clear avatar image from your profile");
		    }
    	}
    	

    	private function checkImageRequirements() {
    		if ($this->UserAvatar['error'] != 0) {
    			return $this->setReturnState($ImageFileErrors[$this->UserAvatar['error']]);
    		}

    		if (!is_uploaded_file($this->UserAvatar['tmp_name'])) {
    			return $this->setReturnState('Not an HTTP upload.');
    		}

    		if (!getimagesize($this->UserAvatar['tmp_name'])) {
    			return $this->setReturnState('Only image uploads are allowed.');
    		}

    		if(exif_imagetype($this->UserAvatar['tmp_name']) != IMAGETYPE_JPEG) {
    			return $this->setReturnState('Only .jpg file types are allowed.');
			}

    		return $this->setReturnState(null, true);
    	}

    	private function isFileExist() {
    		$targetPath = $_SERVER['DOCUMENT_ROOT'].$this->path."/lib/profile_avatars/".$this->UserEmail.'.'.pathinfo($this->UserAvatar['name'], PATHINFO_EXTENSION); // Target path where file is to be stored

    		if(file_exists($targetPath)) {
    			return true;
    		}
    		else {
    			return false;
    		}

    	}

    	private function deleteExistingImage() {
    		unlink($_SERVER['DOCUMENT_ROOT'].$this->path."/lib/profile_avatars/".$this->UserEmail.'.'.pathinfo($this->UserAvatar['name'], PATHINFO_EXTENSION));
    	}

    	private function uploadImageFile() {
    		$targetPath = $_SERVER['DOCUMENT_ROOT'].$this->path."/lib/profile_avatars/".$this->UserEmail.'.'.pathinfo($this->UserAvatar['name'], PATHINFO_EXTENSION);
    		$sourcePath = $this->UserAvatar['tmp_name'];

    		$results = move_uploaded_file($sourcePath, $targetPath); 
    		if ($results) {
    			return $this->setReturnState(null, true);
    		}
    		else {
    			return $this->setReturnState('Could not upload your image to our servers.');
    		}
    	}

    	private function updateDatabaseAvatarUrl() {
    		$host  = $_SERVER['HTTP_HOST'];
			$link = "http://".$host.$this->path."/lib/profile_avatars/".$this->UserEmail.'.'.pathinfo($this->UserAvatar['name'], PATHINFO_EXTENSION);

			try {
		        $STH = $this->DHB->prepare("UPDATE `Users` SET `UserAvatar` =  :avatar_link WHERE `UserEmail` = :my_email");
		        $STH->bindParam(':avatar_link', $link);
		        $STH->bindParam(':my_email', $this->UserEmail);
		        $STH->execute();

		        if ($STH) {
			    	return $this->setReturnState('Avatar image uploaded successfully.', true);
			    }
			    else {
			    	return $this->setReturnState('Could not update your image url to our database.');
			    }
		        

		    } catch (PDOException $e) {
		        file_put_contents('../PDOErrors.txt', $e->getMessage(), FILE_APPEND);
		        return $this->setReturnState('Could not update your image url to our database.');
		    }
    	}	# END updateDatabaseAvatarUrl()

    }	# END updateProfile CLASS
?>