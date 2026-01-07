<?php
defined('BASEPATH') OR exit('No direct script access allowed');
//assistant

class M_patients extends CI_Model
{
	
	function __construct(){ $this->load->database(); }


    public function Search_Patients()
    {
        $_POST += json_decode(file_get_contents('php://input'), true);

        $search = preg_replace('/[^0-9a-zA-Z-ñÑ ]/i','',$_POST['search']);
        $currentLimit = $_POST['currentLimit'];
        $limit = $_POST['limit'];

        $sql = $this->db->query("SELECT ID,DATEREG,DOB,SEX,MOBILENO,PHONENO,LASTNAME,FIRSTNAME,MIDDLENAME,TOTALRECORDS

            FROM patients 
            WHERE (concat(FIRSTNAME,' ',MIDDLENAME,' ',LASTNAME) like ? 
            OR concat(LASTNAME,' ',FIRSTNAME,' ',MIDDLENAME) like ?
            OR concat(FIRSTNAME,' ',LASTNAME) like ? )
            AND CLINICID=?
            AND CANCELLED='N'
            ORDER BY  ".( !empty($search) ? 'LASTNAME ASC, FIRSTNAME ASC' : 'DATEREG DESC')."
            LIMIT ?,?",array('%'.$search.'%','%'.$search.'%','%'.$search.'%',$this->session->CLINICID,$currentLimit,$limit))->result();

        return $sql;
    }



    public function Form_Data($id)
    {

        if( (int)$id == 0 )
        {

            $data = array(
                'TOKEN' => $this->m_utility->tokenRequest(),
                'ID' => 0,
                'HRID' => 0,
                'DATEREG' => date('Y-m-d H:i:s',time()),
                'FIRSTNAME' => '',
                'MIDDLENAME' => '',
                'LASTNAME' => '',
                'DOB' => date('Y-m-d',time()),
                'SEX' => 'MALE',
                'BLOODTYPE' => '',
                'POB' => '',
                'NATIONALITY' => '',
                'RELIGION' => '',
                'CIVILSTATUS' => '',
                'OCCUPATION' => '',
                'STREETNO' => '',
                'CITY' => '',
                'PROVINCE' => '',
                'PHONENO' => '',
                'MOBILENO' => '',
                'EMAIL' => '',
                'PICTURE' => '',
                'EMERGENCYCONTACT' => '',
                'EMERGENCYADDRESS' => '',
                'EMERGENCYPHONENO' => '',
                'EMERGENCYMOBILENO' => '',
                'MEDASTHMA' => 'N',
                'MEDDM' => 'N',
                'MEDHPN' => 'N',
                'MEDIHD' => 'N',
                'MEDSMOKER' => 'N',
                'MEDOTHERS' => '',
                'CANCELLED' => 'N',
                'REVISIT' => 'N'
            );

            return $data;
        }
        else {

            $data = $this->db->query("SELECT *  FROM patients where ID =? AND CLINICID=? LIMIT 1",array($id,$this->session->CLINICID))->row();

            if( isset($data) ){

                $data->TOKEN = $this->m_utility->tokenRequest($id);
                return $data;
            }
        }
    }







    public function Patient_Information($id)
    {
        $data = $this->db->query("SELECT * 
            FROM patients  
            WHERE ID = ? AND CLINICID=? AND CANCELLED='N' 
            LIMIT 1",array($id,$this->session->CLINICID))->row_array();

        if( isset($data) ){

            $data['TOKEN'] = $this->m_utility->tokenRequest($id);


            $data['MEDICALS'] = $this->db->query("SELECT MR.ID,MR.CHECKUPDATE,MR.APPOINTMENT,MR.APPOINTMENTDATE,MR.APPOINTMENTDESCRIPTION,MR.AGE,
                MR.CHEIFCOMPLAINT, MR.PRESENTILLNESS, MR.COMORBIDITIES, MR.FINDINGS, MR.DIAGNOSIS, MR.PROCEDURE_DONE,
                MR.HMOAMOUNT,MR.HMORECEIVED,MR.HMODATE,MR.GROSSAMOUNT,MR.DISCOUNTAMOUNT,MR.PAIDAMOUNT, 
                S.NAME AS FROMCLINIC, S1.NAME AS APPOINTCLINIC,
                U.NAME AS CREATEDNAME

                From medicalrecords MR
                LEFT JOIN users U ON U.ID = MR.CREATEDBY
                LEFT JOIN subclinic S ON S.ID = MR.SUBCLINICID
                LEFT JOIN subclinic S1 ON S1.ID = MR.APPOINTMENTSUBCLINICID
                WHERE MR.PATIENTID=? AND MR.CLINICID=? and MR.CANCELLED='N' 
                ORDER BY MR.CHECKUPDATE DESC, MR.ID DESC",array($id,$this->session->CLINICID))->result();


            foreach ($data['MEDICALS'] as $key => $v) {
                
                $v->LABORATORIES = $this->db->query("SELECT L.NAME, ML.TEMPLATERESULT
                    FROM mr_laboratory ML 
                    INNER JOIN laboratory L ON L.ID = ML.LABORATORYID
                    WHERE ML.MEDICALRECORDID = ? AND ML.CANCELLED='N' ",array($v->ID))->result();
            }


            if(  $data['TOTALRECORDS'] != count($data['MEDICALS']) )
                $this->db->update('patients',array('TOTALRECORDS'=>count($data['MEDICALS'])),array('ID'=>$id));

            return $data;
        }
        else{
            return array('RESPONSE'=> 'PATIENT IS ALREADY DELETED.');
        }

    }




    public function Submit_Patient() {

        $_POST += json_decode(file_get_contents('php://input'), true);

        $data = array();

        $data['err']= '';
        $data['suc']=array();

        $this->load->library('form_validation');
        $this->form_validation->set_rules('TOKEN','token', 'trim|required');
        $this->form_validation->set_rules('DATEREG','Date Register','trim|required');
        $this->form_validation->set_rules('HRID','Hospital Record ID','trim');
        $this->form_validation->set_rules('FIRSTNAME','First Name','trim|required');
        $this->form_validation->set_rules('LASTNAME','Last Name','trim|required');
        $this->form_validation->set_rules('DOB','Date of Birth','trim|required');
        $this->form_validation->set_rules('SEX','Sex','trim');
        $this->form_validation->set_rules('BLOODTYPE','Blood Type','trim');
        $this->form_validation->set_rules('POB','Place of Birth','trim');
        $this->form_validation->set_rules('NATIONALITY','Nationality','trim');                    
        $this->form_validation->set_rules('RELIGION','Religion','trim');                    
        $this->form_validation->set_rules('CIVILSTATUS','Civil Status','trim|required');                    
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
        $this->form_validation->set_rules('REVISIT','Revisit Patient','trim');



        if ($this->form_validation->run() ) {

            if( $this->m_utility->tokenCheck($this->input->post('TOKEN')) ) {

                $ID = $this->m_utility->tokenRetrieve($this->input->post('TOKEN'));

                $data['err'] .= $this->Check_Patient_Exist(
                    $ID,
                    $this->input->post('FIRSTNAME'),
                    $this->input->post('MIDDLENAME'),
                    $this->input->post('LASTNAME'),
                    $this->input->post('DOB')
                );


                if( $ID > 0 && empty($data['err']) ){

                    $this->db->update('patients', array(
                        'DATEREG' => date('Y-m-d H:i:s',strtotime($this->input->post('DATEREG'))),
                        'FIRSTNAME' => ucwords(strtolower($this->input->post('FIRSTNAME'))),
                        'MIDDLENAME' => ucwords(strtolower($this->input->post('MIDDLENAME'))),
                        'LASTNAME' => ucwords(strtolower($this->input->post('LASTNAME'))),
                        'DOB' => date('Y-m-d',strtotime($this->input->post('DOB'))),
                        'SEX' => $this->input->post('SEX'),
                        'BLOODTYPE' => $this->input->post('BLOODTYPE'),
                        'POB' => $this->input->post('POB'),
                        'NATIONALITY' => $this->input->post('NATIONALITY'),
                        'RELIGION' => $this->input->post('RELIGION'),
                        'CIVILSTATUS' => $this->input->post('CIVILSTATUS'),
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
                        'REVISIT' => (Boolean)$this->input->post('REVISIT') ? 'Y' : 'N',
                        'UPDATEDBY' => $this->session->userid,
                        'UPDATEDTIME' => date('Y-m-d H:i:s',time())
                    ), array('ID'=>$ID));

                }
                else if ( empty($data['err']) ) {

                    $this->db->insert('patients', array(
                        'CLINICID' => $this->session->CLINICID,
                        'DATEREG' => date('Y-m-d H:i:s',strtotime($this->input->post('DATEREG'))),
                        'FIRSTNAME' => ucwords(strtolower($this->input->post('FIRSTNAME'))),
                        'MIDDLENAME' => ucwords(strtolower($this->input->post('MIDDLENAME'))),
                        'LASTNAME' => ucwords(strtolower($this->input->post('LASTNAME'))),
                        'DOB' => date('Y-m-d',strtotime($this->input->post('DOB'))),
                        'SEX' => $this->input->post('SEX'),
                        'BLOODTYPE' => $this->input->post('BLOODTYPE'),
                        'POB' => $this->input->post('POB'),
                        'NATIONALITY' => $this->input->post('NATIONALITY'),
                        'RELIGION' => $this->input->post('RELIGION'),
                        'CIVILSTATUS' => $this->input->post('CIVILSTATUS'),
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
                        'REVISIT' => (Boolean)$this->input->post('REVISIT') ? 'Y' : 'N',
                        'CREATEDBY' => $this->session->userid,
                        'CREATEDTIME' => date('Y-m-d H:i:s',time()),
                        'CANCELLED' => 'N'
                    ));

                    $ID = $this->db->insert_id();

                }

                $data['suc']['PATIENTID'] = $ID;
            }
            else 
            {
                $data['err'] .='Expired request. Please refresh the page.';
            }
        }

        $data['err'] .=validation_errors(' ',' ');

        echo json_encode($data);
    }



    public function Submit_Remove_Patient(){

        $_POST += json_decode(file_get_contents('php://input'), true);

        $data = array();

        $data['err']= '';
        $data['suc']=array();

        $this->load->library('form_validation');
        $this->form_validation->set_rules('TOKEN','token', 'trim|required');
        $this->form_validation->set_rules('ID','ID','trim|required');

        if ($this->form_validation->run() ) {

            $sql = $this->db->query("SELECT POSITION FROM users WHERE ID=? LIMIT 1", array($this->session->userid))->row();

            if( $sql->POSITION == 'BRANCH ADMINISTRATOR' || $sql->POSITION == 'BRANCH ASSISTANT' ){

                $sql1 = $this->db->query("SELECT count(ID) as cnt FROM medicalrecords WHERE PATIENTID=? AND CANCELLED='N' LIMIT 1",array($this->input->post('ID')))->row();

                if( $sql1->cnt == 0 ){
                    $this->db->update('patients',
                        array(
                            'CANCELLED' => 'Y',
                            'CANCELLEDBY' => $this->session->userid,
                            'CANCELLEDTIME' => date('Y-m-d H:i:s',time())
                        ),
                        array('ID' => $this->input->post('ID'))
                    );

                    $data['suc'] = 'Successfully Deleted';
                }
                else{
                    $data['err'] .= 'Sorry cant delete patient info with medical records.';
                }


            }
            else{
                $data['err'] = 'Only administrator account can delete patient info.';
            }
        }

        $data['err'] .=validation_errors(' ',' ');

        echo json_encode($data);

    }




    public function  Submit_Patient_Picture(){

        $ID = $this->m_utility->tokenRetrieve($_POST['userid']);

        $config['upload_path']      = './uploads/';
        $config['file_name']        = 'patient_'.$ID;
        $config['allowed_types']    = 'jpg|jpeg|png';
        $config['overwrite']        = true;
        $config['max_size']         = 5000;
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
            $this->db->update('patients',array('PICTURE'=>'uploads/'.$data['file_name']),array('ID'=>$ID));

            $this->session->IMAGENAME = $data['file_name'];
            $this->session->AVATAR    = base_url('uploaded/users/'.$data['file_name']);
            $this->session->upload_response = '';

            $config['image_library']    = 'gd2';
            $config['source_image']     = $data['full_path']; 
            $config['maintain_ratio']   = TRUE;

            $exif = exif_read_data($config['source_image'],'IFD0');
            if($exif && isset($exif['Orientation']))
            {
                $ort = $exif['Orientation'];

                if ($ort == 6 || $ort == 5)
                    $config['rotation_angle'] = '270';
                if ($ort == 3 || $ort == 4)
                    $config['rotation_angle'] = '180';
                if ($ort == 8 || $ort == 7)
                    $config['rotation_angle'] = '90';

                $this->load->library('image_lib',$config);

                try {
                    $this->image_lib->rotate();  
                } catch (Exception $e) {

                }
            }
        }

        redirect(base_url('patients/record/'.$ID));
    }







    public function Admitted_Patient($id)
    {
        $data = $this->db->query("SELECT *  FROM patients where ID =? AND CLINICID=? LIMIT 1",array($id,$this->session->CLINICID))->row_array();

        $data['TOKEN']      = $this->m_utility->tokenRequest($id);
        $data['URL']        = base_url('patients/admitPatient');
        $data['DATEREG']    = date('m/d/Y',strtotime($data['DATEREG']));
        $data['DOB']        = date('m/d/Y',strtotime($data['DOB']));
        return $data;
    }


    public function Submit_Admitted_Patient()
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

                    $data['err'] .= $this->Check_AdmittedPatient_Exist(
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

                $data['err'] .='Expired request. Please refresh the page. ';
            }
        }

        $data['err'] .=validation_errors(' ',' ');

        echo json_encode($data);
    }


    private function Check_Patient_Exist($id,$fname,$mname,$lname,$dob){

        $fname = trim(str_replace('  ','',preg_replace('/[^0-9a-zA-Z-ñÑ ]/i','',$fname)));
        $mname = trim(str_replace('  ','',preg_replace('/[^0-9a-zA-Z-ñÑ ]/i','',$mname)));
        $lname = trim(str_replace('  ','',preg_replace('/[^0-9a-zA-Z-ñÑ ]/i','',$lname)));

        $sql = $this->db->query("SELECT ID 
            FROM patients 
            where CLINICID=? AND ID!=? AND FIRSTNAME=? AND MIDDLENAME=? AND LASTNAME=? AND DATE(DOB)=? AND CANCELLED = 'N' 
            LIMIT 1",array( $this->session->CLINICID,$id,$fname,$mname,$lname,date('Y-m-d',strtotime($dob))))->row();

        if ( isset($sql) ){ 
            return ' PATIENT Information is already registered. '; 
        }
        else{ 
            return ''; 
        }
    }


    private function Check_AdmittedPatient_Exist($id,$fname,$mname,$lname,$dob){

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



    

}
?>