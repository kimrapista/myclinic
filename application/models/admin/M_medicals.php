<?php
defined('BASEPATH') OR exit('No direct script access allowed');


class M_medicals extends CI_Model
{
	
	function __construct(){ $this->load->database(); }


	function submit_medical_record()
	{

		$_POST += json_decode(file_get_contents('php://input'), true);

        $data = array();

        $data['err']= '';
        $data['suc']=array();

        $this->load->library('form_validation');
        $this->form_validation->set_rules('TOKEN','token', 'trim|required');
        $this->form_validation->set_rules('BRANCH','Branch','trim|required');
        $this->form_validation->set_rules('CHECKUPDATE','Date Check-up','trim|required');
        $this->form_validation->set_rules('APPOINTMENT','Appointment','trim');
        $this->form_validation->set_rules('APPOINTMENTDATE','Date Appointment','trim');
        $this->form_validation->set_rules('AGE','Age','trim|required');
        $this->form_validation->set_rules('REFFEREDBY','Reffered By','trim');
        $this->form_validation->set_rules('MEDOTHERS','Medical Other','trim');
        $this->form_validation->set_rules('REFOD','OD','trim');
        $this->form_validation->set_rules('REFOS','FOS','trim');
        $this->form_validation->set_rules('REFADD','ADD','trim');
        $this->form_validation->set_rules('REFPD','PD','trim');
        $this->form_validation->set_rules('CHEIFCOMPLAINT','Chief Complaint','trim');
        $this->form_validation->set_rules('FINDINGS','Findings','trim');                    
        $this->form_validation->set_rules('DIAGNOSIS','Diagnosis','trim');                    
        $this->form_validation->set_rules('MEDICATION','Medication','trim');
        $this->form_validation->set_rules('REMARKS','Remarks','trim');                      
        $this->form_validation->set_rules('HMOID','HMO','trim');                    
        $this->form_validation->set_rules('COMPANY','Company','trim');                    
        $this->form_validation->set_rules('PAYMODE','Pay Mode','trim|required');                    
        $this->form_validation->set_rules('AMOUNT','Amount','trim|required'); 
        $this->form_validation->set_rules('AMOUNTCHANGE','Amount','trim'); 
        $this->form_validation->set_rules('CANCELLED','Cancelled','trim');  

        $this->form_validation->set_rules('SERVICES[]','Services','trim');
        $this->form_validation->set_rules('DISCOUNTS[]','Discount','trim');  
        $this->form_validation->set_rules('IMAGES','Images','trim');                    
        $this->form_validation->set_rules('MEDICINES[]','Discount','trim'); 
        $this->form_validation->set_rules('DISEASES[]','Discount','trim'); 

        if ($this->form_validation->run() == TRUE){

            if( $this->my_utilities->token($this->input->post('TOKEN'),1) ){

            	$MEDID = $this->my_utilities->token($this->input->post('TOKEN'),2)[0];
                $PATIENTID = $this->my_utilities->token($this->input->post('TOKEN'),2)[1];
                
                               
                if( $MEDID > 0 && empty($data['err']) ){

                    $this->db->update('medicalrecords',
                        array(
                            'BRANCH' => $this->input->post('BRANCH'),
                            'CHECKUPDATE' => date('Y-m-d',strtotime($this->input->post('CHECKUPDATE'))),
                            'APPOINTMENT' => $this->input->post('APPOINTMENT'),
                            'APPOINTMENTDATE' => date('Y-m-d H:i:s',strtotime($this->input->post('APPOINTMENTDATE'))),
                            'AGE' => $this->input->post('AGE'),
                            'REFFEREDBY' => $this->input->post('REFFEREDBY'),
                            'MEDOTHERS' => $this->input->post('MEDOTHERS'),
                            'REFOD' => $this->input->post('REFOD'),
                            'REFOS' => $this->input->post('REFOS'),
                            'REFADD' => $this->input->post('REFADD'),
                            'REFPD' => $this->input->post('REFPD'),
                            'CHEIFCOMPLAINT' => $this->input->post('CHEIFCOMPLAINT'),
                            'FINDINGS' => $this->input->post('FINDINGS'),
                            'DIAGNOSIS' => $this->input->post('DIAGNOSIS'),
                            'MEDICATION' => $this->input->post('MEDICATION'),
                            'REMARKS' => $this->input->post('REMARKS'),
                            'HMOID' => $this->input->post('HMOID'),
                            'COMPANY' => $this->input->post('COMPANY'),
                            'PAYMODE' => $this->input->post('PAYMODE'),
                            'AMOUNT' => $this->input->post('AMOUNT'),
                            'AMOUNTCHANGE' => $this->input->post('AMOUNTCHANGE'),
                            'CANCELLED' => $this->input->post('CANCELLED'),
                            
                            'UPDATEDBY' => $this->session->userid,
                            'UPDATEDTIME' => date('Y-m-d H:i:s',time())
                        ),
                        array('ID'=>$MEDID)
                    );

                    $data['suc']['redirect'] = base_url('patients/'.$PATIENTID.'/record');
                
            	}
            	else if ( $MEDID === 0  && empty($data['err']) )
            	{

                    $datec = date('Y-m-d H:i:s',time());

                    $this->db->insert('medicalrecords',
	                    array(
                            'CLINICID' => $this->session->CLINICID,
                            'BRANCH' => $this->input->post('BRANCH'),
                            'CHECKUPDATE' => date('Y-m-d',strtotime($this->input->post('CHECKUPDATE'))),
                            'APPOINTMENT' => $this->input->post('APPOINTMENT'),
                            'APPOINTMENTDATE' => date('Y-m-d H:i:s',strtotime($this->input->post('APPOINTMENTDATE'))),
	                    	'PATIENTID' => $PATIENTID,
                            'AGE' => $this->input->post('AGE'),
                            'REFFEREDBY' => $this->input->post('REFFEREDBY'),
                            'MEDOTHERS' => $this->input->post('MEDOTHERS'),
                            'REFOD' => $this->input->post('REFOD'),
                            'REFOS' => $this->input->post('REFOS'),
                            'REFADD' => $this->input->post('REFADD'),
                            'REFPD' => $this->input->post('REFPD'),
                            'CHEIFCOMPLAINT' => $this->input->post('CHEIFCOMPLAINT'),
                            'FINDINGS' => $this->input->post('FINDINGS'),
                            'DIAGNOSIS' => $this->input->post('DIAGNOSIS'),
                            'MEDICATION' => $this->input->post('MEDICATION'),
                            'REMARKS' => $this->input->post('REMARKS'),
                            'HMOID' => $this->input->post('HMOID'),
                            'COMPANY' => $this->input->post('COMPANY'),
                            'PAYMODE' => $this->input->post('PAYMODE'),
                            'AMOUNT' => $this->input->post('AMOUNT'),
                            'AMOUNTCHANGE' => $this->input->post('AMOUNTCHANGE'),
                            'CANCELLED' => $this->input->post('CANCELLED'),
                            'CREATEDBY' => $this->session->userid,
	                        'CREATEDTIME' => $datec,
                            'CANCELLED' => 'N'
	                    )
	                );

                    $sql = $this->db->query("SELECT ID 
                        FROM medicalrecords 
                        WHERE PATIENTID=? AND CREATEDTIME=? 
                        LIMIT 1",array($PATIENTID,$datec));
                    $MEDID = $sql->row()->ID;

                    $data['suc']['redirect'] = base_url('patients/'.$PATIENTID.'/record');
                }


                // INSERT SERVICES
                if( $MEDID > 0 && empty($data['err']) )
                {
                    foreach ($this->input->post('SERVICES[]') as $key => $v) 
                    {
                        if( $v['EDIT'] ){
                            if( (int)$v['ID'] === 0 ){
                                $this->db->insert('mr_services',array(
                                    'MEDICALRECORDID'=>$MEDID,
                                    'SERVICEID'=>$v['SERVICEID'],
                                    'QUANTITY'=>$v['QUANTITY'],
                                    'CREATEDBY' => $this->session->userid,
                                    'CREATEDTIME' => date('Y-m-d H:i:s',time()),
                                    'CANCELLED'=>'N'
                                    ));
                            }
                            else{
                                $this->db->update('mr_services',array(
                                    'SERVICEID'=>$v['SERVICEID'],
                                    'QUANTITY'=>$v['QUANTITY'],
                                    'UPDATEDBY' => $this->session->userid,
                                    'UPDATEDTIME' => date('Y-m-d H:i:s',time()),
                                    'CANCELLED'=>$v['CANCELLED']
                                    ),
                                    array('ID'=>$v['ID'])
                                );
                            }
                        }
                    }


                    foreach ($this->input->post('DISCOUNTS[]') as $key => $v) 
                    {
                        if( $v['EDIT'] ){
                            if( (int)$v['ID'] === 0 ){
                                $this->db->insert('mr_discounts',array(
                                    'MEDICALRECORDID'=>$MEDID,
                                    'DISCOUNTID'=>$v['DISCOUNTID'],
                                    'AMOUNT' => $v['AMOUNT'],
                                    'CREATEDBY' => $this->session->userid,
                                    'CREATEDTIME' => date('Y-m-d H:i:s',time()),
                                    'CANCELLED'=>'N'
                                    ));
                            }
                            else{
                                $this->db->update('mr_discounts',array(
                                    'DISCOUNTID'=>$v['DISCOUNTID'],
                                    'AMOUNT' => $v['AMOUNT'],
                                    'UPDATEDBY' => $this->session->userid,
                                    'UPDATEDTIME' => date('Y-m-d H:i:s',time()),
                                    'CANCELLED'=>$v['CANCELLED']
                                    ),
                                    array('ID'=>$v['ID'])
                                );
                            }
                        }
                    }

                    foreach ($this->input->post('IMAGES[]') as $key => $v) {
                        $this->db->update('mr_images',array(
                            'CANCELLED'=>$v['CANCELLED']),
                            array('ID'=>$v['ID'])
                        );
                    }

                    foreach ($this->input->post('MEDICINES[]') as $key => $v) 
                    {
                        if( $v['EDIT'] ){
                            if( (int)$v['ID'] === 0 ){
                                $this->db->insert('mr_medicines',array(
                                    'MEDICALRECORDID'=>$MEDID,
                                    'MEDICINEID'=>$v['MEDICINEID'],
                                    'FREQUENCY' => $v['FREQUENCY'],
                                    'INSTRUCTION' => $v['INSTRUCTION'],
                                    'CREATEDBY' => $this->session->userid,
                                    'CREATEDTIME' => date('Y-m-d H:i:s',time()),
                                    'CANCELLED'=> $v['CANCELLED']
                                    ));
                            }
                            else{

                                
                                $this->db->update('mr_medicines',array(
                                    'MEDICINEID'=>$v['MEDICINEID'],
                                    'FREQUENCY' => $v['FREQUENCY'],
                                    'INSTRUCTION' => $v['INSTRUCTION'],
                                    'UPDATEDBY' => $this->session->userid,
                                    'UPDATEDTIME' => date('Y-m-d H:i:s',time()),
                                    'CANCELLED'=>$v['CANCELLED']
                                    ),
                                    array('ID'=>$v['ID'])
                                );
                            }
                        }
                    }

                    foreach ($this->input->post('DISEASES[]') as $key => $v) 
                    {
                        if( $v['EDIT'] ){
                            if( (int)$v['ID'] === 0 ){
                                $this->db->insert('mr_diseases',array(
                                    'MEDICALRECORDID'=>$MEDID,
                                    'DISEASEID'=>$v['DISEASEID'],
                                    'ACTIVE'=>$v['ACTIVE']
                                ));
                            }
                            else{
                                $this->db->update('mr_diseases',array(
                                    'DISEASEID'=>$v['DISEASEID'],
                                    'ACTIVE'=>$v['ACTIVE']
                                    ),
                                    array('ID'=>$v['ID'])
                                );
                            }
                        }
                    }
                }


                $data['suc']['getUrl'] = base_url('medical/edit-medical-record-data/'.$MEDID.'/0');
               

            }
            else
            {
                $data['err'] .='<div>Expired request. Please refresh the page.</div>';
            }

        }
        
        $data['err'] .=validation_errors('<div>','</div>');

        echo json_encode($data);
    }

    



    public function  submit_medical_record_images($MRID){

        
        $error = '';

        if( (int)$MRID > 0 && !empty($_FILES) )
        {

            $sql = $this->db->query("SELECT COUNT(ID) as cnt
                FROM mr_images
                WHERE MEDICALRECORDID=? 
                LIMIT 1",array($MRID));

            $cnt = $sql->row()->cnt;

            $config['upload_path']      = './uploads/';
            $config['file_name']        = 'medicalrecord_image_'.$MRID.'_'.$cnt;
            $config['allowed_types']    = 'jpg|jpeg|png';
            $config['overwrite']        = true;
            $config['max_size']         = 3000;
            $config['max_width']        = 0;

            $this->load->library('upload', $config);
            $this->upload->initialize($config);

            if ( ! $this->upload->do_upload('file'))
            {
                $error = $this->upload->display_errors('<div>','</div>');
                $this->session->upload_save = '';
            }
            else
            {
                $data = $this->upload->data();
                
                $this->db->insert('mr_images',array(
                    'MEDICALRECORDID'=>$MRID,
                    'IMAGEPATH'=>'uploads/'.$data['file_name'],
                    'CANCELLED'=>'N'
                ));
            }
        }


        echo $error;
    }



}



?>