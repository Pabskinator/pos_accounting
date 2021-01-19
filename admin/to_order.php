<?php
	// $user have all the properties and method of the current user

	require_once '../includes/admin/page_head2.php';
	//if(!$user->hasPermission('createorder')) {
		// redirect to denied page
	//	Redirect::to(1);
	//}

?>


	<!-- Page content -->
	<div id="page-content-wrapper">

	<!-- Keep all page content within the page-content inset div! -->
	<div class="page-content inset">
		<div class="content-header">
			<h1>
				<span id="menu-toggle" class='glyphicon glyphicon-list'></span> <span class=''>CRITICAL LEVEL</span></h1>
		</div>
		<div id="orderholder">

		</div>
		<?php
			// get flash message if add or edited successfully
			if(Session::exists('flash')) {
				echo "<br/><div class='alert alert-info' style='width:90%;margin:0 auto'>" . Session::flash('flash') . "</div>";
			}
		?>
		<div class="panel panel-primary">
			<!-- Default panel contents -->
			<div class="panel-heading">Re order</div>
			<div class="panel-body">
				<div class="row">
					<div class="col-md-4">
						<div class="input-group">
							<span class="input-group-addon"><span class='glyphicon glyphicon-search'></span></span>
							<input type="text" id="searchSales" class='form-control' placeholder='Search..' />
						</div>
					</div>
				</div>
				<div class="alert alert-info">UNDER CONSTRUCTION</div>
				<input type="hidden" id="hiddenpage" />
				<div id="holder"></div>
			</div>

		</div>
		<div class="modal fade" id="myModal" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
			<div class="modal-dialog" style=''>
				<div class="modal-content">
					<div class="modal-header">
						<button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
						<h4 class="modal-title" id='mtitle'></h4>
					</div>
					<div class="modal-body" id='mbody'>

					</div>
					<div class="modal-footer">
						<button class='btn btn-default finalizeorder' ><span class='glyphicon glyphicon-floppy-save'></span> Finalize Order</button>
					</div>
				</div>
				<!-- /.modal-content -->
			</div>
			<!-- /.modal-dialog -->
		</div><!-- /.modal -->
	</div>
	<!-- end page content wrapper-->


	<script>
		$(function() {

		//	getPage(0, '');

			$('body').on('click','.btnAdd',function(){
				var row = $(this).parents('tr');
				var branch_id = row.attr('data-branch_id');

				var to_branch = row.attr('data-to_branch');
				var is_sup = row.attr('data-is_sup');
				var assemble = row.attr('data-assemblelbl');
				var item_id = row.attr('data-item_id');
				var qty = row.children().eq(5).find('input').val();
				var rowid = row.attr('id');
				var itemdesc = row.children().eq(1).html();
				var btnid = '';
				var mybranch = row.children().eq(0).text();
				var tobranchname='';
				var supplier_id =0;
				if(to_branch != 0 && to_branch != -2){
					btnid = "b_"+branch_id+"_"+to_branch;
					tobranchname= row.children().eq(3).text();

				} else if(is_sup != 0){
					var selectcon = row.children().eq(3).find('select');
					var selectsupval =selectcon.val();

					var opttext =selectcon.find('option:selected').text();
					var exploded = opttext.split(':');
					opttext = exploded[0];
					if(!selectsupval){
						alertify.alert('No valid supplier');
						return;
					}
					supplier_id = selectsupval;
					tobranchname = opttext;
					btnid = "s_"+branch_id+"_"+supplier_id;
				} else {
					if(to_branch == -2){
						var selectconbranch = row.children().eq(3).find('select');
						var selectbranchid =selectconbranch.val();
						if(!selectbranchid){
							alertify.alert('No valid branch');
							return;
						}
						var optbranch =selectconbranch.find('option:selected').text();
						tobranchname = optbranch;
						btnid = "b_"+branch_id+"_"+selectbranchid;
						to_branch = selectbranchid;
					} else {
						tobranchname = assemble;
						btnid = "a_"+branch_id+"_"+0+"_"+assemble;
					}

				}

				row.fadeOut('fast');
				if($("#"+btnid).length > 0){
					var btncur = $("#"+btnid);
					var newjson = {itemdesc:itemdesc,branch_id:branch_id,supplier_id:supplier_id,to_branch:to_branch,qty:qty,rowid:rowid,item_id:item_id};
					var json = JSON.parse(btncur.attr('data-json'));
					json.push(newjson);
					var count = json.length;
					json = JSON.stringify(json);
					btncur.attr('data-json',json);
					btncur.html(tobranchname+" To "+mybranch+" ("+count+")");

				} else {
					var json = [{itemdesc:itemdesc,branch_id:branch_id,supplier_id:supplier_id,to_branch:to_branch,qty:qty,rowid:rowid,item_id:item_id}];
					var count = json.length;
					json = JSON.stringify(json);
					$('#orderholder').append("<button data-bid='"+branch_id+"' data-supid='"+supplier_id+"' data-br='"+to_branch+"' data-title='"+tobranchname+" To "+mybranch+"'style='margin:5px' class='btn btn-default btnOrderDetails' data-json='"+json+"' id='"+btnid+"'>"+tobranchname+" To "+mybranch+" ("+count+")</button>");

				}
			});

			$('body').on('click','.btnOrderDetails',function(){
				var btn = $(this);
				var id = btn.attr('id');
				var json = JSON.parse(btn.attr('data-json'));
				var title = btn.attr('data-title');
				var ret  = "<h3>"+title+"</h3>";
				ret = ret +"<table data-btnid='"+id+"' id='tblItems' class='table'>";

				for(var i in json){
					ret = ret + "<tr  data-item_id='"+json[i].item_id+"' data-branch_id='"+json[i].branch_id+"' data-supplier_id='"+json[i].supplier_id+"'  data-to_branch='"+json[i].to_branch+"'><td style='border-top:1px solid #ccc'>"+json[i].itemdesc+"</td><td style='border-top:1px solid #ccc'><input type='text' class='form-control' value='"+json[i].qty+"'></td><td style='border-top:1px solid #ccc'><button data-btnid='"+id+"'data-rowid='"+json[i].rowid+"' class='btn btn-default removeItemInCart'><span class='glyphicon glyphicon-remove'></span></button></td></tr>";
				}
				ret = ret + "</table>";
				$('#mbody').html(ret);
				$('#myModal').modal('show');
			});
			$('body').on('change','.supchoices',function(){
				var opt = $(this);
				var row = opt.parents('tr');
				var span = row.children().eq(3).find('.suppur');
				var purchase ='';
				if(opt.val()){
					 purchase = "Price: <span class='text-danger'>" + number_format(opt.find('option:selected').attr('data-cost'),2) + "</span>";
				}
				span.html(purchase);
			});
			$('body').on('click','.removeItemInCart',function(){
				var rowid = $(this).attr('data-rowid');
				var btn = $(this).attr('data-btnid');
				var row = $(this).parents('tr');
				var btndet = $('#'+btn);
				var json = JSON.parse(btndet.attr('data-json'));
				var title =btndet.attr('data-title');
				for(var i in json){
					console.log(json[i].rowid +"=="+ rowid);
					if(json[i].rowid == rowid){
						json.splice(i,1);
						$('#'+rowid).fadeIn('fast');
					}
				}
				var countleft = json.length;
				btndet.attr('data-json',JSON.stringify(json));
				btndet.html(title + " ("+countleft+")");
				row.remove();
				if(countleft == 0){
					btndet.remove();
					$('#myModal').modal('hide');
				}

			});
			$('body').on('click','.finalizeorder',function(){
				if($('#tblItems').length > 0){
					var arr = [];
					var btnid = $('#tblItems').attr('data-btnid');
					var btncon = $('#'+btnid);

					var supid = btncon.attr('data-supid');
					var tobr = btncon.attr('data-br');
					var bid = btncon.attr('data-bid');

					$('#tblItems  tr').each(function(){
						var row = $(this);
						var item_id = row.attr('data-item_id');
						var branch_id =  row.attr('data-branch_id');
						var supplier_id =  row.attr('data-supplier_id');
						var to_branch =  row.attr('data-to_branch');
						var order_qty = row.children().eq(1).find('input').val();
						arr.push({
							item_id:item_id,
							branch_id:branch_id,
							supplier_id:supplier_id,
							to_branch:to_branch,
							order_qty:order_qty
						});
					});

					arr = JSON.stringify(arr);
					$.ajax({
					    url:'../ajax/ajax_query2.php',
					    type:'post',
					    data: {datajson:arr,supid:supid,tobr:tobr,bid:bid,functionName:'processAutoPO'},
					    success: function(data){
					        alertify.alert(data);
						    btncon.remove();
						    $('#myModal').modal('hide');
					    },
					    error:function(){

					    }
					})
					console.log(JSON.stringify(arr));
				} else {
					alertify.alert('No Item In Cart')
				}
			});
			$('body').on('click', '.paging', function(e) {
				e.preventDefault();
				var page = $(this).attr('page');
				$('#hiddenpage').val(page);
				var search = $('#searchSales').val();
				getPage(page, search);
			});

			$("#searchSales").keyup(function() {
				var search = $('#searchSales').val();
				//getPage(0, search);
			});
			function getPage(p, search) {

				$.ajax({
					url: '../ajax/ajax_paging.php',
					type: 'post',
					beforeSend: function() {
						$('#holder').html("<p class='text-center'><i class='fa fa-spinner fa-spin'></i> Loading...</p>");
					},
					data: {
						page: p,
						functionName: 'criticalLevelPaginate',
						cid: <?php echo $user->data()->company_id; ?>,
						search: search
					},
					success: function(data) {
						$('#holder').html(data);
						$('#holder').hide();
						$('.btnOrderDetails').each(function(){
							var jsondata= JSON.parse($(this).attr('data-json'));
							for(var i in jsondata){
								var rowid = jsondata[i].rowid;
								$('#'+rowid).hide();
							}
						});
						initselect();
						$('#holder').fadeIn();

					}
				});
			}
			function formatSelect(o) {
				if (!o.id)
					return o.text; // optgroup
				else {
					var r = o.text.split(':');
					return "<span> "+r[0]+"</span> <span style='float:right' class='text-danger'>" + r[1] + "</span>";
				}
			}
			function initselect(){
				$(".supchoices").select2({
					placeholder: 'Select supplier',
					allowClear: true,
					formatResult: formatSelect,
					formatSelection: formatSelect,
					escapeMarkup: function(m) {
						return m;
					}
				});
			}
		});
	</script>
<?php require_once '../includes/admin/page_tail2.php'; ?>