<section ng-controller="Discounts"  >
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
							<th>Percent/ Amt.</th>
							<th>Amount</th>
							<th></th>
						</tr>
					</thead>
					<tbody>
						<tr ng-repeat="A in LIST | orderBy: ['NAME']">
							<td ng-bind="A.NAME"></td>
							<td>
								<span ng-show="A.PERCENTAGE=='Y'">Percentage</span>
								<span ng-show="A.PERCENTAGE=='N'">Amount</span>
							</td>
							<td class="text-right">
								<span ng-show="A.PERCENTAGE=='Y'" ng-bind="A.AMOUNT+' %'" ></span>
								<span ng-show="A.PERCENTAGE=='N'" ng-bind="A.AMOUNT|number:2 +''""></span>
							</td>
							<td class="action">
								<md-button ng-show="opt.isLoaded" ng-disabled="opt.isDisabled" href="{{opt.editUrl+ A.ID}}" class="btn-icon md-primary" >
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