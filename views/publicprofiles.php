<div class="container mainContainer">
    <div class="row">
      <div class="col-md-8">
         
<?php if (empty($_GET['userid']) ) {?>
         
          <h2>Active Users</h2>
      
          <?php displayUsers(); ?>
          
          
        <?php  } else { ?>
        
        <?php displayTweets($_GET['userid']); ?> 
         
        <?php } ?>
      
      </div>
      <div class="col-md-4">
       <?php displaySearch(); ?>
       
       <hr>
        
        <?php displayTweetBox(); ?>
       </div>
    </div>
</div>