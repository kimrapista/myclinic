<section ng-controller="MedicalHistory" ng-init="init('<?php echo base_url('settings/'); ?>')" class="container" style="max-width: 600px;">
	
	<div class="navigation-page-content d-flex align-items-center justify-content-between">
		<ul class="page-root mb-0">
			<li>Settings</li>
			<li>Medical History</li>
		</ul>
		<a href="#" ng-click="Load_Form()" class="btn btn-secondary btn-sm ml-3">Add Disease</a>
	</div>

	<div class="card card-custom shadow">
		<div class="table-responsive">
			<table class="table table-hover ">
				<thead >
					<tr>
						<th>NO.</th>
						<th>Name</th>
						<th>Active</th>
						<th></th>															
					</tr>		
				</thead>							
				<tbody >
					<tr ng-repeat="l in LIST" >
						<td ng-bind="l.ID"></td>
						<td ng-bind="l.NAME"></td>																							
						<td>
							<span ng-if="l.CANCELLED" class="badge badge-danger">UN-ACTIVE</span>
							<span ng-if="!l.CANCELLED" class="badge badge-light">ACTIVE</span>
						</td>												
						<td class="text-right">
							<button ng-click="Load_Form(l)" type="button" class="btn btn-secondary btn-sm"><i class="fa fa-pencil fa-fw"></i></button>
						</td>		
					</tr> 
				</tbody>
			</table>
		</div>
	</div>			


	<div id="form" class="modal fade"  tabindex="-1" role="dialog" aria-labelledby="form" aria-hidden="true">
		<?php $this->load->view('settings/forma_medicalhistory'); ?>
	</div>		


</section>
<script src="<?php echo base_url('assets/js/settings/pageaMedicalhistory.js') ?>"></script>