<!-- doctor -->
<section ng-controller="PageSales" >
	<md-progress-linear ng-hide="opt.isLoaded" md-mode="indeterminate"></md-progress-linear>
	<md-progress-linear ng-show="opt.isSearch && opt.isLoaded" md-mode="query"></md-progress-linear>
	<div class="container">

		<div layout="column" layout-gt-xs="row" >
			<md-card flex="100" flex-gt-xs="33">
				<md-card-header>
					<md-card-header-text>
						<span class="md-subhead">SUMMARY</span>
					</md-card-header-text>
					<md-button ng-click="Summary_Report($event)" class="btn-icon md-primary md-fab-top-right" >
						<md-icon class="material-icons">print</md-icon>
					</md-button>
				</md-card-header>
				<div class="table-detail padding">
					<table>
						<thead>
							<tr>
								<th>Clinic</th>
								<th>RECORD</th>
								<th class="text-right">Amount</th>
							</tr>
						</thead>
						<tbody>
							<tr ng-repeat="r in SUMMARY">
								<td ng-bind="r.FROMCLINIC"></td>
								<td ng-bind="r.MRCOUNT" class="text-center"></td>
								<td ng-bind="r.NETPAYABLES | number : 2" class="text-right"></td>
							</tr>
						</tbody>
					</table>
				</div>
				<md-card-content layout="row">
					<span flex="" class="text-muted">Records ({{TOTALRECORD}})</span>
					<div ng-bind="TOTALSUMMARY| number : 2" class="text-right text-muted"></div>
				</md-card-content>
			</md-card>


			<md-card flex="100" flex-gt-xs="66">
				<md-card-header>
					<md-card-header-text>
						<span class="md-subhead">SERVICES</span>
					</md-card-header-text>
					<!-- <md-button ng-click="Summary_Report($event)" class="btn-icon md-primary md-fab-top-right" >
						<md-icon class="material-icons">print</md-icon>
					</md-button> -->
				</md-card-header>
				<div  layout="column" layout-gt-xs="row">
					<div flex="" class="table-detail padding" style="max-height: 300px;">
						<table>
							<thead>
								<tr>
									<th colspan="2">Top Ordered</th>
								</tr>
								<!-- <tr>
									<th>Name</th>
									<th>Qty</th>	
								</tr> -->
							</thead>
							<tbody>
								<tr ng-repeat="r in SERVICES | orderBy: ['-QUANTITY']">
									<td ng-bind="r.NAME"></td>
									<td ng-bind="r.QUANTITY" class="text-center"></td>	
								</tr>
							</tbody>
						</table>
					</div>
					<div flex="" class="table-detail padding" style="max-height: 300px;">
						<table>
							<thead>
								<tr>
									<th colspan="2" >Top Income</th>
								</tr>
								<!-- <tr>
									<th>Name</th>	
									<th class="text-right">Amount</th>
								</tr> -->
							</thead>
							<tbody>
								<tr ng-repeat="r in SERVICES | orderBy: ['-AMOUNT']">
									<td ng-bind="r.NAME"></td>							
									<td ng-bind="r.AMOUNT | number : 2" class="text-right"></td>
								</tr>
							</tbody>
						</table>
					</div>
				</div>
			</md-card>
		</div>


		<md-card>
			<md-card-header>
				<md-card-header-text>
					<span class="md-subhead">DETAIL</span>
				</md-card-header-text>
				<md-button ng-click="Detail_Report($event)" class="btn-icon md-primary md-fab-top-right" >
					<md-icon class="material-icons">print</md-icon>
				</md-button>
			</md-card-header>
			<div class="table-detail padding scroll" style="max-height: 300px;">
				<table>
					<thead>
						<tr>
							<th>MR NO.</th>
							<th>Checkup</th>
							<th>Patient</th>
							<th>Clinic</th>
							<th class="text-right">Amount</th>
							<th></th>
						</tr>
					</thead>
					<tbody>
						<tr ng-repeat="r in DETAIL">
							<td ng-bind="r.ID"></td>
							<td ng-bind="r.CHECKUPDATE|date:'MM/dd/y'" class="text-nowrap"></td>
							<td ng-bind="r.NAME"></td>
							<td ng-bind="r.FROMCLINIC"></td>
							<td ng-bind="r.NETPAYABLES | number : 2" class="text-right"></td>
							<td class="action">
								<md-button href="{{opt.editMRUrl+ r.PATIENTID +'/medical-record/'+r.ID}}" class="btn-auto md-primary">
									<md-icon class="material-icon">link</md-icon>
								</md-button>
							</td>
						</tr>
					</tbody>
				</table>
			</div>
			<md-card-content layout="row">
				<span flex="" class="text-muted">Records ({{DETAIL.length}})</span>
				<div ng-bind="TOTALDETAIL| number : 2" class="text-right text-muted"></div>
			</md-card-content>
		</md-card>


		


	</div>
</section>


