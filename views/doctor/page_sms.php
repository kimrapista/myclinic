<!-- doctor -->
<section ng-controller="SMS">
	<md-progress-linear ng-hide="opt.isLoaded" md-mode="indeterminate"></md-progress-linear>
	<div class="container">

		<md-button ng-show="opt.isLoaded" href="{{opt.newUrl}}" class="md-fab md-primary md-fab-bottom-right fixed">
			<md-icon class="material-icons">add</md-icon>
		</md-button>


		<md-card>
			<md-progress-linear ng-show="opt.isSearch && opt.isLoaded" md-mode="query"></md-progress-linear>
			<div class="table-unresponsive">
				<table>
					<thead>
						<tr>
							<th>DATE CREATED</th>
							<th>MESSAGE</th>
							<th>Date to Send</th>
							<th>No. of Patient</th>
							<th>POST</th>
							<th></th>
						</tr>
					</thead>
					<tbody>
						<tr ng-repeat="A in LIST | orderBy: ['NAME']">
							<td ng-bind="A.CREATEDTIME|date:'MM/dd/y'"></td>
							<td><pre ng-bind="A.MESSAGE" class="m-0"></pre></td>
							<td ng-bind="A.SENDDATE|date:'MM/dd/y'"></td>
							<td ng-bind="A.NOPATIENT"></td>
							<td class="text-center">
								<md-icon ng-show="A.POST=='Y'" class="material-icons">check</md-icon>
								<md-icon ng-hide="A.POST=='Y'" class="material-icons">close</md-icon>
							</td>
							<td class="action">
								<md-button ng-show="opt.isLoaded" href="{{opt.editUrl+ A.ID}}" class="btn-icon md-primary" >
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