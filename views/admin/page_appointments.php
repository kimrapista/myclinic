<!-- admin -->
<section ng-controller="PageAppointments" >
	<md-progress-linear ng-hide="opt.isLoaded" md-mode="indeterminate"></md-progress-linear>
	<div class="container-xl">

		<md-card>
			<md-progress-linear ng-show="opt.isSearch && opt.isLoaded" md-mode="query"></md-progress-linear>
			<div class="table-responsive">
				<table>
					<thead>
						<tr>
							<th>Clinic</th>
							<th>Appoint.</th>
							<th>Checkup</th>
							<th>Patient/Age</th>
							<th>Cheif Complaint</th>
							<th>Findings</th>
							<th>Diagnosis</th>
							<th>Doctor</th>
							<th class="text-center">
								<md-button ng-click="Retext_All();" class="md-icon-button">
									<md-icon ng-hide="opt.isRetext" class="material-icons">textsms</md-icon>
									<div ng-show="opt.isRetext" layout="row" layout-align="center">
										<md-progress-circular md-mode="indeterminate" md-diameter="30"></md-progress-circular>
									</div>
								</md-button>
							</th>
						</tr>
					</thead>
					<tbody>
						<tr ng-repeat="A in records"> 
							<td>Clinic</td>
							<td><span ng-bind="A.CLINICNAME"></span></td>
							<td>Appoint.</td>
							<td>
								<div ng-bind="A.APPOINTMENTDATE|date:'MM/dd/y'"></div>
								<div ng-show="A.SUBCLINICNAME"  layout="row">
									<md-icon class="material-icons sm md-warn">near_me</md-icon>
									<small ng-bind="A.SUBCLINICNAME"  flex="auto"></small>
								</div>
								<small ng-bind="A.APPOINTMENTDESCRIPTION" class="text-muted"></small>
							</td>
							<td>Checkup</td>
							<td><span ng-bind="A.CHECKUPDATE|date:'MM/dd/y'"></span></td>
							<td>Patient/Age</td>
							<td>
								<span ng-bind="A.LASTNAME+', '+A.FIRSTNAME+' '+ A.MIDDLENAME"></span> 
								<span ng-bind="A.AGE" class="text-muted"></span>
							</td>
							<td>Cheif Complaint</td>
							<td><pre ng-bind="A.CHEIFCOMPLAINT"></pre></td>
							<td>Findings</td>
							<td><pre ng-bind="A.FINDINGS"></pre></td>
							<td>Diagnosis</td>
							<td><pre ng-bind="A.DIAGNOSIS"></pre></td>
							<td>Doctor</td>
							<td>
								<span ng-bind="A.CREATEDNAME"></span><br>
								<div layout="row">
									<md-icon class="material-icons sm md-primary" >room</md-icon>
									<small ng-bind="A.BRANCH"  flex="auto"></small>
								</div>
							</td>
							<td class="action">
								<md-menu>
									<md-button class="md-primary" ng-click="$mdMenu.open($event)">
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
