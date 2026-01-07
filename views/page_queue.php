

<section ng-controller="Queue" ng-init="init('<?php echo base_url(); ?>')" id="page_queue" >
	
	<button ng-click="Load_Queued();" type="button" class="btn btn-secondary btn-sm navigation-extra-button" data-toggle="modal" data-target="#modalQueue">
		<i class="fa fa-blind fa-fw"></i>
	</button>
	

	<div class="modal fade" id="modalQueue" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
		<div class="modal-dialog modal-lg" role="document">
			<div class="modal-content">
				<div class="card card-custom mb-0">
					<div class="card-header d-flex justify-content-between">
						<span>Patient Queue</span>
						<div>
							<button ng-click="Toggle_Live()" type="button" class="btn {{liveCheck ? 'btn-outline-warning border-0' : 'btn-outline-light border-0'}}"><i class="fa fa-refresh fa-fw"></i></button>
							<button type="button" class="btn btn-outline-light border-0" data-dismiss="modal" aria-label="Close">
								<i class="fa fa-close fa-fw"></i>
							</button>
						</div>
					</div>
					<div class="table-responsive">
						<table class="table table-hover mb-0">
							<thead>
								<tr>
									<th>NO.</th>
									<th>PATIENT NAME</th>
									<th>Status</th>
								</tr>
							</thead>
							<tbody>
								<tr ng-repeat="(key,p) in queues | orderBy: ['PRIORITYNO']">
									<td>
										<input id="pno_{{p.ID}}" ng-model="p.PRIORITYNO" ng-change="Change_PriorityNo(p);" step="1" min="1" type="number" class="form-control form-control-sm" style="width: 55px !important;">
									</td>
									<td>
										<div class="btn-group btn-group-sm">
											<a href="{{baseUrl +'patients/record/'+ p.PATIENTID}}" class="btn btn-secondary"><span ng-bind="p.NAME"></span></i></a>
											<button class="btn btn-secondary dropdown-toggle dropdown-toggle-split px-3" type="button" id="dropdownMenuButton" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false"></button>
											<div class="dropdown-menu" aria-labelledby="dropdownMenuButton">
												<button ng-click="Patient_Qtomorrow(p.PATIENTID);" class="dropdown-item">Queue Tomorrow</button>
												<button ng-click="Remove_Queued(key,p.ID);"  ng-disabled="p.SERVING" class="dropdown-item">Remove</button>
											</div>
										</div>
									</td>
									<td>
										<span  class="badge badge-light">WAITING</span>
										<span ng-if="p.SERVING" class="badge badge-light">SERVED</span>
										<span ng-if="p.PAID" class="badge badge-light">PAID</span>
									</td>
								</tr>
							</tbody>
						</table>
					</div>
				</div>
			</div>
		</div>
	</div>

</section>

<script src="<?php echo base_url('assets/js/queue.js') ?>"></script>