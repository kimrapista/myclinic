<!-- doctor -->
<section ng-controller="FormSubClinic">
    <md-progress-linear ng-hide="opt.isLoaded" md-mode="indeterminate"></md-progress-linear>
    <div class="container-md">
        <form name="formParent" ng-submit="Submit_Form()" role="form">


            <md-card>
                <md-card-content>

                    <md-input-container class="md-block">
                        <label>Subclinic Name or Hospital Acronym</label>
                        <input ng-model="form.NAME" name="NAME" type="text" required autofocus>
                        <div ng-messages="formParent.NAME.$error" role="alert" md-auto-hide="false">
                            <div ng-message="required">This is required.</div>
                        </div>
                    </md-input-container>

                    <md-input-container class="md-block">
                        <label>Hospital</label>
                        <md-select ng-model="form.HOSPITALID" name="HOSPITALID" md-on-close="searchHospital='';">
                            <md-select-header>
                                <input ng-model="searchHospital" ng-keydown="$event.stopPropagation()"  type="text" placeholder="Search Hospital" class="md-select-search">
                            </md-select-header>
                            <md-optgroup label="List of Hospitals">
                                <md-option value="">N/A</md-option>
                                <md-option ng-value="A.ID" ng-bind="A.NAME" ng-repeat="A in form.HOSPITALS|filter: searchHospital"></md-option>
                            </md-optgroup>
                        </md-select>
                        <div ng-messages="formParent.HOSPITALID.$error" role="alert" md-auto-hide="false">
                            <div ng-message="required">This is required.</div>
                        </div>
                    </md-input-container>


                    <md-input-container class="md-block">
                        <label>Building/Room</label>
                        <input ng-model="form.LOCATION">
                    </md-input-container>


                    <div layout="column" layout-gt-xs="row" layout-align="end">
                        <md-button ng-disabled="opt.isSubmit" type="submit" class="md-raised md-primary">
                            <span ng-hide="opt.isSubmit">Save</span>
                            <div ng-show="opt.isSubmit" layout="row" layout-align="center">
                                <md-progress-circular md-mode="indeterminate" md-diameter="30"></md-progress-circular>
                            </div>
                        </md-button>

                        <md-button href="{{opt.cancelUrl}}" class=" md-accent">Cancel</md-button>
                    </div>

                </md-card-content>
            </md-card>

        </form>
    </div>
</section>
