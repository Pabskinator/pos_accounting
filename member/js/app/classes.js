 var services_title = [
      {title: 'Muay Thai'},
      {title: 'Wrestling'},
      {title: 'MMA (Mixed Martial Arts)'},
      {title: 'Jiu Jitsu'},
      {title: 'Fight Burst (Conditioning)'},
       {title: 'Boxing'},
       {title: 'Corporate workout'},
        {title: 'Self defense'},
        {title: 'Kids class (bully-proof)'},
  ];

 var services = [
      {
        title : 'Muay Thai',
        body: 'Our ‘thailand-style’ class will be headed by a Sea Games Gold Medalist, Coach Zaidi Laruan. This physical and mental discipline includes combat on foot is known as "the art of eight limbs" because it is characterized by the combined use of fists, elbows, knees, shins and feet, being associated with a good physical preparation and clinch techniques that makes a full-contact fighter very efficient.',
        img_url : 'img/Muay-thai-cover.jpg'
      
      },
      {
        title: 'Wrestling',
        body: 'Safehouse is the only Academy in the Philippines to have both full time coaches who are Sea Games Gold medalists! Our wrestling program is a combination of Freestyle and Greco-roman wrestling headed by the Two Time Freestyle Wrestling SEA Games Gold medalist, Coach Jimmy Angana and his brother Coach Jun Angana who is also a Two Time SEA Games Gold medalist in Greco-roman Wrestling.',
        img_url : 'img/wrestling_cover.jpg'
      },
      {
        title: 'MMA (Mixed Martial Arts)',
        body: 'Is a full-contact combat sport that allows the use of both striking and grappling techniques, both standing and on the ground, from a variety of other combat sports and martial arts."',
         img_url : 'img/mma_cover.jpg'
      },
      {
        title: 'Jiu Jitsu',
        body: 'BJJ promotes the concept that a smaller, weaker person can successfully defend against a bigger, stronger assailant by using proper technique, leverage, and most notably, taking the fight to the ground, and then applying joint-locks and chokeholds to defeat the other person. This is also a very practical martial art for self defense and for grappling leverage for Mixed Martial Arts',
        img_url : 'img/safehouse4.jpg'
      },
      {
        title: 'Fight Burst (Conditioning)',
        body: 'The newest supplementary conditioning for MMA training designed by Sea Games Champions. Mixing striking drills with burst workout - a method of high intensity training for short durations followed by low intensity down to recovery period. Solves both problems with simple, fast and effective workouts that incorporate different functional Martial Art movement and fat-frying cardio exercises to help you kill two birds with one stone. ',
        img_url : 'img/fight_burst_cover.jpg'
      },
      {
        title: 'Boxing',
        body: 'Boxing is an explosive, anaerobic sport. The act of throwing punches, round after round, while contending with an attacking opponent. It utilizes foot work, head movement, fast, rapid and powerful punches. “Fly like butterfly, sting like a bee –Muhammad Ali” ',
        img_url : 'img/boxing_cover.jpg'
      },
      {
        title: 'Corporate workout',
        body: 'We believe that martial arts is not just a way to get FIT physically, as it also help balance lifestyle, reduce stress and most importantly, character. Our goal is not just to promote an active lifestyle but also teach the skills that can develop more self-confidence in themselves and their sur-roundings. Let martial arts be a tool to help your employees achieve greater focus and more mental strength as they battle out the daily challenges in life. ',
        img_url : 'img/1.jpg'
      }
      ,
      {
        title: 'Self defense',
        body: 'Self-defense classes will give you the ability to protect yourself and overcome an attacker. Knowing how to defend yourself can help you feel less anxious in public, or fearful when walking alone in the night. Learn how to defend not just yourself but also protect your loved ones. ',
        img_url : 'img/self_defense_cover.jpg'
      }
       ,
      {
        title: 'Kids class (bully-proof)',
        body: 'The class will be focused on Muay-thai, kids will learn the art of 8 limbs. Muay-thai kids class will be headed by SEA games gold medalist, Kru Z. The module consists of learning the art of 8 limbs (striking techniques) and fostering the right values. Because kids deserve a healthy and fun learning experience! The benefits are not only physical, as it also builds self-confidence, discipline and most importantly, character.',
        img_url : 'img/bullyproof.jpg'
      }
    ];

    var vm = new Vue({
        el:'#app',
        data:{
          services:services ,
          services_title:services_title,
          booking_name:'',
          booking_phone:'',
          booking_email:'',
          booking_age:'',
          booking_class:'Muay Thai'
        },
        mounted: function(){
          var vuecon = this;
         $('#booking_class').material_select();
          vuecon.booking_class = 'Muay Thai'
         $('#booking_class').change(function(){
            vuecon.booking_class = $(this).val();
         });

        },
        methods: {
           bookNowScroll: function(s){
            this.booking_class = s.title;
              $('#booking_class').val(s.title);
               $('#booking_class').material_select();
              this.booking_class = s.title;
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
    $(function(){
     $(".button-collapse").sideNav();
      $('body').on('click','#btnDown',function(){
           $('html,body').animate({
            scrollTop: $("#part-1").offset().top},
            'slow');
      });
       
      
    });