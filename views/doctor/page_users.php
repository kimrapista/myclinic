<!-- doctor -->
<section ng-controller="Users"  >
	<md-progress-linear ng-hide="opt.isLoaded" md-mode="indeterminate"></md-progress-linear>
	<div class="container">

		<md-button ng-show="opt.isLoaded" href="{{opt.newUrl}}" class="md-fab md-primary md-fab-bottom-right fixed" >
			<md-icon class="material-icons">add</md-icon>
		</md-button>


		<md-card>
			<md-progress-linear ng-show="opt.isSearch && opt.isLoaded" md-mode="query"></md-progress-linear>
			<div class="table-unresponsive">
				<table>
					<thead>
						<tr>
							<th>Name</th>
							<th>Default Clinic</th>
							<th>user level</th>
							<th>Username</th>
							<th>ACTIVE</th>			
							<th></th>
						</tr>
					</thead>
					<tbody>
						<tr ng-repeat="A in LIST | orderBy: ['NAME']">
							<td ng-bind="A.NAME"></td>
							<td ng-bind="A.SUBCLINIC"></td>
							<td ng-bind="A.POSITION"></td>
							<td ng-bind="A.USERNAME"></td>
							<td>
								<md-switch ng-model="A.CANCELLED" ng-change="Submit_Active(A);" aria-label="ACTIVE" class="md-primary my-0"></md-switch>
							</td>
							<td class="action">
								<md-menu ng-show="opt.isLoaded" >
									<md-button class="md-primary btn-icon" ng-click="$mdMenu.open($event)">
										<md-icon class="material-icons">more_vert</md-icon>
									</md-button>
									<md-menu-content width="4">
										<md-menu-item>
											<md-button href="{{opt.editUrl+ A.ID}}" >
												<md-icon class="material-icons">edit</md-icon>
												Edit
											</md-button>
										</md-menu-item>
										<md-menu-item>
											<md-button ng-click="Submit_Reset_Password(A)">
												<md-icon class="material-icons">replay</md-icon>
												Reset Password
											</md-button>
										</md-menu-item>
									</md-menu-content>
								</md-menu>
							</td>					
						</tr> 
					</tbody>
				</table>
			</div>				
		</md-card>


	</div>
</section>

