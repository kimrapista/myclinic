<!-- ASSISTANT -->

<section ng-controller="PageMedical" >
	<md-progress-linear ng-hide="opt.isLoaded" md-mode="indeterminate"></md-progress-linear>
	<div class="container-lg">

		
		<md-card>
			<md-progress-linear ng-show="opt.isSearch && opt.isLoaded" md-mode="query"></md-progress-linear>
			<div class="table-responsive">
				<table>
					<thead>
						<tr>
							<th>Checkup</th>
							<th>Patient</th>
							<th ng-if="opt.AUTHORIZATION">Medical Info</th>
							<th>Billing</th>
							<th>Doctor</th>
							<th class="action"></th>
						</tr>
					</thead>
					<tbody>
						<tr ng-repeat="A in records"> 
							<td>Checkup</td>
							<td><span ng-bind="A.CHECKUPDATE|date:'MM/dd/y'"></span></td>
							<td>Patient</td>
							<td><span ng-bind="A.LASTNAME+', '+A.FIRSTNAME+' '+ A.MIDDLENAME"></span></td>
							<td ng-if="opt.AUTHORIZATION">Medical Info</td>
							<td ng-if="opt.AUTHORIZATION">

								<span class="text-muted">Chief Complaint</span>
								<pre ng-bind="A.CHEIFCOMPLAINT"></pre>

								<div ng-show="A.LABORATORIES.length > 0" class="my-3">
									<md-button ng-click="A.viewLab = !A.viewLab" ng-hide="A.viewLab"  class="md-primary md-raised mx-0" >lab</md-button>
									<div ng-show="A.viewLab">
										<div  ng-repeat="B in A.LABORATORIES" class="mt-3">
											<span ng-bind="B.NAME" class="text-muted"></span>
											<pre ng-bind="B.TEMPLATERESULT" ></pre>
										</div>
										<md-button ng-click="A.viewLab = !A.viewLab" class="md-primary md-raised mx-0" >hide</md-button>
									</div>
								</div>

								<span class="text-muted">Findings</span>
								<pre ng-bind="A.FINDINGS"></pre>

								<span class="text-muted">Diagnosis</span>
								<pre ng-bind="A.DIAGNOSIS"></pre>

							</td>
							<td>Billing</td>
							<td style="min-width: 200px;">
								<div class="row no-gutters">
									<div class="col-6 text-muted">Services</div>
									<div class="col-6 text-right" ng-bind="A.GROSSAMOUNT|number:2"></div>
								</div>
								<div class="row no-gutters">
									<div class="col-6 text-muted">Discount</div>
									<div class="col-6 text-right" ng-bind="A.DISCOUNTAMOUNT|number:2"></div>
								</div>
								<md-divider></md-divider>
								<div class="row no-gutters">
									<div class="col-6 text-muted">Net Payables</div>
									<div class="col-6 text-right" ng-bind="A.NETPAYABLES|number:2"></div>
								</div>
								<div class="row no-gutters">
									<div class="col-6 text-muted">HMO</div>
									<div class="col-6 text-right" ng-bind="A.HMOAMOUNT|number:2"></div>
								</div>
								<md-divider></md-divider>
								<div class="row no-gutters">
									<div class="col-6 text-muted">Paid</div>
									<div class="col-6 text-right" ng-bind="A.PAIDAMOUNT|number:2"></div>
								</div>
							</td>
							<td>Doctor</td>
							<td>

								<div class="mb-3">
									<small ng-show="A.CREATEDNAME" ng-bind="A.CREATEDNAME"></small>
									<small ng-hide="A.CREATEDNAME" class="text-muted">Prepared MR</small>
									<div layout="row" layout-align="start start">
										<div>
											<md-icon class="material-icons sm md-primary" >room</md-icon>	
										</div>
										<div>
											<small ng-bind="A.FROMCLINIC" class="d-block"></small>	
										</div>
									</div>
								</div>

								<div ng-show="A.APPOINTMENT == 'Y'" >
									<md-tooltip md-direction="top">Appointment</md-tooltip>
									<div layout="row" layout-align="start start">
										<div>
											<md-icon class="material-icons sm md-warn">near_me</md-icon>
										</div>
										<div>
											<small ng-bind="A.APPOINTCLINIC" class="d-block"></small>	
											<small  ng-bind="A.APPOINTMENTDATE|date:'MM/dd/y'"></small>
											<small ng-bind="A.APPOINTMENTDESCRIPTION" class="text-muted"></small>
										</div>
									</div>
								</div>
							</td>

							<td class="action">
								<md-menu>
									<md-button class="md-primary btn-icon" ng-click="$mdMenu.open($event)">
										<md-icon class="material-icons">more_vert</md-icon>
									</md-button>
									<md-menu-content width="4">
										<md-menu-item>
											<md-button href="{{opt.viewMRUrl + A.PATIENTID +'/medical-record/' + A.ID}}" >
												View Medical Record
											</md-button>
										</md-menu-item>
										<md-menu-item >
											<md-button href="{{opt.viewPatientUrl + A.PATIENTID}}">
												View Patient Records
											</md-button>
										</md-menu-item>
									</md-menu-content>
								</md-menu>
							</td>
						</tr>
					</tbody>
					<tfoot>
						<tr ng-show="records.length == 0 && opt.searchText == '' ">
							<td colspan="10">
								No medical records.
							</td>
						</tr>
						<tr ng-show="records.length == 0 && opt.searchText != '' ">
							<td colspan="10">
								Search not found "{{opt.searchText}}".
							</td>
						</tr>
					</tfoot>
				</table>
			</div>
		</md-card>


	</div>
</section>

