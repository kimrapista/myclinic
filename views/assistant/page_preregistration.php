<section ng-controller="Preregistration" ng-init="init('<?php echo base_url(''); ?>')" class="container-fluid">
	

	<div class="navigation-page-content d-flex align-items-center justify-content-between">
		<ul class="page-root mb-0">
			<li>Preregistration</li>
		</ul>
		<a ng-href="<?php echo base_url('preregistration/new'); ?>" class="btn btn-secondary btn-sm ml-3"><i class="fa fa-plus fa-fw"></i>Patient</a>
	</div>
		<div class="card card-custom shadow">
			<div class="table-responsive">
							<table class="table table-hover ">
								<thead>
									<tr>
										<th>ID</th>
										<th>Patient Name</th>
										<th>Patient Address</th>
										<th>Sex</th>
										<th>Clinic Name</th>
										<th>Doctor Name</th>
										<th>POSTED</th>
										<th></th>
									</tr>
								</thead>
								<tbody>
									<tr ng-repeat="l in LIST" >
										<td ng-bind="l.ID"></td>
										<td ng-bind="l.name"></td>
										<td ng-bind="l.address"></td>
										<td><i class="fa {{l.SEX == 'MALE' ? 'fa-male':'fa-female'}} fa-fw"></i></td>
										<td ng-bind="l.CLINICNAME"></td>
										<td ng-bind="l.DOCTORNAME"></td>
										<td><i class="fa {{l.POST == 'N' ? 'fa-times':'fa-check'}} fa-fw"></i></td>
										<td class="text-right">
											<a ng-href="<?php echo base_url('preregistration/edit/'); ?>{{l.ID}}" class="btn btn-secondary btn-sm"><i class="fa fa-pencil fa-fw"></i></a>
										</td>
									</tr>
								</tbody>
							</table>
		</div>
	</div>
</section>
<script src="<?php echo base_url('assets/js/preregistration/pageaPreregistration.js') ?>"></script>
