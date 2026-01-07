<!-- ADMIN -->

<section ng-controller="PageClinics" >	
	<md-progress-linear ng-hide="opt.isLoaded" md-mode="indeterminate"></md-progress-linear>
	<div class="container-lg">

		<md-button ng-show="opt.isLoaded" href="{{opt.newUrl}}" class="md-fab md-primary md-fab-bottom-right fixed" >
			<md-icon class="material-icons">add</md-icon>
		</md-button>


		<md-card>
			<md-progress-linear ng-show="opt.isSearch && opt.isLoaded" md-mode="query"></md-progress-linear>
			<div class="table-responsive">
				<table>
					<thead>
						<tr>
							<th>ID</th>
							<th>Hospital</th>
							<th>Clinic</th>
							<th>Users</th>
							<th>Options On</th>
							<th style="width:50px;"></th>
						</tr>
					</thead>
					<tbody>
						<tr ng-repeat="A in LIST">
							<td>ID</td>
							<td ng-bind="A.ID"></td>
							<td>Hospital</td>
							<td ng-bind="A.HOSPITALNAME"></td>
							<td>Clinic</td>
							<td ng-bind="A.CLINICNAME"></td>
							<td>Doctor</td>
							<td>
							 	<div ng-repeat="B in A.ADMINS" ng-bind="B.NAME"></div>
							</td>
							<td>Options</td>
							<td>
								<span ng-show="A.SALES">SALES</span>

								<span ng-show="A.IS_BLAST" >, TEXT BLAST</span>

								<span ng-show="A.ACCOUNTBASE" >, ACCOUNT BASE</span>

								<span ng-show="A.ASSISTANTRECORD" >, ASSISTANT RECORD</span>
							</td>
							<td class="action">
								<md-button ng-show="opt.isLoaded" href="{{opt.editUrl+ A.ID}}" class="md-primary btn-icon">
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


