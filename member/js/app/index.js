 $(function(){
      $("#main_nav_button,#fix_nav_button").sideNav();
       
      $('.carousel.carousel-slider').carousel({full_width: true});
      var isMobile = window.matchMedia("only screen and (max-width: 760px)");

      if (!isMobile.matches) {
          
      } else {
           $('#part-1-content,#part-2-content,#part-3-content').css("visibility","visible");
      }
       $('.pushpin-demo-nav').each(function() {
        var $this = $(this);
        var $target = $('#' + $(this).attr('data-target'));
        $this.pushpin({
          top: $target.offset().top,
          bottom: $target.offset().top + $target.outerHeight() - $this.height()
        });
      });

       $('#slide-about').slider({full_width: true,height:500,indicators: false});
     
       $('select').material_select();
       
     /*
       var fireMeEveryTenSec = function(){
        setTimeout(function(){
            $('.main_label_2').css("display","block");
              $('.main_label_2').removeClass("bounceOutUp");
            $('.main_label_2').addClass("animated bounceInUp");
        },2000);
        setTimeout(function(){
            $('.main_label_2').removeClass("bounceInUp");
            $('.main_label_2').addClass("bounceOutUp");
        },5000);
        setTimeout(function(){
            $('.main_label_3').css("display","block");
              $('.main_label_3').removeClass("bounceOutUp");
            $('.main_label_3').addClass("animated bounceInUp");
            
        },6000);
     
        setTimeout(function(){
            $('.main_label_3').removeClass("bounceInUp");
            $('.main_label_3').addClass("bounceOutUp");
           
        },9000);
       };
        fireMeEveryTenSec();
        setInterval(fireMeEveryTenSec,9000);
*/
     
    });

  // this will be fetch to DB
  
      var services_title = [
      {title: 'Muay Thai'},
      {title: 'Wrestling'},
      {title: 'MMA (Mixed Martial Arts)'},
      {title: 'Jiu Jitsu'},
      {title: 'Fight Burst (Conditioning)'},
       {title: 'Boxing'},
       {title: 'Corporate workout'},
        {title: 'Selft defense'},
  ];
  var services = [
      {
        title : 'Muay Thai',
        body: 'This physical and mental discipline includes combat on foot is known as "the art of eight limbs" because it is characterized by the combined use of fists, elbows, knees, shins and feet, being associated with a good physical preparation and clinch techniques that makes a full-contact fighter very efficient. Our ‘thailand-style’ class is 1 and a half hour with 2-3 coaches depending on the number of students.',
        img_url : 'img/safehouse1.jpg'
      
      },
      {
        title: 'Wrestling',
        body: 'Is one of the most explosive combat sport involving grappling type techniques such as clinch fighting, throws and takedowns, takedown defense, joint locks, pins and other grappling holds. It is also one of the best foundations in Mixed Martial Arts because it enhances one’s explosiveness, strength and conditioning and core balance.',
        img_url : 'img/safehouse2.jpg'
      },
      {
        title: 'MMA (Mixed Martial Arts)',
        body: 'Is a full-contact combat sport that allows the use of both striking and grappling techniques, both standing and on the ground, from a variety of other combat sports and martial arts."',
         img_url : 'img/safehouse3.jpg'
      },
      {
        title: 'Jiu Jitsu',
        body: 'BJJ promotes the concept that a smaller, weaker person can successfully defend against a bigger, stronger assailant by using proper technique, leverage, and most notably, taking the fight to the ground, and then applying joint-locks and chokeholds to defeat the other person. This is also a very practical martial art for self defense and for grappling leverage for Mixed Martial Arts',
        img_url : 'img/safehouse4.jpg'
      },
      {
        title: 'Fight Burst (Conditioning)',
        body: 'The newest supplementary conditioning for MMA training designed by Sea Games Champions. Mixing striking drills with burst workout - a method of high intensity training for short durations followed by low intensity down to recovery period. Solves both problems with simple, fast and effective workouts that incorporate different functional Martial Art movement and fat- frying cardio exercises to help you kill two birds with one stone. ',
        img_url : 'img/safehouse1.jpg'
      },
      {
        title: 'Boxing',
        body: 'Boxing is an explosive, anaerobic sport. The act of throwing punches, round after round, while contending with an attacking opponent. It utilizes foot work, head movement, fast, rapid and powerful punches. “Fly like butterfly, sting like a bee –Muhammad Ali” ',
        img_url : 'img/safehouse2.jpg'
      },
      {
        title: 'Self Defense (Close Quarters Combat)',
        body: 'To be determined',
        img_url : 'img/safehouse2.jpg'
      }
    ];
    var reviews = [
      {
        name: "Andres Velasco",
        message: "The best crew ever, keep the good work guys, all the best!"
      },
      {
        name: "JR Ditching",
        message: "The house of Champions! Great coaches and trainers!"
      },
       {
        name: "Monic Toledo",
        message: "Beginner-friendly gym and amazing coaches/fighters."
      },
       {
        name: "Mikki Aguilar",
        message: "My second home... Very kind and great people. I really Love this place. Outstanding training, Outstanding camaraderie... "
      },
      {
        name: "Franz Kevin",
        message: "The Best up and coming MMA and Fitness gym in the Philippines!"
      },
    ];
    /*

        {
        url: 'img/h2.jpg',
        title:'Family makes this house a home',
        description: '',
         align: 'right-align'
      },
       {
        url: 'img/h3.jpg',
        title:'Whether You Think You Can Or Think You Can’t, You’re Right.',
        description: '',
        align: 'left-align'
      }
    */
    var about_data = [
      {
        url: 'img/fight_academy_pic.jpg',
        title:'Defeat what defeats you',
        description: '',
        align: 'center-align'
      }
    ];
    //If You Are Working On Something That You Really Care About, You Don’t Have To Be Pushed. The Vision Pulls You.
    //Steve Jobs
    /*
,
       {
        url: 'img/safehouse1.jpg',
        title:'If You Are Working On Something That You Really Care About, You Don’t Have To Be Pushed. The Vision Pulls You.',
        description: 'Steve Jobs',
         align: 'right-align'
      },
       {
        url: 'img/safehouse3.jpg',
        title:'Whether You Think You Can Or Think You Can’t, You’re Right.',
        description: 'Henry Ford',
        align: 'left-align'
      },

    */
    var facebook_videos_main = [
      { url : 'https://www.facebook.com/safehousemanila/videos/920480817996386/' }
    ];
     var facebook_video_trailer = [
    { url: 'https://www.facebook.com/safehousemanila/videos/1034094933301640/', title:'Trailer 1'},
    { url: 'https://www.facebook.com/safehousemanila/videos/1329285623782568/', title:'Trailer 2'}
  ];
    var vm = new Vue({
      el: '#app',
      data: {

          order_parts: {part_1 : true,part_2:false},
          tshirt: {item_code:'Safehouse TShirt',price:550,item_id:20,qty:1},
          services : services,
          reviews:reviews,
          about_data:about_data,
          facebook_videos_main:facebook_videos_main,
          facebook_video_trailer:facebook_video_trailer,
          member_username:'',
          member_password: '',
          modal: {title:'',body:''},
          videos: [],
          booking_name:'',
          booking_phone:'',
          booking_email:'',
          booking_age:'',
          booking_class:'Muay Thai',
          order_name:'',
         order_phone:'',
         order_email:'',
         order_address:'',
          services_title:services_title,
          cart: []
      },
      mounted: function(){
        var vuecon = this;
        $('.modal').modal();
         $('.carousel .carousel-slider').carousel();
        setTimeout(function(){
          var vid = [
            {src:'https://www.youtube.com/embed/AVAvFn0QPR8'},
            {src:'https://www.youtube.com/embed/AVAvFn0QPR8'},
            {src:'https://www.youtube.com/embed/AVAvFn0QPR8'}
          ];
          vuecon.videos = vid;
        },2000);

         var vuecon = this;
         $('#booking_class').material_select();
         vuecon.booking_class = 'Muay Thai';
         $('#booking_class').change(function(){
            vuecon.booking_class = $(this).val();
         });

      },
      computed: {
        totalOrder : function(){
          if(this.cart.length > 0){
            var total = 0;
            for(var i in this.cart){
                total = parseFloat(total) + (parseFloat(this.cart[i].qty) * parseFloat(this.cart[i].price)) 
            }
            if(!total) total = 0;
            return total.toFixed(2);
          }

          return 0.00;
        }
      },
      methods: {
        showCartModal: function(){
            $('#modal2').modal('open');
        },
        addCart: function(i){
            var total = i.price * i.qty;
            if(this.cart.length > 0){
              var cart = this.cart;
              var is_exist = false;
              for(var j in cart){
                  if(cart[j].item_id == i.item_id){
                    cart[j].qty++;
                    is_exist = true;
                  }
              }
              if(!is_exist){
                cart.push({
                   item_id: i.item_id,
                    item_code: i.item_code,
                    qty: i.qty,
                    price:i.price,
                    total:total
                })
              }
            } else {
               this.cart.push({
                item_id: i.item_id,
                item_code: i.item_code,
                qty: i.qty,
                price:i.price,
                total:total
              });
            }
           
            this.order_parts.part_1 = true;
            this.order_parts.part_2 =  false;
        Materialize.toast("Added on cart",1000,"green lighten-2");
        },
        removeItem: function(i){
            this.cart = this.cart.filter(function( obj ) {
                return obj.item_id !== i.item_id;
            });
        },
        checkOut: function(){
            this.order_parts.part_1 = false;
            this.order_parts.part_2 =  true;
        },
        reviewOrder: function(){
              this.order_parts.part_1 = true;
            this.order_parts.part_2 =  false;
        },  
        showDetails : function(s){
            this.modal.title = s.title;
            this.modal.body = s.body;
            this.modal.src = s.img_url;
            $('#modal1').modal('open');
        },
        checkOutFinal: function(){
           
           var vuecon = this;
           var cart = vuecon.cart;

            var name = vuecon.order_name;
            var phone = vuecon.order_phone;
            var email = vuecon.order_email;
            var address = vuecon.order_address;

            if(name && phone && email && address && cart.length > 0){
              // valid
            $.ajax({
              url:'service/service.php',
              type:'POST',
              data: {functionName:'sendOrder',name:name,phone:phone,email:email,address:address, cart: JSON.stringify(cart)},
              success: function(data){
                  if(data == '1'){
                     Materialize.toast("You order was successfully placed. Thank you.",4000,"green lighten-2");
                    vuecon.order_name ='';
                     vuecon.order_phone ='';
                      vuecon.order_email ='';
                       vuecon.order_address ='';
                       vuecon.cart = [];
                       $('#modal2').modal('close')
                  } else {
                     Materialize.toast("Invalid Data",2000,"red lighten-2");
                  }
              },
              error:function(){
                  
              }
              });
            } else {
               Materialize.toast("Invalid Order Request",2000,"red lighten-2");
            }

        },
        showSignInModal: function(){
            $('#modal1').modal('open');
        },
        loginMember: function(){
          if(this.member_username && this.member_password){
              $.ajax({
              url:'service/service.php',
              type:'POST',
              data: {functionName:'login',username:this.member_username, password:this.member_password},
              success: function(data){
                  if(data == '1'){
                    location.href='members.php';

                  } else {
                     Materialize.toast("Invalid Credentials",2000,"red lighten-2");
                  }
              },
              error:function(){
                  
              }
              });
          } else {
            Materialize.toast("Please complete the form.",2000,"red lighten-2");
          }
        },
        bookSLide: function(){
          
           $('html,body').animate({
          scrollTop: $("#part-5a").offset().top},
          'slow');
        },
        bookNow: function(e){
          
              e.preventDefault();
              if(this.booking_name && this.booking_phone && this.booking_email && this.booking_age ){
                var vuecon = this;
             $.ajax({
              url:'service/service.php',
              type:'POST',
              data: {functionName:'sendBooking',class_name:vuecon.booking_class,name:vuecon.booking_name,phone:vuecon.booking_phone,email:vuecon.booking_email,age:vuecon.booking_age},
              success: function(data){
                  if(data == '1'){
                     Materialize.toast("Thank you for choosing us. We'll contact you as soon as we see the message.",4000,"green lighten-2");
                    vuecon.booking_name ='';
                     vuecon.booking_phone ='';
                      vuecon.booking_email ='';
                       vuecon.booking_age ='';
                  } else {
                     Materialize.toast("Invalid Data",2000,"red lighten-2");
                  }
              },
              error:function(){
                  
              }
              });
              } else {
                   Materialize.toast("Please complete the form.",2000,"red lighten-2");
              }
        }
      }
    });