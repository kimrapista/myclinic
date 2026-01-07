<!-- ADMIN -->

<section ng-controller="PageHospitals" >	
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
							<th>Name</th>
							<th>Code</th>
							<th>PMCC</th>
							<th>Address</th>
							<th>Zip Code</th>
							<th style="width:50px;"></th>
						</tr>
					</thead>
					<tbody>
						<tr ng-repeat="A in LIST | orderBy: ['NAME']">
							<td>Name</td>
							<td ng-bind="A.NAME"></td>
							<td>Code</td>
							<td ng-bind="A.CODE"></td>
							<td>PMCC</td>
							<td ng-bind="A.PMCC"></td>
							<td>Address</td>
							<td ng-bind="A.ADDRESS"></td>
							<td>Zip Code</td>
							<td ng-bind="A.ZIPCODE"></td>
							<td class="action">
								<md-button ng-show="opt.isLoaded" href="{{opt.editUrl+ A.ID}}" class="md-primary" >
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


