<!-- admin -->

<section ng-controller="PageDashboard" >
    <md-progress-linear ng-hide="opt.isLoaded" md-mode="indeterminate"></md-progress-linear>
    <div class="container">



        <div  layout="row" layout-wrap="" layout-align-gt-sm="start start">

            <md-card flex="100" flex-gt-xs="45" flex-gt-sm="35" >
                <md-card-header>
                    <md-card-header-text >
                        Today 
                    </md-card-header-text>
                </md-card-header>
                <md-card-content>

                    <div id="chartTodaySex">
                        <canvas height="80" ></canvas>
                    </div>
                    <h3 ng-bind="DATA.TOTAL_TODAY_SERVED|number:0" class="m-0 text-center"></h3>   
                    <small class="text-muted text-center mb-5 d-block">Served</small>   
                    

                    <div ng-repeat="(key, A) in DATA.CLINICS | orderBy: ['-AVERAGE','NAME']" class="mb-3">
                        <div class="d-flex justify-content-between">
                            <span ng-bind="A.NAME"></span>
                            <small ng-bind="A.TODAY+' / '+(A.AVERAGE|number:2) +' %'" class="text-muted text-nowrap"></small>    
                        </div>
                        <md-progress-linear md-mode="determinate" value="{{(A.AVERAGE)}}"></md-progress-linear>
                    </div>

                </md-card-content>
            </md-card>

             <md-card flex="100" flex-gt-xs="50" flex-gt-sm="60" >
                <md-card-header>
                    <md-card-header-text >
                        Month 
                    </md-card-header-text>
                </md-card-header>
                <md-card-content>

                     <div id="chartMonthMR" class="mb-3">
                        <canvas height="100" ></canvas>
                    </div>

                    <div class="d-flex justify-content-around flex-wrap">
                        <div class="text-center">
                            <h4 ng-bind="DATA.TOTAL_MONTH_SERVED|number:0" class="m-0"></h4>
                            <small class="text-muted">Served</small>
                        </div>
                        <div class="text-center">
                            <h4 ng-bind="DATA.TOTAL_MONTH_NEWREG|number:0" class="m-0"></h4>
                            <small class="text-muted">New</small>
                        </div>
                        <div class="text-center">
                            <h4 ng-bind="DATA.TOTAL_MONTH_MALE|number:0" class="m-0"></h4>
                            <small class="text-muted">Male</small>
                        </div>
                        <div class="text-center">
                            <h4 ng-bind="DATA.TOTAL_MONTH_FEMALE|number:0" class="m-0"></h4>
                            <small class="text-muted">Female</small>
                        </div>
                    </div>

                </md-card-content>
            </md-card>

        </div>




    </div>
</section>

