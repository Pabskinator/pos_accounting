<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1">
<title>Safehouse</title>
<link rel="stylesheet" href="css/materialize.min.css">
<link rel="stylesheet" href="css/animate.css">
<link href="https://fonts.googleapis.com/icon?family=Material+Icons" rel="stylesheet">
<script src="https://unpkg.com/masonry-layout@4/dist/masonry.pkgd.min.js"></script>
<style>
	[v-cloak] { display: none }
.nmpd-wrapper {display: none;}
.nmpd-target {cursor: pointer;}
.nmpd-grid {position:absolute; left:50px; top:50px; z-index:5000; -khtml-user-select: none; padding:10px; width: initial;}
.nmpd-overlay {z-index:4999;}
input.nmpd-display {text-align: right;}

.btn_num{
	width:50px;
}
.btn_action{
	width:100px;
}
.sep{
	width:50px;
}
#check-out-container{
	width:30%;
	position: fixed;
	z-index: -1;
	right: 0;
	top:40px;
	height: 100%;
	background: #ccc;
	overflow-y: auto;
}
#testnum{
	margin-top:0px !important;
margin-bottom:0px !important;

}

#footer{
	position:fixed;
	z-index: 2;
	height: 40px;
	bottom: 0px;
	width: 70%;
	overflow-y:auto;

}
body{
	overflow-y: hidden;
}
#item-body{
		width:70%;
	position: absolute;
	overflow-y:auto;
	height: 90%;
}
.category-wrap {
   overflow: hidden;
   margin: 10px;
}
.category-box {
   float: left;
   position: relative;
   width: 20%;
   padding-bottom: 20%;

}
.category-boxInner {
   position: absolute;
   left: 10px;
   right: 10px;
   top: 10px;
   bottom: 10px;
   overflow: hidden;
  color: #fff;
}
.category-boxInner img {
   width: 100%;
}
.category-boxInner .category-titleBox {
   position: absolute;
   bottom: 0;
   left: 0;
   right: 0;
   margin-bottom: 0px;
   background: #fff;
   color: #333;
   padding: 10px;
   text-align: center;
   font-weight: bold;
   -webkit-transition: all 0.3s ease-out;
   -moz-transition: all 0.3s ease-out;
   -o-transition: all 0.3s ease-out;
   transition: all 0.3s ease-out;
}

@media only screen and (max-width : 480px) {
   /* Smartphone view: 1 tile */
   .category-box {
      width: 100%;
      padding-bottom: 100%;
   }
}
@media only screen and (max-width : 650px) and (min-width : 481px) {
   /* Tablet view: 2 tiles */
   .category-box {
      width: 50%;
      padding-bottom: 50%;
   }
}
@media only screen and (max-width : 1050px) and (min-width : 651px) {
   /* Small desktop / ipad view: 3 tiles */
   .category-box {
      width: 33.3%;
      padding-bottom: 33.3%;
   }
}
@media only screen and (max-width : 1290px) and (min-width : 1051px) {
   /* Medium desktop: 4 tiles */
   .category-box {
      width: 25%;
      padding-bottom: 25%;
   }
}


</style>
</head>
<body class='no-touch'>

<div id='app'>
	<div id="head">
	<div class="navbar-fixed">
		 <nav>
		    <div class="nav-wrapper green ligthen-2">
		    	<a href="#" data-activates="slide-out" class="button-collapse">
				    		Menu
				 </a>
		    		<ul id="nav-mobile" class="">
			        <li><a href="#" @click="categoryView">Category View</a></li>
			        <li><a href="#" @click="itemView">Item View</a></li>
			       
			      
			     </ul>
		    	<ul id="nav-mobile" class="right">
			        <li><a href="#">Check Out</a></li>
			        <li>
			        	
			        </li>
			        <li>
			        	<a href="#" >
				    		Cart
				    	</a>
			        </li>
			      
			     </ul>
  
		    </div>
		  </nav>
		  </div>
	</div>
	<div id="sidebar">
		<ul id="slide-out" class="side-nav">
			    <li>
			    <div class="userView">
			      <div class="background">
			        <img src="img/1.jpg">
			      </div>
			      <a href="#!user" ><img class="circle" src="img/safehouse1.jpg"></a>
			      <h4 class='text-white'>Safehouse Fight Academy</h4>
			    </div>
			    </li>
			    <li>
			       <a class="waves-effect waves-teal btn-flat" href="index.php">
			     <i class="material-icons left">home</i>
			    Home
			    </a>
			    </li>
			    <li>
			      
			    <a class="waves-effect waves-teal btn-flat"  href="about.php">
			    <i class="material-icons left">list</i>
			    About
			    </a>
			    </li>
			    <li>
			       <a class="waves-effect waves-teal btn-flat">
			    <i class="material-icons left">book</i>
			    Booking
			    </a>
			    </li>
			     <li>
			       <a class="waves-effect waves-teal btn-flat">
			    <i class="material-icons left">room</i>
			    Facilities
			    </a>
			    </li>
			     <li>
			       <a href="trainers.php" class="waves-effect waves-teal btn-flat">
			    <i class="material-icons left">person</i>
			    Trainers
			    </a>
			    </li>
			    
			  </ul>
	</div>
	<div  id="item-body">
	<br>
		<div  v-show="view_type == 2">
			
			<div class="category-box waves-effect"  v-for="item in itemByCateg"   @click="addToCart(item)">
				    <div class="category-boxInner">
				       <img src="img/safehouse1.jpg" >
				      <div class="category-titleBox"> 
				      {{item.item_code}}
						
				      </div>
				    </div>
			</div>
		</div>
		<div class="row" v-show="view_type == 1">
			<div id='category-wrap'>
					<div class="category-box"  v-for="category in categories" @click="showItemByCategory(category.id)">
					    <div class="category-boxInner">
					      <img v-bind:src="category.url" />
					      <div class="category-titleBox">  {{category.name}}</div>
					    </div>
				  	</div>
			</div>
		</div>

	</div>
	<div id="check-out-container" class='white z-depth-2'>
		 <ul class="collection with-header">
		   <li class="collection-header"><h4>Cart</h4></li>
	        <li class="collection-item"><div>Amount in Peso: <input type='text' id="testnum" class='black-text' placeholder="Enter amount"></div></li>
	        <li class="collection-item"><div><strong>Total<a href="#!" class="secondary-content">1.00</a></strong></div></li>
	        <li class="collection-item cart-item" v-for="item_cart in carts" v-bind:data-id="item_cart.item_id"  ><div>
	        	{{item_cart.item_code}}
	        <a href="#!" class="secondary-content">{{item_cart.price * item_cart.qty}}</a></div></li>
	      
	      </ul>
	</div>
	
</div>


<script src='js/jquery.js'></script>
<script src='js/materialize.min.js'></script>
<script src='js/vue.js'></script>
<script src='js/numpad.js'></script>
<script>

 var vm = new Vue({
 	el: '#app',

 	data: {
 		items : [
 				{item_id: 1,item_code: 'Item 1', price:12, img_url:'img/safehouse1.jpg',category_id:1},
 				{item_id: 2,item_code: 'Item 2', price:12, img_url:'img/safehouse2.jpg',category_id:1},
 				{item_id: 3,item_code: 'Item 3', price:12, img_url:'img/safehouse3.jpg',category_id:1},
 				{item_id: 4,item_code: 'Item 4', price:12, img_url:'img/safehouse3.jpg',category_id:1},
 				{item_id: 5,item_code: 'Item 5', price:12, img_url:'img/safehouse3.jpg',category_id:2},
 				{item_id: 6,item_code: 'Item 6', price:12, img_url:'img/safehouse3.jpg',category_id:2},
 				{item_id: 7,item_code: 'Item 7', price:12, img_url:'img/safehouse1.jpg',category_id:2},
 				{item_id: 8,item_code: 'Item 8', price:12, img_url:'img/safehouse2.jpg',category_id:3},
 				{item_id: 9,item_code: 'Item 9', price:12, img_url:'img/safehouse3.jpg',category_id:3},
 				{item_id: 10,item_code: 'Item 10', price:12, img_url:'img/safehouse3.jpg',category_id:3},
 				{item_id: 11,item_code: 'Item 11', price:12, img_url:'img/safehouse3.jpg',category_id:4},
 				{item_id: 12,item_code: 'Item 12', price:12, img_url:'img/safehouse3.jpg',category_id:4},
 		],
 		categories : [
 			{ name : 'Food' ,id: 1,url:'img/category/food.jpg'},
 			{ name : 'Beverage' ,id: 2,url:'img/category/beverages.jpg'},
 			{ name : 'Deserts' ,id: 3,url:'img/category/deserts.jpg'},
 			{ name : 'Phone' ,id: 4,url:'img/category/phone.jpg'},
 			{ name : 'Food' ,id: 5,url:'img/category/food.jpg'},
 			{ name : 'Beverage' ,id: 6,url:'img/category/beverages.jpg'},
 			{ name : 'Deserts' ,id: 7,url:'img/category/deserts.jpg'},
 			{ name : 'Phone' ,id: 8,url:'img/category/phone.jpg'}
 		],
 		carts : [
 			{item_id:1, item_code: 'Item 1', price:12, img_url:'img/safehouse1.jpg',qty:2},
 			{item_id:2,item_code: 'Item 2', price:12, img_url:'img/safehouse1.jpg',qty:3},
 			{item_id:3,item_code: 'Item 3', price:12, img_url:'img/safehouse1.jpg',qty:4},
 			
 		],
 		current_category:1,
 		view_type : 1
 	},
 	computed: {
	  itemByCateg: function () {
	  	var con = this;
	    return this.items.filter(function (item) {
	      return item.category_id == con.current_category;
	    })
	  }
	},
	methods:{
		showItemByCategory : function(id){
			this.current_category = id;
			this.view_type =2;
		},
		categoryView: function(){
			
			this.view_type =1;
		},
		itemView: function(){
			this.view_type =2;
			
		},
		deleteEntry: function(id){
			
			this.carts = this.carts.filter(function(item){
				return item.item_id != id;
			});
		},
		addToCart: function(item){
			var cart = this.carts;
			if(cart.length){
				var hit= false;
				for(var i=0; i<cart.length;i++){
					if(cart[i].item_id == item.item_id){
							cart[i].qty++;
							hit =true;
					}
				}
				if(!hit){
					cart.push({item_id:item.item_id, item_code: item.item_code, price:item.price,qty:1});
				}
			} else {
				cart.push({item_id:item.item_id, item_code: item.item_code, price:item.price,qty:1});
			}
			
		}
	}

 });

	(function(vm){

		$(".button-collapse").sideNav();
		 $('#testnum').numpad();
		 var timer;
		
		$('body').on('mousedown','.cart-item',function(){
			var id = $(this).attr('data-id');
		    timer = setTimeout(function(){
		       vm.deleteEntry(id);
		    },1*1000);
		});
		$('body').on('mouseup mouseleave','.cart-item',function(){
			 clearTimeout(timer);
		});
		
	})(vm);
</script>
</body>
</html>