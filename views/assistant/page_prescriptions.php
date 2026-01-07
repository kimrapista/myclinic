<!-- ASSISTANT -->
<section ng-controller="Prescriptions" >
	<md-progress-linear ng-hide="opt.isLoaded" md-mode="indeterminate"></md-progress-linear>
	<div class="container-md">

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
							<th></th>
						</tr>
					</thead>
					<tbody>
						<tr ng-repeat="A in LIST | orderBy: ['NAME']">
							<td ng-bind="A.NAME"></td>
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