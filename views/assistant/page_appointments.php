<!-- ASSISTANT -->
<section ng-controller="PageAppointment" >
	<md-progress-linear ng-hide="opt.isLoaded" md-mode="indeterminate"></md-progress-linear>
	<div class="container">

		<md-card>
			<md-progress-linear ng-show="opt.isSearch && opt.isLoaded" md-mode="query"></md-progress-linear>
			<div class="table-responsive">
				<table>
					<thead>
						<tr>
							<th>Appoint.</th>
							<th>Checkup</th>
							<th>Patient</th>
							<th>Doctor</th>
							<th class="action"></th>
						</tr>
					</thead>
					<tbody>
						<tr ng-repeat="A in records"> 
							<td>Appoint.</td>
							<td>
								<div ng-bind="A.APPOINTMENTDATE|date:'MM/dd/y'"></div>
								<div ng-show="A.APPOINTCLINIC"  layout="row">
									<md-icon class="material-icons sm md-warn">near_me</md-icon>
									<small ng-bind="A.APPOINTCLINIC"  flex="auto"></small>
								</div>
								<small ng-bind="A.APPOINTMENTDESCRIPTION" class="text-muted"></small>
							</td>
							<td>Checkup</td>
							<td><span ng-bind="A.CHECKUPDATE|date:'MM/dd/y'"></span></td>
							<td>Patient</td>
							<td>
								<span ng-bind="A.LASTNAME+', '+A.FIRSTNAME+' '+ A.MIDDLENAME"></span> 
							</td>
							<td>Doctor</td>
							<td>
								<span ng-bind="A.CREATEDNAME"></span><br>
								<div layout="row">
									<md-icon class="material-icons sm md-primary" >room</md-icon>
									<small ng-bind="A.FROMCLINIC"  flex="auto"></small>
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
								No appointment.
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
