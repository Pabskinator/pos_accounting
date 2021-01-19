 <div class='below-overlay' id='part-1' >

      <div  id='part-1-content' >
        <img class="responsive-img" src="img/mapdisc.jpg">
      <br>
      <div class="container">
        <h3 class='center-align'>ABOUT THE  FIGHT-SCIENCE ACADEMY</h3>
        <p class='center-align'> <strong>Get in fight shape while learning life-saving skills</strong></p>

      </div>
      <div>
             <div class="slider" id='slide-about'>
                <ul class="slides" >
                  <li v-for="about in about_data">
                    <img  v-bind:src="about.url"  v-bind:alt="about.title">
                    <div class="caption" v-bind:class="about.align">
                      <h3 class='white-text with-text-shadow'>{{about.title}}</h3>
                      <h5 class="red-text">{{about.description}}</h5>
                    </div>
                  </li>
                </ul>
            </div>
          <div style='height:200px;width:100%;padding:20px;'class="valign-wrapper">
            <h5 style='margin: 0 auto;' class='hide-on-small-only'>
               <span class='<?php echo $highlight; ?>'>Safehouse</span> is the Country's first fight-science academy. 
        We exist to instill the science behind each art form. We believe that fitness is secondary to the art of martial world. 
         We are currently the only gym in the Country to have the  <span class='<?php echo $highlight; ?>'>three SEA Games Gold medalists athlete</span> to teach full time at the Fight-Science Academy.
            </h5>
            <p style='margin: 0 auto;' class='hide-on-med-and-up'>
              <strong>
               Safehouse is the Country's first fight-science academy. We aim to put Filipino athletes on the World map. We are currently the only gym in the Country to have the three SEA Games Gold medalists athlete to teach full time at the Fight-Science Academy
            </p>
              </strong>
          </div>
      </div>
      <br>

  <h3 class='center-align'>CLASSES & BENEFITS</h3>
 <p class='center-align'> <strong>Family makes this house a home</strong></p>
 
  <div class="container">
      <img class="responsive-img"  alt="Muay thai training at safehouse" src="img/Muay-thai-cover.jpg">
        <p>
        The gym focuses not just on physical fitness but the makings of a fighter. Believing that everyone is always confronted with a challenge, it offers a unique training program that includes boxing, Muay Thai, wrestling submission, close combat training, Jiujitsu training, high intensity workout and the like designed to train the body and the mind to push, and push harder. 
        Experience the only Pro series 2 Zebra mats in the Philippines – the finest and safest mats used for Mixed Martial Arts. The benefits are not only physical (endurance, increased metabolism, cardio strength, self-defense, weight loss) as it also builds confidence, discipline and most importantly, character. 
       Everyone is engaged in his own fight and overcoming begins with an attitude that constantly rises to the challenge. Are you up for it? Begin your training at Safehouse!
      </p>     
      <a href="classes.php" class="waves-effect waves-teal btn-flat right red-text">View More Classes</a>
  </div>
  <br>
 </div> <!-- part 1 end -->