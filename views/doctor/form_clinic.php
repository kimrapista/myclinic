<!-- doctor -->
<section ng-controller="FormClinic">
    <md-progress-linear ng-hide="opt.isLoaded" md-mode="indeterminate"></md-progress-linear>
    <div class="container">

        <form name="formParent" ng-submit="Submit_Form()" role="form">

            <md-card>
                <md-tabs md-dynamic-height md-border-bottom md-center-tabsx>
                    <md-tab label="Clinic"> 
                        <md-card-content>

                            <md-input-container class="md-block">
                                <label>Clinic Title</label>
                                <input ng-model="f.CLINICNAME" autofocus required>
                            </md-input-container>

                            <md-input-container ng-if="!opt.ACCOUNTBASE" class="md-block">
                                <label>Specialist 1</label>
                                <input ng-model="f.CLINICSUBNAME">
                            </md-input-container>

                            <md-input-container ng-if="!opt.ACCOUNTBASE" class="md-block">
                                <label>Specialist 2</label>
                                <input ng-model="f.CLINICSUBNAME1">
                            </md-input-container>


                            <div layout="column" layout-gt-xs="row">
                                <md-input-container flex-gt-xs="30" class="md-block">
                                    <label>TIN</label>
                                    <input ng-model="f.TIN">
                                </md-input-container>
                                <md-input-container flex-gt-xs="40" class="md-block">
                                    <label>Tel No.</label>
                                    <input ng-model="f.CONTACTNO">
                                </md-input-container>
                                <md-input-container flex-gt-xs="40" class="md-block">
                                    <label>Mobile No.</label>
                                    <input ng-model="f.MOBILENO">
                                </md-input-container>
                            </div>

                            <md-input-container class="md-block">
                                <label>Email</label>
                                <input ng-model="f.EMAIL">
                            </md-input-container>

                            <md-input-container class="md-block">
                                <label>Address</label>
                                <input ng-model="f.ADDRESS">
                            </md-input-container>

                        </md-card-content>
                    </md-tab>

                    <md-tab label="Referral"> 
                        <md-card-content>

                            <div layout="row" layout-align="start start">
                                <md-input-container flex>
                                    <label>Default Referral message</label>
                                    <textarea ng-model="f.REFERRALDEFAULTTEXT" name="REFERRALDEFAULTTEXT" ng-trim="false" md-maxlength="1000"></textarea>
                                </md-input-container>
                                <md-button ng-click="Referral_Report($event);" class="btn-icon md-primary">
                                    <md-tooltip md-direction="top">Preview Referral letter</md-tooltip>
                                    <md-icon class="material-icons">find_in_page</md-icon>
                                </md-button>
                            </div>

                        </md-card-content>
                    </md-tab>

                    <md-tab label="Clearance"> 
                        <md-card-content>

                            <div layout="row" layout-align="start start">
                                <md-input-container flex>
                                    <label>Default Clearance message</label>
                                    <textarea ng-model="f.CLEARANCEDEFAULTTEXT" name="CLEARANCEDEFAULTTEXT" ng-trim="false" md-maxlength="1000"></textarea>
                                </md-input-container>
                                <md-button ng-click="Clearance_Report($event);" class="btn-icon md-primary">
                                    <md-tooltip md-direction="top">Preview Clearance letter</md-tooltip>
                                    <md-icon class="material-icons">find_in_page</md-icon>
                                </md-button>
                            </div>

                        </md-card-content>
                    </md-tab>
                </md-tabs>



                <md-card-content>
                    <div layout="column" layout-gt-xs="row" layout-align="end">
                        <md-button ng-disabled="opt.isSubmit" type="submit" class="md-raised md-primary">
                            <span ng-hide="opt.isSubmit">Save</span>
                            <div ng-show="opt.isSubmit" layout="row" layout-align="center">
                                <md-progress-circular md-mode="indeterminate" md-diameter="30"></md-progress-circular>
                            </div>
                        </md-button>
                        <md-button href="{{opt.cancelUrl}}" class="md-accent">CANCEL</md-button>
                    </div>
                </md-card-content>
            </md-card>


        </form>
    </div>
</section>
