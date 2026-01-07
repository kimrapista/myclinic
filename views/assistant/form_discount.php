<!-- ASSISTANT -->
<section ng-controller="FormDiscount">
    <md-progress-linear ng-hide="opt.isLoaded" md-mode="indeterminate"></md-progress-linear>
    <div class="container-xs">
        <form name="formParent" ng-submit="Submit_Form()" role="form">


            <md-card>
                <md-card-content>

                    <md-input-container class="md-block">
                        <label>Name</label>
                        <input ng-model="form.NAME" name="NAME" type="text" required autofocus>
                        <div ng-messages="formParent.NAME.$error" role="alert" md-auto-hide="false">
                            <div ng-message="required">This is required.</div>
                        </div>
                    </md-input-container>


                    <div layout="row" layout-align="start start">
                        <md-button ng-click="form.PERCENTAGE = !form.PERCENTAGE;" class="md-raised md-primary mt-3">
                            <span ng-show="form.PERCENTAGE">%</span>
                            <span ng-hide="form.PERCENTAGE">Php</span>
                        </md-button>

                        <md-input-container flex="" class="md-block">
                            <label>Amount</label>
                            <input ng-model="form.AMOUNT" name="AMOUNT" type="number" step="0.01" class="text-right" required>
                            <div ng-messages="formParent.AMOUNT.$error" role="alert" md-auto-hide="false">
                                <div ng-message="required">This is required.</div>
                            </div>
                        </md-input-container>
                    </div>


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
