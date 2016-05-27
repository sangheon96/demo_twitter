<?php

session_start();

date_default_timezone_set('America/Toronto');

$link = mysqli_connect("localhost", "root", "", "twitter");

if(mysqli_connect_errno()) {


    print_r(mysqli_connect_error());
    exit();
}



if (empty($_GET['function'])) {


}

else if ($_GET['function'] == "logout") {

        session_unset();
}

function time_since($since) {
    $chunks = array(
        array(60 * 60 * 24 * 365 , 'year'),
        array(60 * 60 * 24 * 30 , 'month'),
        array(60 * 60 * 24 * 7, 'week'),
        array(60 * 60 * 24 , 'day'),
        array(60 * 60 , 'hour'),
        array(60 , 'min'),
        array(1 , 'sec')
    );

    for ($i = 0, $j = count($chunks); $i < $j; $i++) {
        $seconds = $chunks[$i][0];
        $name = $chunks[$i][1];
        if (($count = floor($since / $seconds)) != 0) {
            break;
        }
    }

    $print = ($count == 1) ? '1 '.$name : "$count {$name}s";
    return $print;
}


function displayTweets($type) {

    global $link;
    
/////////////////////////////////////////////GET TYPE/////////////////////////////////////////////////////////////////////////

    if($type == 'public') {

        $whereClause = "";

    }  else if ($type == 'isFollowing') {
        
        $currentUser = mysqli_real_escape_string($link, $_SESSION['id']);
        
        $query = "SELECT * FROM isFollowing WHERE follower = '{$currentUser}'";
        $result = mysqli_query($link, $query);
        $whereClause = "";
            
        while ($row = mysqli_fetch_assoc($result)) {
            
            if ($whereClause == "") {
                
                $whereClause = " WHERE ";
                
            } else {
                
                $whereClause.= " OR ";
            }
            
            $whereClause.= "userid = ".$row['isFollowing'];
            
            
            
        }
        
    } else if ($type == 'yourtweets') {
        
        $currentUser = mysqli_real_escape_string($link, $_SESSION['id']);
        
        $whereClause = "WHERE userid = '{$currentUser}'";
        
    } else if ($type == 'search') {
        
        $keyword = mysqli_real_escape_string($link, $_GET['keyword']);
        
        echo "<p>Showing results for {$keyword}";
        
        $whereClause = "WHERE tweet LIKE '%{$keyword}%' ";
     
    } else if (is_numeric($type)) {
        
        $id = mysqli_real_escape_string($link, $type);
        
        $userQuery = "SELECT * FROM users WHERE id = {$id} LIMIT 1";

        $userQueryResult = mysqli_query($link, $userQuery);

        $user = mysqli_fetch_assoc($userQueryResult);
        
        $email = mysqli_real_escape_string($link, $user['email']);
        
        echo "<h2>{$email}'s Tweets</h2>";
        
        $whereClause = "WHERE userid = {$id}";
    }

    
    
    
    
    
///////////////////////////////////////////Display Tweets//////////////////////////////////////////////////////////////////////////////    
    
    $query = "SELECT * FROM tweets ".$whereClause." ORDER BY datetime DESC LIMIT 10";

    $result = mysqli_query($link, $query);

    if (mysqli_num_rows($result) == 0) {

        echo "There are no tweets to display";


    } else {

        while($row = mysqli_fetch_assoc($result)) {

            $userQuery = "SELECT * FROM users WHERE id = ". mysqli_real_escape_string($link, $row['userid'])." LIMIT 1";

            $userQueryResult = mysqli_query($link, $userQuery);

            $user = mysqli_fetch_assoc($userQueryResult);

            echo "<div class='tweet'><p><a href='?page=publicprofiles&userid=".$user['id']."'>".$user['email']."</a><span class='time'>".time_since(time() - strtotime($row['datetime']) )." ago </span>: <p>";

            echo "<p>".$row['tweet']."</p>";

            echo "<p><a class='toggleFollow' data-userId='".$row['userid']."'>";
            
            
            $currentUser = mysqli_real_escape_string($link, $_SESSION['id']);
            $followedUser = $row['userid'];
        
            $isFollowingQuery = "SELECT * FROM isFollowing WHERE follower = '{$currentUser}' AND isFollowing = '{$followedUser}' LIMIT 1";
            $isFollowingResult = mysqli_query($link, $isFollowingQuery);
            
            if(mysqli_num_rows($isFollowingResult) > 0) {
            
                echo "unfollow";
                
            } else {
                
                echo "follow";
            }
            
            echo "</a></p></div>";
        }

    }



}


function displaySearch() {
    
    echo '<form class="form">
  <div class="form-group">
    <input type="hidden" name="page" value="search">  
    <input type="text" name="keyword" class="form-control" id="search" placeholder="Search">
  </div>
  <button class="btn btn-primary">Search Tweet</button>
  
</form>';

}


function displayTweetBox() {
    
    if($_SESSION['id'] > 0) {
        
        
        echo '<div id="tweetSuccess" class="alert alert-success">You tweet was posted successfully</div>
        <div id="tweetFail" class="alert alert-danger"></div><div class="form">
  <div class="form-group">
    <textarea type="text" class="form-control" id="tweetContent"></textarea>
  </div>
  <button id="postTweetButton" class="btn btn-primary">Post Tweet</button>
</div>';
    }
}


function displayUsers() {
    
    global $link;
    
    $query = "SELECT * FROM users LIMIT 20";

    $result = mysqli_query($link, $query);

    while($row = mysqli_fetch_assoc($result)) {
        
        if ($_SESSION['id'] == $row['id']) {
            
        } else {
    
        $id = $row['id'];
        
        echo "<p><a href=?page=publicprofiles&userid={$id}>".$row['email']."</a><p>";
        
        }
    }
    
}



?>