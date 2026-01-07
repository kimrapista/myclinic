
<section  ng-controller="PageAccount" >
	<md-progress-linear ng-hide="opt.isLoaded" md-mode="indeterminate"></md-progress-linear>
	<div class="container-xs">


		<md-card>
			<md-tabs md-dynamic-height md-border-bottom >
				<md-tab label="INFO">
					<md-card-content>

						<form name="formParent" ng-submit="Submit_Form()" role="form"  >
							
							<md-input-container class="md-block">
								<label>Name</label>
								<input ng-model="f.NAME" name="NAME" autofocus required>
								<div ng-messages="formParent.NAME.$error" role="alert" md-auto-hide="false">
									<div ng-message="required">This is required.</div>
								</div>
							</md-input-container>

							<md-input-container class="md-block">
								<label>Job Title</label>
								<input ng-model="f.JOBTITLE" >
							</md-input-container>


							<md-input-container ng-show="f.DoctorFields"  class="md-block" >
								<label >Specialty</label>
								<md-select ng-model="f.SPECIALISTID" name="SPECIALISTID" ng-change="f.SPECIALISTID = Check_Select_Value(f.SPECIALISTID);" md-on-close="searchSpecialty='';"  ng-required="f.DoctorFields">
									<md-select-header>
										<input ng-model="searchSpecialty" ng-keydown="$event.stopPropagation()"  type="text" placeholder="Search Specialty" class="md-select-search">
									</md-select-header>
									<md-optgroup label="List of Specialty">
										<md-option ng-value="A.ID" ng-bind="A.SPECIALTY" ng-repeat="A in f.SPECIALIST|filter: searchSpecialty"></md-option>
									</md-optgroup>
								</md-select>
								<div ng-messages="formParent.SPECIALISTID.$error" role="alert" md-auto-hide="false">
									<div ng-message="required">This is required.</div>
								</div>
							</md-input-container>

							<md-input-container  ng-show="f.DoctorFields" class="md-block" >
								<label>License No.</label>
								<input ng-model="f.LICENSENO" name="LICENSENO" ng-required="f.DoctorFields">
								<div ng-messages="formParent.LICENSENO.$error" role="alert" md-auto-hide="false">
									<div ng-message="required">This is required.</div>
								</div>
							</md-input-container>

 
							<md-input-container ng-show="f.DoctorFields" class="md-block" >
								<label>PTR No.</label>
								<input ng-model="f.PTR" name="PTR" ng-required="f.DoctorFields">
								<div ng-messages="formParent.PTR.$error" role="alert" md-auto-hide="false">
									<div ng-message="required">This is required.</div>
								</div>
							</md-input-container>

							
							<md-input-container  ng-show="f.DoctorFields" class="md-block" >
								<label>S2 No.</label>
								<input ng-model="f.S2NO" >
							</md-input-container>

							<md-input-container class="md-block">
								<label>Email</label>
								<input ng-model="f.EMAIL" type="email" >
							</md-input-container>

							<h6>Account Settings</h6>

							
							<md-input-container class="md-block " >
								<label>Your default clinic</label>
								<md-select ng-model="f.SUBCLINICID" name="SUBCLINICID"  >
									<md-option value=""></md-option>
									<md-option ng-repeat="(key, S) in f.CLINICS" ng-value="S.ID" ng-bind="S.NAME"></md-option>
								</md-select>
								<div ng-messages="formParent.SUBCLINICID.$error" role="alert" md-auto-hide="false">
									<div ng-message="required">This is required.</div>
								</div>
							</md-input-container>

							<md-input-container class="md-block " >
								<label>User level</label>
								<md-select ng-model="f.POSITION" disabled>
									<md-option value="BRANCH ADMINISTRATOR">ADMINISTRATOR</md-option>
									<md-option value="BRANCH CONSULTANT">CONSULTANT</md-option>
									<md-option value="BRANCH RESIDENT">RESIDENT</md-option>
									<md-option value="BRANCH ASSISTANT">ASSISTANT</md-option>
								</md-select>
							</md-input-container>


							<md-input-container class="md-block">
								<label>Username</label>
								<input ng-model="f.USERNAME" name="USERNAME" required>
								<div ng-messages="formParent.USERNAME.$error" role="alert" md-auto-hide="false">
									<div ng-message="required">This is required.</div>
								</div>
							</md-input-container>
							

							<div layout="column"  >
								<md-button ng-disabled="opt.isSubmit || formParent.$invalid"  type="submit" class="md-raised md-primary">
									<span ng-hide="opt.isSubmit">Save</span>
									<div ng-show="opt.isSubmit" layout="row" layout-align="center">
										<md-progress-circular md-mode="indeterminate" md-diameter="30"></md-progress-circular>
									</div>
								</md-button>
							</div>
						</form>


					</md-card-content>
				</md-tab>
				<md-tab label="CHANGE PASSWORD">
					<md-card-content>

						<form name="formParent1" ng-submit="Submit_Form_Password()" role="form"  >
							<md-input-container class="md-block">
								<label>Current Password</label>
								<input ng-model="f.currentPassword" name="currentPassword" type="password" required>
								<div ng-messages="formParent1.currentPassword.$error" role="alert" md-auto-hide="false">
									<div ng-message="required">This is required.</div>
								</div>
							</md-input-container>

							<md-input-container class="md-block">
								<label>New Password</label>
								<input ng-model="f.newPassword" name="newPassword" ng-required="f.currentPassword" ng-disabled="!f.currentPassword" type="password" minlength="5" >
								<div ng-messages="formParent1.newPassword.$error" role="alert" md-auto-hide="false">
									<div ng-message="required">This is required.</div>
									<div ng-message="minlength">Minimum 5 characters.</div>
								</div>
							</md-input-container>

							<md-input-container class="md-block">
								<label>Re-Type Password</label>
								<input ng-model="f.retypePassword" name="retypePassword" ng-required="f.currentPassword" ng-disabled="!f.currentPassword" type="password" minlength="5" >
								<div ng-messages="formParent1.retypePassword.$error"  role="alert" md-auto-hide="false">
									<div ng-message="required">This is required.</div>
									<div ng-message="minlength">Minimum 5 characters.</div>
								</div>
							</md-input-container>


							<div layout="column"  >
								<md-button ng-disabled="opt.isSubmitPass"  type="submit" class="md-raised md-primary">
									<span ng-hide="opt.isSubmitPass">SAVE</span>
									<div ng-show="opt.isSubmitPass" layout="row" layout-align="center">
										<md-progress-circular md-mode="indeterminate" md-diameter="30"></md-progress-circular>
									</div>
								</md-button>
							</div>

						</form>

					</md-card-content>
				</md-tab>
			</md-tabs>
		</md-card>


	</div>
</section>

