<!-- ADMIN -->
<section  ng-controller="FormUser">
	<md-progress-linear ng-hide="opt.isLoaded" md-mode="indeterminate"></md-progress-linear>
	<div class="container-xs">
		<form  name="formParent" ng-submit="Submit_Form()" role="form"  >


			<md-card>
				<md-card-content>

					<md-input-container class="md-block">
						<label>NAME</label>
						<input ng-model="form.NAME" name="NAME" type="text" required autofocus>
						<div ng-messages="formParent.NAME.$error" role="alert" md-auto-hide="false">
							<div ng-message="required">This is required.</div>
						</div>
					</md-input-container>

					<md-input-container class="md-block">
						<label>Job Title</label>
						<input ng-model="form.JOBTITLE" >
					</md-input-container>


					<md-input-container class="md-block" >
						<label>Clinic</label>
						<md-select ng-model="form.CLINICID"  name="CLINICID"  required>
							<md-option ng-repeat="A in form.CLINICS" ng-value="A.ID" ng-bind="A.CLINICNAME"></md-option>
						</md-select>
						<div ng-messages="formParent.CLINICID.$error" role="alert" md-auto-hide="false">
							<div ng-message="required">This is required.</div>
						</div>
					</md-input-container>


					<md-input-container class="md-block" >
						<label>User level</label>
						<md-select ng-model="form.POSITION" ng-change="form.POSITION = Check_Select_Value(form.POSITION);" name="POSITION"  required>
							<md-option value="ADMINISTRATOR">SUPPER ADMINISTRATOR</md-option>
							<md-option value="BRANCH ADMINISTRATOR">ADMINISTRATOR</md-option>
							<md-option value="BRANCH CONSULTANT" >CONSULTANT</md-option>
							<md-option value="BRANCH RESIDENT" >RESIDENT</md-option>
							<md-option value="BRANCH ASSISTANT">ASSISTANT</md-option>
						</md-select>
						<div ng-messages="formParent.POSITION.$error" role="alert" md-auto-hide="false">
							<div ng-message="required">This is required.</div>
						</div>
					</md-input-container>


					<md-input-container class="md-block">
						<label>Username</label>
						<input ng-model="form.USERNAME" name="USERNAME" type="text" minlength="4" ng-disabled="form.ID > 0" required>
						<div ng-messages="formParent.USERNAME.$error" role="alert" md-auto-hide="false">
							<div ng-message="required">This is required.</div>
							<div ng-message="minlength">Minimum 4 characters.</div>
						</div>
					</md-input-container>


					<div layout="column" layout-gt-xs="row" layout-align="end" >
						<md-button ng-disabled="opt.isSubmit || formParent.$invalid"  type="submit" class="md-raised md-primary">
							<span ng-hide="opt.isSubmit">Save</span>
							<div ng-show="opt.isSubmit" layout="row" layout-align="center">
								<md-progress-circular md-mode="indeterminate" md-diameter="30"></md-progress-circular>
							</div>
						</md-button>

						<md-button href="{{opt.cancelUrl}}"  class=" md-accent" >Cancel</md-button>
					</div>

				</md-card-content>
			</md-card>

		</form>
	</div>
</section>

