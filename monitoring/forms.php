<?php

	require_once '../includes/monitoring/page_head.php';
	if(!$user->hasPermission('dashboard')){
		// redirect to denied page
		//	Redirect::to(1);
	}
	$process = new Process();
	$processes = $process->get_active('processes',array('company_id' ,'=', $user->data()->company_id));

	$position = new Position();
	$positions = $position->get_active('positions', array('company_id' ,'=', $user->data()->company_id));
?>
	<style>
		body.dragging, body.dragging * {
			cursor: move !important;
		}

		.dragged {
			position: absolute;
			opacity: 0.5;
			z-index: 2000;
		}

		ol.ul_sort li.placeholder {
			position: relative;
			list-style: none;
			border-top: 5px solid #ccc;

			/** More li styles **/
		}
		ol.ul_sort li.placeholder:before {
			position: absolute;

			/** Define arrowhead **/
		}
	</style>

	<!-- Sidebar -->
<?php include_once '../includes/monitoring/sidebar.php';?>
	<!-- Page content -->
	<div id="page-content-wrapper" style='padding-top: 30px;'>
		<div class="container-fluid">
				<h1>Form Details</h1>
				<?php
					$navContainer = '';
					$bodyContainer = '';
					$isFirst = true;
					foreach($processes as $p){
						$form  = new FormRequest();
						$forms = $form->getForms($p->id);

						$ul='';
						if($forms){
							$active = "";
							if($isFirst){
								$active = "active";
								$isFirst =false;
							}
							$navContainer .= '<li role="presentation" class="'.$active.'"><a href="#con'.$p->id.'" aria-controls="home" role="tab" data-toggle="tab">'.$p->name.'</a></li>';
							$bodyContent = "";
							$bodyContent .= "<h3>$p->name</h3>";
							$bodyContent .= "<table class='table table-bordered'>";
							$bodyContent .= "<tr><th>Field</th><th>Element</th><th></th></tr>";
							$arrreq = ['No','Yes'];
							$ul = "<ol class='list-group ul_sort' id='ol_{$p->id}'>";
							$whocanrequest = [];
							foreach($forms as $f){
								$ul .= "<li class='list-group-item' data-id='$f->id'>$f->label</li>";
								$btnRemove = "<button class='btn btn-danger btn-sm btnRemove' data-id='$f->id'><i class='fa fa-trash'></i></button>";
								$btnEdit = "<button class='btn btn-primary btn-sm btnEdit' data-details='".json_encode($f)."' data-id='$f->id'><i class='fa fa-pencil'></i></button>";
								$bodyContent .= "<tr><td>$f->label</td><td>" . ucwords($f->element_name) . "</td><td>$btnRemove $btnEdit</td></tr>";
								$explodpos = explode(',',$f->who_can_request);
								foreach($explodpos as $pos){
									if(!in_array($pos,$whocanrequest))$whocanrequest[] = $pos;
								}
							}
							$ul .= "</ol>";
							$bodyContent .= "</table>";

							$bodyContent .= "<h3>Form Order</h3>";
							$bodyContent .=  $ul;
							$bodyContent .= "<hr>";
							$bodyContent .="<div>";
							$bodyContent .= "<button data-pid='$p->id' class='btn btn-default saveOrder'>Save Order</button>";
							$bodyContent .= "</div><br>";
							$selectpos = "<br><div class='row'><div class='col-md-8'><select id='select2_{$p->id}' class='form-control posselect' multiple>";
							foreach($positions as $ind){
								$selected = '';
								if(in_array($ind->id,$whocanrequest)){
									$selected = 'selected';
								}
								$selectpos .= "<option value='$ind->id' $selected>$ind->position</option>";
							}
							$selectpos .= "</select></div><div class='col-md-4'><button class='btn btn-default btnSavePosition' data-pid='$p->id'>Save Position</button></div></div>";

							$bodyContent = $selectpos . $bodyContent;

							$bodyContainer .= '<div role="tabpanel" class="tab-pane '.$active.'" id="con'.$p->id.'">'.$bodyContent.'</div>';
						}
					}
			?>
			<div>

				<!-- Nav tabs -->
				<ul class="nav nav-tabs" role="tablist">
					<?php echo $navContainer; ?>
				</ul>

				<!-- Tab panes -->
				<div class="tab-content">
					<?php echo $bodyContainer; ?>
				</div>

			</div>
		</div>
		<div class="modal fade" id="myModal" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
			<div class="modal-dialog">
				<div class="modal-content">
						<div class="modal-header">
							<button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
							<h4 class="modal-title" id='mtitle'>Edit Form Details</h4>
						</div>
						<div class="modal-body" id='mbody'>
							<input type="hidden" id='edit_hid_id'>
							<strong>Label</strong>
							<input type="text" class='form-control' id='edit_label'>
							<span class="help-block"></span>
							<strong>Element</strong>
							<select id="edit_element_name" name="edit_element_name" class="form-control element_name">
								<option value='text'>text</option>
								<option value='textarea'>textarea</option>
								<option value='select'>select</option>
								<option value='radio'>radio</option>
								<option value='form_label'>form label</option>
							</select>
							<span class="help-block"></span>
							<strong>Choices</strong>
							<input type="text" class='form-control' id='edit_choices'>
							<span class="help-block">Choices, comma separated</span>
							<strong>Data type</strong>
							<select id="edit_data_type" name="edit_data_type" class="form-control">
								<option value='int'>Number</option>
								<option value='string'>String</option>
								<option value='date'>Date</option>
							</select>
							<span class="help-block"></span>
							<strong>Required?</strong>
							<select id="edit_required" name="edit_required" class="form-control">
								<option value='1'>Required</option>
								<option value='0'>Not Required</option>
							</select><span class="help-block">is data required?</span>
							<hr>
							<button class='btn btn-default' id='btnEditSave'>SAVE</button>
						</div>
				</div><!-- /.modal-content -->
			</div><!-- /.modal-dialog -->
		</div><!-- /.modal -->
	</div>
	<script src='../js/jquery-sortable.js'> </script>
	<script type="text/javascript">
		$(function(){
			$('body').on('click','.btnSavePosition',function(){
				var pid = $(this).attr('data-pid');
				var pos = $('#select2_'+pid).val();
				$.ajax({
					url:'query.php',
					type:'POST',
					data: {functionName:'updatePosition',pid: pid,position:JSON.stringify(pos)},
					success: function(data){
						alert(data);
						location.href='forms.php';
					},
					error:function(){
						alert('Error Occur');
					}
				});
			});
			$('.posselect').select2({});
			$('body').on('click','.saveOrder',function(){
				saveOutput($(this).attr('data-pid'));
			});
			$('body').on('click','.btnRemove',function(){
				var id = $(this).attr('data-id');
				alertify.confirm("Are you sure you want to delete this record?",function(e){
					if(e){
						$.ajax({
							url:'query.php',
							type:'POST',
							data: {functionName:'deleteForm',id: id},
							success: function(data){
								location.href='forms.php';
							},
							error:function(){
								alert('Error Occur');
							}
						});
					}
				});

			});
			function saveOutput(pid){
				var arr =[];
				$("#ol_"+pid+" li").each(function(index){
					var i = parseInt(index) + 1;
					var id = $(this).attr('data-id');
					arr.push({i:i,id:id});
				});
				if(arr.length){
					$.ajax({
						url:'query.php',
						type:'POST',
						data: {functionName:'saveOrder',arr: JSON.stringify(arr)},
						success: function(data){
							alert(data);
						},
						error:function(){
							alert('Error Occur');
						}
					});
				} else {
					alert('Error');
				}

			}

			var group = $("ol.ul_sort").sortable({
				group: 'ul_sort',
				isValidTarget: function  ($item, container) {
					if($item.is(".highlight"))
						return true;
					else
						return $item.parent("ol")[0] == container.el[0];
				},
				onDrop: function ($item, container, _super) {
					$('#serialize_output').text(
						group.sortable("serialize").get().join("\n"));

					_super($item, container);
				},
				serialize: function (parent, children, isContainer) {
					return isContainer ? children.join() : parent.text();
				},
				tolerance: 6,
				distance: 10
			});
			$('body').on('click','.btnEdit',function(){
				var details = $(this).attr('data-details');
				try{
					details = JSON.parse(details);
					$('#edit_hid_id').val(details.id);
					$('#edit_label').val(details.label);
					$('#edit_element_name').val(details.element_name);
					$('#edit_choices').val(details.choices);
					$('#edit_data_type').val(details.data_type);
					$('#edit_required').val(details.is_required);
					$('#myModal').modal('show');
				} catch(e){

				}
			});
			$('body').on('click','#btnEditSave',function(){
				var id = $('#edit_hid_id').val();
				var label = $('#edit_label').val();
				var element_name = $('#edit_element_name').val();
				var choices = $('#edit_choices').val();
				var data_type = $('#edit_data_type').val();
				var is_required = $('#edit_required').val();
				$.ajax({
				    url:'query.php',
				    type:'POST',
				    data: {functionName:'saveEditedForm',id:id,label:label,element_name:element_name,choices:choices,data_type:data_type,is_required:is_required},
				    success: function(data){
						alert(data);
					    location.href='forms.php';
				    },
				    error:function(){
				        alert('Error Occur');
				    }
				});
			});
		});
	</script>


<?php require_once '../includes/monitoring/page_tail.php'; ?>