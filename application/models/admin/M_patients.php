<?php
defined('BASEPATH') OR exit('No direct script access allowed');


class M_patients extends CI_Model
{
	
	function __construct(){ $this->load->database(); }


	function submit_patient()
	{

		$_POST += json_decode(file_get_contents('php://input'), true);

        $data = array();

        $data['err']= '';
        $data['suc']=array();

        $this->load->library('form_validation');
        $this->form_validation->set_rules('token','token', 'trim|required');
        $this->form_validation->set_rules('DATEREG','Date Register','trim|required');
        $this->form_validation->set_rules('FIRSTNAME','First Name','trim|required');
        $this->form_validation->set_rules('MIDDLENAME','Middle Name','trim|required');
        $this->form_validation->set_rules('LASTNAME','Last Name','trim|required');
        $this->form_validation->set_rules('DOB','Date of Birth','trim|required');
        $this->form_validation->set_rules('SEX','Sex','trim');
        $this->form_validation->set_rules('BLOODTYPE','Blood Type','trim');
        $this->form_validation->set_rules('POB','Place of Birth','trim');
        $this->form_validation->set_rules('NATIONALITY','Nationality','trim');                    
        $this->form_validation->set_rules('RELIGION','Religion','trim');                    
        $this->form_validation->set_rules('OCCUPATION','Occupation','trim');                    
        $this->form_validation->set_rules('STREETNO','Street No.','trim');                    
        $this->form_validation->set_rules('CITY','City','trim');                    
        $this->form_validation->set_rules('PROVINCE','Province','trim');                    
        $this->form_validation->set_rules('PHONENO','Tel. No','trim');                    
        $this->form_validation->set_rules('MOBILENO','Mobile No.','trim');                    
        $this->form_validation->set_rules('EMAIL','Email','trim');                    
        $this->form_validation->set_rules('PICTURE','Patient Image','trim');                    
        $this->form_validation->set_rules('EMERGENCYCONTACT','Emergency Name','trim');                    
        $this->form_validation->set_rules('EMERGENCYADDRESS','Emergency Address','trim');                    
        $this->form_validation->set_rules('EMERGENCYPHONENO','Emergency Tel. No','trim');                    
        $this->form_validation->set_rules('EMERGENCYMOBILENO','Emergency Mobile No.','trim');                    
        $this->form_validation->set_rules('MEDASTHMA','Medical Check Asthma','trim');                    
        $this->form_validation->set_rules('MEDDM','Medical Check DM','trim');                    
        $this->form_validation->set_rules('MEDHPN','Medical Check HPN','trim');                    
        $this->form_validation->set_rules('MEDIHD','Medical Check IHD','trim');                    
        $this->form_validation->set_rules('MEDSMOKER','Medical Check Smoke','trim');                    
        $this->form_validation->set_rules('MEDOTHERS','Medical Check Other','trim');
        $this->form_validation->set_rules('CANCELLED','Cancel Patient Account','trim');                    


        if ($this->form_validation->run() == TRUE){

            if( $this->my_utilities->token($this->input->post('token'),1) ){

            	$ID = $this->my_utilities->token($this->input->post('token'),2)[0];
                
                $data['err'] .= $this->check_patient_exist(
                    $ID,
                    $this->input->post('FIRSTNAME'),
                    $this->input->post('MIDDLENAME'),
                    $this->input->post('LASTNAME'),
                    $this->input->post('DOB')
                );

                if( $ID > 0 &&  $this->input->post('CANCELLED') == 'Y' ){ $data['err'] = ''; }

                if( $ID > 0 && empty($data['err']) ){

            		$this->db->update('patients',
                        array(
                            'DATEREG' => date('Y-m-d',strtotime($this->input->post('DATEREG'))),
                            'FIRSTNAME' => ucwords(strtolower($this->input->post('FIRSTNAME'))),
                            'MIDDLENAME' => ucwords(strtolower($this->input->post('MIDDLENAME'))),
                            'LASTNAME' => ucwords(strtolower($this->input->post('LASTNAME'))),
                            'DOB' => date('Y-m-d',strtotime($this->input->post('DOB'))),
                            'SEX' => $this->input->post('SEX'),
                            'BLOODTYPE' => $this->input->post('BLOODTYPE'),
                            'POB' => $this->input->post('POB'),
                            'NATIONALITY' => $this->input->post('NATIONALITY'),
                            'RELIGION' => $this->input->post('RELIGION'),
                            'OCCUPATION' => $this->input->post('OCCUPATION'),
                            'STREETNO' => $this->input->post('STREETNO'),
                            'CITY' => $this->input->post('CITY'),
                            'PROVINCE' => $this->input->post('PROVINCE'),
                            'PHONENO' => $this->input->post('PHONENO'),
                            'MOBILENO' => $this->input->post('MOBILENO'),
                            'EMAIL' => $this->input->post('EMAIL'),
                            'PICTURE' => $this->input->post('PICTURE'),
                            'EMERGENCYCONTACT' => $this->input->post('EMERGENCYCONTACT'),
                            'EMERGENCYADDRESS' => $this->input->post('EMERGENCYADDRESS'),
                            'EMERGENCYPHONENO' => $this->input->post('EMERGENCYPHONENO'),
                            'EMERGENCYMOBILENO' => $this->input->post('EMERGENCYMOBILENO'),
                            'MEDASTHMA' => $this->input->post('MEDASTHMA'),
                            'MEDDM' => $this->input->post('MEDDM'),
                            'MEDHPN' => $this->input->post('MEDHPN'),
                            'MEDIHD' => $this->input->post('MEDIHD'),
                            'MEDSMOKER' => $this->input->post('MEDSMOKER'),
                            'MEDOTHERS' => $this->input->post('MEDOTHERS'),
                            'UPDATEDBY' => $this->session->userid,
                            'UPDATEDTIME' => date('Y-m-d H:i:s',time()),
                            'CANCELLED' => $this->input->post('CANCELLED')
                        ),
                        array('ID'=>$ID)
                    );

                    if( $this->input->post('CANCELLED') == 'N' ){
                        $data['suc']['redirect'] = base_url('patients/'.$ID.'/record');
                    }
                    else{
                        $data['suc']['redirect'] = base_url('patients');   
                    }

            	}
            	else if ( $ID === 0  && empty($data['err']) )
            	{

                    $datec = date('Y-m-d H:i:s',time());

                    $this->db->insert('patients',
	                    array(
                            'CLINICID' => $this->session->CLINICID,
                            'DATEREG' => date('Y-m-d',strtotime($this->input->post('DATEREG'))),
	                    	'FIRSTNAME' => ucwords(strtolower($this->input->post('FIRSTNAME'))),
                            'MIDDLENAME' => ucwords(strtolower($this->input->post('MIDDLENAME'))),
                            'LASTNAME' => ucwords(strtolower($this->input->post('LASTNAME'))),
                            'DOB' => date('Y-m-d',strtotime($this->input->post('DOB'))),
                            'SEX' => $this->input->post('SEX'),
                            'BLOODTYPE' => $this->input->post('BLOODTYPE'),
                            'POB' => $this->input->post('POB'),
                            'NATIONALITY' => $this->input->post('NATIONALITY'),
                            'RELIGION' => $this->input->post('RELIGION'),
                            'OCCUPATION' => $this->input->post('OCCUPATION'),
                            'STREETNO' => $this->input->post('STREETNO'),
                            'CITY' => $this->input->post('CITY'),
                            'PROVINCE' => $this->input->post('PROVINCE'),
                            'PHONENO' => $this->input->post('PHONENO'),
                            'MOBILENO' => $this->input->post('MOBILENO'),
                            'EMAIL' => $this->input->post('EMAIL'),
                            'PICTURE' => $this->input->post('PICTURE'),
                            'EMERGENCYCONTACT' => $this->input->post('EMERGENCYCONTACT'),
                            'EMERGENCYADDRESS' => $this->input->post('EMERGENCYADDRESS'),
                            'EMERGENCYPHONENO' => $this->input->post('EMERGENCYPHONENO'),
                            'EMERGENCYMOBILENO' => $this->input->post('EMERGENCYMOBILENO'),
                            'MEDASTHMA' => $this->input->post('MEDASTHMA'),
                            'MEDDM' => $this->input->post('MEDDM'),
                            'MEDHPN' => $this->input->post('MEDHPN'),
                            'MEDIHD' => $this->input->post('MEDIHD'),
                            'MEDSMOKER' => $this->input->post('MEDSMOKER'),
                            'MEDOTHERS' => $this->input->post('MEDOTHERS'),
	                        'CREATEDBY' => $this->session->userid,
	                        'CREATEDTIME' => $datec,
                            'CANCELLED' => 'N'
	                    )
	                );

                    
                    $ID = $this->db->insert_id();
                      
                    $data['suc']['redirect'] = base_url('patients/'.$ID.'/record');
                    
                }

            }
            else
            {
                $data['err'] .='<div>Expired request. Please refresh the page.</div>';
            }

        }
        
        $data['err'] .=validation_errors('<div>','</div>');

        echo json_encode($data);
    }

    


    private function check_patient_exist($id,$fname,$mname,$lname,$dob){

        $fname = trim(str_replace('  ','',preg_replace('/[^0-9a-zA-Z-ñÑ ]/i','',$fname)));
        $mname = trim(str_replace('  ','',preg_replace('/[^0-9a-zA-Z-ñÑ ]/i','',$mname)));
        $lname = trim(str_replace('  ','',preg_replace('/[^0-9a-zA-Z-ñÑ ]/i','',$lname)));

        $sql = $this->db->query("SELECT ID 
            FROM patients 
            where ID!=? AND (FIRSTNAME=? AND MIDDLENAME=? AND LASTNAME=? AND DATE(DOB)=?) AND CANCELLED = 'N' 
            LIMIT 1",array($id,$fname,$mname,$lname,date('Y-m-d',strtotime($dob))));

        if ( $sql->row() ){ return '<div>PATIENT Information is already registered.</div>'; }
        return '';
    }




    public function  submit_patient_picture(){

        $ID = $this->my_utilities->token($_POST['userid'],2)[0];

        $config['upload_path']      = './uploads/';
        $config['file_name']        = 'patient_'.$ID;
        $config['allowed_types']    = 'jpg|jpeg|png';
        $config['overwrite']        = true;
        $config['max_size']         = 1000;
        $config['max_width']        = 0;

        $this->load->library('upload', $config);
        $this->upload->initialize($config);

        if ( ! $this->upload->do_upload('userfile'))
        {
            $this->session->upload_error = $this->upload->display_errors('<div>','</div>');
            $this->session->upload_save = '';
        }
        else
        {
            $data = $this->upload->data();
            $this->session->upload_save = 'Picture uploaded';
            $this->db->update('patients',array('PICTURE'=>'uploads/'.$data['file_name']),array('ID'=>$ID));
        }

    
        redirect(base_url('patients/'.$ID.'/record'));
        
    }





}



?>