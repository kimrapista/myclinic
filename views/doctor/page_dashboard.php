<!-- doctor -->

<section ng-controller="PageDashboard" >
	<md-progress-linear ng-hide="opt.isLoaded" md-mode="indeterminate"></md-progress-linear>
	<div class="container">

		<!-- Sales dashboard -->
		<div ng-if="opt.USER.SALES">

			<div layout="row" layout-wrap="">

				<div flex="100" flex-gt-sm="50" >
					<md-card>
						<md-card-header>
							<md-card-header-text>
								<span  class="md-subhead">Today</span>
							</md-card-header-text>
						</md-card-header>
						<md-card-content layout="row" layout-wrap="" >

							<div flex="" class="text-center">
								<h5 ng-bind="summary.served" class="m-0"></h5>
								<small class="text-muted">Served</small>
							</div>
							<div flex="" class="text-center" >
								<h5 ng-bind="(summary.servicesAmount)|number:0" class="m-0"></h5>
								<small class="text-muted">Services</small>
							</div>
							<div flex="" class="text-center"  >
								<h5 ng-bind="(summary.discountAmount)|number:0" class="m-0"></h5>
								<small class="text-muted">Discount</small>
							</div>
							<div flex="" class="text-center" >
								<h5 ng-bind="(summary.hmoAmount)|number:0" class="m-0"></h5>
								<small class="text-muted">HMO</small>
							</div>
						</md-card-content>
					</md-card>	
				</div>

				<div flex="100" flex-gt-xs="50" flex-gt-sm="25" >
					<md-card >
						<md-card-header>
							<md-card-header-text>
								<span class="md-subhead">Patients</span>
							</md-card-header-text>
						</md-card-header>
						<md-card-content layout="row">
							<div flex class="text-center">
								<h5 ng-bind="summary.newPatient" class="m-0">0</h5>
								<small class="text-muted">New</small>
							</div>
							<div flex class="text-center">
								<h5 ng-bind="summary.totalPatient|number:0" class="m-0" >0</h5>
								<small class="text-muted">Total</small>
							</div>
						</md-card-content>
					</md-card>
				</div>

				<div flex="100" flex-gt-xs="50" flex-gt-sm="25" >
					<md-card >
						<md-card-header>
							<md-card-header-text>
								<span class="md-subhead">Appointment</span>
							</md-card-header-text>
						</md-card-header>
						<md-card-content layout="row">
							<div flex class="text-center">
								<h5 ng-bind="summary.appointToday" class="m-0">0</h5>
								<small class="text-muted">Today</small>
							</div>
							<div flex class="text-center">
								<h5 ng-bind="summary.appointUpcoming" class="m-0">0</h5>
								<small class="text-muted">Upcoming</small>
							</div>
						</md-card-content>
					</md-card>
				</div>

				<div flex="100" flex-gt-xs="50">
					<md-card >
						<md-card-header>
							<md-card-header-text layout="row" layout-align="start start">
								<div class="md-subhead" flex>Income this Month</div>
								<div layout="column" layout-align="end end">
									<span flex ng-bind="'Php '+ (monthly.totalservices - monthly.totaldiscounts |number:2)"></span>	
								</div>
							</md-card-header-text>
						</md-card-header>
						<md-card-content>
							<canvas  id="chartMonthlCollectables" ></canvas>
						</md-card-content>
					</md-card>
				</div>

				<div flex="100" flex-gt-xs="50">
					<md-card >
						<md-card-header>
							<md-card-header-text layout="row" layout-align="start start">
								<span class="md-subhead" flex>Served this Month</span>
								<span ng-bind="monthly.totalserved|number:0"></span>
							</md-card-header-text>
						</md-card-header>
						<md-card-content>
							<canvas  id="chartMonthlServed"></canvas>
						</md-card-content>
					</md-card>
				</div>

				<div flex="100"  flex-gt-xs="50">
					<md-card >
						<md-card-header>
							<md-card-header-text layout="row" layout-align="start start">
								<span class="md-subhead" flex>Overall Income</span>
								<span ng-bind="'Php '+ (yearly.netincome|number:2)"></span>
							</md-card-header-text>
						</md-card-header>
						<md-card-content>
							<canvas  id="chartYearlyCollectables"  ></canvas>
						</md-card-content>
					</md-card>
				</div>

				<div flex="100" flex-gt-xs="50">
					<md-card >
						<md-card-header>
							<md-card-header-text  layout="row" layout-align="start start">
								<span class="md-subhead" flex>Medical Records</span>
								<span ng-bind="medical.total|number:0"></span>
							</md-card-header-text>
						</md-card-header>
						<md-card-content>
							<canvas id="chartMedicalHistory" ></canvas>
						</md-card-content>
					</md-card>
				</div>

			
				<div flex="100" flex-gt-xs="50">
					<md-card >
						<md-card-header>
							<md-card-header-text>
								<span class="md-subhead">Patient Statistics</span>
							</md-card-header-text>
						</md-card-header>
						<md-card-content >
							<canvas  id="chartPatientSex" height="80" ></canvas>
							<canvas  id="chartPatientAge" height="100" ></canvas>
						</md-card-content>
					</md-card>
				</div>

				<div flex="100" flex-gt-xs="50">
					<md-card >
						<md-card-header>
							<md-card-header-text>
								<span class="md-subhead">Top ICD</span>
							</md-card-header-text>
						</md-card-header>
						<div class="table-detail padding" style="max-height: 300px;">
							<table>
								<thead>
									<tr>
										<th>Code</th>
										<th>Description</th>
										<th></th>
									</tr>
								</thead>
								<tbody>
									<tr ng-repeat="A in summary.icd|orderBy:['-TOTAL','ITEMCODE']">
										<td ng-bind="A.ITEMCODE"></td>
										<td ng-bind="A.ITEMDESCRIPTION"></td>
										<td ng-bind="A.TOTAL" class="text-center"></td>
									</tr>
								</tbody>
							</table>
						</div>
						<md-card-header>
							<md-card-header-text>
								<span class="md-subhead">Top RVS</span>
							</md-card-header-text>
						</md-card-header>
						<div class="table-detail padding" style="max-height: 300px;">
							<table>
								<thead>
									<tr>
										<th>Code</th>
										<th>Description</th>
										<th></th>
									</tr>
								</thead>
								<tbody>
									<tr ng-repeat="A in summary.rvs|orderBy:['-TOTAL','ITEMCODE']">
										<td ng-bind="A.ITEMCODE"></td>
										<td ng-bind="A.ITEMDESCRIPTION"></td>
										<td ng-bind="A.TOTAL" class="text-center"></td>
									</tr>
								</tbody>
							</table>
						</div>
					</md-card>
				</div>

			</div>

		</div>
		

		<!-- Not Sales dashboard -->
		<div ng-if="!opt.USER.SALES">

			<div layout="row" layout-wrap="" >

				<div flex="100" flex-gt-xs="33"  >
					<md-card>
						<md-card-header>
							<md-card-header-text>
								<span  class="md-subhead">Today</span>
							</md-card-header-text>
						</md-card-header>
						<md-card-content layout="row" layout-wrap="" >

							<div flex="" class="text-center">
								<h5 ng-bind="summary.served" class="m-0"></h5>
								<small class="text-muted">Served</small>
							</div>

						</md-card-content>
					</md-card>	
				</div>

				<div flex="100" flex-gt-xs="33"  >
					<md-card >
						<md-card-header>
							<md-card-header-text>
								<span class="md-subhead">Patients</span>
							</md-card-header-text>
						</md-card-header>
						<md-card-content layout="row">
							<div flex class="text-center">
								<h5 ng-bind="summary.newPatient" class="m-0">0</h5>
								<small class="text-muted">New</small>
							</div>
							<div flex class="text-center">
								<h5 ng-bind="summary.totalPatient|number:0" class="m-0" >0</h5>
								<small class="text-muted">Total</small>
							</div>
						</md-card-content>
					</md-card>
				</div>

				<div flex="100" flex-gt-xs="33"  >
					<md-card >
						<md-card-header>
							<md-card-header-text>
								<span class="md-subhead">Appointment</span>
							</md-card-header-text>
						</md-card-header>
						<md-card-content layout="row">
							<div flex class="text-center">
								<h5 ng-bind="summary.appointToday" class="m-0">0</h5>
								<small class="text-muted">Today</small>
							</div>
							<div flex class="text-center">
								<h5 ng-bind="summary.appointUpcoming" class="m-0">0</h5>
								<small class="text-muted">Upcoming</small>
							</div>
						</md-card-content>
					</md-card>
				</div>

			
				<div flex="100" flex-gt-xs="50">
					<md-card >
						<md-card-header>
							<md-card-header-text layout="row" layout-align="start start">
								<span class="md-subhead" flex>Served this Month</span>
								<span ng-bind="monthly.totalserved|number:0"></span>
							</md-card-header-text>
						</md-card-header>
						<md-card-content>
							<canvas  id="chartMonthlServed"></canvas>
						</md-card-content>
					</md-card>
				</div>

				<div flex="100" flex-gt-xs="50">
					<md-card >
						<md-card-header>
							<md-card-header-text  layout="row" layout-align="start start">
								<span class="md-subhead" flex>Medical Records</span>
								<span ng-bind="medical.total|number:0"></span>
							</md-card-header-text>
						</md-card-header>
						<md-card-content>
							<canvas id="chartMedicalHistory" ></canvas>
						</md-card-content>
					</md-card>
				</div>

				<div flex="100" flex-gt-xs="50">
					<md-card >
						<md-card-header>
							<md-card-header-text>
								<span class="md-subhead">Patient Statistics</span>
							</md-card-header-text>
						</md-card-header>
						<md-card-content >
							<canvas  id="chartPatientSex" height="80" ></canvas>
							<canvas  id="chartPatientAge" height="100" ></canvas>
						</md-card-content>
					</md-card>
				</div>

				<div flex="100" flex-gt-xs="50">
					<md-card >
						<md-card-header>
							<md-card-header-text>
								<span class="md-subhead">Top ICD</span>
							</md-card-header-text>
						</md-card-header>
						<div class="table-detail padding" style="max-height: 300px;">
							<table>
								<thead>
									<tr>
										<th>Code</th>
										<th>Description</th>
										<th></th>
									</tr>
								</thead>
								<tbody>
									<tr ng-repeat="A in summary.icd|orderBy:['-TOTAL','ITEMCODE']">
										<td ng-bind="A.ITEMCODE"></td>
										<td ng-bind="A.ITEMDESCRIPTION"></td>
										<td ng-bind="A.TOTAL" class="text-center"></td>
									</tr>
								</tbody>
							</table>
						</div>
						<md-card-header>
							<md-card-header-text>
								<span class="md-subhead">Top RVS</span>
							</md-card-header-text>
						</md-card-header>
						<div class="table-detail padding" style="max-height: 300px;">
							<table>
								<thead>
									<tr>
										<th>Code</th>
										<th>Description</th>
										<th></th>
									</tr>
								</thead>
								<tbody>
									<tr ng-repeat="A in summary.rvs|orderBy:['-TOTAL','ITEMCODE']">
										<td ng-bind="A.ITEMCODE"></td>
										<td ng-bind="A.ITEMDESCRIPTION"></td>
										<td ng-bind="A.TOTAL" class="text-center"></td>
									</tr>
								</tbody>
							</table>
						</div>
					</md-card>
				</div>

			</div>

		</div>
		

	</div>
</section>

