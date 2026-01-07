<?php
defined('BASEPATH') OR exit('No direct script access allowed');
// DOCTOR

class M_medicals extends CI_Model
{
	
	function __construct(){ $this->load->database(); }


    public function index()
    {

        $_POST += json_decode(file_get_contents('php://input'), true);

        $search = preg_replace('/[^0-9a-zA-Z-ñÑ ]/i','',$_POST['text']);
        $dateFrom = date('Y-m-d',strtotime($_POST['dateFrom']));
        $dateTo = date('Y-m-d',strtotime($_POST['dateTo']));


        $sql = $this->db->query("SELECT MR.ID, MR.PATIENTID, P.FIRSTNAME, P.MIDDLENAME, P.LASTNAME, P.SEX, 
            MR.CHECKUPDATE, MR.AGE, MR.CHEIFCOMPLAINT, MR.FINDINGS, MR.DIAGNOSIS, MR.APPOINTMENT,MR.APPOINTMENTDATE,MR.APPOINTMENTDESCRIPTION, 
            U.NAME AS CREATEDNAME, S.NAME AS FROMCLINIC, S1.NAME AS APPOINTCLINIC

            FROM patients P 
            INNER JOIN medicalrecords MR  ON MR.PATIENTID = P.ID
            LEFT JOIN users U ON U.ID = MR.CREATEDBY
            LEFT JOIN subclinic S ON S.ID = MR.SUBCLINICID
            LEFT JOIN subclinic S1 ON S1.ID = MR.APPOINTMENTSUBCLINICID
            WHERE MR.CLINICID = ?
            AND ( MR.ID = ? 
            OR concat(P.FIRSTNAME,' ',P.MIDDLENAME,' ',P.LASTNAME) like ? 
            OR concat(P.LASTNAME,' ',P.FIRSTNAME,' ',P.MIDDLENAME) like ?
            OR concat(P.FIRSTNAME,' ',P.LASTNAME) like ? )
            AND DATE(MR.CHECKUPDATE) BETWEEN ? AND ?
            AND P.CANCELLED = 'N' 
            AND MR.CANCELLED = 'N'
            ORDER BY MR.CHECKUPDATE DESC, ID DESC",
            array(
                $this->session->CLINICID, 
                $search,
                '%'.$search.'%',
                '%'.$search.'%',
                '%'.$search.'%',
                $dateFrom,
                $dateTo
            ))->result();

        foreach ($sql as $key => $v) {

            $v->LABORATORIES = $this->db->query("SELECT L.NAME, ML.TEMPLATERESULT
                FROM mr_laboratory ML 
                INNER JOIN laboratory L ON L.ID = ML.LABORATORYID
                WHERE ML.MEDICALRECORDID = ? AND ML.CANCELLED='N' ",array($v->ID))->result();
        }

        return $sql;

    }


    public function MR_Preview($MRID){

        $mr = $this->db->query("SELECT M.ID, M.CHECKUPDATE, M.AGE, P.FIRSTNAME, P.MIDDLENAME, P.LASTNAME,
            M.CHEIFCOMPLAINT, M.PRESENTILLNESS, M.COMORBIDITIES, M.FINDINGS, M.DIAGNOSIS, M.PROCEDURE_DONE, M.INSTRUCTION, M.MEDICATION,
            M.BP_SYSTOLIC, M.BP_DIASTOLIC, M.HEART_RATE, M.RESPIRATORY, M.TEMPERATURE, M.WEIGHT, M.HEIGHT, M.BMI, M.LMP           
            FROM medicalrecords M
            INNER JOIN patients P ON P.ID = M.PATIENTID
            WHERE M.ID=? LIMIT 1",array($MRID))->row();


        $mr->LABORATORIES =  $this->db->query("SELECT L.NAME, ML.TEMPLATERESULT
            FROM mr_laboratory ML 
            INNER JOIN laboratory L ON L.ID = ML.LABORATORYID
            WHERE ML.MEDICALRECORDID=? AND ML.CANCELLED='N'"
            ,array($mr->ID))->result();


        $mr->MEDICINES = $this->db->query("SELECT M.NAME, MM.FREQUENCY, MM.QUANTITY, MM.INSTRUCTION
            FROM mr_medicines MM 
            INNER JOIN medicines M ON M.ID = MM.MEDICINEID 
            WHERE MM.MEDICALRECORDID=? AND MM.CANCELLED = 'N'
            ORDER BY MM.ID "
            ,array($mr->ID))->result();


        return $mr;
    }



    public function Form_Data($patientid,$medid)
    {

        if( (int)$medid == 0 ) {

            $patient = $this->getPatient($patientid);

            $rec = $this->db->query("SELECT CHECKUPDATE 
                FROM medicalrecords 
                WHERE PATIENTID=? AND CANCELLED='N' 
                ORDER BY CHECKUPDATE ",array($patient->ID))->result();

            $consuldates = '';
            foreach ($rec as $key => $value) {
                $consuldates .= date('m/d/y',strtotime($value->CHECKUPDATE)).', ';
            }

            
            $data = array(
                'TOKEN' => $this->m_utility->tokenRequest(),
                'TOKEN1' => $this->m_utility->tokenRequest($patient->ID),
                'TOKENLAB' => $this->m_utility->tokenRequest(0),
                'ID' => 0,
                'PATIENT' => $patient,
                'SUBCLINICID' => NULL,
                'CHECKUPDATE' => date('Y-m-d',time()),
                'APPOINTMENT' => 'N',
                'APPOINTMENTDATE' => NULL,
                'APPOINTMENTDESCRIPTION' => '',
                'APPOINTMENTCLINICID' => '',
                'AGE' => $this->m_utility->ageCompute($patient->DOB,date('Y-m-d',time())),
                'REFFEREDBY' => '',
                'COMORBIDITIES' => '',
                'CHEIFCOMPLAINT' => '',
                'FINDINGS' => '',
                'DIAGNOSIS' => '',
                'MEDICATION' => '',
                'PROCEDURE_DONE' => '',
                'INSTRUCTION' => '',
                'PRESENTILLNESS' => '',
                'BP_SYSTOLIC' => '',
                'BP_DIASTOLIC' => '',
                'WEIGHT' => '', 
                'HEIGHT' => '',
                'REMARKS' => '',
                'HMOID' => 0,
                'HMOAMOUNT' => 0,
                'HMORECEIVED' => 'N',
                'HMODATE' => date('Y-m-d',time()),
                'COMPANY' => '',
                'PAYMODE' => 'CASH',
                'GROSSAMOUNT' => 0,
                'DISCOUNTAMOUNT' => 0,
                'AMOUNT' => 0,
                'AMOUNTCHANGE' => 0,
                'NETPAYABLES' => 0,
                'PAIDAMOUNT' => 0,
                'ACTIVE' => 1,
                'SERVICES' => array(),
                'DISCOUNTS' => array(),
                'IMAGES' => array(),
                'MEDICINES' => array(),
                'LABORATORIES' => array(),
                'CONFINEMENT_DATE_FROM' => date('Y-m-d',time()),
                'CONFINEMENT_DATE_TO' => date('Y-m-d',time()),
                'ESTIMATED_HEAL_PERIOD' => '',
                'CONSULTATIONDATES' => '',
                'DEFAULTCONSULTATIONS' => $consuldates,
                'DEFAULTREFERRAL' => $this->Clinic()->REFERRALDEFAULTTEXT,
                'DEFAULTCLEARANCE' => $this->Clinic()->CLEARANCEDEFAULTTEXT,
                'LMP' => NULL,
                'ICD_CODE' => '',
                'RVS_CODE' => '',
                'SPECIALISTID'  => NULL,
                'REFERRALTO' => '',
                'REFERRALMSG' => '',
                'CLEARANCETO' => '',
                'CLEARANCEMSG' => '',
                'HRID' => '',
                'READONLY' => FALSE
            );

            return $data;
            
        }
        else if( (int)$medid > 0 ) {

            $data = $this->db->query("SELECT M.*, U1.NAME AS CREATEDNAME, P.itemdescription AS RVS_DESCRIPTION, D.icd10desc AS ICD_DESCRIPTION
                FROM medicalrecords M
                LEFT JOIN users U1 ON U1.ID = M.CREATEDBY
                LEFT JOIN doh_icd D ON D.icd10code = M.ICD_CODE
                LEFT JOIN ph_icd P ON P.itemcode = M.RVS_CODE
                where M.ID =? AND M.CLINICID=? AND M.CANCELLED = 'N'
                LIMIT 1",array($medid,$this->session->CLINICID))->row_array();

            if( count($data) ){

                $data['TOKEN'] = $this->m_utility->tokenRequest($medid);
                $data['TOKEN1'] = $this->m_utility->tokenRequest($data['PATIENTID']);
                $data['TOKENLAB'] = $this->m_utility->tokenRequest(0);

                $data['PATIENT'] = $this->getPatient($data['PATIENTID']);
                $data['IMAGES'] = $this->IMAGES($medid);
                $data['SERVICES'] = $this->SERVICES($medid);
                $data['DISCOUNTS'] = $this->DISCOUNTS($medid);
                $data['MEDICINES'] = $this->MEDICINES($medid);
                $data['LABORATORIES'] = $this->LABORATORIES($medid);


                $rec = $this->db->query("SELECT CHECKUPDATE 
                    FROM medicalrecords 
                    WHERE PATIENTID=? AND CANCELLED='N' 
                    ORDER BY CHECKUPDATE ",array($data['PATIENT']->ID))->result();

                $consuldates = '';
                foreach ($rec as $key => $value) {
                    $consuldates .= date('m/d/y',strtotime($value->CHECKUPDATE)).', ';
                }

                $data['DEFAULTCONSULTATIONS'] = $consuldates;

                $data['DEFAULTREFERRAL'] = $this->Clinic()->REFERRALDEFAULTTEXT;
                $data['DEFAULTCLEARANCE'] = $this->Clinic()->CLEARANCEDEFAULTTEXT;
                                

                if( $this->session->position == 'BRANCH ADMINISTRATOR' ){
                    $data['READONLY'] = FALSE;
                }
                else {

                    // check have previous data 
                    $sql = $this->db->query("SELECT MAX(CHECKUPDATE) AS MAXDATE 
                        FROM medicalrecords 
                        WHERE PATIENTID=? AND CLINICID=? AND CANCELLED='N'  
                        ORDER BY ID DESC
                        LIMIT 1", array($data['PATIENTID'],$this->session->CLINICID))->row();

                    if( isset($sql) ){

                        if( $data['CREATEDBY'] == NULL || $data['CREATEDBY'] == 0 ){
                            $data['READONLY'] = FALSE;  
                        }
                        else if( $this->session->userid == $data['CREATEDBY'] ){

                            if( $sql->MAXDATE == $data['CHECKUPDATE'] ){
                                $data['READONLY'] = FALSE;  
                            }
                            else{
                                $data['READONLY'] = TRUE;
                            }
                        }
                        else{
                            $data['READONLY'] = TRUE;
                        }
                    }
                    else{
                        $data['READONLY'] = FALSE;
                    }
                }

                return $data;
            }
            else {
                return array('RESPONSE'=> 'MEDICAL RECORD IS ALREADY DELETED.');
            }

        }
        
    }


    private function Clinic(){

        $sql = $this->db->query("SELECT * FROM clinics WHERE ID=? LIMIT 1",array($this->session->CLINICID))->row();

        return $sql;
    }


    private function getPatient($patientid){

        $sql=$this->db->query("SELECT ID,DOB,FIRSTNAME,LASTNAME,MIDDLENAME,MOBILENO,TOTALRECORDS FROM patients WHERE ID=? LIMIT 1",array($patientid))->row();
        return $sql;
    }

    private function SERVICES($medid){

        $sql = $this->db->query("SELECT * From mr_services WHERE MEDICALRECORDID=? AND CANCELLED='N' ",array($medid))->result();
        return $sql;
    }

    private function DISCOUNTS($medid){

        $sql = $this->db->query("SELECT * From mr_discounts WHERE MEDICALRECORDID=? AND CANCELLED='N' ",array($medid))->result();
        return $sql;
    }

    private function IMAGES($medid){

        $sql = $this->db->query("SELECT * From mr_images WHERE MEDICALRECORDID=? AND CANCELLED='N' ",array($medid))->result();
        foreach ($sql as $key => $v) {
            $sql[$key]->IMAGEPATH = base_url($v->IMAGEPATH);
        }
        return $sql;    
    }

    private function MEDICINES($medid){

        $data = $this->db->query("SELECT * From mr_medicines WHERE MEDICALRECORDID=? AND CANCELLED='N' ",array($medid))->result();
        return $data;
    }

    private function LABORATORIES($medid){

        $data = $this->db->query("SELECT * From mr_laboratory WHERE MEDICALRECORDID=? AND CANCELLED='N' ",array($medid))->result();
        return $data;
    }


    public function list_of_services(){
        $sql = $this->db->query("SELECT * FROM services where CLINICID=?  ORDER BY NAME",array($this->session->CLINICID))->result();
        return $sql;
    }

    public function list_of_discounts(){
        $sql = $this->db->query("SELECT * FROM discounts where CLINICID=? ORDER BY NAME",array($this->session->CLINICID))->result();
        return $sql;
    }

    public function list_of_hmo(){
        $sql = $this->db->query("SELECT * FROM hmo where CLINICID=? ORDER BY NAME ",array($this->session->CLINICID))->result();
        return $sql;
    }

    public function list_of_medicines(){
        $sql = $this->db->query("SELECT * FROM medicines where CLINICID=? ORDER BY NAME",array($this->session->CLINICID))->result();
        return $sql;
    }

    public function list_of_diseases()
    {
        $sql = $this->db->query("SELECT * FROM diseases where CLINICID=? ORDER BY NAME",array($this->session->CLINICID))->result();
        return $sql;
    }

    public function list_of_instruction()
    {
        $sql = $this->db->query("SELECT * FROM instruction where CLINICID=? AND ACTIVE='Y' order by NAME ",array($this->session->CLINICID))->result();
        return $sql;
    }


    public function list_of_subclinic()
    {
        $sql = $this->db->query("SELECT * FROM subclinic where CLINICID=? order by NAME ",array($this->session->CLINICID))->result();
        return $sql;
    }

    public function list_of_specialist()
    {
        $sql = $this->db->query("SELECT * FROM specialist order by SPECIALTY ")->result();
        return $sql;
    }

    public function list_of_laboratory()
    {   
        $sql = $this->db->query("SELECT * FROM laboratory where CLINICID=? order by NAME ",array($this->session->CLINICID))->result();
        return $sql;
    }



    public function Get_Latest_Prescription($PATIENTID, $MRID){

        $sql = $this->db->query("SELECT ID
            FROM medicalrecords 
            WHERE  CLINICID=? AND PATIENTID=? AND ID != ? AND CANCELLED='N'  
            ORDER BY CHECKUPDATE DESC, ID DESC
            LIMIT 1", array($this->session->CLINICID, $PATIENTID, $MRID))->row();

        if( $sql ){
            return $this->MEDICINES($sql->ID);    
        }
        else{
           return array();
       }
   }


   public function Submit_Form() {

    $_POST += json_decode(file_get_contents('php://input'), true);

    $data = array();

    $data['err']= '';
    $data['suc']=array();

    $this->load->library('form_validation');
    $this->form_validation->set_rules('TOKEN','token', 'trim|required');
    $this->form_validation->set_rules('TOKEN1','token', 'trim|required');
    $this->form_validation->set_rules('SUBCLINICID','Clinic','trim|required');
    $this->form_validation->set_rules('CHECKUPDATE','Date Check-up','trim|required');
    $this->form_validation->set_rules('APPOINTMENT','Appointment','trim');
    $this->form_validation->set_rules('APPOINTMENTDATE','Date Appointment','trim');
    $this->form_validation->set_rules('APPOINTMENTDESCRIPTION','Appointment Description','trim');
    $this->form_validation->set_rules('APPOINTMENTSUBCLINICID','TO Clinic','trim');
    $this->form_validation->set_rules('AGE','Age','trim|required');
    $this->form_validation->set_rules('REFFEREDBY','Reffered By','trim');
    $this->form_validation->set_rules('CHEIFCOMPLAINT','Chief Complaint','trim');
    $this->form_validation->set_rules('COMORBIDITIES','CO-MORBIDITIES','trim');
    $this->form_validation->set_rules('FINDINGS','Findings','trim');                    
    $this->form_validation->set_rules('DIAGNOSIS','Diagnosis','trim');                    
    $this->form_validation->set_rules('MEDICATION','Medication','trim');
    $this->form_validation->set_rules('PROCEDURE_DONE','Procedure was/were Done','trim');
    $this->form_validation->set_rules('INSTRUCTION','Instruction','trim');
    $this->form_validation->set_rules('PRESENTILLNESS','History Present Illness','trim'); 
    $this->form_validation->set_rules('REMARKS','Remarks');                      
    $this->form_validation->set_rules('COMPANY','Company','trim');                    
    $this->form_validation->set_rules('PAYMODE','Pay Mode','trim|required');                    
    $this->form_validation->set_rules('AMOUNT','Amount','trim|required'); 
    $this->form_validation->set_rules('AMOUNTCHANGE','Amount','trim'); 
    $this->form_validation->set_rules('SERVICES[]','Services','trim');
    $this->form_validation->set_rules('DISCOUNTS[]','Discount','trim');  
    $this->form_validation->set_rules('IMAGES','Images','trim');                    
    $this->form_validation->set_rules('MEDICINES[]','Discount','trim'); 
    $this->form_validation->set_rules('DISEASES[]','Discount','trim');
    $this->form_validation->set_rules('LABORATORY[]','Laboratory','trim');
    $this->form_validation->set_rules('CONFINEMENT_DATE_FROM','Confinement Date From','trim');
    $this->form_validation->set_rules('CONFINEMENT_DATE_TO','Confinement Date To','trim');
    $this->form_validation->set_rules('ESTIMATED_HEAL_PERIOD','Estimated Healing Period','trim');
    $this->form_validation->set_rules('ICD_CODE','ICD CODE','trim');
    $this->form_validation->set_rules('RVS_CODE','RVS CODE','trim');
    $this->form_validation->set_rules('SPECIALISTID','Specialty','trim|required');
    $this->form_validation->set_rules('HRID','Hospital Record No','trim');
    $this->form_validation->set_rules('REFERRALTO','Referral To','trim');
    $this->form_validation->set_rules('REFERRALMSG','Referral Message');
    $this->form_validation->set_rules('CLEARANCETO','Clearnace To','trim');
    $this->form_validation->set_rules('CLEARANCEMSG','Clearance Message');
    $this->form_validation->set_rules('CONSULTATIONDATES','Consultation Dates');
    $this->form_validation->set_rules('LMP','Last menstruation period');
    $this->form_validation->set_rules('BMI','Body Mass Index');

    if ($this->form_validation->run() == TRUE){

        if( $this->m_utility->tokenCheck($this->input->post('TOKEN')) && $this->m_utility->tokenCheck($this->input->post('TOKEN1')) ){

            $MEDID = $this->m_utility->tokenRetrieve($this->input->post('TOKEN'));
            $PATIENTID = $this->m_utility->tokenRetrieve($this->input->post('TOKEN1'));


            if( (Boolean)$this->input->post('APPOINTMENT') ){

                $APPOINTMENT = 'Y';
                $APPOINTMENTDATE = date('Y-m-d H:i:s',strtotime($this->input->post('APPOINTMENTDATE')));
                $APPOINTMENTDESCRIPTION = $this->input->post('APPOINTMENTDESCRIPTION');
                $APPOINTMENTSUBCLINICID = $this->input->post('APPOINTMENTSUBCLINICID');
            }
            else{
                $APPOINTMENT = 'N';
                $APPOINTMENTDATE = NULL;
                $APPOINTMENTDESCRIPTION = '';
                $APPOINTMENTSUBCLINICID = 0; 
            }


            if( $MEDID > 0 ){

                $this->db->update('medicalrecords',  
                    array(
                        'CHECKUPDATE'           => date('Y-m-d',strtotime($this->input->post('CHECKUPDATE'))),
                        'SUBCLINICID'           => $this->input->post('SUBCLINICID'),
                        'APPOINTMENT'           => $APPOINTMENT,
                        'APPOINTMENTDATE'       => $APPOINTMENTDATE,
                        'APPOINTMENTSUBCLINICID'=> $APPOINTMENTSUBCLINICID,
                        'APPOINTMENTDESCRIPTION'=> $APPOINTMENTDESCRIPTION,
                        'AGE'                   => $this->input->post('AGE'),
                        'REFFEREDBY'            => $this->input->post('REFFEREDBY'),
                        'COMORBIDITIES'         => $this->input->post('COMORBIDITIES'),
                        'CHEIFCOMPLAINT'        => $this->input->post('CHEIFCOMPLAINT'),
                        'FINDINGS'              => $this->input->post('FINDINGS'),
                        'DIAGNOSIS'             => $this->input->post('DIAGNOSIS'),
                        'MEDICATION'            => $this->input->post('MEDICATION'),
                        'PROCEDURE_DONE'        => $this->input->post('PROCEDURE_DONE'),
                        'INSTRUCTION'           => $this->input->post('INSTRUCTION'),
                        'PRESENTILLNESS'        => $this->input->post('PRESENTILLNESS'),
                        'REMARKS'               => $this->input->post('REMARKS'),
                        'BP_SYSTOLIC'           => $this->input->post('BP_SYSTOLIC'),
                        'BP_DIASTOLIC'          => $this->input->post('BP_DIASTOLIC'),
                        'HEART_RATE'            => $this->input->post('HEART_RATE'),
                        'RESPIRATORY'           => $this->input->post('RESPIRATORY'),
                        'TEMPERATURE'           => $this->input->post('TEMPERATURE'),
                        'HEIGHT'                => $this->input->post('HEIGHT'),
                        'WEIGHT'                => $this->input->post('WEIGHT'),
                        'BMI'                   => $this->input->post('BMI'),
                        'CONFINEMENT_DATE_FROM' => $this->input->post('CONFINEMENT_DATE_FROM') != '' ? date('Y-m-d',strtotime($this->input->post('CONFINEMENT_DATE_FROM'))) : null,
                        'CONFINEMENT_DATE_TO'   => $this->input->post('CONFINEMENT_DATE_TO') != '' ?  date('Y-m-d',strtotime($this->input->post('CONFINEMENT_DATE_TO'))) : null,
                        'ESTIMATED_HEAL_PERIOD' => $this->input->post('ESTIMATED_HEAL_PERIOD'),
                        'CONSULTATIONDATES'     => $this->input->post('CONSULTATIONDATES'),
                        'LMP'                   => $this->input->post('LMP') != '' ? date('Y-m-d',strtotime($this->input->post('LMP'))) : null,
                        'ICD_CODE'              => $this->input->post('ICD_CODE'),
                        'RVS_CODE'              => $this->input->post('RVS_CODE'),
                        'SPECIALISTID'          => $this->input->post('SPECIALISTID'),
                        'HRID'                  => $this->input->post('HRID'),
                        'REFERRALTO'            => $this->input->post('REFERRALTO'),
                        'REFERRALMSG'           => $this->input->post('REFERRALMSG'),
                        'CLEARANCETO'           => $this->input->post('CLEARANCETO'),
                        'CLEARANCEMSG'          => $this->input->post('CLEARANCEMSG'),
                        'HMOID'                 => $this->input->post('HMOID'),
                        'HMOAMOUNT'             => $this->input->post('HMOAMOUNT'),
                        'HMORECEIVED'           => (Boolean)$this->input->post('HMORECEIVED') ? 'Y' : 'N',
                        'HMODATE'               => date('Y-m-d',strtotime($this->input->post('HMODATE'))),
                        'COMPANY'               => $this->input->post('COMPANY'),
                        'PAYMODE'               => $this->input->post('PAYMODE'),
                        'GROSSAMOUNT'           => $this->input->post('GROSSAMOUNT'),
                        'DISCOUNTAMOUNT'        => $this->input->post('DISCOUNTAMOUNT'),
                        'NETPAYABLES'           => $this->input->post('NETPAYABLES'),
                        'AMOUNT'                => $this->input->post('AMOUNT'),
                        'AMOUNTCHANGE'          => $this->input->post('AMOUNTCHANGE'),
                        'PAIDAMOUNT'            => $this->input->post('PAIDAMOUNT'),
                        'CREATEDBY'             => $this->session->userid,
                        'UPDATEDBY'             => $this->session->userid,
                        'UPDATEDTIME'           => date('Y-m-d H:i:s',time()) 
                    ), array('ID'=>$MEDID));

                    /* JUST COMMENT TO CORRECT THE LININGS OF CODE
                    */

                } 

                else {

                    $this->db->insert('medicalrecords', array(
                        'PATIENTID'             => $PATIENTID,
                        'CLINICID'              => $this->session->CLINICID,
                        'CHECKUPDATE'           => date('Y-m-d',strtotime($this->input->post('CHECKUPDATE'))),
                        'SUBCLINICID'           => $this->input->post('SUBCLINICID'),
                        'APPOINTMENT'           => $APPOINTMENT,
                        'APPOINTMENTDATE'       => $APPOINTMENTDATE,
                        'APPOINTMENTSUBCLINICID'=> $APPOINTMENTSUBCLINICID,
                        'APPOINTMENTDESCRIPTION'=> $APPOINTMENTDESCRIPTION,
                        'AGE'                   => $this->input->post('AGE'),
                        'REFFEREDBY'            => $this->input->post('REFFEREDBY'),
                        'COMORBIDITIES'         => $this->input->post('COMORBIDITIES'),
                        'CHEIFCOMPLAINT'        => $this->input->post('CHEIFCOMPLAINT'),
                        'FINDINGS'              => $this->input->post('FINDINGS'),
                        'DIAGNOSIS'             => $this->input->post('DIAGNOSIS'),
                        'MEDICATION'            => $this->input->post('MEDICATION'),
                        'PROCEDURE_DONE'        => $this->input->post('PROCEDURE_DONE'),
                        'INSTRUCTION'           => $this->input->post('INSTRUCTION'),
                        'PRESENTILLNESS'        => $this->input->post('PRESENTILLNESS'),
                        'REMARKS'               => $this->input->post('REMARKS'),
                        'BP_SYSTOLIC'           => $this->input->post('BP_SYSTOLIC'),
                        'BP_DIASTOLIC'          => $this->input->post('BP_DIASTOLIC'),
                        'HEART_RATE'            => $this->input->post('HEART_RATE'),
                        'RESPIRATORY'           => $this->input->post('RESPIRATORY'),
                        'TEMPERATURE'           => $this->input->post('TEMPERATURE'),
                        'HEIGHT'                => $this->input->post('HEIGHT'),
                        'WEIGHT'                => $this->input->post('WEIGHT'),
                        'BMI'                   => $this->input->post('BMI'),
                        'CONFINEMENT_DATE_FROM' => $this->input->post('CONFINEMENT_DATE_FROM') != '' ? date('Y-m-d',strtotime($this->input->post('CONFINEMENT_DATE_FROM'))) : null,
                        'CONFINEMENT_DATE_TO'   => $this->input->post('CONFINEMENT_DATE_TO') != '' ?  date('Y-m-d',strtotime($this->input->post('CONFINEMENT_DATE_TO'))) : null,
                        'ESTIMATED_HEAL_PERIOD' => $this->input->post('ESTIMATED_HEAL_PERIOD'),
                        'CONSULTATIONDATES'     => $this->input->post('CONSULTATIONDATES'),
                        'LMP'                   => $this->input->post('LMP') != '' ? date('Y-m-d',strtotime($this->input->post('LMP'))) : null,
                        'ICD_CODE'              => $this->input->post('ICD_CODE'),
                        'RVS_CODE'              => $this->input->post('RVS_CODE'),
                        'SPECIALISTID'          => $this->input->post('SPECIALISTID'),
                        'HRID'                  => $this->input->post('HRID'),
                        'REFERRALTO'            => $this->input->post('REFERRALTO'),
                        'REFERRALMSG'           => $this->input->post('REFERRALMSG'),
                        'CLEARANCETO'           => $this->input->post('CLEARANCETO'),
                        'CLEARANCEMSG'          => $this->input->post('CLEARANCEMSG'),
                        'HMOID'                 => $this->input->post('HMOID'),
                        'HMOAMOUNT'             => $this->input->post('HMOAMOUNT'),
                        'HMORECEIVED'           => (Boolean)$this->input->post('HMORECEIVED') ? 'Y' : 'N',
                        'HMODATE'               => date('Y-m-d',strtotime($this->input->post('HMODATE'))),
                        'COMPANY'               => $this->input->post('COMPANY'),
                        'PAYMODE'               => $this->input->post('PAYMODE'),
                        'GROSSAMOUNT'           => $this->input->post('GROSSAMOUNT'),
                        'DISCOUNTAMOUNT'        => $this->input->post('DISCOUNTAMOUNT'),
                        'NETPAYABLES'           => $this->input->post('NETPAYABLES'),
                        'AMOUNT'                => $this->input->post('AMOUNT'),
                        'AMOUNTCHANGE'          => $this->input->post('AMOUNTCHANGE'),
                        'PAIDAMOUNT'            => $this->input->post('PAIDAMOUNT'),
                        'CREATEDBY'             => $this->session->userid,
                        'CREATEDTIME'           => date('Y-m-d H:i:s',time()),
                        'CANCELLED'             => 'N'
                    ));

                    /* lining
                    */

                    $MEDID = $this->db->insert_id();
                }


                if( $MEDID > 0 ) {

                    foreach ($this->input->post('SERVICES[]') as $key => $v) {

                        if( (int)$v['ID'] > 0 ){

                            $this->db->update('mr_services',array(
                                'SERVICEID'=>$v['SERVICEID'],
                                'QUANTITY'=>$v['QUANTITY'],
                                'UNITPRICE'=>$v['UNITPRICE'],
                                'AMOUNT'=>$v['AMOUNT'],
                                'UPDATEDBY' => $this->session->userid,
                                'UPDATEDTIME' => date('Y-m-d H:i:s',time()),
                                'CANCELLED'=> (Boolean)$v['CANCELLED'] ? 'Y' : 'N'
                            ), array('ID'=>$v['ID']));

                        }
                        else if( !(Boolean)$v['CANCELLED'] ){

                            $this->db->insert('mr_services',array(
                                'MEDICALRECORDID'=>$MEDID,
                                'SERVICEID'=>$v['SERVICEID'],
                                'QUANTITY'=>$v['QUANTITY'],
                                'UNITPRICE'=>$v['UNITPRICE'],
                                'AMOUNT'=>$v['AMOUNT'],
                                'CREATEDBY' => $this->session->userid,
                                'CREATEDTIME' => date('Y-m-d H:i:s',time()),
                                'CANCELLED'=>'N'
                            )); 
                        }
                    }

                    foreach ($this->input->post('DISCOUNTS[]') as $key => $v) {

                        if( (int)$v['ID'] > 0  ){

                            $this->db->update('mr_discounts',array(
                                'DISCOUNTID'=>$v['DISCOUNTID'],
                                'PERCENTAGE' => (Boolean)$v['PERCENTAGE'] ? 'Y' : 'N',
                                'PERCENT' => $v['PERCENT'],
                                'AMOUNT' => $v['AMOUNT'],
                                'UPDATEDBY' => $this->session->userid,
                                'UPDATEDTIME' => date('Y-m-d H:i:s',time()),
                                'CANCELLED'=> (Boolean)$v['CANCELLED'] ? 'Y' : 'N'
                            ), array('ID'=>$v['ID']) );

                        }
                        else if( !(Boolean)$v['CANCELLED'] ){

                            $this->db->insert('mr_discounts',array(
                                'MEDICALRECORDID'=>$MEDID,
                                'DISCOUNTID'=>$v['DISCOUNTID'],
                                'PERCENTAGE' => (Boolean)$v['PERCENTAGE'] ? 'Y' : 'N',
                                'PERCENT' => $v['PERCENT'],
                                'AMOUNT' => $v['AMOUNT'],
                                'CREATEDBY' => $this->session->userid,
                                'CREATEDTIME' => date('Y-m-d H:i:s',time()),
                                'CANCELLED'=> 'N'
                            )); 
                        }
                    }


                    foreach ($this->input->post('MEDICINES[]') as $key => $v) {

                        if( (int)$v['ID'] > 0 ){

                            $this->db->update('mr_medicines',array(
                                'MEDICINEID'    => $v['MEDICINEID'],
                                'FREQUENCY'     => $v['FREQUENCY'],
                                'INSTRUCTION'   => $v['INSTRUCTION'],
                                'QUANTITY'      => $v['QUANTITY'],
                                'UPDATEDBY'     => $this->session->userid,
                                'UPDATEDTIME'   => date('Y-m-d H:i:s',time()),
                                'CANCELLED'     => (Boolean)$v['CANCELLED'] ? 'Y' : 'N'
                            ),array('ID'=>$v['ID']));

                            $this->Check_Instruction($v['INSTRUCTION']);

                        }
                        else if( !(Boolean)$v['CANCELLED'] ){

                            $this->db->insert('mr_medicines',array(
                                'MEDICALRECORDID'   => $MEDID,
                                'MEDICINEID'        => $v['MEDICINEID'],
                                'FREQUENCY'         => $v['FREQUENCY'],
                                'INSTRUCTION'       => $v['INSTRUCTION'],
                                'QUANTITY'          => $v['QUANTITY'],
                                'CREATEDBY'         => $this->session->userid,
                                'CREATEDTIME'       => date('Y-m-d H:i:s',time()),
                                'CANCELLED'         => 'N'
                            ));

                            $this->Check_Instruction($v['INSTRUCTION']);
                        }
                    }   


                    foreach ($this->input->post('LABORATORIES[]') as $key => $v) {

                        if( (int)$v['ID'] > 0 ){

                            $this->db->update('mr_laboratory',array(
                                'LABORATORYID'      => $v['LABORATORYID'],
                                'TEMPLATERESULT'    => $v['TEMPLATERESULT'],
                                'UPDATEDBY'         => $this->session->userid,
                                'UPDATEDTIME'       => date('Y-m-d H:i:s',time()),
                                'CANCELLED'         => (Boolean)$v['CANCELLED'] ? 'Y' : 'N'
                            ),array('ID'=>$v['ID']));
                        }
                        else if( !(Boolean)$v['CANCELLED'] ){

                            $this->db->insert('mr_laboratory',array(
                                'MEDICALRECORDID'   => $MEDID,
                                'LABORATORYID'      => $v['LABORATORYID'],
                                'TEMPLATERESULT'    => $v['TEMPLATERESULT'],
                                'CREATEDBY'         => $this->session->userid,
                                'CREATEDTIME'       => date('Y-m-d H:i:s',time()),
                                'CANCELLED'         => 'N'
                            ));
                        }
                    }
                }


                // if( $APPOINTMENT = 'Y' ){
                //     $this->load->model('m_text');
                //     $this->m_text->Text_Patient($MEDID);
                // }

                $data['suc']['MRID'] = $MEDID;

            }
            else {
                $data['err'] .='Expired request. Please refresh the page.';
            }

        }

        $data['err'] .=validation_errors(' ',' ');

        echo json_encode($data);
    }


    private function Check_Instruction($INSTRUCTION){

        $q = $this->db->query("SELECT * FROM instruction WHERE NAME =? AND CLINICID=? LIMIT 1",array($INSTRUCTION,$this->session->CLINICID))->row();

        if( ! isset($q) ){

            $this->db->insert('instruction',array(
                'CLINICID'  => $this->session->CLINICID,
                'NAME'      => $INSTRUCTION, 
                'ACTIVE'    => 'N'
            ));
        }
    }



    public function  Submit_Upload($ID){

        $folderdate = 'records/'.$ID;

        if (!is_dir('uploads/'.$folderdate)) {
            mkdir('./uploads/' . $folderdate, 0777, TRUE);
        }

        $config['upload_path']      = './uploads/'.$folderdate;
        $config['allowed_types']    = '*';
        $config['overwrite']        = false;
        $config['max_size']         = 100000;


        $this->load->library('upload', $config);
        $this->upload->initialize($config);

        if ( ! $this->upload->do_upload('file'))
        {
         $response = $this->upload->display_errors(' ',' ');
     }
     else {

        $data = $this->upload->data();

        $this->db->insert('mr_images', 
            array(
                'MEDICALRECORDID'   => $ID,
                'IMAGEPATH'         => 'uploads/'.$folderdate.'/'.$data['file_name'],
                'EXTENSION'         => $data['file_ext'],
                'FILENAME'          => $data['file_name'],
                'DATEUPLOADED'      => date('Y-m-d H:i:s'),
                'CANCELLED'         => 'N'
            )
        );

        $response = $this->db->query("SELECT * FROM mr_images WHERE ID=?  LIMIT 1",array( $this->db->insert_id() ))->row();

        if( $response ){
            $response->IMAGEPATH = base_url($response->IMAGEPATH);
        }
    }

    return $response;
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

            $sql = $this->db->query("SELECT CREATEDBY FROM medicalrecords WHERE ID=? LIMIT 1",array($this->input->post('ID')))->row();

            if( $sql->CREATEDBY == $this->session->userid || $sql->CREATEDBY == NULL || $sql->CREATEDBY == 0 ) {                

                $this->db->update('medicalrecords',
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