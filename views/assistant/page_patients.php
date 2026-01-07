<!-- ASSISTANT -->
<section ng-controller="PagePatients" >
	<md-progress-linear ng-hide="opt.isLoaded" md-mode="indeterminate"></md-progress-linear>
	<div class="container">


		<md-button ng-show="opt.isLoaded" href="{{opt.newPatientUrl}}" class="md-fab md-primary md-fab-bottom-right fixed" >
			<md-icon class="material-icons">person_add</md-icon>
		</md-button>

		<md-card>
			<md-progress-linear ng-show="opt.isSearch && opt.isLoaded" md-mode="query"></md-progress-linear>
			<div class="table-responsive">
				<table>
					<thead>
						<tr>
							<th>Date Reg</th>
							<th>name</th>
							<th>Date of Birth</th>
							<th>Sex</th>
							<th>Mobile No.</th>
							<th class="action"></th>
						</tr>
					</thead>
					<tbody>
						<tr ng-repeat="(key,A) in PATIENTS"> 
							<td>Date Reg</td>
							<td><span ng-bind="A.DATEREG|date:'MM/dd/y'"></span></td>
							<td>Name</td>
							<td><span ng-bind="A.LASTNAME+', '+A.FIRSTNAME+' '+ A.MIDDLENAME"></span></td>
							<td>Date of Birth</td>
							<td><span ng-bind="A.DOB|date:'MM/dd/y'"></span></td>
							<td>Sex</td>
							<td><span ng-bind="A.SEX"></span></td>
							<td>MObile No.</td>
							<td><span ng-bind="A.MOBILENO"></span></td>
							<td class="action">
								<md-button href="{{opt.viewPatientUrl + A.ID}}" class="md-primary btn-icon">
									<md-icon class="material-icons">arrow_forward</md-icon>
								</md-button>
							</td>
						</tr>
					</tbody>
					<tfoot>
						<tr ng-show="PATIENTS.length == 0 && opt.searchText == '' ">
							<td colspan="10">
								No patient registered.
							</td>
						</tr>
						<tr ng-show="PATIENTS.length == 0 && opt.searchText != '' ">
							<td colspan="10">
								Search not found "{{opt.searchText}}".
							</td>
						</tr>
					</tfoot>
				</table>
			</div>
		</md-card>

	</div>
</section>



