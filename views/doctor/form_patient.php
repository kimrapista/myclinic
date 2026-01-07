<!-- doctor -->
<section ng-controller="FormPatient" >
	<md-progress-linear ng-hide="opt.isLoaded" md-mode="indeterminate"></md-progress-linear>
	<div class="container">

		<form name="formParent" ng-submit="Submit_Form()" role="form">

			<md-card>
				<md-card-content>

					<div layout="column" layout-gt-xs="row">
						<md-input-container flex-gt-xs="50" class="md-block">
							<label>First Name</label>
							<input ng-model="f.FIRSTNAME" name="FIRSTNAME" autofocus required>
							<div ng-messages="formParent.FIRSTNAME.$error" role="alert" md-auto-hide="false">
								<div ng-message="required">This is required.</div>
							</div>
						</md-input-container>
						<md-input-container flex-gt-xs="50" class="md-block">
							<label>Middle Name</label>
							<input ng-model="f.MIDDLENAME" name="MIDDLENAME" min="2" >
							<div ng-show="f.MIDDLENAME.length < 3" md-colors="{color:'accent'}">
								<small>Fillup with full middle name.</small>
							</div>
						</md-input-container>
						<md-input-container flex-gt-xs="50" class="md-block">
							<label>Last Name</label>
							<input ng-model="f.LASTNAME" name="LASTNAME" required>
							<div ng-messages="formParent.LASTNAME.$error" role="alert" md-auto-hide="false">
								<div ng-message="required">This is required.</div>
							</div>
						</md-input-container>
					</div>

					<div layout="row" layout-wrap="">
						<md-input-container flex="50" flex-gt-xs="33" >
							<label>DATE OF BIRTH</label>
							<input ng-model="f.DOB" name="DOB" type="date" required>
							<div ng-messages="formParent.DOB.$error" role="alert" md-auto-hide="false">
								<div ng-message="required">This is required.</div>
							</div>
						</md-input-container>
						<md-input-container flex="50" flex-gt-xs="33"  >
							<label>SEX</label>
							<md-select ng-model="f.SEX" name ="SEX" required>
								<md-option value="FEMALE">FEMALE</md-option>
								<md-option value="MALE">MALE</md-option>
							</md-select>
							<div ng-messages="formParent.SEX.$error" role="alert" md-auto-hide="false">
								<div ng-message="required">This is required.</div>
							</div>
						</md-input-container>
						<md-input-container flex="100"  flex-gt-xs="33"  class="md-block">
							<label>BLOOD TYPE</label>
							<md-select ng-model="f.BLOODTYPE" >
								<md-option value=""></md-option>
								<md-option value="A">A</md-option>
								<md-option value="A-">A-</md-option>
								<md-option value="A+">A+</md-option>
								<md-option value="B">B</md-option>
								<md-option value="B-">B-</md-option>
								<md-option value="B+">B+</md-option>
								<md-option value="AB">AB</md-option>
								<md-option value="AB-">AB-</md-option>
								<md-option value="AB+">AB+</md-option>
								<md-option value="O">O</md-option>
								<md-option value="O-">O-</md-option>
								<md-option value="O+">O+</md-option>
							</md-select>
						</md-input-container>
					</div>

					<div layout="column" layout-gt-xs="row">
						<md-input-container flex-gt-xs="50" class="md-block">
							<label>PLACE OF BIRTH</label>
							<input ng-model="f.POB"  >
						</md-input-container>
						<md-input-container flex-gt-xs="50" class="md-block">
							<label>NATIONALITY</label>
							<input ng-model="f.NATIONALITY"  >
						</md-input-container>
						<md-input-container flex-gt-xs="50" class="md-block">
							<label>RELIGION</label>
							<input ng-model="f.RELIGION"  >
						</md-input-container>
					</div>

					<div layout="column" layout-gt-xs="row">
						<md-input-container  flex="100"  flex-gt-xs="33"  class="md-block" >
							<label>Civil Status</label>
							<md-select ng-model="f.CIVILSTATUS" name="CIVILSTATUS"  required>
								<md-option value=""></md-option>
								<md-option value="MARRIED">MARRIED</md-option>
								<md-option value="WIDOWED">WIDOWED</md-option>
								<md-option value="SEPARATED">SEPARATED</md-option>
								<md-option value="DIVORCED">DIVORCED</md-option>
								<md-option value="SINGLE">SINGLE</md-option>
								<md-option value="CHILD">CHILD</md-option>
							</md-select>
							<div ng-messages="formParent.CIVILSTATUS.$error" role="alert" md-auto-hide="false">
								<div ng-message="required">This is required.</div>
							</div>
						</md-input-container>

						<md-input-container  flex="100"  flex-gt-xs="33" class="md-block">
							<label>OCCUPATION</label>
							<input ng-model="f.OCCUPATION"  >
						</md-input-container>
					</div>

					

				</md-card-content>

				<md-card-header>
					<md-card-header-text>
						<span class="md-subhead">Contact Infomation</span>
					</md-card-header-text>
				</md-card-header>

				<md-card-content>

					<div layout="column" layout-gt-xs="row">
						<md-input-container flex-gt-xs="50" class="md-block">
							<label>STREET NO.</label>
							<input ng-model="f.STREETNO"  >
						</md-input-container>
						<md-input-container flex-gt-xs="50" class="md-block">
							<label>CITY</label>
							<input ng-model="f.CITY"  >
						</md-input-container>
						<md-input-container flex-gt-xs="50" class="md-block">
							<label>PROVINCE</label>
							<input ng-model="f.PROVINCE"  >
						</md-input-container>
					</div>


					<div layout="column" layout-gt-xs="row">
						<md-input-container flex-gt-xs="50" class="md-block">
							<label>TEL NO.</label>
							<input ng-model="f.PHONENO"  >
						</md-input-container>
						<md-input-container flex-gt-xs="50" class="md-block">
							<label>MOBILE NO.</label>
							<input ng-model="f.MOBILENO" name="MOBILENO"  maxlength="11" required >
							<div ng-messages="formParent.MOBILENO.$error" role="alert" md-auto-hide="false">
								<div ng-message="required">This is required.</div>
								<div ng-message="maxlength">Ex. 09123456789</div>
							</div>
						</md-input-container>
						<md-input-container flex-gt-xs="50" class="md-block">
							<label>EMAIL</label>
							<input ng-model="f.EMAIL" name="EMAIL" type="email"  >
						</md-input-container>
					</div>

					

				</md-card-content>

				<md-card-header>
					<md-card-header-text>
						<span class="md-subhead">Emergency Infomation</span>
					</md-card-header-text>
				</md-card-header>

				<md-card-content>

					<div layout="column" layout-gt-xs="row">
						<md-input-container flex-gt-xs="35" class="md-block">
							<label>NAME</label>
							<input ng-model="f.EMERGENCYCONTACT"  >
						</md-input-container>
						<md-input-container flex-gt-xs="auto" class="md-block">
							<label>ADDRESS</label>
							<input ng-model="f.EMERGENCYADDRESS"  >
						</md-input-container>
					</div>


					<div layout="column" layout-gt-xs="row">
						<md-input-container flex-gt-xs="35" class="md-block">
							<label>TEL NO.</label>
							<input ng-model="f.EMERGENCYPHONENO"  >
						</md-input-container>
						<md-input-container flex-gt-xs="35" class="md-block">
							<label>MOBILE NO.</label>
							<input ng-model="f.EMERGENCYMOBILENO"  >
						</md-input-container>
					</div>


					<div layout="column" layout-gt-xs="row">
						<div>
							<md-switch ng-model="f.REVISIT" aria-label="POST" class="md-primary" >
								<div>Re-visit Patient</div>
								<small class="text-muted">Base date registered {{f.DATEREG|date: 'M/d/y'}}</small>
							</md-switch>
						</div>
						<div flex="100" class="pt-3">
							<div layout="column" layout-gt-xs="row" layout-align="end">
								<md-button ng-disabled="opt.isSubmit || formParent.$invalid"  type="submit" class="md-raised md-primary">
									<span ng-hide="opt.isSubmit">Save</span>
									<div ng-show="opt.isSubmit" layout="row" layout-align="center">
										<md-progress-circular md-mode="indeterminate" md-diameter="30"></md-progress-circular>
									</div>
								</md-button>
								<md-button href="{{opt.cancelUrl}}"  class=" md-accent" >Cancel</md-button>
							</div>
						</div>
					</div>


				</md-card-content>
			</md-card>


		</form>

	</div>
</section>

