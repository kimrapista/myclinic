<!-- admin -->
<section  ng-controller="FormSpecialist" >
	<md-progress-linear ng-hide="opt.isLoaded" md-mode="indeterminate"></md-progress-linear>
	<div class="container-xs">
		<form  name="formParent" ng-submit="Submit_Form()" role="form"  >


			<md-card>
				<md-card-content>

					<md-input-container class="md-block">
						<label>Specialty</label>
						<input ng-model="form.SPECIALTY" name="SPECIALTY" type="text" required autofocus>
						<div ng-messages="formParent.SPECIALTY.$error" role="alert" md-auto-hide="false">
							<div ng-message="required">This is required.</div>
						</div>
					</md-input-container>


					<md-input-container class="md-block" >
						<label>Group</label>
						<md-select ng-model="form.SPECIALTYGROUP" ng-change="form.SPECIALTYGROUP = Check_Select_Value(form.SPECIALTYGROUP);" name="SPECIALTYGROUP">
							<md-option value=""></md-option>
							<md-option value="D">Diagnostic</md-option>
							<md-option value="M">Medicine</md-option>
							<md-option value="S">Surgery</md-option>
						</md-select>
						<div ng-messages="formParent.SPECIALTYGROUP.$error" role="alert" md-auto-hide="false">
							<div ng-message="required">This is required.</div>
						</div>
					</md-input-container>


					<md-input-container class="md-block">
						<label>Specialty practice</label>
						<input ng-model="form.SPECIALTYPRACTICE" name="SPECIALTYPRACTICE" type="text" >
						<div ng-messages="formParent.SPECIALTYPRACTICE.$error" role="alert" md-auto-hide="false">
							<div ng-message="required">This is required.</div>
						</div>
					</md-input-container>


					<md-input-container class="md-block">
						<label>Specialist titles</label>
						<input ng-model="form.SPECIALTYTITLE" name="SPECIALTYTITLE" type="text">
						<div ng-messages="formParent.SPECIALTYTITLE.$error" role="alert" md-auto-hide="false">
							<div ng-message="required">This is required.</div>
						</div>
					</md-input-container>


					<md-input-container class="md-block" >
						<label>Age range of patients</label>
						<md-select ng-model="form.AGEPATIENT" ng-change="form.AGEPATIENT = Check_Select_Value(form.AGEPATIENT);" name="AGEPATIENT" >
							<md-option value=""></md-option>
							<md-option value="ALL">ALL</md-option>
							<md-option value="PEDIATRIC">Pediatric</md-option>
							<md-option value="ADULTS">Adults</md-option>
							<md-option value="GERIATRIC">Geriatric</md-option>
							<md-option value="NEONATAL">Neonatal</md-option>
						</md-select>
						<div ng-messages="formParent.AGEPATIENT.$error" role="alert" md-auto-hide="false">
							<div ng-message="required">This is required.</div>
						</div>
					</md-input-container>


					<md-input-container class="md-block" >
						<label>Diagnostics(D) or Therapeutic(T) specialty</label>
						<md-select ng-model="form.DIAG_THERA" ng-change="form.DIAG_THERA = Check_Select_Value(form.DIAG_THERA);" name="DIAG_THERA" >
							<md-option value=""></md-option>
							<md-option value="B">Both</md-option>
							<md-option value="D">Diagnostics</md-option>
							<md-option value="T">Therapeutic</md-option>
							<md-option value="N">Neither</md-option>
						</md-select>
						<div ng-messages="formParent.DIAG_THERA.$error" role="alert" md-auto-hide="false">
							<div ng-message="required">This is required.</div>
						</div>
					</md-input-container>


					<md-input-container class="md-block" >
						<label>Surgical(S) or Internal Medicine(I) specialty</label>
						<md-select ng-model="form.SURG_IM" ng-change="form.SURG_IM = Check_Select_Value(form.SURG_IM);" name="SURG_IM"  >
							<md-option value=""></md-option>
							<md-option value="B">Both</md-option>
							<md-option value="S">Surgical</md-option>
							<md-option value="I">Internal Medicine</md-option>
							<md-option value="N">Neither</md-option>
						</md-select>
						<div ng-messages="formParent.SURG_IM.$error" role="alert" md-auto-hide="false">
							<div ng-message="required">This is required.</div>
						</div>
					</md-input-container>

					<md-input-container class="md-block" >
						<label>Organ-based(O) or Technique-based(T) specialty</label>
						<md-select ng-model="form.ORG_TECH" ng-change="form.ORG_TECH = Check_Select_Value(form.ORG_TECH);" name="ORG_TECH" >
							<md-option value=""></md-option>
							<md-option value="B">Both</md-option>
							<md-option value="O">Organ-based</md-option>
							<md-option value="T">Technique-based</md-option>
							<md-option value="N">Neither</md-option>
							<md-option value="M">Multidisciplinary</md-option>
						</md-select>
						<div ng-messages="formParent.ORG_TECH.$error" role="alert" md-auto-hide="false">
							<div ng-message="required">This is required.</div>
						</div>
					</md-input-container>


					<div layout="column" layout-gt-xs="row" layout-align="end" >
						<md-button ng-disabled="opt.isSubmit  || formParent.$invalid"  type="submit" class="md-raised md-primary">
							<span ng-hide="opt.isSubmit">Save</span>
							<div ng-show="opt.isSubmit" layout="row" layout-align="center">
								<md-progress-circular md-mode="indeterminate" md-diameter="30"></md-progress-circular>
							</div>
						</md-button>

						<md-button href="{{opt.cancelUrl}}"  class=" md-primary" >Cancel</md-button>
					</div>

				</md-card-content>
			</md-card>

		</form>
	</div>
</section>

