<section ng-controller="PageSpecialist" >
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
							<th>Specialty</th>
							<th>Group</th>
							<th style="width:50px;"></th>
						</tr>
					</thead>
					<tbody>
						<tr ng-repeat="A in LIST | orderBy: ['SPECIALTY','SPECIALTYPRACTICE','SPECIALTYTITLE']">
							<td ng-bind="A.SPECIALTY"></td>
							<td>
								<span ng-show="A.SPECIALTYGROUP == 'D'">DIAGNOSTIC</span>
								<span ng-show="A.SPECIALTYGROUP == 'M'">MEDICINE</span>
								<span ng-show="A.SPECIALTYGROUP == 'S'">SURGERY</span>
							</td>
							<td class="action">
								<md-button ng-show="opt.isLoaded"  href="{{opt.editUrl+ A.ID}}" class="md-primary" >
									Edit
								</md-button>
							</td>					
						</tr> 
					</tbody>
					<tfoot>
						<tr>
							<td>Total {{LIST.length}}</td>
						</tr>
					</tfoot>
				</table>
			</div>				
		</md-card>


	</div>
</section>
