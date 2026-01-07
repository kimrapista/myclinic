<!-- ASSISTANT -->

<section ng-controller="PageDashboard" >
	<md-progress-linear ng-hide="opt.isLoaded" md-mode="indeterminate"></md-progress-linear>
	<div class="container">

	
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

			<div flex="100">
				<md-card >
					<md-card-header>
						<md-card-header-text layout="row" layout-align="start start">
							<span class="md-subhead" flex>Served this Month</span>
							<span ng-bind="monthly.totalserved|number:0"></span>
						</md-card-header-text>
					</md-card-header>
					<md-card-content>
						<canvas  id="chartMonthlServed" height="100"></canvas>
					</md-card-content>
				</md-card>
			</div>

			<div flex="100">
				<md-card >
					<md-card-header>
						<md-card-header-text>
							<span class="md-subhead" flex>Patient Queue</span>
						</md-card-header-text>
					</md-card-header>
					<md-card-content>
						<div class="text-muted">Patient queue still in development</div>
					</md-card-content>
				</md-card>
			</div>

		</div>


	</div>
</section>

