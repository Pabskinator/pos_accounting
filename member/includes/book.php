
<div  id='part-5a'>
      <div id='part-5-content'>
   
     
        <div class="row" id='part-5'>
            <div class="col m3"></div>
          <div class="col m6"  id='main-book-con'>
          <div>
            <div class="row" >
             
             <h4></h4>
            <form  >
                 <h3  class='center-align white-text'>Are you up for it?</h4>
                <p  class='center-align white-text'>Begin your training at Safehouse!</p>
            <div class="input-field col s12">
                <i class="material-icons prefix white-text">book</i>
                <select name="booking_class" id="booking_class" v-model='booking_class' class="white-text" required>
                    <option v-for="st in services_title" v-bind:value="st.title">{{st.title}}</option>
                </select>
                <label for="booking_class">Class</label>
              </div>
              <div class="input-field col s12">
                <i class="material-icons prefix white-text">account_circle</i>
                <input id="booking_name" v-model='booking_name' type="text" class="white-text" required>
                <label for="booking_name">Name</label>
              </div>
              <div class="input-field col s12">
                <i class="material-icons prefix white-text">phone</i>
                <input id="booking_phone" v-model='booking_phone' type="tel" class="white-text" required>
                <label for="booking_phone">Contact Number</label>
              </div>
              <div class="input-field col s12">
                <i class="material-icons prefix white-text">mail</i>
                <input id="booking_email" v-model='booking_email' type="email" class="white-text" required>
                <label for="booking_email">Email</label>
              </div>
            
              <div class="input-field col s12">
                  <i class="material-icons prefix white-text">view_agenda</i>
                <input id="booking_age" v-model='booking_age' type="number" class="white-text" required>
                <label for="booking_age">Age</label>
              </div>
    
              <div class="input-field col s12 center-align ">
                 <button class="waves-effect waves-light grey btn" @click='bookNow($event)'>Book Now</button>
              </div>
            </form>
       
                </div>
          </div>
        </div>
        <div class="col m3"></div>

      </div>
</div>
</div>