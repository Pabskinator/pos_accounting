<div class="grey lighten-5" id='part-3'>
      <div id='part-3-content'>

      <h3 class='center-align'> <br>Reviews</h3>
    
     <div class="carousel carousel-slider center" data-indicators="true">
     <div class="carousel-fixed-item right-align">
     <i class='material-icons circle'>touch_app</i> <span>&nbsp;&nbsp;&nbsp;</span>
    </div>
      <div class="carousel-item  black-text" href="#one!" v-for="review in reviews">
        <div style='height:100%;'>
        <div class='absolute-center' style='height:100%'>

          <div class='review-msg'> 
          <span class='red-text'>
          {{review.message}}  
        </span>
              <br><span class='review-name '><i class='material-icons circle'>person</i> <span class="title">{{ review.name }} </span></span>
          </div>
  
        </div>
        </div>
    </div>
   </div>
    </div>
</div>