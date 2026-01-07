<!-- doctor -->
<section ng-controller="FormLaboratory">
	<md-progress-linear ng-hide="opt.isLoaded" md-mode="indeterminate"></md-progress-linear>
	<div class="container-md">
		<form name="formParent" ng-submit="Submit_Form()" role="form">


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
						<label>Template Field</label>
						<textarea ng-model="form.TEMPLATE" name="TEMPLATE" md-maxlength="500" ></textarea>
						<div ng-messages="formParent.TEMPLATE.$error" role="alert" md-auto-hide="false">
							<div ng-message="required">This is required.</div>
							<div ng-message="md-maxlength">character reach the max limit.</div>
						</div>
					</md-input-container>

					<div layout="column" layout-gt-xs="row" layout-align="end">
						<md-button ng-disabled="opt.isSubmit" type="submit" class="md-raised md-primary">
							<span ng-hide="opt.isSubmit">Save</span>
							<div ng-show="opt.isSubmit" layout="row" layout-align="center">
								<md-progress-circular md-mode="indeterminate" md-diameter="30"></md-progress-circular>
							</div>
						</md-button>

						<md-button href="{{opt.cancelUrl}}" class=" md-accent">Cancel</md-button>
					</div>

				</md-card-content>
			</md-card>

		</form>
	</div>
</section>