<div class="modal-dialog " role="document">
	<div class="modal-content card-custom">
		<form name="formParent" ng-submit="Submit_Form()">

			<div class="card-header">
				<span ng-bind="title"></span>
			</div>
			<div class="modal-body">
				
				<label class="col-form-label">NAME</label>
				<input ng-model="form.NAME" type="text" class="form-control mb-3" required autofocus>

			</div>
			<div class="modal-body d-flex justify-content-between form-action">
				<button ng-click="form.CANCELLED = !form.CANCELLED" type="button" class="btn {{ form.CANCELLED ? 'btn-danger' : 'btn-info' }} btn-sm">
					<span ng-if="!form.CANCELLED">ACTIVE</span>
					<span ng-if="form.CANCELLED">UN-ACTIVE</span>
				</button>	
				<div>
					<button type="submit" ng-disabled="isSubmit" class="btn btn-secondary btn-sm">SAVE</button>
					<button type="button" class="btn btn-secondary btn-sm" data-dismiss="modal">CLOSE</button>
				</div>
			</div>

		</form>
	</div>
</div>