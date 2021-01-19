 <div class="grey lighten-5">
    <div class="container" id='part-2'>
      <div id='part-2-content'>
          <br>
        <h3 class='black-text center-align'>Urijah Faber in Safehouse</h3>
           <div class="video-container z-depth-4">
              <div class="row">
             
              <div class="col m12 s12" v-for="vid in facebook_videos_main">
                <div  class="fb-video" v-bind:data-href="vid.url" data-show-text="false">
                <div class="fb-xfbml-parse-ignore">
                  
                    </div>

                </div>
              </div>
              
              </div>
          </div>
       </div>
      <br>
        <div class="row">
          <div class="col m6 s12" v-for="vid in facebook_video_trailer">
              <div class="video-container z-depth-4">
                  <div  class="fb-video" v-bind:data-href="vid.url" data-show-text="false">
                <div class="fb-xfbml-parse-ignore">
                  
                    </div>

                </div>
              
            </div>
          </div>
        </div>
       
    </div>

    <br><br>
</div>