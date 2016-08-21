<?php 

	session_start();

    // get the requirements for the file name
    $MyEmail = $_SESSION['login'];
    $FriendEmail = $_POST['friend'];
    $last = $_POST['last'];

    if (strcmp($MyEmail, $FriendEmail) < 0) {
        $FileName = $MyEmail.'-'.$FriendEmail.'.txt';
    }
    else {
        $FileName = $FriendEmail.'-'.$MyEmail.'.txt';
    }


    // the array that holds the logs data
    $messages = array(); 


    $File = file('data/'.$FileName) or die(json_encode(array_push($messages, "Error")));


    /*if (count($File) > 10 ) {
        $slice_buffer = array_slice($File,5);
        $file_save=fopen('data/'.$FileName,"w+");
        fwrite($file_save, $slice_buffer);
        fflush($file_save);
        flock($file_save,LOCK_EX);
        fclose($file_save);
        $last -= 6;
    }*/


    for ($i=0; $i < count($File); $i++) {

        $msg1 = explode("<!@#@>", $File[$i]);
        if ($msg1[0] > $last) {
            array_push($messages, $msg1);
            $last++;
        }
    }


    // return the array
    array_push($messages, $last);
    echo json_encode($messages);
    
    //fclose($File)
?>