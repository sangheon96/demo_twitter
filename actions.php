<?php
    include("functions.php");
    
    global $link;

    if($_GET['action'] == "loginSignUp") {
        
        $error = "";
        
        if(!$_POST['email']) {
            
            echo "An email address is required.";
            
        } else if (!$_POST['password']) {
            
            $error = "Password is required";
            
        } else if (filter_var($_POST['email'], FILTER_VALIDATE_EMAIL) === false) {
  
            $error = "Please enter a valid email address.";
        }   
        
        
        if ($error != "") {
            echo $error;
            exit();
        
            
        } 
        
        $email = mysqli_real_escape_string($link, $_POST['email']);
        $password = mysqli_real_escape_string($link, $_POST['password']);
        
        
        if ($_POST['loginActive'] == '0') {
            
            
            
            $query = "SELECT * FROM users WHERE email = '{$email}' LIMIT 1";
            $result = mysqli_query($link, $query);
            
            if(mysqli_num_rows($result) > 0) {
                
                
                $error = "That email address is already taken.";
            
            
            } else {
            
            $query = "INSERT INTO users(email, password) VALUES ('{$email}', '{$password}')";
            
                if (mysqli_query($link, $query)) {
                    
                    $_SESSION['id'] = mysqli_insert_id($link);
                    
                    $query = "UPDATE users SET password = '". md5(md5($_SESSION['id']).$_POST['password']) ."' WHERE id = ".$_SESSION['id']." LIMIT 1";
                    mysqli_query($link, $query);
                    
                    echo 1;
                    

                
                
                } else {
                
                    $error = "Couldn't create user - please try again later";
                }
            }
        
        } else {
            
            $query = "SELECT * FROM users WHERE email = '{$email}' LIMIT 1";
            $result = mysqli_query($link, $query);
            $row = mysqli_fetch_assoc($result);
                
            if ($row['password'] == md5(md5($row['id']).$_POST['password'])) {

                
                echo 1;
                
                $_SESSION['id'] = $row['id'];
                
            } else {

                $error = "Could not find that username/password combination. Please try again";
            }
                
            
            
            
        }
        
        if ($error != "") {
            echo $error;
            exit();
        
        } 
        
        
    }



    if ($_GET['action'] == 'toggleFollow') {
        
        $currentUser = mysqli_real_escape_string($link, $_SESSION['id']);
        $followedUser = mysqli_real_escape_string($link, $_POST['userId']);
        
        
        
        
        $query = "SELECT * FROM isFollowing WHERE follower = '{$currentUser}' AND isFollowing = '{$followedUser}' LIMIT 1";
        $result = mysqli_query($link, $query);
            
        if(mysqli_num_rows($result) > 0) {
            
            $row = mysqli_fetch_assoc($result);
            
            $unfollowUser = mysqli_real_escape_string($link, $row['id']);
            
            
            mysqli_query($link, "DELETE FROM isFollowing WHERE id = '{$unfollowUser}' LIMIT 1");
            
            echo "1";
                
                
                
        } else {
            
             mysqli_query($link, "INSERT INTO isFollowing(follower, isFollowing) VALUES (
             {$currentUser}, {$followedUser})");
            
            echo "2";
            
        }
        
        
    }


    if ($_GET['action'] == 'postTweet') {
        
        
        if ($_POST['tweetContent'] == "") {
                    
                    echo "Your tweet is empty!";
                    
        } else if (strlen($_POST['tweetContent']) > 140) {
            
            echo "Your tweet is too long!";
            
        } else {
            
            $currentUser = mysqli_real_escape_string($link, $_SESSION['id']);
            $content = mysqli_real_escape_string($link, $_POST['tweetContent']);
            
            mysqli_query($link, "INSERT INTO tweets(tweet, userid, datetime) VALUES ('{$content}',
             {$currentUser}, NOW())");
            
            echo "1";
            
            
        }
        
    }
        
?>