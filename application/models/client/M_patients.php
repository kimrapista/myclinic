<?php

defined('BASEPATH') OR exit('No direct script access allowed');



class M_patients extends CI_Model

{

	

	function __construct(){ $this->load->database(); }





    public function Search_Patients()

    {

        $_POST += json_decode(file_get_contents('php://input'), true);



        $SEARCH = trim(preg_replace('/[^0-9a-zA-Z-ñÑ ]/i','',$_POST['SEARCH']));



        $FROM = $_POST['FROM'];

        $TO = $_POST['TO'];



        $ORDERBY = (empty($SEARCH)) ? " DATEREG DESC, CREATEDTIME DESC " : " LASTNAME,FIRSTNAME ";



        $sql = $this->db->query("SELECT ID,DATEREG,DOB,SEX,MOBILENO,LASTNAME,FIRSTNAME,MIDDLENAME

            FROM patients 

            WHERE CLINICID=? 

            AND (concat(FIRSTNAME,' ',LASTNAME) LIKE ? OR concat(LASTNAME,' ',FIRSTNAME) LIKE ? OR MOBILENO LIKE ?)

            AND CANCELLED='N'

            ORDER BY $ORDERBY

            LIMIT ?,? "

            ,array(

                $this->session->CLINICID,

                '%'.$SEARCH.'%',

                '%'.$SEARCH.'%',

                '%'.$SEARCH.'%',

                $FROM,

                $TO

            ))->result();



        return $sql;

    }







    public function Patient_Information($id) {



        $data = $this->db->query("SELECT ID, DATEREG, FIRSTNAME, MIDDLENAME, LASTNAME, DOB, SEX, BLOODTYPE, PICTURE, 

            CIVILSTATUS, STREETNO, CITY, PROVINCE, MOBILENO, OCCUPATION, RELIGION, NATIONALITY, EMAIL

            FROM patients  

            WHERE ID = ? AND CLINICID=? AND CANCELLED='N' 

            LIMIT 1",array($id,$this->session->CLINICID))->row_array();



        if( isset($data) ){



            $data['TOKEN'] = $this->m_utility->tokenRequest($data['ID']);



            return $data;

        }

        else{

            return array('error'=> 'INVALID PATIENT RECORD');

        }



    }







    public function Patient_Medical_Record(){



        $_POST += json_decode(file_get_contents('php://input'), true);



        $PATIENTID = trim(preg_replace('/[^0-9a-zA-Z-ñÑ ]/i','',$_POST['PATIENTID']));

        $FROM = $_POST['FROM'];

        $TO = $_POST['TO'];





        $sql = $this->db->query("SELECT MR.ID, MR.CHECKUPDATE, MR.AGE, MR.APPOINTMENT, MR.APPOINTMENTDATE, MR.APPOINTMENTDESCRIPTION, MR.APPOINTMENTSERVED,

            MR.CHEIFCOMPLAINT, MR.FINDINGS, MR.DIAGNOSIS, MR.OTHERS,  MR.REMARKS,

            MR.REFOD, MR.REFOS, MR.SPHLEFT, MR.SPHRIGHT, MR.CYLAXISLEFT, MR.CYLAXISRIGHT, MR.AXISLEFT, MR.AXISRIGHT, MR.REFADD, MR.REFPD, MR.FRAME, MR.LENS,

            MR.NETPAYABLES, 

            H.NAME AS HMONAME,

            S.NAME AS FROMCLINIC, S1.NAME AS APPOINTCLINIC,

            U.NAME AS CREATEDNAME



            From medicalrecords MR

            INNER JOIN subclinic S ON S.ID = MR.SUBCLINICID

            LEFT JOIN users U ON U.ID = MR.CREATEDBY

            LEFT JOIN subclinic S1 ON S1.ID = MR.APPOINTMENTSUBCLINICID

            LEFT JOIN hmo H ON H.ID = MR.HMOID

            WHERE MR.PATIENTID=? AND MR.CLINICID=? and MR.CANCELLED='N' 

            ORDER BY MR.CHECKUPDATE DESC, MR.ID DESC 

            LIMIT ?,? "

            ,array(

                $PATIENTID,

                $this->session->CLINICID,

                $FROM,

                $TO

            ))->result();





        if( $sql ){



            foreach ($sql as $key => $v) {

                $v->TOTAL_LAB = $this->db->query("SELECT COUNT(ML.ID) AS CNT

                    FROM mr_laboratory ML 

                    INNER JOIN laboratory L ON L.ID = ML.LABORATORYID

                    WHERE ML.MEDICALRECORDID = ? AND ML.CANCELLED='N' ",array($v->ID))->row()->CNT;

                $v->SERVICES = $this->db->query("SELECT S.NAME, MS.AMOUNT

                FROM mr_services MS

                INNER JOIN services S ON S.ID = MS.SERVICEID

                WHERE MS.MEDICALRECORDID = ? AND MS.CANCELLED='N' ",array($v->ID))->result();
            }

        }



        return $sql;

    }





    public function Medical_Record_Preview(){


        $_POST += json_decode(file_get_contents('php://input'), true);

        $MRID = trim(preg_replace('/[^0-9a-zA-Z-ñÑ ]/i','',$_POST['MRID']));

        if( $MRID > 0 ){

            $sql = $this->db->query("SELECT M.*,  P.FIRSTNAME, P.MIDDLENAME, P.LASTNAME
                FROM medicalrecords M
                INNER JOIN patients P ON P.ID = M.PATIENTID
                WHERE M.ID=? LIMIT 1",array($MRID))->row();


            if( $sql ){


                $sql->LABORATORIES =  $this->db->query("SELECT L.NAME, ML.TEMPLATERESULT, ML.IMAGEPATH, ML.FILENAME
                    FROM mr_laboratory ML 
                    INNER JOIN laboratory L ON L.ID = ML.LABORATORYID
                    WHERE ML.MEDICALRECORDID=? AND ML.CANCELLED='N' " ,array($sql->ID))->result();

                $sql->MEDICINES = $this->db->query("SELECT M.NAME, MM.FREQUENCY, MM.QUANTITY, MM.INSTRUCTION
                    FROM mr_medicines MM 
                    INNER JOIN medicines M ON M.ID = MM.MEDICINEID 
                    WHERE MM.MEDICALRECORDID=? AND MM.CANCELLED = 'N'
                    ORDER BY MM.ID ",array($sql->ID))->result();

                $sql->LABMONITORING = $this->db->query("SELECT L.GROUPNAME, L.NAME, L.INDENT, M.LABVALUE
                    FROM lab_monitoring L
                    LEFT JOIN mr_lab_monitoring M ON M.LABMONITORINGID = L.ID AND M.MEDICALRECORDID=?
                    WHERE L.CLINICID=? AND M.CANCELLED='N'
                    ORDER BY L.GROUPNAME, L.SORT", array($sql->ID, $this->session->CLINICID ))->result();

                $sql->PREVIOUS = $this->db->query("SELECT CHECKUPDATE, WEIGHT, HEIGHT, BMI
                    FROM medicalrecords 
                    WHERE  CLINICID=? AND PATIENTID=? AND ID != ? AND DATE(CHECKUPDATE) <= ? AND CANCELLED='N'  
                    ORDER BY CHECKUPDATE DESC, ID DESC
                    LIMIT 1"
                    ,array($this->session->CLINICID, $sql->PATIENTID, $sql->ID, date('Y-m-d',strtotime($sql->CHECKUPDATE)) ))->row();
            }

            return $sql;
        }
        else{
            return array();
        }
    }





    public function Medical_Record_Preview_Laboratory(){


        $_POST += json_decode(file_get_contents('php://input'), true);

        $MRID = trim(preg_replace('/[^0-9a-zA-Z-ñÑ ]/i','',$_POST['MRID']));

        if( $MRID > 0 ){

            $sql = $this->db->query("SELECT L.NAME, ML.TEMPLATERESULT, ML.IMAGEPATH, ML.FILENAME
                FROM mr_laboratory ML 
                INNER JOIN laboratory L ON L.ID = ML.LABORATORYID
                WHERE ML.MEDICALRECORDID=? AND ML.CANCELLED='N' "
                ,array($MRID))->result();

            if( $sql ){


                $pat = $this->db->query("SELECT P.FIRSTNAME, P.MIDDLENAME, P.LASTNAME
                    FROM patients P 
                    INNER JOIN medicalrecords M ON M.PATIENTID = P.ID
                    WHERE M.ID=? 
                    LIMIT 1", array($MRID))->row();

                $lb = $this->db->query("SELECT L.GROUPNAME, L.NAME, L.INDENT, M.LABVALUE
                    FROM lab_monitoring L
                    LEFT JOIN mr_lab_monitoring M ON M.LABMONITORINGID = L.ID AND M.MEDICALRECORDID=?
                    WHERE L.CLINICID=? AND M.CANCELLED='N'
                    ORDER BY L.GROUPNAME, L.SORT", array($MRID, $this->session->CLINICID ))->result();


                return array(
                    'LABORATORY' => $sql,
                    'PATIENT' => $pat,
                    'LAB_MONITORING' => $lb
                );
            }

            return array();
        }
        else{
            return array();
        }
    }



    public function Form_Data($id){

        if( (int)$id == 0 ){

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



            $data = $this->db->query("SELECT *  FROM patients where ID =? AND CLINICID=? AND CANCELLED='N' LIMIT 1",array($id,$this->session->CLINICID))->row();



            if( isset($data) ){



                $data->TOKEN = $this->m_utility->tokenRequest($id);

                return $data;

            }

            else{

                return array('error' => 'INVALID PATIENT INFO');

            }

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

                        'REVISIT' => (Boolean)$this->input->post('REVISIT') ? 'Y' : 'N',

                        'UPDATEDBY' => $this->session->USERID,

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

                        'REVISIT' => (Boolean)$this->input->post('REVISIT') ? 'Y' : 'N',

                        'CREATEDBY' => $this->session->USERID,

                        'CREATEDTIME' => date('Y-m-d H:i:s',time()),

                        'CANCELLED' => 'N'

                    ));



                    $ID = $this->db->insert_id();



                }



                $data['suc']['ID'] = $ID;

            }

            else 

            {

                $data['err'] .='Expired request. Please refresh the page.';

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





    public function Submit_Remove_Patient(){



        $_POST += json_decode(file_get_contents('php://input'), true);



        $data = array();



        $data['err']= '';

        $data['suc']=array();



        $this->load->library('form_validation');

        $this->form_validation->set_rules('TOKEN','token', 'trim|required');

        $this->form_validation->set_rules('ID','ID','trim|required');



        if ($this->form_validation->run() ) {



            $sql = $this->db->query("SELECT POSITION FROM users WHERE ID=? LIMIT 1", array($this->session->USERID))->row();



            if( $sql->POSITION == 'BRANCH ADMINISTRATOR' || $sql->POSITION == 'BRANCH ASSISTANT' ){



                $sql1 = $this->db->query("SELECT count(ID) as cnt FROM medicalrecords WHERE PATIENTID=? AND CANCELLED='N' LIMIT 1",array($this->input->post('ID')))->row();



                if( $sql1->cnt == 0 ){



                    $this->db->update('patients',

                        array(

                            'CANCELLED' => 'Y',

                            'CANCELLEDBY' => $this->session->USERID,

                            'CANCELLEDTIME' => date('Y-m-d H:i:s',time())

                        ),

                        array('ID' => $this->input->post('ID'))

                    );



                    $data['suc'] = 'Successfully Deleted';

                }

                else{

                    $data['err'] .= 'Sorry cant delete patient with medical records.';

                }





            }

            else{

                $data['err'] = 'Only administrator account can delete patient info.';

            }

        }



        $data['err'] .=validation_errors(' ',' ');



        return $data;



    }









    public function Submit_Remove_Record(){





        $_POST += json_decode(file_get_contents('php://input'), true);



        $data = array();



        $data['err']= '';

        $data['suc']=array();



        $this->load->library('form_validation');

        $this->form_validation->set_rules('TOKEN','token', 'trim|required');

        $this->form_validation->set_rules('ID','MEDID', 'trim|required');





        if ($this->form_validation->run() == TRUE){



            if( $this->m_utility->tokenCheck($this->input->post('TOKEN'))){



                $sql = $this->db->query("SELECT CREATEDBY FROM medicalrecords WHERE ID=? AND CLINICID=? LIMIT 1"

                    ,array(

                        $this->input->post('ID'), 

                        $this->session->CLINICID

                    ))->row();



                if( $sql->CREATEDBY == $this->session->USERID || $sql->CREATEDBY == NULL || $sql->CREATEDBY == 0 ) {                



                    $this->db->update('medicalrecords',

                        array(

                            'CANCELLED' => 'Y',

                            'CANCELLEDBY' => $this->session->USERID,

                            'CANCELLEDTIME' => date('Y-m-d H:i:s',time())

                        ),

                        array('ID' => $this->input->post('ID'))

                    );



                    $data['suc'] = 'Successfully Deleted';

                }

                else{

                    $data['err'] .= 'You dont have permission to delete this record.';

                }



            }

            else{

                $data['err'] .= 'Invalid Request.';

            }



        }

        else {

            $data['err'] .='Expired request. Please refresh the page.';

        }



        $data['err'] .=validation_errors(' ',' ');



        return $data;

    }





    







    



}

?>