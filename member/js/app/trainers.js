 $(function(){
     $(".button-collapse").sideNav();
      $('body').on('click','#btnDown',function(){
           $('html,body').animate({
            scrollTop: $("#part-1").offset().top},
            'fast');
      });
      
    });
    var trainer_videos = [
      {
        url: 'https://www.facebook.com/safehousemanila/videos/1329285623782568/',
       name: 'Jun Angana' 
     },
    
     {
      url: 'https://www.facebook.com/safehousemanila/videos/1270225933021871/',
      name : 'Zaidi Laruan'
     },
     {
      url: 'https://www.facebook.com/safehousemanila/videos/1251237058254092/',
      name: 'Zaidi Laruan'
     },
     {
      url: 'https://www.facebook.com/safehousemanila/videos/1225054924205639/' ,
      name : 'Zaidi Laruan'
     },
     {
      url: 'https://www.facebook.com/safehousemanila/videos/1223052771072521/' ,
      name : 'Jimmy Angana'
     },
     {
      url: 'https://www.facebook.com/safehousemanila/videos/1196207800423685/' ,
      name : 'Jun Angana'
     } ];
    var trainers = [
      {
        name: '',
        src: 'img/trainers/2.png',
      },
      {
        name: '',
        src: 'img/trainers/6.png',
      },
       {
        name: '',
        src: 'img/trainers/4.png',
      },
       {
        name: '',
        src: 'img/trainers/3.png',
      },
      {
        name: '',
        description: '',
        src: 'img/trainers/1.png',
      },
      {
        name: '',
        src: 'img/trainers/5.png',
      },
      {
        name: '',
        src: 'img/trainers/7.jpg',
      },
      {
        name: '',
        src: 'img/trainers/8.jpg',
      }
    ]
    var vm = new Vue({
      el:'#app',
      data:{
        trainers:trainers,
        trainer_videos:trainer_videos,
        data:{name:'',description:'',src:''}
      },
      mounted: function(){
        var vuecon = this;
           setTimeout(function(){
      //  vuecon.
       // console.log("trigger");
           },2000);
    
      },
      methods:{
        showDetails: function(trainer){
        
        }
      }
    });