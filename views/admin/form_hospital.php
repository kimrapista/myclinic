<!-- ADMIN -->
<section  ng-controller="FormHospital"  >
	<md-progress-linear ng-hide="opt.isLoaded" md-mode="indeterminate"></md-progress-linear>
	<div class="container-md">

		<form  name="formParent" ng-submit="Submit_Form()" role="form"   >

			<md-card>
				<md-card-content >

					
					<md-input-container class="md-block">
						<label>Name</label>
						<input ng-model="f.NAME" autofocus required>
					</md-input-container>

					<md-input-container class="md-block">
						<label>Acronym</label>
						<input ng-model="f.CODE" >
					</md-input-container>

					<md-input-container class="md-block">
						<label>PMCC</label>
						<input ng-model="f.PMCC" >
					</md-input-container>
					


					<div layout="column" layout-gt-xs="row">
						<md-input-container flex-gt-xs="80" class="md-block">
							<label>Address</label>
							<input ng-model="f.ADDRESS" >
						</md-input-container>
						<md-input-container flex-gt-xs="30" class="md-block">
							<label>Zip Code</label>
							<input ng-model="f.ZIPCODE" >
						</md-input-container>
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

