<!-- doctor -->

<section ng-controller="PageClinic" >
	<md-progress-linear ng-hide="opt.isLoaded" md-mode="indeterminate"></md-progress-linear>
	<div class="container-md">

		<md-button ng-show="opt.isLoaded && !opt.isDisabled" href="{{opt.newSubUrl}}" class="md-fab md-primary md-fab-bottom-right fixed" >
			<md-icon class="material-icons">add</md-icon>
		</md-button>
 
		<md-card>

			<md-button ng-hide="opt.isDisabled" href="{{opt.editUrl}}" class="btn-icon md-primary md-fab-top-right" >
				<md-icon class="material-icons">edit</md-icon>
			</md-button>
			<md-tabs md-dynamic-height md-border-bottom md-center-tabsx>
				<md-tab label="Clinic"> 
					<md-card-content >

						<span class="text-muted">Clinic Title</span>
						<p ng-bind="CLINIC.CLINICNAME"></p>

						<span class="text-muted">Specialist 1</span>
						<p ng-bind="CLINIC.CLINICSUBNAME"></p>

						<span class="text-muted">Specialist 2</span>
						<p ng-bind="CLINIC.CLINICSUBNAME1"></p>

						<div layout="column" layout-gt-xs="row">
							<div flex>
								<span class="text-muted">TIN</span>
								<p ng-bind="CLINIC.TIN"></p>
							</div>
							<div flex>
								<span class="text-muted">Tel No.</span>
								<p ng-bind="CLINIC.CONTACTNO"></p>
							</div>
							<div flex>
								<span class="text-muted">Mobile No.</span>
								<p ng-bind="CLINIC.MOBILENO"></p>
							</div>
						</div>

						<span class="text-muted">Email</span>
						<p ng-bind="CLINIC.EMAIL"></p>

						<span class="text-muted">Address</span>
						<p ng-bind="CLINIC.ADDRESS"></p>

					</md-card-content>
				</md-tab>
				<md-tab label="Referral"> 
					<md-card-content>
						<span class="text-muted">Default Referral message</span>
						<pre ng-bind="CLINIC.REFERRALDEFAULTTEXT"></pre>
					</md-card-content>
				</md-tab>
				<md-tab label="Clearance"> 
					<md-card-content>
						<span class="text-muted">Default Clearance message</span>
						<pre ng-bind="CLINIC.CLEARANCEDEFAULTTEXT"></pre>
					</md-card-content>
				</md-tab>
			</md-tabs>
		</md-card>


		<md-card>
			<div class="table-responsive">
				<table>
					<thead>
						<tr>
							<th colspan="5">CLINICS LOCATION</th>
						</tr>
						<tr>
							<th>SUBCLINIC</th>
							<th>Hospital</th>
							<th>Room</th>
							<th></th>
						</tr>
					</thead>
					<tbody>
						<tr ng-repeat="(key,A) in CLINIC.SUBCLINICS"> 
							<td>NAME</td>
							<td ng-bind="A.NAME"></td>
							<td>Hospital</td>
							<td ng-bind="A.HOSPITALNAME"></td>
							<td>Room</td>
							<td ng-bind="A.LOCATION"></td>
							<td class="action">
								<md-button ng-show="opt.isLoaded && !opt.isDisabled" href="{{opt.editSubUrl+ A.ID}}" class="btn-icon md-primary" >
									<md-icon class="material-icons">edit</md-icon>
								</md-button>
							</td>
						</tr>
					</tbody>
				</table>
			</div>
		</md-card>


	</div>
</section>



