<!-- admin -->
<section ng-controller="PageSubspecialty" >
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
							<th style="width:50px;"></th>
						</tr>
					</thead>
					<tbody>
						<tr ng-repeat="A in LIST | orderBy: ['NAME']">
							<td ng-bind="A.NAME"></td>
							<td class="action">
								<md-button ng-show="opt.isLoaded"  href="{{opt.editUrl+ A.ID}}" class="md-primary" >
									Edit
								</md-button>
							</td>					
						</tr> 
					</tbody>
				</table>
			</div>				
		</md-card>


	</div>
</section>