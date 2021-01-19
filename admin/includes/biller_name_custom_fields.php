			<div class="form-group">
			<div class="row">
				<div class="col-md-4">
					Same As:
					<select class='form-control' name="same_as_biller_id" id="same_as_biller_id">
					<?php
						if(isset($page_biller_names)){
							foreach($page_biller_names as $p_data){
								?>
								<option value="<?php echo $p_data->id; ?>"><?php echo $p_data->name; ?></option>
								<?php
							}
						}
					?>
					</select>
				</div>
				<div class='col-md-4'>
					<br>
					<button class='btn btn-default' id='btnSameAsBiller'>Submit</button>
				</div>
			</div>
			</div>
			<div class="row">
				<div class="col-md-12">
					<form id='custom_fields_form' class="form-horizontal" action="" method="POST">
						<fieldset>
							<legend></legend>
							<div class="form-group">
								<div class="col-md-6">
									<p><strong>Biller Name: </strong> <strong id='biller_name_field' class='text-danger'></strong></p>
									<input type='hidden' id="biller_id_field" name="biller_id_field" class="form-control">
								</div>
							</div>
							<div id="clonethis">
								<div style='border:1px solid #ccc;padding:5px;margin-top:10px;'>
								<div class="form-group">
									<div class="col-md-4">
										<strong>Element</strong>
										<select id="element_name" name="element_name[]" class="form-control element_name">
											<option value=''>--Select Element--</option>
											<option value='text'>text</option>
											<option value='textarea'>textarea</option>
											<option value='select'>select</option>
										</select>
										<span class="help-block">Choose element name</span>
									</div>

									<div class="col-md-4">
										<strong>Choices</strong>
										<input type='text' id="choices" name="choices[]"  class="form-control choices">
										<span class="help-block">Choices, comma separated</span>
									</div>

									<div class="col-md-4">
										<strong>Type</strong>
										<select id="data_type[]" name="data_type[]" class="form-control">
											<option value=''>--Select Type--</option>
											<option value='int'>Number</option>
											<option value='string'>String</option>
											<option value='date'>Date</option>
										</select><span class="help-block">Choose the data type</span>
									</div>

									<div class="col-md-4">
										<strong>Label</strong>
										<input id="label"  name="label[]" placeholder="Label" class="form-control input-md " type="text">
										<span class="help-block">Enter label</span>
									</div>

									<div class="col-md-4">
										Required?
										<select id="is_required[]" name="is_required[]" class="form-control">
											<option value='1'>Required</option>
											<option value='0'>Not Required</option>
										</select><span class="help-block">is data required?</span>
									</div>
								</div>
								</div>
							</div> <!-- Clone end -->
							<div id="appendclone"></div>
							<div class="form-group" id='addmore'>
							</div>
							<input type="button" id='btnAddMore' value='Add more item' class='btn btn-default pull-right'/>

							       <!-- Button (Double) -->
							<div class="form-group">
								<label class="col-md-1 control-label" for="button1id"></label>
								<div class="col-md-8">
									<button class='btn btn-default' id='btnSubmitFields'>SAVE</button>
								</div>
							</div>

						</fieldset>
					</form>
				</div>
			</div>



