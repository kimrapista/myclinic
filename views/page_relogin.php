<md-dialog aria-label="Medical Record Report" class="dialog-relogin">
	<md-dialog-content class="px-3">

		<div ng-hide="opt.isSignIn">
			<h5 class="mb-3">Relogin</h5>

			<form  name="Form" ng-submit="Submit_Form()" class="my-5">

				<md-input-container class="md-block">
					<label>Username</label>
					<input ng-model="form.USERNAME" ng-disabled="form.USERNAME" type="text" required>
				</md-input-container>

				<md-input-container class="md-block">
					<label>Password</label>
					<input ng-model="form.PASSWORD" type="password" ng-disabled="opt.isSubmit" ng-required="!form.AUTOLOGIN">
				</md-input-container>

				<div layout="column">
					<md-button ng-disabled="opt.isSubmit" type="submit" class="md-raised md-primary">
						<span ng-hide="opt.isSubmit">Sign In</span>
						<div ng-show="opt.isSubmit" layout="row" layout-align="center">
							<md-progress-circular md-mode="indeterminate" md-diameter="30"></md-progress-circular>
						</div>
					</md-button>	
				</div>

				<!-- <md-switch ng-model="form.AUTOLOGIN" ng-click="Reset_Appointment();" ng-disabled="opt.isSubmit" aria-label="Appointment" class="md-primary">
					Auto Sign In 
				</md-switch> -->
			</form>
			

			<div class="text-center text-muted pb-3">
				<small>Internet is interrupted</small>
				<div>or</div> 
				<small>Not active.</small>
			</div>
		</div>

	</md-dialog-content>
</md-dialog>