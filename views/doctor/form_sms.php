<!-- doctor -->
<section  ng-controller="FormSMS" >
	<md-progress-linear ng-hide="opt.isLoaded" md-mode="indeterminate"></md-progress-linear>
	<div class="container-xs">
		<form  name="formParent" ng-submit="Submit_Form()" role="form"  >


			<md-card>
				<md-card-content>


					<md-input-container class="md-block">
						<label>MESSAGE</label>
						<textarea ng-model="form.MESSAGE" name="MESSAGE" md-maxlength="150" ng-disabled="ReadOnly" required ></textarea>
						<div ng-messages="formParent.MESSAGE.$error" role="alert" md-auto-hide="false">
							<div ng-message="required">This is required.</div>
							<div ng-message="md-maxlength">character reach the max limit.</div>
						</div>
					</md-input-container>

					<div layout="column" >
						<md-input-container >
							<label>Date to send</label>
							<input ng-model="form.SENDDATE" name="SENDDATE" type="date" ng-disabled="ReadOnly" required autofocus>
							<div ng-messages="formParent.SENDDATE.$error" role="alert" md-auto-hide="false">
								<div ng-message="required">This is required.</div>
							</div>
						</md-input-container>
					</div>

 

					<label class="text-muted d-block mb-3">Select Patient Status</label>
					<md-checkbox ng-model="form.APPOINTMENT" ng-change="SMS_Post()" ng-disabled="ReadOnly" aria-label="Appointment Patient" class="md-primary">Appointment</md-checkbox>
					<md-checkbox ng-model="form.NEWPATIENT" ng-change="SMS_Post()" ng-disabled="ReadOnly" aria-label="New Patient" class="md-primary">New</md-checkbox>
					<md-checkbox ng-model="form.REVISITPATIENT" ng-change="SMS_Post()" ng-disabled="ReadOnly" aria-label="Revisit Patient" class="md-primary">Revisit</md-checkbox>

					<div layout="row">
						<md-switch ng-model="form.POST" aria-label="Post" ng-disabled="ReadOnly || (!form.APPOINTMENT && !form.NEWPATIENT && !form.REVISITPATIENT)" class="md-primary">POST SMS</md-switch>
					</div>
					
					<div layout="column" layout-gt-xs="row" layout-align="end" >
						<md-button ng-disabled="opt.isSubmit || formParent.$invalid || ReadOnly"  type="submit" class="md-raised md-primary">
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

