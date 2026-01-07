<!-- ASSISTANT -->
<section  ng-controller="FormMedical" class="h-100">
	<md-progress-linear ng-hide="opt.isLoaded" md-mode="indeterminate"></md-progress-linear>
	<div class="container-lg"> 

		<form  name="formParent" ng-submit="Submit_Form()" enctype="multipart/form-data">
			<div  layout="row" layout-wrap="" layout-align-gt-sm="start start">

				<md-card flex="100" flex-gt-sm="30" class="sticky-gt-sm">
					<md-card-header>
						<table>
							<tr>
								<td class="text-muted">Name</td>
								<td><span ng-bind="f.PATIENT.LASTNAME+', '+f.PATIENT.FIRSTNAME+' '+f.PATIENT.MIDDLENAME"></span></td>
							</tr>
							<tr>
								<td class="text-muted">DOB</td>
								<td><span ng-bind="f.PATIENT.DOB|date:'MM/dd/y'"></span></td>
							</tr>
							<tr>
								<td class="text-muted">Age</td>
								<td><span ng-bind="f.AGE"></span></td>
							</tr>
						</table>
					</md-card-header>
					<md-card-content >
						
						<div layout="row" layout-wrap="" >
							<md-input-container flex="50" flex-gt-xs="25" flex-gt-sm="100">
								<label>DATE CHECKUP</label>
								<input ng-model="f.CHECKUPDATE" name="CHECKUPDATE" type="date" ng-disabled="f.READONLY"  required>
								<div ng-messages="formParent.CHECKUPDATE.$error" role="alert" md-auto-hide="false">
									<div ng-message="required">This is required.</div>
								</div>
							</md-input-container>

							<md-input-container flex="50" flex-gt-xs="25" flex-gt-sm="100" >
								<label>Clinic location</label>
								<md-select ng-model="f.SUBCLINICID" name="SUBCLINICID" ng-change="f.SUBCLINICID = Check_Select_Value(f.SUBCLINICID)" ng-disabled="f.READONLY"  required>
									<md-option ng-repeat="(key, S) in LIST.SUBCLINICS" ng-value="S.ID" ng-bind="S.NAME"></md-option>
								</md-select>
								<div ng-messages="formParent.SUBCLINICID.$error" role="alert" md-auto-hide="false">
									<div ng-message="required">This is required.</div>
								</div>
							</md-input-container>

							<div ng-show="LIST.SUBCLINICS.length == 0">
								<span>Please specify clinic location </span>
								<a href="#!/clinic">goto</a>
							</div>

							<md-input-container flex="50" flex-gt-xs="25" flex-gt-sm="100" >
								<label>Referred by</label>
								<input ng-model="f.REFFEREDBY"  ng-disabled="f.READONLY" type="text">
							</md-input-container>

							<md-input-container flex="50" flex-gt-xs="25" flex-gt-sm="100" >
								<label>Hospital Record No.</label>
								<input ng-model="f.HRID"  ng-disabled="f.READONLY" type="text">
							</md-input-container>
						</div>
						

						<md-switch  ng-hide="f.READONLY" ng-model="f.APPOINTMENT" ng-change="Reset_Appointment();"  aria-label="Appointment" class="md-primary text-muted">Appointment</md-switch>


						<div layout="row" layout-wrap="" >
							<md-input-container ng-show="f.APPOINTMENT" flex="50" flex-gt-xs="33" flex-gt-sm="100">
								<label>Appointment Date</label>
								<input ng-model="f.APPOINTMENTDATE" name="APPOINTMENTDATE" type="date" ng-disabled="f.READONLY"  ng-required="f.APPOINTMENT">
								<div ng-messages="formParent.APPOINTMENTDATE.$error">
									<div ng-message="required">This is required.</div>
								</div>
							</md-input-container>


							<md-input-container ng-show="f.APPOINTMENT" flex="50" flex-gt-xs="33" flex-gt-sm="100" >
								<label>To Clinic</label>
								<md-select ng-model="f.APPOINTMENTSUBCLINICID" name="APPOINTMENTSUBCLINICID" ng-disabled="f.READONLY" >
									<md-option value=""></md-option>
									<md-option ng-repeat="(key, S) in LIST.SUBCLINICS" ng-value="S.ID" ng-bind="S.NAME"></md-option>
								</md-select>
								<div ng-messages="formParent.APPOINTMENTSUBCLINICID.$error" role="alert" md-auto-hide="false">
									<div ng-message="required">This is required.</div>
								</div>
							</md-input-container>

							<md-input-container ng-show="f.APPOINTMENT" flex="100" flex-gt-xs="33" flex-gt-sm="100">
								<label>Bring on next visit</label>
								<input ng-model="f.APPOINTMENTDESCRIPTION"  ng-disabled="f.READONLY" type="text">
							</md-input-container>
						</div>

					</md-card-content>
				</md-card>

				<md-card flex="100" flex-gt-sm="65">
					<md-tabs md-dynamic-height md-border-bottom >

						<md-tab label="MEDICAL IN">
							<md-card-content ng-if="f.CREATEDBY > 0 && !opt.EDITMR" class="text-center">
								<md-icon class="material-icons lg">remove_red_eye</md-icon>
								<h6 class="text-muted">Medical info is already editted by doctor.</h6>
							</md-card-content>

							<md-card-content ng-if="f.CREATEDBY == 0 || opt.EDITMR">

								<md-input-container class="md-block">
									<label>Chief Complaint</label>
									<textarea ng-model="f.CHEIFCOMPLAINT" name="CHEIFCOMPLAINT" md-maxlength="500" ng-disabled="f.READONLY" required=""></textarea>
									<div ng-messages="formParent.CHEIFCOMPLAINT.$error" role="alert" md-auto-hide="false">
										<div ng-message="required">This is required.</div>
										<div ng-message="md-maxlength">character reach the max limit.</div>
									</div>
								</md-input-container>


								<md-input-container class="md-block">
									<label>History Present Illness</label>
									<textarea ng-model="f.PRESENTILLNESS" name="PRESENTILLNESS" md-maxlength="500" ng-disabled="f.READONLY" ></textarea>
									<div ng-messages="formParent.PRESENTILLNESS.$error" role="alert" md-auto-hide="false">
										<div ng-message="md-maxlength">character reach the max limit.</div>
									</div>
								</md-input-container>

								
								<div layout="row" layout-align="start start">
									<md-input-container flex>
										<label>CO-Morbidities</label>
										<textarea ng-model="f.COMORBIDITIES" name="COMORBIDITIES" md-maxlength="500" ng-disabled="f.READONLY" ></textarea>
										<div ng-messages="formParent.COMORBIDITIES.$error" role="alert" md-auto-hide="false">
											<div ng-message="md-maxlength">character reach the max limit.</div>
										</div>
									</md-input-container>
									<md-button ng-click="Copy_CO_Morbidities()" ng-disabled="f.READONLY" class="md-primary btn-icon">
										<md-tooltip md-direction="top">Copy Previous CO-Morbidities</md-tooltip>
										<md-icon class="material-icons">filter_none</md-icon>
									</md-button>
								</div>


							</md-card-content>

							<md-tabs ng-if="f.CREATEDBY == 0 || opt.EDITMR" md-dynamic-height md-border-bottom md-center-tabsx>
								<md-tab label="Vital Signs">
									<md-card-content>
										<div layout="row" layout-wrap="">
											<md-input-container flex="50" flex-gt-xs="20">
												<label>Systolic</label>
												<input ng-model = "f.BP_SYSTOLIC"  ng-disabled="f.READONLY" >
											</md-input-container>
											<md-input-container flex="50" flex-gt-xs="20">
												<label>Diastolic</label>
												<input ng-model = "f.BP_DIASTOLIC"  ng-disabled="f.READONLY"  >
											</md-input-container>

											<md-input-container flex="30" flex-gt-xs="20" >
												<label>HR</label>
												<input ng-model = "f.HEART_RATE"  ng-disabled="f.READONLY"  >
											</md-input-container>

											<md-input-container flex="30" flex-gt-xs="20">
												<label>RR</label>
												<input ng-model = "f.RESPIRATORY"  ng-disabled="f.READONLY"  >
											</md-input-container>

											<md-input-container flex="40" flex-gt-xs="20">
												<label>Temp.</label>
												<input ng-model = "f.TEMPERATURE"  ng-disabled="f.READONLY"  >
											</md-input-container>

											<md-input-container flex="30" flex-gt-xs="20" >
												<label>Height (cm)</label>
												<input ng-model = "f.HEIGHT"  ng-change="Calc_BMI();" ng-disabled="f.READONLY"  >
											</md-input-container> 

											<md-input-container  flex="30" flex-gt-xs="20" >
												<label>Weight (kg)</label>
												<input ng-model = "f.WEIGHT"  ng-change="Calc_BMI();" ng-disabled="f.READONLY"  >
											</md-input-container>

											<md-input-container  flex="30" flex-gt-xs="20" >
												<label>BMI</label>
												<input ng-model = "f.BMI"  disabled >
											</md-input-container>

											<md-input-container flex="40" flex-gt-xs="40" >
												<label>LMP</label>
												<input ng-model="f.LMP" name="LMP" type="date" ng-disabled="f.READONLY" >
											</md-input-container>
										</div>
									</md-card-content>
								</md-tab>

								<md-tab label="{{A.REF.NAME}}" ng-repeat="(key, A) in f.LABORATORIES">
									<md-card-content layout="row" layout-align="start end">

										<md-button ng-hide="A.CANCELLED" ng-click="Remove_Laboratory(key)" ng-if="!f.READONLY" class="md-fab-top-right md-fab md-mini md-primary ">
											<md-icon class="material-icons">remove</md-icon>
										</md-button>

										<md-button ng-show="A.CANCELLED" ng-click="Remove_Laboratory(key)" ng-if="!f.READONLY" class="md-fab-top-right md-fab md-mini md-warn">
											<md-icon class="material-icons">replay</md-icon>
										</md-button>

										<md-input-container flex="">
											<label>Result</label>
											<textarea ng-model="A.TEMPLATERESULT" name="TEMPLATERESULT" md-maxlength="500" ng-disabled="A.CANCELLED || f.READONLY" ></textarea>
											<div ng-messages="formParent.TEMPLATERESULT.$error" role="alert" md-auto-hide="false">
												<div ng-message="md-maxlength">character reach the max limit.</div>
											</div>
										</md-input-container>

									</md-card-content>
								</md-tab>


								<md-tab label="Add Lab" ng-if="!f.READONLY">
									<md-card-content class="">
										
										<div ng-show="LIST.LABORATORY.length > 0" class="table-unresponsive shadow scroll" style="max-height:300px;">
											<table>
												<tr ng-repeat="(key,A) in LIST.LABORATORY|orderBy:['NAME']">
													<td>
														<label ng-bind="A.NAME"></label>
														<pre ng-bind="A.TEMPLATE" class="text-muted"></pre>
													</td>
													<td class="action">
														<md-button ng-click="Add_Laboratory(A)" class="md-primary">
															<md-icon class="material-icons">add</md-icon>
														</md-button>
													</td>
												</tr>
											</table>
										</div>
										

										<h6 ng-show="LIST.LABORATORY.length == 0" class="text-muted">
											No current lab template
										</h6>

									</md-card-content>
								</md-tab>

							</md-tabs>

							<md-card-content ng-if="f.CREATEDBY == 0 || opt.EDITMR">

								<div layout="row" layout-align="start start">
									<md-input-container flex>
										<label>Findings</label>
										<textarea ng-model="f.FINDINGS" name="FINDINGS" md-maxlength="500"  ng-disabled="f.READONLY" ></textarea>
										<div ng-messages="formParent.FINDINGS.$error" role="alert" md-auto-hide="false">
											<div ng-message="md-maxlength">character reach the max limit.</div>
										</div>
									</md-input-container>
									<md-button ng-click="Copy_Findings()" ng-disabled="f.READONLY" class="md-primary btn-icon">
										<md-tooltip md-direction="top">Copy Previous Findings</md-tooltip>
										<md-icon class="material-icons">filter_none</md-icon>
									</md-button>
								</div>

								<div layout="row" layout-align="start start">
									<md-input-container flex>
										<label>Diagnosis</label>
										<textarea ng-model="f.DIAGNOSIS" name="DIAGNOSIS" md-maxlength="500" ng-disabled="f.READONLY" ></textarea>
										<div ng-messages="formParent.DIAGNOSIS.$error" role="alert" md-auto-hide="false">
											<div ng-message="md-maxlength">character reach the max limit.</div>
										</div>
									</md-input-container>
									<md-button ng-click="Copy_Diagnosis()" ng-disabled="f.READONLY" class="md-primary btn-icon">
										<md-tooltip md-direction="top">Copy Previous Diagnosis</md-tooltip>
										<md-icon class="material-icons">filter_none</md-icon>
									</md-button>
								</div>


								<div layout="row" layout-align="start start">
									<md-input-container flex>
										<label>Procedure/s Done</label>
										<textarea ng-model="f.PROCEDURE_DONE" name="PROCEDURE_DONE" md-maxlength="500" ng-disabled="f.READONLY" ></textarea>
										<div ng-messages="formParent.PROCEDURE_DONE.$error" role="alert" md-auto-hide="false">
											<div ng-message="md-maxlength">character reach the max limit.</div>
										</div>
									</md-input-container>
									<md-button ng-click="Copy_Procedure()" ng-disabled="f.READONLY" class="md-primary btn-icon">
										<md-tooltip md-direction="top">Copy Previous Procedure/s Done</md-tooltip>
										<md-icon class="material-icons">filter_none</md-icon>
									</md-button>
								</div>

								<div layout="row" layout-align="start start">
									<md-input-container flex>
										<label>Instruction</label>
										<textarea ng-model="f.INSTRUCTION" name="INSTRUCTION" md-maxlength="500" ng-disabled="f.READONLY" ></textarea>
										<div ng-messages="formParent.INSTRUCTION.$error" role="alert" md-auto-hide="false">
											<div ng-message="md-maxlength">character reach the max limit.</div>
										</div>
									</md-input-container>
									<md-button ng-click="Copy_Instruction()" ng-disabled="f.READONLY" class="md-primary btn-icon">
										<md-tooltip md-direction="top">Copy Previous Instruction</md-tooltip>
										<md-icon class="material-icons">filter_none</md-icon>
									</md-button>
								</div>


								<div class="table-detail mb-5">
									<table>
										<thead>
											<tr>
												<th colspan="4" rowspan="2">
													<label>Prescription</label>
												</th>
											</tr>
											<tr>
												<th class="action">
													<md-button ng-disabled="f.READONLY" ng-click="Get_Lastest_Prescription($event)" class="md-primary">
														<md-tooltip md-direction="top">Copy Previous prescription</md-tooltip>
														<md-icon class="material-icons">filter_none</md-icon>
													</md-button>
												</th>
											</tr>
											<tr>
												<th>Detail</th>
												<th style="width:70px;">Duration</th>
												<th style="width:70px;">Tabs</th>
												<th>Instruction</th>
												<th class="action">
													<md-button ng-click="Add_Prescription();"  ng-disabled="f.READONLY" class="md-primary ">
														<md-tooltip md-direction="top">Add Prescription</md-tooltip>
														<md-icon class="material-icons">add</md-icon>
													</md-button>
												</th>
											</tr>
										</thead>
										<tbody>
											<tr ng-repeat="(key,m) in f.MEDICINES">
												<td>
													<md-input-container class="md-block"> 
														<label>&nbsp;</label>
														<md-select ng-model="m.MEDICINEID" ng-disabled="m.CANCELLED || f.READONLY" md-on-open="m.search='';" md-on-close="m.search= '';" ng-required="!m.CANCELLED" aria-label="true">
															<md-select-header>
																<input ng-model="m.search" ng-keydown="$event.stopPropagation()"  type="text" placeholder="Search Medicines" class="md-select-search">
															</md-select-header>
															<md-optgroup label="List of Medicine">
																<md-option ng-value="A.ID" ng-bind="A.NAME" ng-repeat="A in LIST.MEDICINES | filter: (m.search || m.MEDICINEID) | limitTo: 50"></md-option>
															</md-optgroup>
														</md-select>
													</md-input-container>
												</td>
												<td>
													<div style="max-width: 100px;">
														<md-autocomplete md-search-text="m.FREQUENCY" md-items="item in LIST.DURATIONS | filter: m.FREQUENCY" md-item-text="item.NAME" md-min-length="0"  ng-disabled="m.CANCELLED || f.READONLY" >
															<md-item-template>
																<span ng-bind="item.NAME"></span>
															</md-item-template>
															<md-not-found>
																<span class="text-muted">New data will be added.</span>
															</md-not-found>
														</md-autocomplete>
													</div>
												</td>
												<td>
													<md-input-container class="md-block">
														<input ng-model="m.QUANTITY" ng-disabled="m.CANCELLED || f.READONLY"  type="number" class="text-center" ng-required="!m.CANCELLED" aria-label="true">
													</md-input-container>
												</td>
												<td>
													<md-autocomplete md-search-text="m.INSTRUCTION" md-items="item in Instruction_Items(m.INSTRUCTION)" md-item-text="item.NAME" md-min-length="0"  ng-disabled="m.CANCELLED || f.READONLY" md-mode="virtual" md-menu-class="autocomplete-virtual-template" md-menu-container-class="virtual-container">
														<md-item-template>
															<p ng-bind="item.NAME" class="item-desc"></p>
														</md-item-template>
														<md-not-found>
															<span class="text-muted">New data will be added.</span>
														</md-not-found>
													</md-autocomplete>
												</td>
												<td class="action">
													<md-button ng-click="Remove_Prescription(key);" ng-disabled="f.READONLY" class="{{m.CANCELLED ? 'md-warn' : 'md-primary'}} ">
														<md-icon ng-hide="m.CANCELLED" class="material-icons">remove</md-icon>
														<md-icon ng-show="m.CANCELLED" class="material-icons">replay</md-icon>
													</md-button>
												</td>
											</tr>
										</tbody>
									</table>  
								</div>


								<div layout="row" layout-align="start start" class="mb-5">
									<md-input-container flex>
										<label>Medication</label>
										<textarea ng-model="f.MEDICATION" name="MEDICATION" md-maxlength="500" ng-disabled="f.READONLY" ></textarea>
										<div ng-messages="formParent.MEDICATION.$error" role="alert" md-auto-hide="false">
											<div ng-message="md-maxlength">character reach the max limit.</div>
										</div>
									</md-input-container>
									<md-button ng-click="Copy_Medication()" ng-disabled="f.READONLY" class="md-primary btn-icon">
										<md-tooltip md-direction="top">Copy Previous Medication</md-tooltip>
										<md-icon class="material-icons">filter_none</md-icon>
									</md-button>
								</div> 
								

								<table class="w-100">
									<tr>
										<td>
											<div ng-hide="opt.editICD">
												<small class="text-muted"><strong>ICD CODE</strong></small>
												<small ng-bind="f.ICD_CODE"></small>
												<pre ng-bind="f.ICD_DESCRIPTION" class="text-mutxed"></pre>
											</div>

											<div ng-show="opt.editICD" >
												<md-autocomplete  md-search-text="opt.searchICD"  md-items="item in Search_ICD(opt.searchICD)"	md-item-text="item.ITEMCODE" md-selected-item-change="Selected_ICD(item)" md-min-length="0"  ng-disabled="f.READONLY" placeholder="Search ICD" md-mode="virtual" md-menu-class="autocomplete-virtual-template" md-menu-container-class="virtual-container">
													<md-item-template>
														<span class="item-title">
															<span> {{item.ITEMCODE}} </span>
														</span>
														<p class="item-desc">{{item.ITEMDESCRIPTION}}</p>
													</md-item-template>
													<md-not-found>
														<span class="text-muted">Search not found.</span>
													</md-not-found>
												</md-autocomplete>
											</div>

										</td>
										<td style="width:1px;">
											<div layout="row" layout-align="end start">
												<md-button ng-click="opt.editICD = true;" ng-hide="f.READONLY || opt.editICD" class="btn-icon md-primary m-0">
													<md-tooltip md-direction="top">Select ICD</md-tooltip>
													<md-icon class="material-icons">refresh</md-icon>
												</md-button>
												<md-button ng-click="Clear_ICD();" ng-hide="f.READONLY || opt.editICD || f.ICD_CODE.length==0" class="btn-icon md-warn m-0">
													<md-tooltip md-direction="top">Clear Selected ICD</md-tooltip>
													<md-icon class="material-icons">remove_circle</md-icon>
												</md-button>
												<md-button ng-click="opt.editICD = false;" ng-hide="f.READONLY || !opt.editICD" class="btn-icon md-warn m-0">
													<md-tooltip md-direction="top">Cancel</md-tooltip>
													<md-icon class="material-icons">close</md-icon>
												</md-button>
											</div>
										</td>
									</tr>

									<tr>
										<td colspan="5" >
											<md-divider></md-divider>
										</td>
									</tr>

									<tr>
										<td>
											<div ng-hide="opt.editRVS">
												<small class="text-muted"><strong>RVS CODE</strong></small>
												<small ng-bind="f.RVS_CODE"></small>
												<pre ng-bind="f.RVS_DESCRIPTION" class="text-mutexd"></pre>
											</div>

											<div ng-show="opt.editRVS">
												<md-autocomplete flex="" md-search-text="opt.searchRVS"  md-items="item in Search_RVS(opt.searchRVS)"	md-item-text="item.ITEMCODE" md-selected-item-change="Selected_RVS(item)" md-min-length="0"  ng-disabled="f.READONLY" placeholder="Search RVS" md-mode="virtual" md-menu-class="autocomplete-virtual-template" md-menu-container-class="virtual-container">
													<md-item-template>
														<span class="item-title">
															<span> {{item.ITEMCODE}} </span>
														</span>
														<p class="item-desc">{{item.ITEMDESCRIPTION}}</p>
													</md-item-template>
													<md-not-found>
														<span class="text-muted">Search not found.</span>
													</md-not-found>
												</md-autocomplete>
											</div>

										</td>
										<td>
											<div layout="row" layout-align="end start">
												<md-button ng-click="opt.editRVS = true;" ng-hide="f.READONLY || opt.editRVS" class="btn-icon md-primary m-0">
													<md-tooltip md-direction="top">Select RVS</md-tooltip>	
													<md-icon class="material-icons">refresh</md-icon>
												</md-button>
												<md-button ng-click="Clear_RVS();" ng-hide="f.READONLY || opt.editRVS || f.RVS_CODE.length==0" class="btn-icon md-warn m-0">
													<md-tooltip md-direction="top">Clear Selected RVS</md-tooltip>	
													<md-icon class="material-icons">remove_circle</md-icon>
												</md-button>
												<md-button  ng-click="opt.editRVS = false;" ng-hide="f.READONLY || !opt.editRVS" class="btn-icon md-warn m-0">
													<md-tooltip md-direction="top">Cancel</md-tooltip>	
													<md-icon class="material-icons">close</md-icon>
												</md-button>
											</div>
										</td>
									</tr>
								</table>

							</md-card-content>
						</md-tab>


						<md-tab ng-if="f.CREATEDBY == 0" label="FILES">
							<md-card-content style="min-height: 100px;">
								<div ng-show="f.ID > 0">

									<div ng-hide="opt.uploadView">
										<div layout="row" layout-wrap="" layout-align="start start">
											<div ng-repeat="A in f.IMAGES" flex="auto">

												<md-button ng-show="A.EXT == 'IMG'" ng-click="File_View($event,A);" class="md-icon-botton" style="height: 60px;width: 60px;" aria-label="a">
													<img src="{{A.IMAGEPATH}}" style="height: 80%;width: 100%;" alt="{{A.IMAGEPATH}}">
												</md-button>

												<md-button ng-show="A.EXT == 'OTH'" class="md-icon-botton" style="height: 60px;width: 60px;">
													<md-icon class="material-icons">attachment</md-icon>
												</md-button>

												<md-button ng-show="A.EXT == 'PDF'" ng-click="File_View($event,A);" class="md-icon-botton" style="height: 60px;width: 60px;">
													<md-icon class="material-icons">picture_as_pdf</md-icon>
												</md-button>

											</div>
										</div>

										<div ng-hide="f.READONLY">
											<md-button ng-click="Toggle_Upload()" class="btn-icon md-primary">
												<md-tooltip md-direction="top">Attach file</md-tooltip>	
												<md-icon class="material-icons">attachment</md-icon>
											</md-button>
										</div>
									</div>
									
									<div ng-show="opt.uploadView">
										<div class="text-right">
											<md-button ng-click="Toggle_Upload()" class="md-fab md-mini md-warn md-fab-top-right">
												<md-tooltip md-direction="top">Toggle Attachment</md-tooltip>	
												<md-icon class="material-icons">close</md-icon>
											</md-button>
										</div>
										<ng-dropzone  options="dzOptions" callbacks="dzCallbacks" methods="dzMethods"  class="dropzone"></ng-dropzone>
									</div>
								</div>	
								<div ng-show="f.ID == 0" class="text-muted">
									Please save first before uploading files.
								</div>

							</md-card-content>
						</md-tab>

						<md-tab ng-if="opt.viewSales" label="BILLING" >
							<md-card-content >

								<div class="table-detail mb-5">
									<table>
										<thead>
											<tr>
												<th colspan="4"><div class="p-2">Services</div></th>
												<th>
													<md-button ng-click="Refresh_Services_Discount();"  ng-disabled="f.READONLY || f.CREATEDBY == 0" class="md-primary ">
														<md-tooltip md-direction="left">Refresh Services & Discount</md-tooltip>
														<md-icon class="material-icons">refresh</md-icon>
													</md-button>
												</th>
											</tr>
											<tr>
												<th>DETAIL</th>
												<th style="width: 80px;">Unit Price</th>
												<th style="width: 70px;">Qty.</th>
												<th style="width: 100px;">Amount</th>
												<th class="action">
													<md-button ng-click="Add_Services();"  ng-disabled="f.READONLY" class="md-primary ">
														<md-icon class="material-icons">add</md-icon>
													</md-button>
												</th>
											</tr>
										</thead>
										<tbody>
											<tr ng-repeat="(key,s) in f.SERVICES">
												<td>
													<md-input-container class="md-block">
														<label>&nbsp;</label>
														<md-select ng-model="s.SERVICEID" ng-change="Lookup_Service(s);Calculate_Discount();" md-on-close="s.search='';" ng-disabled="s.CANCELLED || f.READONLY || (f.CREATEDBY > 0 && s.ID)" ng-required="!s.CANCELLED" aria-label="true">
															<md-select-header>
																<input ng-model="s.search" ng-keydown="$event.stopPropagation()"  type="text" placeholder="Search Services" autofocus class="md-select-search">
															</md-select-header>
															<md-optgroup label="List of Services">
																<md-option ng-value="A.ID" ng-bind="A.NAME" ng-repeat="A in LIST.SERVICES |filter: s.search "></md-option>
															</md-optgroup>
														</md-select>
													</md-input-container>
												</td>
												<td>
													<md-input-container class="md-block">
														<!-- <div ng-bind="s.UNITPRICE|number:2" class="text-muted p-2 text-right"></div> -->
														<input ng-model="s.UNITPRICE" ng-change="Calculate_Services();Calculate_Discount();" ng-disabled="s.CANCELLED || f.READONLY || (f.CREATEDBY > 0 && s.ID)" ng-required="!s.CANCELLED" type="number" min="0" step="0.01" class="text-center" aria-label="true"> 
													</md-input-container>
												</td>
												<td>
													<md-input-container class="md-block">
														<input ng-model="s.QUANTITY" ng-change="Calculate_Services();Calculate_Discount();" ng-disabled="s.CANCELLED || f.READONLY || (f.CREATEDBY > 0 && s.ID)" ng-required="!s.CANCELLED" type="number" min="1" class="text-center" aria-label="true"> 
													</md-input-container>
												</td>
												<td>
													<md-input-container class="md-block">
														<div ng-bind="s.AMOUNT|number:2" class="text-muted p-2 text-right"></div>
													</md-input-container>
												</td>
												<td >
													<md-button ng-click="Remove_Services(key);" ng-disabled="f.READONLY || (f.CREATEDBY > 0 && s.ID)" class="{{s.CANCELLED ? 'md-warn' : 'md-primary'}} ">
														<md-icon ng-hide="s.CANCELLED" class="material-icons">remove</md-icon>
														<md-icon ng-show="s.CANCELLED" class="material-icons">replay</md-icon>
													</md-button>
												</td>
											</tr>
										</tbody>
									</table>
								</div>



								<div class="table-detail mb-5">
									<table>
										<thead>
											<tr>
												<th colspan="5"><div class="p-2">Discount</div></th>
											</tr>
											<tr>
												<th>DETAIL</th>
												<th style="width: 100px;" class="text-center">% / Php</th>
												<th style="width: 100px;">Amount</th>
												<th  class="action">
													<md-button ng-click="Add_Discounts()"  ng-disabled="f.READONLY" class="md-primary ">
														<md-icon class="material-icons">add</md-icon>
													</md-button>
												</th>
											</tr>
										</thead>
										<tbody>
											<tr ng-repeat="(key,d) in f.DISCOUNTS">
												<td>
													<md-input-container class="md-block">
														<label>&nbsp;</label>
														<md-select ng-model="d.DISCOUNTID" ng-change="Lookup_Discount(d)" md-on-close="d.search='';" ng-disabled="d.CANCELLED || f.READONLY || (f.CREATEDBY > 0 && d.ID)" ng-required="!d.CANCELLED" aria-label="true">
															<md-select-header>
																<input ng-model="d.search" ng-keydown="$event.stopPropagation()"  type="text" placeholder="Search Discount" class="md-select-search">
															</md-select-header>
															<md-optgroup label="List of Discount">
																<md-option ng-value="A.ID" ng-bind="A.NAME" ng-repeat="A in LIST.DISCOUNTS |filter: d.search "></md-option>
															</md-optgroup>
														</md-select>
													</md-input-container>
												</td>
												<td>
													<md-input-container class="md-block">
														<div ng-bind="( d.PERCENTAGE ? d.PERCENT +' %' : 'Php')" class="text-center p-2"></div>
													</md-input-container>
												</td>
												<td>
													<md-input-container >
														<input ng-model="d.AMOUNT" ng-change="Calculate_Discount()" ng-disabled="d.CANCELLED || d.PERCENTAGE || f.READONLY || (f.CREATEDBY > 0 && d.ID)" ng-required="!d.CANCELLED" type="number" step="0.01" min="0" class="text-right" aria-label="true">
													</md-input-container>

												</td>
												<td>
													<md-button ng-click="Remove_Discounts(key)"  ng-disabled="f.READONLY || (f.CREATEDBY > 0 && d.ID)" class="{{d.CANCELLED ? 'md-warn' : 'md-primary'}} ">
														<md-icon ng-hide="d.CANCELLED" class="material-icons">remove</md-icon>
														<md-icon ng-show="d.CANCELLED" class="material-icons">replay</md-icon>
													</md-button>
												</td>
											</tr>
										</tbody>
									</table>
								</div>


								<h6>Payment Info</h6>

								<div layout="row" layout-wrap="">

									<div flex="100" flex-gt-xs="50" >

										<table class="mb-4">
											<tr>
												<td><label class="text-muted">Services</label></td>
												<td><div ng-bind="f.GROSSAMOUNT|number:2" class=" text-right "></div></td>
											</tr>
											<tr>
												<td><label class="text-muted">Discounts</label></td>
												<td><div ng-bind="f.DISCOUNTAMOUNT|number:2" class="text-right"></div></td>
											</tr>
											<tr>
												<td colspan="2">
													<md-divider></md-divider>
												</td>
											</tr>
											<tr>
												<td><label md-colors="{color:'primary'}" class="pr-5">Net Payables</label></td>
												<td><div ng-bind="f.NETPAYABLES|number:2" class="text-right "></div></td>
											</tr>

											<tr>
												<td><label class="text-muted">HMO</label></td>
												<td><div ng-bind="f.HMOAMOUNT|number:2" class="text-right"></div></td>
											</tr>
											<tr>
												<td><label class="text-muted">CASH</label></td>
												<td><div ng-bind="f.PAIDAMOUNT|number:2" class="text-right"></div></td>
											</tr>
											<tr>
												<td colspan="2">
													<md-divider></md-divider>
												</td>
											</tr>
											<tr>
												<td><label md-colors="{color:'warn'}">Amount Due</label></td>
												<td><div ng-bind="f.NETPAYABLES - (f.PAIDAMOUNT + f.HMOAMOUNT) | number:2"  class="text-right"></div></td>
											</tr>
										</table>

									</div>

									<div flex="100" flex-gt-xs="50" >

										<div layout="row">
											<md-input-container >
												<label>Type</label>
												<md-select ng-model="f.PAYMODE" name="PAYMODE" ng-change="f.PAYMODE = Check_Select_Value(f.PAYMODE); HMO();" ng-disabled="f.READONLY" required>
													<md-option value=""></md-option>
													<md-option value="CASH">CASH</md-option>
													<md-option value="CHARGE">CHARGE</md-option>
												</md-select>
												<div ng-messages="formParent.PAYMODE.$error" role="alert" md-auto-hide="false">
													<div ng-message="required">This is required.</div>
												</div>
											</md-input-container>

											<md-input-container >
												<label>HMO</label>
												<md-select ng-model="f.HMOID" name="HMOID" ng-change="f.HMOID = Check_Select_Value(f.HMOID); " ng-disabled="f.PAYMODE!='CHARGE' || f.READONLY" ng-required="f.PAYMODE=='CHARGE'">
													<md-option value=""></md-option>
													<md-option value="{{A.ID}}" ng-bind="A.NAME" ng-repeat="A in LIST.HMO"></md-option>
												</md-select>
												<div ng-messages="formParent.HMOID.$error" role="alert" md-auto-hide="false">
													<div ng-message="required">This is required.</div>
												</div>
											</md-input-container>

											<md-input-container flex="">
												<label>HMO Amount</label>
												<input name="HMOAMOUNT" ng-model="f.HMOAMOUNT" ng-change="Billing_Changed();" ng-disabled="f.PAYMODE!='CHARGE' || f.READONLY" ng-required="f.PAYMODE=='CHARGE'" min="0" type="number" step="0.01"  class="text-right">
											</md-input-container>
										</div>


										<md-input-container class="md-block">
											<label>Amount Received</label>
											<input name="AMOUNT" ng-model="f.AMOUNT" ng-change="Billing_Changed();" type="number" step="0.01" ng-disabled="f.READONLY" class="text-right" required>
											<div ng-messages="formParent.AMOUNT.$error" role="alert" md-auto-hide="false">
												<div ng-message="required">This is required.</div>
											</div>
										</md-input-container>

										<div layout="row">
											<div flex="" class="text-muted">Change</div>
											<h5 ng-bind="f.AMOUNTCHANGE|number:2" class="text-right m-0"></h5>
										</div>

									</div>
								</div>


							</md-card-content>
						</md-tab>

					</md-tabs>

					<md-card-content>
						<div layout="column" layout-gt-xs="row" layout-align="end" >
							<md-button ng-disabled="opt.isSubmit || formParent.$invalid || f.READONLY"  type="submit" class="md-raised md-primary">
								<div ng-hide="formParent.$invalid">
									<span ng-hide="opt.isSubmit">Save</span>
									<div ng-show="opt.isSubmit" layout="row" layout-align="center">
										<md-progress-circular md-mode="indeterminate" md-diameter="30"></md-progress-circular>
									</div>
								</div>
								<div ng-show="formParent.$invalid">
									<span>Required field!</span>
								</div>
							</md-button>

							<md-button href="{{opt.cancelUrl}}"  class="md-accent" >Cancel</md-button>
						</div>
					</md-card-content>
				</md-card>

			</div>
		</form>

	</div> <!-- container end -->


</section>


