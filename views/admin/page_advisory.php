<!-- ADMIN -->
<section ng-controller="PageAdvisory"  >
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
							<th>CLINIC</th>
							<th>TITLE</th>
							<th>MESSAGE</th>
							<th>LINK</th>
							<th>POST</th>
							<th>POST DATE</th>
							<th style="width:50px;"></th>
						</tr>
					</thead>
					<tbody>
						<tr ng-repeat="A in LIST ">
							<td ng-bind="A.CLINICNAME"></td>
							<td ng-bind="A.TITLE"></td>
							<td><pre ng-bind-html="A.BODY"></pre></td>
							<td ng-bind="A.LINK"></td>
							<td>
								<md-icon ng-show="A.POST" class="material-icons">check</md-icon>
								<md-icon ng-hide="A.POST" class="material-icons">close</md-icon>
							</td>
							<td>
								<span ng-show="A.POST" ng-bind="A.POSTDATE|date:'M/d/y hh:mm a'"></span>
							</td>
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