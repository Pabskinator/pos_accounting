<?php 
   $q= "Select * from uploads where tags ='fow' order by created desc limit 1";
    $result = $mysqli->query($q);
    $num_rows = $result->num_rows;

    if($result->num_rows > 0){
        $row = $result->fetch_array(MYSQLI_ASSOC);
?>
<br>
<div class='black'>
<br>
<div class="container">
<h3 class='center-align white-text'>Fighter of the Month</h3>
<div class="row">
  <div class="row">
<div class="col m3">  </div>
        <div class="col s12 m6">
          <div class="card">
            <div class="card-image">
             <img style='max-height:450px;' class="responsive-img" src="<?php echo  "http://safehouse.apollosystems.ph/uploads/".$row['filename']; ?>">
           
            </div>
            <div class="card-content">
              <p><strong><?php echo $row['title']; ?></strong></p>
            

            </div>
            <div class="card-action">
             <p><?php echo  $row['description']; ?></p>
            </div>
          </div>
        </div>
      </div>
      <div class="col m3">  </div>
</div>
</div>
</div>
<?php } ?>