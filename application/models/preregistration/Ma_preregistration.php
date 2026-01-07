<?php
defined('BASEPATH') OR exit('No direct script access allowed');


class Ma_preregistration extends CI_Model
{
	
	function __construct(){ $this->load->database(); }


	function submit_preregistration()
	{

		$_POST += json_decode(file_get_contents('php://input'), true);

        $data = array();

        $data['err']= '';
        $data['suc']=array();

        $this->load->library('form_validation');
        $this->form_validation->set_rules('TOKEN','token', 'trim|required');
        $this->form_validation->set_rules('FIRSTNAME','First Name','trim|required');
        $this->form_validation->set_rules('MIDDLENAME','Middle Name','trim|required');
        $this->form_validation->set_rules('LASTNAME','Last Name','trim|required');
        $this->form_validation->set_rules('DATEREG','Date Registerd','trim');
        $this->form_validation->set_rules('DOB','Date of Birth','trim|required');
        $this->form_validation->set_rules('SEX','Sex','trim');
        $this->form_validation->set_rules('BLOODTYPE','Blood Type','trim');
        $this->form_validation->set_rules('POB','Place of Birth','trim');
        $this->form_validation->set_rules('NATIONALITY','Nationality','trim');                    
        $this->form_validation->set_rules('RELIGION','Religion','trim');                    
        $this->form_validation->set_rules('OCCUPATION','Occupation','trim');                    
        $this->form_validation->set_rules('STREETNO','Street No.','trim'); 
        $this->form_validation->set_rules('BARANGAY','Barangay','trim');                   
        $this->form_validation->set_rules('CITY','City','trim');                    
        $this->form_validation->set_rules('PROVINCE','Province','trim');                    
        $this->form_validation->set_rules('PHONENO','Tel. No','trim');                    
        $this->form_validation->set_rules('MOBILENO','Mobile No.','trim');                    
        $this->form_validation->set_rules('EMAIL','Email','trim');                                  
        $this->form_validation->set_rules('EMERGENCYNAME','Emergency Name','trim');                
        $this->form_validation->set_rules('EMERGENCYADDRESS','Emergency Address','trim');      
        $this->form_validation->set_rules('EMERGENCYTELPHONENO','Emergency Tel. No','trim');        
        $this->form_validation->set_rules('EMERGENCYMOBILENO','Emergency Mobile No.','trim');  
        $this->form_validation->set_rules('ALLERGIES','Allergies','trim');
        $this->form_validation->set_rules('SCNO','scno','trim');
        $this->form_validation->set_rules('DATEISSUE','Date Issued', 'trim');
        $this->form_validation->set_rules('SPOUSENAME','Name of The Spouse','trim');
        $this->form_validation->set_rules('SPOUSEADDRESS','Address of The Spouse','trim');
        $this->form_validation->set_rules('SPOUSETELPHONENO','Spouse Telephone No.','trim');
        $this->form_validation->set_rules('SPOUSEOCCUPATION','Spouse Occupation','trim');
        $this->form_validation->set_rules('FATHERNAME','Father Name','trim');
        $this->form_validation->set_rules('FATHERADDRESS','Father address', 'trim');
        $this->form_validation->set_rules('FATHERTELPHONENO','Father Telephone No.','trim');
        $this->form_validation->set_rules('MOTHERNAME','Mother Name', 'trim');
        $this->form_validation->set_rules('MOTHERADDRESS','Mother Address', 'trim');
        $this->form_validation->set_rules('MOTHERTELPHONENO','Mother Telephone No.','trim');

        if ($this->form_validation->run())
        {
            if( $this->m_utility->tokenCheck($this->input->post('TOKEN')))
            {
            	$ID = $this->m_utility->tokenRetrieve($this->input->post('TOKEN'));
                if($ID > 0 &&  !(Boolean)$this->input->post('CANCELLED'))
                {
                    $data['err'] .= $this->check_admittedpatient_exist(
                        $ID,
                        $this->input->post('FIRSTNAME'),
                        $this->input->post('MIDDLENAME'),
                        $this->input->post('LASTNAME'),
                        $this->input->post('DOB')
                    );
                }
                    if( $ID > 0 && empty($data['err']))
                    {
                        $this->db->update('admitted_patient', array(
                        'DATEREG'               => date('Y-m-d H:i:s',strtotime($this->input->post('DATEREG'))),
                        'FIRSTNAME'             => ucwords(strtolower($this->input->post('FIRSTNAME'))),
                        'MIDDLENAME'            => ucwords(strtolower($this->input->post('MIDDLENAME'))),
                        'LASTNAME'              => ucwords(strtolower($this->input->post('LASTNAME'))),
                        'DOB'                   => date('Y-m-d',strtotime($this->input->post('DOB'))),
                        'SEX'                   => $this->input->post('SEX'),
                        'CIVILSTATUS'           => $this->input->post('CIVILSTATUS'),
                        'BLOODTYPE'             => $this->input->post('BLOODTYPE'),
                        'POB'                   => $this->input->post('POB'),
                        'NATIONALITY'           => $this->input->post('NATIONALITY'),
                        'RELIGION'              => $this->input->post('RELIGION'),
                        'OCCUPATION'            => $this->input->post('OCCUPATION'),
                        'STREETNO'              => $this->input->post('STREETNO'),
                        'BARANGAY'              => $this->input->post('BARANGAY'),
                        'CITY'                  => $this->input->post('CITY'),
                        'PROVINCE'              => $this->input->post('PROVINCE'),
                        'TELEPHONENO'           => $this->input->post('TELEPHONENO'),
                        'EMERGENCYNAME'         => $this->input->post('EMERGENCYNAME'),
                        'EMERGENCYADDRESS'      => $this->input->post('EMERGENCYADDRESS'),
                        'EMERGENCYTELPHONENO'   => $this->input->post('EMERGENCYTELPHONENO'),
                        'EMERGENCYRELATION'     => $this->input->post('EMERGENCYRELATION'),
                        'ALLERGIES'             => $this->input->post('ALLERGIES'),
                        'SCNO'                  => $this->input->post('SCNO'),
                        'DATEISSUE'             => date('Y-m-d',strtotime($this->input->post('DATEISSUE'))),
                        'SPOUSENAME'            => $this->input->post('SPOUSENAME'),
                        'SPOUSEADDRESS'         => $this->input->post('SPOUSEADDRESS'),
                        'SPOUSETELPHONENO'      => $this->input->post('SPOUSETELPHONENO'),
                        'SPOUSEOCCUPATION'      => $this->input->post('SPOUSEOCCUPATION'),
                        'FATHERNAME'            => $this->input->post('FATHERNAME'),
                        'FATHERADDRESS'         => $this->input->post('FATHERADDRESS'),
                        'FATHERTELPHONENO'      => $this->input->post('FATHERTELPHONENO'),
                        'MOTHERNAME'            => $this->input->post('MOTHERNAME'),
                        'MOTHERADDRESS'         => $this->input->post('MOTHERADDRESS'),
                        'MOTHERTELPHONENO'      => $this->input->post('MOTHERTELPHONENO'),
                        'ADMITTED'              => 'Y',
                        'POSTDATE'              => date('Y-m-d H:i:s',time()),
                        'POSTBY'                => $this->session->userid,
                        'UPDATEDDATE'           => date('Y-m-d H:i:s',time()),
                        'UPDATEDBY'             => $this->session->userid,
                        'CANCELLEDBY'           => $this->session->userid,
                        'CANCELLEDBY'           => $this->session->userid,
                        'CANCELLED'             => $this->input->post('CANCELLED') ? 'N' : 'Y',
                        'POST'                  => $this->input->post('POST') ? 'N' : 'Y'
                    ), array('ID'=>$ID));

                    $data['suc']['redirect'] = base_url('preregistration');

                    if((Boolean)$this->input->post('CANCELLED')){
                        $this->db->update('admitted_patient', array(
                            'CANCELLEDBY' => $this->session->userid,
                            'CANCELLEDDATE' => date('Y-m-d H:i:s',time())
                        ), array('ID'=>$ID) );
                        $data['suc']['redirect'] = base_url('preregistration');  
                    } 
                    else{
                        $data['suc']['redirect'] = base_url('preregistration');
                    }

                    }
                    else if ( empty($data['err']) ) 
                    {
                        $datec = date('Y-m-d H:i:s',time());
                        $this->db->insert('admitted_patient', array(
                            'CLINICID'              => $this->session->CLINICID,
                            'DATEREG'               => date('Y-m-d',strtotime($this->input->post('DATEREG'))),
                            'FIRSTNAME'             => ucwords(strtolower($this->input->post('FIRSTNAME'))),
                            'MIDDLENAME'            => ucwords(strtolower($this->input->post('MIDDLENAME'))),
                            'LASTNAME'              => ucwords(strtolower($this->input->post('LASTNAME'))),
                            'DOB'                   => date('Y-m-d',strtotime($this->input->post('DOB'))),
                            'SEX'                   => $this->input->post('SEX'),
                            'CIVILSTATUS'           => $this->input->post('CIVILSTATUS'),
                            'BLOODTYPE'             => $this->input->post('BLOODTYPE'),
                            'POB'                   => $this->input->post('POB'),
                            'NATIONALITY'           => $this->input->post('NATIONALITY'),
                            'RELIGION'              => $this->input->post('RELIGION'),
                            'OCCUPATION'            => $this->input->post('OCCUPATION'),
                            'STREETNO'              => $this->input->post('STREETNO'),
                            'BARANGAY'              => $this->input->post('BARANGAY'),
                            'CITY'                  => $this->input->post('CITY'),
                            'PROVINCE'              => $this->input->post('PROVINCE'),
                            'TELEPHONENO'           => $this->input->post('TELEPHONENO'),
                            'EMERGENCYNAME'         => $this->input->post('EMERGENCYNAME'),
                            'EMERGENCYADDRESS'      => $this->input->post('EMERGENCYADDRESS'),
                            'EMERGENCYTELPHONENO'   => $this->input->post('EMERGENCYTELPHONENO'),
                            'EMERGENCYRELATION'     => $this->input->post('EMERGENCYRELATION'),
                            'ALLERGIES'             => $this->input->post('ALLERGIES'),
                            'SCNO'                  => $this->input->post('SCNO'),
                            'DATEISSUE'             => date('Y-m-d',strtotime($this->input->post('DATEISSUE'))),
                            'SPOUSENAME'            => $this->input->post('SPOUSENAME'),
                            'SPOUSEADDRESS'         => $this->input->post('SPOUSEADDRESS'),
                            'SPOUSETELPHONENO'      => $this->input->post('SPOUSETELPHONENO'),
                            'SPOUSEOCCUPATION'      => $this->input->post('SPOUSEOCCUPATION'),
                            'FATHERNAME'            => $this->input->post('FATHERNAME'),
                            'FATHERADDRESS'         => $this->input->post('FATHERADDRESS'),
                            'FATHERTELPHONENO'      => $this->input->post('FATHERTELPHONENO'),
                            'MOTHERNAME'            => $this->input->post('MOTHERNAME'),
                            'MOTHERADDRESS'         => $this->input->post('MOTHERADDRESS'),
                            'MOTHERTELPHONENO'      => $this->input->post('MOTHERTELPHONENO'),
                            'ADMITTED'              => 'Y',
                            'POSTBY'                => $this->session->userid,
                            'POSTDATE'              => date('Y-m-d H:i:s',time()),
                            'CREATEDBY'             => $this->session->userid,
                            'CREATEDDATE'           => date('Y-m-d H:i:s',time()),
                            'POST'                  => 'N',
                            'CANCELLED'             => 'N'
                        ) );
                        $ID = $this->db->insert_id();
                        $data['suc']['redirect'] = base_url('preregistration');
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
    private function check_admittedpatient_exist($id,$fname,$mname,$lname,$dob)
    {
        $fname = trim(str_replace('  ','',preg_replace('/[^0-9a-zA-Z-ñÑ ]/i','',$fname)));
        $mname = trim(str_replace('  ','',preg_replace('/[^0-9a-zA-Z-ñÑ ]/i','',$mname)));
        $lname = trim(str_replace('  ','',preg_replace('/[^0-9a-zA-Z-ñÑ ]/i','',$lname)));

        $sql = $this->db->query("SELECT ID 
            FROM admitted_patient 
            where ID!=? AND (FIRSTNAME=? AND MIDDLENAME=? AND LASTNAME=? AND DATE(DOB)=?) 
            LIMIT 1",array($id,$fname,$mname,$lname,date('Y-m-d',strtotime($dob))));

        if ( $sql->row()){ return '<div>PATIENT Information is already registered.</div>'; }
        else{ return ''; }
    }
    public function admitPatient()
    {
    $_POST += json_decode(file_get_contents('php://input'), true);

        $data = array();

        $data['err']= '';
        $data['suc']=array();

       $this->load->library('form_validation');
        $this->form_validation->set_rules('TOKEN','token', 'trim|required');
        $this->form_validation->set_rules('FIRSTNAME','First Name','trim|required');
        $this->form_validation->set_rules('LASTNAME','Last Name','trim|required');
        $this->form_validation->set_rules('DATEREG','Date Registerd','trim');
        $this->form_validation->set_rules('DOB','Date of Birth','trim|required');
        $this->form_validation->set_rules('SEX','Sex','trim');
        $this->form_validation->set_rules('BLOODTYPE','Blood Type','trim');
        $this->form_validation->set_rules('POB','Place of Birth','trim');
        $this->form_validation->set_rules('NATIONALITY','Nationality','trim');                    
        $this->form_validation->set_rules('RELIGION','Religion','trim');                    
        $this->form_validation->set_rules('OCCUPATION','Occupation','trim');                    
        $this->form_validation->set_rules('STREETNO','Street No.','trim'); 
        $this->form_validation->set_rules('BARANGAY','Barangay','trim');                   
        $this->form_validation->set_rules('CITY','City','trim');                    
        $this->form_validation->set_rules('PROVINCE','Province','trim');                    
        $this->form_validation->set_rules('PHONENO','Tel. No','trim');                    
        $this->form_validation->set_rules('MOBILENO','Mobile No.','trim');                    
        $this->form_validation->set_rules('EMAIL','Email','trim');                                    
        $this->form_validation->set_rules('EMERGENCYNAME','Emergency Name','trim');                    
        $this->form_validation->set_rules('EMERGENCYADDRESS','Emergency Address','trim');                 
        $this->form_validation->set_rules('EMERGENCYTELPHONENO','Emergency Tel. No','trim');                
        $this->form_validation->set_rules('EMERGENCYMOBILENO','Emergency Mobile No.','trim');  
        $this->form_validation->set_rules('ALLERGIES','Allergies','trim');
        $this->form_validation->set_rules('SCNO','scno','trim');
        $this->form_validation->set_rules('DATEISSUE','Date Issued', 'trim');
        $this->form_validation->set_rules('SPOUSENAME','Name of The Spouse','trim');
        $this->form_validation->set_rules('SPOUSEADDRESS','Address of The Spouse','trim');
        $this->form_validation->set_rules('SPOUSETELPHONENO','Spouse Telephone No.','trim');
        $this->form_validation->set_rules('SPOUSEOCCUPATION','Spouse Occupation','trim');
        $this->form_validation->set_rules('FATHERNAME','Father Name','trim');
        $this->form_validation->set_rules('FATHERADDRESS','Father address', 'trim');
        $this->form_validation->set_rules('FATHERTELPHONENO','Father Telephone No.','trim');
        $this->form_validation->set_rules('MOTHERNAME','Mother Name', 'trim');
        $this->form_validation->set_rules('MOTHERADDRESS','Mother Address', 'trim');
        $this->form_validation->set_rules('MOTHERTELPHONENO','Mother Telephone No.','trim');                    
        if ($this->form_validation->run() ){

            if( $this->m_utility->tokenCheck($this->input->post('TOKEN')) ){

                $ID = $this->m_utility->tokenRetrieve($this->input->post('TOKEN'));

                if( $ID > 0 &&  !(Boolean)$this->input->post('CANCELLED')){

                    $data['err'] .= $this->check_admittedpatient_exist(
                        $ID,
                        $this->input->post('FIRSTNAME'),
                        $this->input->post('MIDDLENAME'),
                        $this->input->post('LASTNAME'),
                        $this->input->post('DOB')
                    );
                }
                if ( empty($data['err']) ) {

                    $datec = date('Y-m-d H:i:s',time());

                    $this->db->insert('admitted_patient', array(
                        'CLINICID'              => $this->session->CLINICID,
                        'DATEREG'               => date('Y-m-d',strtotime($this->input->post('DATEREG'))),
                        'FIRSTNAME'             => ucwords(strtolower($this->input->post('FIRSTNAME'))),
                        'MIDDLENAME'            => ucwords(strtolower($this->input->post('MIDDLENAME'))),
                        'LASTNAME'              => ucwords(strtolower($this->input->post('LASTNAME'))),
                        'DOB'                   => date('Y-m-d',strtotime($this->input->post('DOB'))),
                        'SEX'                   => $this->input->post('SEX'),
                        'CIVILSTATUS'           => $this->input->post('CIVILSTATUS'),
                        'BLOODTYPE'             => $this->input->post('BLOODTYPE'),
                        'POB'                   => $this->input->post('POB'),
                        'NATIONALITY'           => $this->input->post('NATIONALITY'),
                        'RELIGION'              => $this->input->post('RELIGION'),
                        'OCCUPATION'            => $this->input->post('OCCUPATION'),
                        'STREETNO'              => $this->input->post('STREETNO'),
                        'BARANGAY'              => $this->input->post('BARANGAY'),
                        'CITY'                  => $this->input->post('CITY'),
                        'PROVINCE'              => $this->input->post('PROVINCE'),
                        'TELEPHONENO'           => $this->input->post('TELEPHONENO'),
                        'EMERGENCYNAME'         => $this->input->post('EMERGENCYNAME'),
                        'EMERGENCYADDRESS'      => $this->input->post('EMERGENCYADDRESS'),
                        'EMERGENCYTELPHONENO'   => $this->input->post('EMERGENCYTELPHONENO'),
                        'EMERGENCYRELATION'     => $this->input->post('EMERGENCYRELATION'),
                        'ALLERGIES'             => $this->input->post('ALLERGIES'),
                        'SCNO'                  => $this->input->post('SCNO'),
                        'DATEISSUE'             => date('Y-m-d',strtotime($this->input->post('DATEISSUE'))),
                        'SPOUSENAME'            => $this->input->post('SPOUSENAME'),
                        'SPOUSEADDRESS'         => $this->input->post('SPOUSEADDRESS'),
                        'SPOUSETELPHONENO'      => $this->input->post('SPOUSETELPHONENO'),
                        'SPOUSEOCCUPATION'      => $this->input->post('SPOUSEOCCUPATION'),
                        'FATHERNAME'            => $this->input->post('FATHERNAME'),
                        'FATHERADDRESS'         => $this->input->post('FATHERADDRESS'),
                        'FATHERTELPHONENO'      => $this->input->post('FATHERTELPHONENO'),
                        'MOTHERNAME'            => $this->input->post('MOTHERNAME'),
                        'MOTHERADDRESS'         => $this->input->post('MOTHERADDRESS'),
                        'MOTHERTELPHONENO'      => $this->input->post('MOTHERTELPHONENO'),
                        'ADMITTED'              => 'Y',
                        'CANCELLED'             => 'N',
                        'POSTBY'                => $this->session->userid,
                        'POSTDATE'              => date('Y-m-d H:i:s',time()),
                        'CREATEDBY'             => $this->session->userid,
                        'CREATEDDATE'           => date('Y-m-d H:i:s',time()),
                        'POST'                  => $this->input->post('POST') ? 'Y' : 'N'
                    ) );

                    $ID = $this->db->insert_id();

                    $data['suc']['redirect'] = base_url('preregistration');
                }
            }
            else {

                $data['err'] .='<div>Expired request. Please refresh the page.</div>';
            }
        }

        $data['err'] .=validation_errors('<div>','</div>');

        echo json_encode($data);
    }
}
?>