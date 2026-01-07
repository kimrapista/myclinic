<!-- DOCTOR -->

<section ng-controller="PageMedical" >
	<md-progress-linear ng-hide="opt.isLoaded" md-mode="indeterminate"></md-progress-linear>
	<div class="container-xl">

		
		<md-card>
			<md-progress-linear ng-show="opt.isSearch && opt.isLoaded" md-mode="query"></md-progress-linear>
			<div class="table-responsive">
				<table>
					<thead>
						<tr>
							<th>Checkup</th>
							<th>Patient/Age</th>
							<th>Chief Complaint</th>
							<th>Findings</th>
							<th>Diagnosis</th>
							<th>Doctor</th>
							<th class="action"></th>
						</tr>
					</thead>
					<tbody>
						<tr ng-repeat="A in records"> 
							<td>Checkup</td>
							<td><span ng-bind="A.CHECKUPDATE|date:'MM/dd/y'"></span></td>
							<td>Patient/Age</td>
							<td>
								<span ng-bind="A.LASTNAME+', '+A.FIRSTNAME+' '+ A.MIDDLENAME"></span> 
								<span ng-bind="A.AGE" class="text-muted"></span>
							</td>
							<td>Chief Complaint</td>
							<td>
								<pre ng-bind="A.CHEIFCOMPLAINT"></pre>

								<div ng-show="A.LABORATORIES.length > 0">
									<md-button ng-click="A.viewLab = !A.viewLab" ng-hide="A.viewLab"  class="md-primary md-raised mx-0" >lab</md-button>
									<div ng-show="A.viewLab">
										<div  ng-repeat="B in A.LABORATORIES" class="mt-3">
											<span ng-bind="B.NAME" class="text-muted"></span>
											<pre ng-bind="B.TEMPLATERESULT" ></pre>
										</div>
										<md-button ng-click="A.viewLab = !A.viewLab" class="md-primary md-raised mx-0" >hide</md-button>
									</div>
								</div>
							</td> 
							<td>Findings</td>
							<td><pre ng-bind="A.FINDINGS"></pre></td>
							<td>Diagnosis</td>
							<td><pre ng-bind="A.DIAGNOSIS"></pre></td>
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
								<div layout="column">
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
									<div>
										<md-button ng-click="MR_Preview($event,A.PATIENTID, A.ID)" class="md-primary" style="min-width: 30px;">
											<md-tooltip md-direction="top">Preview</md-tooltip>
											<md-icon class="material-icons">find_in_page</md-icon>
										</md-button>
									</div>
								</div>
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

