
<section  ng-controller="FormPreregistration" ng-init="init('<?php echo base_url(); ?>','<?php echo $id; ?>')" class="container">
	<form  name="formParent" ng-submit="submitForm()" role="form"   >

		<div class="navigation-page-content d-flex align-items-center">
			<ul class="page-root mb-0">
				<li><a ng-href="<?php echo base_url('patients'); ?>"><i class="fa fa-wheelchair fa-fw"></i> Patients</a></li>
				<li ng-if="f.ID > 0"><a ng-href="<?php echo base_url('patients/record/'); ?>{{f.ID}}">Patient Records</a></li>
				<li ng-bind-html="title"></li>
			</ul>
		</div>

		<div class="card card-custom mx-auto shadow" >
			<div class="card-header" ng-bind-html="title"></div>
			<div class="card-body ">
				<div class="form-row">
					<div class="col-md-3 ">
						<label class="col-form-label">First Name</label>
						<input ng-model="f.FIRSTNAME" type="text" class="form-control form-control-sm" required >
					</div>	
					<div class="col-md-3 ">
						<label class="col-form-label">Middle Name</label>
						<input ng-model="f.MIDDLENAME" type="text" class="form-control  form-control-sm">
					</div>	
					<div class="col-md-3 ">
						<label class="col-form-label">Last Name</label>
						<input ng-model="f.LASTNAME" type="text" class="form-control  form-control-sm" required>
					</div>	
					<div class="col-md-3 ">
						<label class="col-form-label">DOB</label>
						<input ng-model="f.DOB" type="text" data-toggle="datepicker" class="form-control form-control-sm " required>
					</div>
				</div>

				<div class="form-row">
					<div class="col-md-3 ">
						<label class="col-form-label">Sex</label>
						<select ng-model="f.SEX"  class="form-control form-control-sm">
							<option value="FEMALE">FEMALE</option>
							<option value="MALE">MALE</option>
						</select>
					</div>
					<div class="col-md-3 ">
						<label class="col-form-label">Civil Status</label>
						<select ng-model="f.CIVILSTATUS"  class="form-control form-control-sm">
							<option value="SINGLE">Single</option>
							<option value="MARRIED">Married</option>
							<option value="WIDOW">Widow</option>
							<option value="SEPARATED">Separated</option>
						</select>
					</div>
					<div class="col-md-3 ">
						<label class="col-form-label">Blood Type</label>
						<select ng-model="f.BLOODTYPE" class="form-control form-control-sm">
							<option value=""></option>
							<option value="A">A</option>
							<option value="AB">AB</option>
							<option value="O">O</option>
							<option value="A-">A-</option>
							<option value="AB-">AB-</option>
							<option value="O-">O-</option>
							<option value="A+">A+</option>
							<option value="AB+">AB+</option>
							<option value="O+">O+</option>
						</select>
					</div>	
					<div class="col-md-3 ">
						<label class="col-form-label">Place of Birth </label>
						<input ng-model="f.POB"  type="text" class="form-control form-control-sm ">
					</div>
				</div>

				<div class="form-row">
					<div class="col-md-3 ">
						<label class="col-form-label">Nationality</label>
						<input ng-model="f.NATIONALITY" type="text" class="form-control form-control-sm " >
					</div>	
					<div class="col-md-3 ">
						<label class="col-form-label">Religion</label>
						<input ng-model="f.RELIGION" type="text" class="form-control form-control-sm " >
					</div>
					<div class="col-md-6 ">
						<label class="col-form-label">Occupation</label>
						<input ng-model="f.OCCUPATION" type="text" class="form-control form-control-sm " >
					</div>
				</div>

				<div class="form-row">
					<div class="col-md-4">
						<label class="col-form-label">Allergies</label>
						<input type="text" ng-model = "f.ALLERGIES" class="form-control form-control-sm">
					</div>
					<div class="col-md-4">
						<label class="col-form-label">SCNO</label>
						<input type="text" ng-model="f.SCNO" class="form-control form-control-sm">
					</div>
					<div class="col-md-4">
						<label class="col-form-label">Date Issued</label>
						<input type="text" ng-model="f.DATEISSUE" data-toggle = "datepicker" class="form-control form-control-sm">
					</div>
				</div>
				
				<h6>Contact Information</h6>

				<div class="form-row">
					<div class="col-md-6 ">
						<label class="col-form-label">Street No</label>
						<input ng-model="f.STREETNO" type="text" class="form-control form-control-sm ">
					</div>	
					<div class="col-md-6 ">
						<label class="col-form-label">Barangay</label>
						<input ng-model="f.BARANGAY" type="text" class="form-control form-control-sm" >
					</div>	
				</div>
				<div class="form-row">
					<div class="col-md-4 ">
						<label class="col-form-label">City</label>
						<input ng-model="f.CITY" type="text" class="form-control form-control-sm" >
					</div>	
					<div class="col-md-4 ">
						<label class="col-form-label">Province</label>
						<input ng-model="f.PROVINCE" type="text" class="form-control form-control-sm " >
					</div>	
					<div class="col-md-4 ">
						<label class="col-form-label">Tel. No</label>
						<input ng-model="f.TELEPHONENO"  type="text" class="form-control form-control-sm " >
					</div>	
				</div>

				<h6>Parent Information</h6>
				<div class="form-row" >
					<div class="col-md-4" >
						<label class="col-form-label" >Father's Name</label>
						<input type="text" ng-model = "f.FATHERNAME" class="form-control form-control-sm" >
					</div>
					<div class="col-md-4">
						<label class="col-form-label" >Father's Address</label>
						<input type="text" ng-model = "f.FATHERADDRESS" class="form-control form-control-sm">
					</div>
					<div class="col-md-4">
						<label class="col-form-label" >Father's Telephone Number</label>
						<input type="text" ng-model = "f.FATHERTELPHONENO" class="form-control form-control-sm">
					</div>
				</div>
				<div class="form-row" >
					<div class="col-md-4" >
						<label class="col-form-label" >Mother's Name</label>
						<input type="text" ng-model = "f.MOTHERNAME" class="form-control form-control-sm" >
					</div>
					<div class="col-md-4">
						<label class="col-form-label" >Mother's Address</label>
						<input type="text" ng-model = "f.MOTHERADDRESS" class="form-control form-control-sm">
					</div>
					<div class="col-md-4">
						<label class="col-form-label" >Mother's Telephone Number</label>
						<input type="text" ng-model = "f.MOTHERTELPHONENO" class="form-control form-control-sm">
					</div>
				</div>

				<h6>Spouse Information</h6>
				<div class="form-row">
					<div class="col-md-4">
						<label class="col-form-label">Spouse Name</label>
						<input type="text" ng-model="f.SPOUSENAME" class="form-control form-control-sm">
					</div>
					<div class="col-md-8">
						<label class="col-form-label">Spouse Address</label>
						<input type="text" ng-model="f.SPOUSEADDRESS" class="form-control form-control-sm">
					</div>
				</div>
				<div class="form-row">
					<div class="col-md-4">
						<label class="col-form-label">Spouse Phone Number</label>
						<input type="text" ng-model="f.SPOUSETELPHONENO" class="form-control form-control-sm">
					</div>
					<div class="col-md-8">
						<label class="col-form-label">Spouse Occupation</label>
						<input type="text" ng-model="f.SPOUSEOCCUPATION" class="form-control form-control-sm">
					</div>
				</div>

				<h6>Emergency Information</h6>

				<div class="form-row">
					<div class="col-md-4 ">
						<label class="col-form-label">Name</label>
						<input ng-model="f.EMERGENCYNAME"  type="text" class="form-control form-control-sm" >
					</div>	
					<div class="col-md-8 ">
						<label class="col-form-label">Address</label>
						<input ng-model="f.EMERGENCYADDRESS"  type="text" class="form-control form-control-sm" >
					</div>	
				</div>

				<div class="form-row">
					<div class="col-md-4 ">
						<label class="col-form-label">Tel. No</label>
						<input ng-model="f.EMERGENCYTELPHONENO"  type="text" class="form-control form-control-sm " >
					</div>
					<!-- <div class="col-md-4 ">
						<label class="col-form-label">Mobile No</label>
						<input ng-model="f.EMERGENCYMOBILENO"  type="text" class="form-control form-control-sm" >
					</div> -->
					<div class="col-md-8 ">
						<label class="col-form-label">RELATION</label>
						<input ng-model="f.EMERGENCYRELATION"  type="text" class="form-control form-control-sm" >
					</div>
				</div>	

			</div>
			<div class="card-footer form-action">
				<div class="d-flex align-items-center justify-content-between">
					<button ng-click="f.CANCELLED = !f.CANCELLED" type="button" class="btn {{f.CANCELLED ? 'btn-danger' : 'btn-success' }} btn-sm" ng-bind="f.CANCELLED ? 'DELETE' :'ACTIVE'" ></button>
					<div>
						<button ng-disabled="isSubmit" type="submit" class="btn btn-secondary btn-sm">SAVE</button>
						<a ng-if="f.ID == 0" ng-href="<?php echo base_url('patients'); ?>" class="btn btn-secondary btn-sm" role="button">CANCEL</a>
						<a ng-if="f.ID > 0" ng-href="<?php echo base_url('patients/record/'); ?>{{f.ID}}" class="btn btn-secondary btn-sm" role="button">CANCEL</a>
					</div>
				</div>
			</div>
		</div>

	</form>
</section>

<script src="<?php echo base_url('assets/js/patients/formaPreregistration.js') ?>"></script>