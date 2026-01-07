<!-- ADMIN -->
<section  ng-controller="FormClinic"  >
	<md-progress-linear ng-hide="opt.isLoaded" md-mode="indeterminate"></md-progress-linear>
	<div class="container-md">

		<form  name="formParent" ng-submit="Submit_Form()" role="form"   >

			<md-card>
				<md-card-header>
					<md-card-header-text>
						<span class="md-subhead">CLINIC INFORMATION</span>
					</md-card-header-text>
				</md-card-header>
				<md-card-content >


					<md-input-container class="md-block mb-5">
						<label>Hospital</label>
						<md-select ng-model="f.HOSPITALID" name="HOSPITALID" >
							<md-option value=""></md-option>
							<md-option ng-repeat="(key, S) in f.HOSPITALS" ng-value="S.ID" ng-bind="S.NAME"></md-option>
						</md-select>
						<div ng-messages="formParent.HOSPITALID.$error" role="alert" md-auto-hide="false">
							<div ng-message="required">This is required.</div>
						</div>
					</md-input-container>


					<md-input-container class="md-block">
						<label>Clinic Name</label>
						<input ng-model="f.CLINICNAME" name="CLINICNAME" autofocus required>
						<div ng-messages="formParent.CLINICNAME.$error" role="alert" md-auto-hide="false">
							<div ng-message="required">This is required.</div>
						</div>
					</md-input-container>

					<md-input-container class="md-block">
						<label>Specialist 1</label>
						<input ng-model="f.CLINICSUBNAME" >
					</md-input-container>

					<md-input-container class="md-block">
						<label>Specialist 2</label>
						<input ng-model="f.CLINICSUBNAME1" >
					</md-input-container>
					

					<div layout="column" layout-gt-xs="row">
						<md-input-container flex-gt-xs="30" class="md-block">
							<label>TIN</label>
							<input ng-model="f.TIN" >
						</md-input-container>
						<md-input-container flex-gt-xs="40" class="md-block">
							<label>Tel No.</label>
							<input ng-model="f.CONTACTNO" >
						</md-input-container>
						<md-input-container flex-gt-xs="40" class="md-block">
							<label>Mobile No.</label>
							<input ng-model="f.MOBILENO" >
						</md-input-container>
					</div>


					<div layout="column" layout-gt-xs="row">
						<md-input-container flex-gt-xs="30" class="md-block">
							<label>Email</label>
							<input ng-model="f.EMAIL" >
						</md-input-container>
						<md-input-container flex-gt-xs="80" class="md-block">
							<label>Address</label>
							<input ng-model="f.ADDRESS" >
						</md-input-container>
					</div>
					
						
					<div layout="row" layout-wrap="">
						<md-switch ng-model="f.SALES"  aria-label="Sales " class="md-primary">
							Sales 
						</md-switch>

						<md-switch ng-model="f.IS_BLAST"  aria-label="Text Blast " class="md-primary">
							Text Blast 
						</md-switch>

						<!-- <md-switch ng-model="f.ACCOUNTBASE"  aria-label="Account Base " class="md-primary">
							Account Base 
						</md-switch>

						<md-switch ng-model="f.ASSISTANTRECORD"  aria-label="Assistant Record " class="md-primary">
							Assistant Record 
						</md-switch>

						<md-switch ng-model="f.MEDICALHISTORY"  aria-label="Medical History " class="md-primary">
							Medical History 
						</md-switch>

						<md-switch ng-model="f.REFRACTION"  aria-label="Refraction " class="md-primary">
							Refraction 
						</md-switch> -->
					</div>


					<div layout="column" layout-gt-xs="row" layout-align="end" >
						<md-button ng-disabled="opt.isSubmit"  type="submit" class="md-raised md-primary">
							<span ng-hide="opt.isSubmit">Save</span>
							<div ng-show="opt.isSubmit" layout="row" layout-align="center">
								<md-progress-circular md-mode="indeterminate" md-diameter="30"></md-progress-circular>
							</div>
						</md-button>
						<md-button href="{{opt.cancelUrl}}" class="md-primary">CANCEL</md-button>
					</div>

				</md-card-content>
			</md-card>


		</form>
	</div>
</section>

