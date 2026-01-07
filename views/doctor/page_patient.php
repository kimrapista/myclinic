<!-- DOCTOR -->

<section ng-controller="PagePatient" >
	<md-progress-linear ng-hide="opt.isLoaded" md-mode="indeterminate"></md-progress-linear>
	<div class="container-xl">


		<md-button ng-show="opt.isLoaded" href="{{opt.newMRUrl}}" class="md-fab md-primary md-fab-bottom-right fixed" >
			<md-icon class="material-icons">add</md-icon>
		</md-button>

		<md-card >
			<md-card-header>
				<md-card-avatar>
					<img src="{{opt.avatarUrl}}" class="md-user-avatar" />
				</md-card-avatar>
				<md-card-header-text >
					<div ng-bind="PATIENT.LASTNAME+', '+PATIENT.FIRSTNAME+' '+PATIENT.MIDDLENAME" class="md-title"></div>
					<span ng-bind="'ID: '+ PATIENT.ID" class="md-subhead"></span>
				</md-card-header-text>
				<md-menu ng-show="opt.isLoaded"  md-position-mode="target-right target" class="md-fab-top-right"> 
					<md-button class="btn-icon md-primary" ng-click="$mdMenu.open($event)" >
						<md-icon class="material-icons">more_vert</md-icon>
					</md-button>
					<md-menu-content>
						<md-menu-item >
							<md-button href="{{opt.editPatientUrl}}">
								<md-icon class="material-icons">edit</md-icon>
								Edit
							</md-button>
						</md-menu-item>
						<md-menu-item >
							<md-button ng-click="Remove_Patient($event);" >
								<md-icon class="material-icons">delete</md-icon>   
								Delete
							</md-button>
						</md-menu-item>
					</md-menu-content>
				</md-menu>
			</md-card-header>
			<md-tabs md-dynamic-height md-border-bottom >
				<md-tab label="MEDICAL RECORDS [{{PATIENT.MEDICALS.length}}]">

					<div class="table-responsive">
						<table>
							<thead>
								<tr>
									<th>Checkup</th>
									<th>Age</th>
									<th>Chief Complaint</th>
									<th>Findings</th>
									<th>Diagnosis</th>
									<th>Doctor</th>
									<th class="action"></th>
								</tr>
							</thead>
							<tbody>
								<tr ng-repeat="(key,A) in PATIENT.MEDICALS"> 
									<td>Checkup</td>
									<td><span ng-bind="A.CHECKUPDATE|date:'MM/dd/y'"></span></td>
									<td>Age</td>
									<td><span ng-bind="A.AGE"></span></td>
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

										<div ng-show="A.APPOINTMENT" >
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
												<md-menu-content>
													<md-menu-item>
														<md-button href="{{opt.editMRUrl + A.ID}}" >
															<md-icon class="material-icons">edit</md-icon>
															Edit
														</md-button>
													</md-menu-item>
													<md-menu-item>
														<md-button ng-click="Medical_Record_Report($event,A.ID)">
															<md-icon class="material-icons">print</md-icon>
															Print
														</md-button>
													</md-menu-item>
													<md-menu-item>
														<md-button ng-click="Remove_Record($event,PATIENT.TOKEN,A.ID,key)">
															<md-icon class="material-icons">delete</md-icon>
															Delete
														</md-button>
													</md-menu-item>
												</md-menu-content>
											</md-menu>
											<div>
												<md-button ng-click="MR_Preview($event,A.ID)" class="md-primary" style="min-width: 30px;">
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

				</md-tab>
				<md-tab label="OTHERS [{{PATIENT.OTHERS.length}}]" >
					<div ng-show="PATIENT.OTHERS.length > 0" layout-padding="" class="custom-list-item">
						<div ng-repeat="A in PATIENT.OTHERS | orderBy: ['-CHECKUPDATE']" >

							<div layout="row" layout-wrap="">
								<div flex="25" flex-gt-xs="15"><label class="text-muted">Clinic</label></div>
								<div flex="75" flex-gt-xs="75"><span ng-bind="A.CLINICNAME"></span></div>
							</div>

							<div layout="row" layout-wrap="">
								<div flex="25" flex-gt-xs="15"><label class="text-muted">Check Up</label></div>
								<div flex="75" flex-gt-xs="75"><span ng-bind="A.CHECKUPDATE|date:'MM/dd/y'"></span></div>
							</div>

							<div layout="row" layout-wrap="">
								<div flex="25" flex-gt-xs="15"><label class="text-muted">Cheif Complaint</label></div>
								<div flex="75" flex-gt-xs="85"><pre  ng-bind="A.CHEIFCOMPLAINT"></pre></div>
							</div>
						</div>
					</div>
					<div ng-show="PATIENT.OTHERS.length == 0" layout-padding="">
						<p class="text-muted">No Chief Complaint found in other clinic</p>
					</div>
				</md-tab>
				<md-tab label="INFO">
					<div layout="row" layout-wrap="" layout-padding="">
						<div flex="100" flex-gt-xs="25" >
							<span class="text-muted">Date of Birth</span>
							<div ng-bind="PATIENT.DOB | date: 'MM/dd/y'" ></div>
						</div>
						<div flex="100" flex-gt-xs="25">
							<span class="text-muted">Sex</span>
							<div ng-bind="PATIENT.SEX" ></div>
						</div>
						<div flex="100" flex-gt-xs="25">
							<span class="text-muted">Blood type</span>
							<div ng-bind="PATIENT.BLOODTYPE" ></div>
						</div>
						<div flex="100" flex-gt-xs="25">
							<span class="text-muted">Place of Birth</span>
							<div ng-bind="PATIENT.POB" ></div>
						</div>
						<div flex="100" flex-gt-xs="25">
							<span class="text-muted">Nationality</span>
							<div ng-bind="PATIENT.NATIONALITY" ></div>
						</div>
						<div flex="100" flex-gt-xs="25">
							<span class="text-muted">Religion</span>
							<div ng-bind="PATIENT.RELIGION" ></div>
						</div>
						<div flex="100" flex-gt-xs="25">
							<span class="text-muted">Civil Status</span>
							<div ng-bind="PATIENT.CIVILSTATUS" ></div>
						</div>
						<div flex="100" flex-gt-xs="25">
							<span class="text-muted">Occupation</span>
							<div ng-bind="PATIENT.OCCUPATION" ></div>
						</div>
						<div flex="100" flex-gt-xs="25">
							<span class="text-muted">Street No.</span>
							<div ng-bind="PATIENT.STREETNO" ></div>
						</div>
						<div flex="100" flex-gt-xs="25">
							<span class="text-muted">City</span>
							<div ng-bind="PATIENT.CITY" ></div>
						</div>
						<div flex="100" flex-gt-xs="25">
							<span class="text-muted">Province</span>
							<div ng-bind="PATIENT.PROVINCE" ></div>
						</div>
						<div flex="100" flex-gt-xs="25">
							<span class="text-muted">Tel No.</span>
							<div ng-bind="PATIENT.PHONENO" ></div>
						</div>
						<div flex="100" flex-gt-xs="25">
							<span class="text-muted">Mobile No.</span>
							<div ng-bind="PATIENT.MOBILENO" ></div>
						</div>
						<div flex="100" flex-gt-xs="25">
							<span class="text-muted">Date Registered</span>
							<div ng-bind="PATIENT.DATEREG | date: 'MM/dd/y'" ></div>
						</div>
						<div ng-show="PATIENT.HRID != ''" flex="100" flex-gt-xs="25">
							<span class="text-muted">Hospital Record ID</span>
							<div ng-bind="PATIENT.HRID" ></div>
						</div>
					</div>
				</md-tab>
			</md-tabs>
		</md-card>


	</div>
</section>


