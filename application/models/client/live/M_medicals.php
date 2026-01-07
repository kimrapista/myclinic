<?php

defined('BASEPATH') OR exit('No direct script access allowed');



class M_medicals extends CI_Model

{

	

	function __construct(){ $this->load->database(); }





    public function Search_Medical_Records() {

 

        $_POST += json_decode(file_get_contents('php://input'), true);



        $DATEFROM = date('Y-m-d',strtotime($_POST['DATEFROM']));

        $DATETO = date('Y-m-d',strtotime($_POST['DATETO']));


        $SEARCH = trim(preg_replace('/[^0-9a-zA-Z-ñÑ ]/i','',$_POST['SEARCH']));


        $FROM = $_POST['FROM'];

        $TO = $_POST['TO'];



        $sql = $this->db->query("SELECT MR.ID, MR.PATIENTID, P.FIRSTNAME, P.MIDDLENAME, P.LASTNAME, 

            MR.CHECKUPDATE, MR.AGE, MR.CHEIFCOMPLAINT, MR.FINDINGS, MR.DIAGNOSIS, 

            MR.APPOINTMENT, MR.APPOINTMENTDATE, MR.APPOINTMENTDESCRIPTION, 

            MR.NETPAYABLES, 

            U.NAME AS CREATEDNAME, 

            H.NAME AS HMONAME,

            S.NAME AS FROMCLINIC, 

            S1.NAME AS APPOINTCLINIC



            FROM patients P 

            INNER JOIN medicalrecords MR  ON MR.PATIENTID = P.ID

            INNER JOIN subclinic S ON S.ID = MR.SUBCLINICID

            LEFT JOIN users U ON U.ID = MR.CREATEDBY

            LEFT JOIN subclinic S1 ON S1.ID = MR.APPOINTMENTSUBCLINICID

            LEFT JOIN hmo H ON H.ID = MR.HMOID

            WHERE MR.CLINICID = ?

            AND ( concat(P.FIRSTNAME,' ',P.LASTNAME) like ? OR concat(P.LASTNAME,' ',P.FIRSTNAME) like ?)

            AND DATE(MR.CHECKUPDATE) BETWEEN ? AND ?

            AND P.CANCELLED = 'N' 

            AND MR.CANCELLED = 'N'

            ORDER BY MR.CHECKUPDATE DESC, MR.CREATEDTIME DESC

            LIMIT ?,? ",

            array(

                $this->session->CLINICID, 

                '%'.$SEARCH.'%',

                '%'.$SEARCH.'%',

                $DATEFROM,

                $DATETO,

                $FROM,

                $TO

            ))->result();



        foreach ($sql as $key => $v) {

            $v->TOTAL_LAB = $this->db->query("SELECT COUNT(ML.ID) AS CNT

                FROM mr_laboratory ML 

                INNER JOIN laboratory L ON L.ID = ML.LABORATORYID

                WHERE ML.MEDICALRECORDID = ? AND ML.CANCELLED='N' ",array($v->ID))->row()->CNT;

        }



        return $sql;



    }





    public function Form_Data($patientid,$medid){



        if( (int)$medid == 0 ) {



            $patient = $this->Patient_Info($patientid);



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

                'WEIGHT' => '0', 

                'HEIGHT' => '0',

                'REMARKS' => '',

                'HMOID' => 0,

                'HMOAMOUNT' => 0,

                'HMORECEIVED' => 'N',

                'HMODATE' => null,



                'CHEQUENO' => '',

                'CHEQUEDATE' => null,

                'CHEQUEACCNO' => '',

                'CHEQUEACCNAME' => '',

                'CHEQUEBANKNAME' => '',

                'CHEQUEAMOUNT' => 0,



                'COMPANY' => '',

                'PAYMODE' => 'NO CHARGE',

                'GROSSAMOUNT' => 0,

                'DISCOUNTAMOUNT' => 0,

                'AMOUNT' => 0,

                'AMOUNTCHANGE' => 0,

                'NETPAYABLES' => 0,

                'PAIDAMOUNT' => 0,

                'ACTIVE' => 1,

                'SERVICES' => array(),

                'DISCOUNTS' => array(),

                'MEDICINES' => array(),

                'LABORATORIES' => array(),

                'CONFINEMENT_DATE_FROM' => date('Y-m-d',time()),

                'CONFINEMENT_DATE_TO' => date('Y-m-d',time()),

                'ESTIMATED_HEAL_PERIOD' => '',

                'CONSULTATIONDATES' => '',

                'DEFAULTCONSULTATIONS' => $consuldates,

                'LMP' => NULL,

                'ICD_CODE' => '',

                'RVS_CODE' => '',

                'SPECIALISTID'  => NULL,

                'REFERRALTO' => '',

                'REFERRALMSG' => '',

                'CLEARANCETO' => '',

                'CLEARANCEMSG' => '',

                'HRID' => '',

                'CREATEDBY' => 0,

                'LASTMR' => 0,

                'READONLY' => 'N',

                'PREVIOUS' => $this->Get_Previous_MR($patient->ID),

                'PHILHEALTH' => 0,

                'PHILHEALTHRECEIVED' => 0,

                'PHILHEALTHCHEQUENO' => '',

                'PHILHEALTHCHEQUEDATE' => NULL,

                'PHILHEALTHAMOUNT' => 0

            );



            return $data;

            

        }

        else if( (int)$medid > 0 ) {



            $data = $this->db->query("SELECT M.*, U1.NAME AS CREATEDNAME

                FROM medicalrecords M

                LEFT JOIN users U1 ON U1.ID = M.CREATEDBY

                where M.ID =? AND M.CLINICID=? AND M.CANCELLED = 'N'

                LIMIT 1",array($medid,$this->session->CLINICID))->row_array();



            if( $data ){



                $data['TOKEN'] = $this->m_utility->tokenRequest($medid);

                $data['TOKEN1'] = $this->m_utility->tokenRequest($data['PATIENTID']);



                $data['PATIENT'] = $this->Patient_Info($data['PATIENTID']);

                $data['SERVICES'] = $this->SERVICES($medid);

                $data['DISCOUNTS'] = $this->DISCOUNTS($medid);

                $data['MEDICINES'] = $this->MEDICINES($medid);

                $data['LABORATORIES'] = $this->LABORATORIES($medid);



                $data['PREVIOUS'] = $this->Get_Previous_MR($data['PATIENTID'], $data['ID'], $data['CHECKUPDATE']);





                $rec = $this->db->query("SELECT CHECKUPDATE 

                    FROM medicalrecords 

                    WHERE PATIENTID=? AND CANCELLED='N' 

                    ORDER BY CHECKUPDATE ",array($data['PATIENT']->ID))->result();



                $consuldates = '';



                foreach ($rec as $key => $value) {

                    $consuldates .= date('m/d/y',strtotime($value->CHECKUPDATE)).', ';

                }



                $data['DEFAULTCONSULTATIONS']   = $consuldates;

                



                $sql = $this->db->query("SELECT ID

                    FROM medicalrecords 

                    WHERE PATIENTID=? AND CLINICID=? AND CANCELLED='N'  

                    ORDER BY CHECKUPDATE DESC

                    LIMIT 1", array($data['PATIENTID'],$this->session->CLINICID))->row();



                if( isset($sql) ){

                    $data['LASTMR'] = $sql->ID;

                }

                else{

                    $data['LASTMR'] = 0; 

                }



                return $data;

            }

            else {

                return array('error'=> 'MEDICAL RECORD IS ALREADY DELETED.');

            }



        }

        

    }



    private function Clinic(){ 
        $sql = $this->db->query("SELECT * FROM clinics WHERE ID=? LIMIT 1",array($this->session->CLINICID))->row();
        return $sql;
    }

    private function Patient_Info($patientid){

        $sql=$this->db->query("SELECT ID, DOB, FIRSTNAME, LASTNAME, MIDDLENAME, MOBILENO, PICTURE FROM patients WHERE ID=? LIMIT 1",array($patientid))->row();
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



    public function Images($medid){


        $sql = $this->db->query("SELECT * From mr_images WHERE MEDICALRECORDID=? AND CANCELLED='N' ",array($medid))->result();

        if( $sql ){

            if ( is_dir('uploads/records/'.$medid) ) {

                $this->m_utility->Add_Index_File('uploads/records/'.$medid);
            }
        }

        return $sql;    
    }



    private function MEDICINES($medid){


        $data = $this->db->query("SELECT MR.*, M.NAME as searchMeds
            From mr_medicines MR 
            INNER JOIN medicines M ON M.ID = MR.MEDICINEID
            WHERE MR.MEDICALRECORDID=? AND MR.CANCELLED='N' ",array($medid))->result();

        return $data;
    }



    private function LABORATORIES($medid){

        $data = $this->db->query("SELECT * From mr_laboratory WHERE MEDICALRECORDID=? AND CANCELLED='N' ",array($medid))->result();
        return $data;
    }



    private function Get_Previous_MR($PATIENTID, $MRID = NULL, $CHECKUPDATE = NULL) {



        if( $MRID != NULL ){


            $sql = $this->db->query("SELECT COMORBIDITIES,FINDINGS,DIAGNOSIS,PROCEDURE_DONE,INSTRUCTION,MEDICATION

                FROM medicalrecords 

                WHERE  CLINICID=? AND PATIENTID=? AND ID != ? AND DATE(CHECKUPDATE) <= ? AND CANCELLED='N'  

                ORDER BY CHECKUPDATE DESC, ID DESC

                LIMIT 1"

                ,array($this->session->CLINICID, $PATIENTID, $MRID, date('Y-m-d',strtotime($CHECKUPDATE)) ))->row();



            return $sql;

        }

        else{



            $sql = $this->db->query("SELECT COMORBIDITIES,FINDINGS,DIAGNOSIS,PROCEDURE_DONE,INSTRUCTION,MEDICATION

                FROM medicalrecords 

                WHERE  CLINICID=? AND PATIENTID=? AND CANCELLED='N'  

                ORDER BY CHECKUPDATE DESC, ID DESC

                LIMIT 1"

                ,array($this->session->CLINICID, $PATIENTID))->row();



            return $sql;

        }

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
        $this->form_validation->set_rules('MEDICINES[]','Discount','trim');
        $this->form_validation->set_rules('DISEASES[]','Discount','trim');
        $this->form_validation->set_rules('LABORATORY[]','Laboratory','trim');
        $this->form_validation->set_rules('CONFINEMENT_DATE_FROM','Confinement Date From','trim');
        $this->form_validation->set_rules('CONFINEMENT_DATE_TO','Confinement Date To','trim');
        $this->form_validation->set_rules('ESTIMATED_HEAL_PERIOD','Estimated Healing Period','trim');
        $this->form_validation->set_rules('ICD_CODE','ICD CODE','trim');
        $this->form_validation->set_rules('RVS_CODE','RVS CODE','trim');
        $this->form_validation->set_rules('SPECIALISTID','Specialty','trim');
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

                $newMR = $MEDID > 0  ? FALSE : TRUE;

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

                    $this->db->update('medicalrecords', array(

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

                        'HMORECEIVED'           => (Boolean)$this->input->post('HMORECEIVED') ? 'Y' : 'N',

                        'HMODATE'               => (Boolean)$this->input->post('HMORECEIVED') ? date('Y-m-d',strtotime($this->input->post('HMODATE'))) : null,

                        'HMOAMOUNT'             => $this->input->post('HMOAMOUNT'),  

                        

                        'PHILHEALTH'            => (Boolean)$this->input->post('PHILHEALTH') ? 'Y' : 'N',

                        'PHILHEALTHRECEIVED'    => (Boolean)$this->input->post('PHILHEALTHRECEIVED') ? 'Y' : 'N',

                        'PHILHEALTHCHEQUENO'    => $this->input->post('PHILHEALTHCHEQUENO'),

                        'PHILHEALTHCHEQUEDATE'  => (Boolean)$this->input->post('PHILHEALTHRECEIVED') ? date('Y-m-d',strtotime($this->input->post('PHILHEALTHCHEQUEDATE'))) : null,

                        'PHILHEALTHAMOUNT'      => $this->input->post('PHILHEALTHAMOUNT'),



                        'CHEQUENO'              => $this->input->post('CHEQUENO'),

                        'CHEQUEDATE'            => is_null($this->input->post('CHEQUEDATE')) ? null : date('Y-m-d',strtotime($this->input->post('CHEQUEDATE'))),

                        'CHEQUEACCNO'           => $this->input->post('CHEQUEACCNO'),

                        'CHEQUEACCNAME'         => $this->input->post('CHEQUEACCNAME'),

                        'CHEQUEBANKNAME'        => $this->input->post('CHEQUEBANKNAME'),

                        'CHEQUEAMOUNT'          => $this->input->post('CHEQUEAMOUNT'),



                        'COMPANY'               => $this->input->post('COMPANY'),

                        'PAYMODE'               => $this->input->post('PAYMODE'),

                        'GROSSAMOUNT'           => $this->input->post('GROSSAMOUNT'),

                        'DISCOUNTAMOUNT'        => $this->input->post('DISCOUNTAMOUNT'),

                        'NETPAYABLES'           => $this->input->post('NETPAYABLES'),

                        'AMOUNT'                => $this->input->post('AMOUNT'),

                        'AMOUNTCHANGE'          => $this->input->post('AMOUNTCHANGE'),

                        'PAIDAMOUNT'            => $this->input->post('PAIDAMOUNT'),

                        'CREATEDBY'             => $this->input->post('CREATEDBY'),

                        'UPDATEDBY'             => $this->session->USERID,

                        'UPDATEDTIME'           => date('Y-m-d H:i:s',time()),

                        'CANCELLED'             => 'N'

                    ), array('ID'=>$MEDID));

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

                        'HMORECEIVED'           => (Boolean)$this->input->post('HMORECEIVED') ? 'Y' : 'N',

                        'HMODATE'               => (Boolean)$this->input->post('HMORECEIVED') ? date('Y-m-d',strtotime($this->input->post('HMODATE'))) : null,

                        'HMOAMOUNT'             => $this->input->post('HMOAMOUNT'),   

                        

                        'PHILHEALTH'            => (Boolean)$this->input->post('PHILHEALTH') ? 'Y' : 'N',

                        'PHILHEALTHRECEIVED'    => (Boolean)$this->input->post('PHILHEALTHRECEIVED') ? 'Y' : 'N',

                        'PHILHEALTHCHEQUENO'    => $this->input->post('PHILHEALTHCHEQUENO'),

                        'PHILHEALTHCHEQUEDATE'  => (Boolean)$this->input->post('PHILHEALTHRECEIVED') ? date('Y-m-d',strtotime($this->input->post('PHILHEALTHCHEQUEDATE'))) : null,

                        'PHILHEALTHAMOUNT'      => $this->input->post('PHILHEALTHAMOUNT'),            



                        'CHEQUENO'              => $this->input->post('CHEQUENO'),

                        'CHEQUEDATE'            => is_null($this->input->post('CHEQUEDATE')) ? null : date('Y-m-d',strtotime($this->input->post('CHEQUEDATE'))),

                        'CHEQUEACCNO'           => $this->input->post('CHEQUEACCNO'),

                        'CHEQUEACCNAME'         => $this->input->post('CHEQUEACCNAME'),

                        'CHEQUEBANKNAME'        => $this->input->post('CHEQUEBANKNAME'),

                        'CHEQUEAMOUNT'          => $this->input->post('CHEQUEAMOUNT'),



                        'COMPANY'               => $this->input->post('COMPANY'),

                        'PAYMODE'               => $this->input->post('PAYMODE'),

                        'GROSSAMOUNT'           => $this->input->post('GROSSAMOUNT'),

                        'DISCOUNTAMOUNT'        => $this->input->post('DISCOUNTAMOUNT'),

                        'NETPAYABLES'           => $this->input->post('NETPAYABLES'),

                        'AMOUNT'                => $this->input->post('AMOUNT'),

                        'AMOUNTCHANGE'          => $this->input->post('AMOUNTCHANGE'),

                        'PAIDAMOUNT'            => $this->input->post('PAIDAMOUNT'),

                        'CREATEDBY'             => $this->input->post('CREATEDBY'),

                        'CREATEDTIME'           => date('Y-m-d H:i:s',time()),

                        'CANCELLED'             => 'N'

                    ));



                    $MEDID = $this->db->insert_id();
                }



                    if( $MEDID > 0 ) {


                        foreach ($this->input->post('LABORATORIES[]') as $key => $v) {


                            if( (int)$v['ID'] > 0 ){


                                $this->db->update('mr_laboratory',array(

                                    'LABORATORYID'      => $v['LABORATORYID'],

                                    'TEMPLATERESULT'    => $v['TEMPLATERESULT'],

                                    'UPDATEDBY'         => $this->session->USERID,

                                    'UPDATEDTIME'       => date('Y-m-d H:i:s',time()),

                                    'CANCELLED'         => (Boolean)$v['CANCELLED'] ? 'Y' : 'N'

                                ),array('ID'=>$v['ID']));

                            }

                            else if( !(Boolean)$v['CANCELLED'] ){



                                $this->db->insert('mr_laboratory',array(

                                    'MEDICALRECORDID'   => $MEDID,

                                    'LABORATORYID'      => $v['LABORATORYID'],

                                    'TEMPLATERESULT'    => $v['TEMPLATERESULT'],

                                    'CREATEDBY'         => $this->session->USERID,

                                    'CREATEDTIME'       => date('Y-m-d H:i:s',time()),

                                    'CANCELLED'         => 'N'

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

                                    'UPDATEDBY'     => $this->session->USERID,

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

                                    'CREATEDBY'         => $this->session->USERID,

                                    'CREATEDTIME'       => date('Y-m-d H:i:s',time()),

                                    'CANCELLED'         => 'N'

                                ));

                                $this->Check_Instruction($v['INSTRUCTION']);

                            }

                        } 


                        foreach ($this->input->post('SERVICES[]') as $key => $v) {


                            if( (int)$v['ID'] > 0 ){


                                $this->db->update('mr_services',array(

                                    'SERVICEID'=>$v['SERVICEID'],

                                    'QUANTITY'=>$v['QUANTITY'],

                                    'UNITPRICE'=>$v['UNITPRICE'],

                                    'AMOUNT'=>$v['AMOUNT'],

                                    'UPDATEDBY' => $this->session->USERID,

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

                                    'CREATEDBY' => $this->session->USERID,

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

                                    'UPDATEDBY' => $this->session->USERID,

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

                                    'CREATEDBY' => $this->session->USERID,

                                    'CREATEDTIME' => date('Y-m-d H:i:s',time()),

                                    'CANCELLED'=> 'N'

                                )); 

                            }

                        }

                    }



                    $this->Update_Previous_Appointment($MEDID);



                    $data['suc']['MRID'] = $MEDID;



                    $data['suc']['LABORATORIES'] = $this->LABORATORIES($MEDID);



                    $data['suc']['MEDICINES'] = $this->MEDICINES($MEDID);



                    $data['suc']['SERVICES'] = $this->SERVICES($MEDID);



                    $data['suc']['DISCOUNTS'] = $this->DISCOUNTS($MEDID);

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





        private function Update_Previous_Appointment($MRID){



            // CREATEDBY SERVED AS DOCTOR ALREADY CHECK AND UPDATED

            // IF NULL MEANS IT JUST PREPARED BY



            $sql = $this->db->query("SELECT PATIENTID, CHECKUPDATE FROM medicalrecords WHERE ID=? AND CREATEDBY > 0 LIMIT 1", array($MRID))->row();



            if( $sql ){



                $served = $this->db->query("SELECT ID FROM medicalrecords 

                    WHERE PATIENTID=? 

                    AND DATE(CHECKUPDATE) < ?

                    AND APPOINTMENT = 'Y' 

                    AND APPOINTMENTSERVED IS NULL 

                    AND CANCELLED='N' ", array(

                        $sql->PATIENTID,

                        date('Y-m-d',strtotime($sql->CHECKUPDATE))

                    ))->result();

                

                foreach ($served as $key => $value) {

                    $this->db->update('medicalrecords',array(

                        'APPOINTMENTSERVED' => 'Y'

                    ), array('ID'=> $value->ID));

                }

            }



        }





        public function  Submit_Upload($MRID){


            $folderdate = 'records/'.$MRID;

            if ( !is_dir('uploads/'.$folderdate) ) {
                mkdir('uploads/' . $folderdate, 0777, TRUE);
            }            

            $this->m_utility->Add_Index_File('uploads/' . $folderdate);


            $config['upload_path']      = 'uploads/'.$folderdate;
            $config['allowed_types']    = '*';
            $config['overwrite']        = false;
            $config['max_size']         = 100000;


            $this->load->library('upload', $config);
            $this->upload->initialize($config);


            if ( ! $this->upload->do_upload('file')) {

                $response = $this->upload->display_errors(' ',' ');
            }
            else {

                $data = $this->upload->data();

                $this->db->insert('mr_images',
                    array(
                        'MEDICALRECORDID'   => $MRID,
                        'IMAGEPATH'         => 'uploads/'.$folderdate.'/'.$data['file_name'],
                        'EXTENSION'         => $data['file_ext'],
                        'FILENAME'          => $data['file_name'],
                        'DATEUPLOADED'      => date('Y-m-d H:i:s'),
                        'CANCELLED'         => 'N'
                    )
                );

                $response = $this->db->query("SELECT * FROM mr_images WHERE ID=?  LIMIT 1",array( $this->db->insert_id() ))->row();
            }

            return $response;
        }



        public function  Submit_Laboratory_Upload($MRID, $MLABID){


            $folderdate = 'records/'.$MRID;

            if ( !is_dir('uploads/'.$folderdate) ) {
                mkdir('uploads/' . $folderdate, 0777, TRUE);
            }      
            $this->m_utility->Add_Index_File('uploads/' . $folderdate);
            
            $folderdate .= '/laboratory';

            if ( !is_dir('uploads/'.$folderdate) ) {
                mkdir('uploads/' . $folderdate, 0777, TRUE);
            }      

            $this->m_utility->Add_Index_File('uploads/' . $folderdate);


            $config['upload_path']      = 'uploads/'.$folderdate;
            $config['file_name']        = 'lab_'.$MLABID.'_'.date('his', time());
            $config['allowed_types']    = '*';
            $config['overwrite']        = true;
            $config['max_size']         = 10000;


            $this->load->library('upload', $config);
            $this->upload->initialize($config);


            if ( ! $this->upload->do_upload('file')) {

                $response = $this->upload->display_errors(' ',' ');
            }
            else {

                $data = $this->upload->data();

                $this->db->update('mr_laboratory',array(
                    'IMAGEPATH'         => 'uploads/'.$folderdate.'/'.$data['file_name'],
                    'EXTENSION'         => $data['file_ext'],
                    'FILENAME'          => $data['file_name']
                ), array('ID' => $MLABID));

                $response = $this->db->query("SELECT * FROM mr_laboratory WHERE ID=?  LIMIT 1",array( $MLABID ))->row();
            }

            return $response;
        }

        



        public function Email_Prescription(){


            $_POST += json_decode(file_get_contents('php://input'), true);



            $data = array(

                'err' => '',

                'suc' => array()

            );



            $this->load->library('form_validation');

            $this->form_validation->set_rules('TOKEN','token', 'trim|required');    



            if ($this->form_validation->run() == TRUE){



                if( $this->m_utility->tokenCheck($this->input->post('TOKEN')) ){ 



                    $MEDID = $this->m_utility->tokenRetrieve($this->input->post('TOKEN'));



                    $sql = $this->db->query("SELECT M.ID, P.FIRSTNAME, P.MIDDLENAME, P.LASTNAME, P.EMAIL AS PATIENTEMAIL,

                        U.NAME AS DOCTORNAME, U.EMAIL AS USEREMAIL

                        FROM medicalrecords M

                        INNER JOIN patients P ON P.ID = M.PATIENTID

                        INNER JOIN users U ON U.ID = M.CREATEDBY

                        WHERE M.ID=?",array($MEDID))->row();



                    if( $sql ){



                        if( filter_var( $sql->PATIENTEMAIL, FILTER_VALIDATE_EMAIL) && filter_var($sql->USEREMAIL, FILTER_VALIDATE_EMAIL) ) {



                            $this->load->model('report/m_medical_prescription');



                            $pdfPath = $this->m_medical_prescription->index($sql->ID, TRUE);

                            

                            if( $pdfPath ){



                                $config = array(

                                    'protocol' => 'sendmail',

                                    'smtp_host' => 'ssl://smtp.googlemail.com',

                                    'smtp_port' => 465,

                                    'smtp_user' => 'admin@cerebrodiagnostics.com',

                                    'smtp_pass' => 'Alp65230071',

                                    'mailtype' => 'html',

                                    'smtp_crypto' => 'ssl',

                                    'charset' => 'iso-8859-1'

                                );



                                $this->load->library('email', $config);

                                $this->email->set_newline("\r\n");

                                

                                



                                $this->email->from('admin@cerebrodiagnostics.com', 'Cerebro Diagnostic System');

                                $this->email->to($sql->PATIENTEMAIL);

                                $this->email->cc($sql->USEREMAIL);

                                $this->email->reply_to($sql->USEREMAIL, $sql->DOCTORNAME);



                                



                                $this->email->subject('Medical Prescription');

                                

                                $msg = $this->m_utility->Email_Header_Template();



                                $msg .= '<div style="padding:1rem; box-shadow: 0 0.5rem 1rem rgba(0, 0, 0, 0.15); background-color: rgba(250,250,250,0.7); ">';

                                $msg .= '<p>Hi Mr/Ms '.$sql->LASTNAME.', '.$sql->FIRSTNAME.' '.$sql->MIDDLENAME.'!</p>';

                                $msg .= '<p>Please see attached file for your electronic prescription from '.$sql->DOCTORNAME.'. This document is strictly private, confidential and personal to its recipients and should not be copied, distributed or reproduced in whole or in part, nor passed to any third party.</p>';

                                $msg .= '</div>';

                                



                                $msg .= $this->m_utility->Email_Footer_Template();



                                $this->email->message($msg);

                                $this->email->attach($pdfPath, 'attachment', 'Prescription.pdf');



                                

                                if ( $this->email->send() ) {

                                    

                                }

                                else{

                                    $data['err'] .= 'Send failed';

                                }



                                $this->email->clear(TRUE);

                            }

                            else{

                                $data['err'] .= 'Please try again. Internal server error';  

                            }

                        }

                        else{

                            $data['err'] .='Email is invalid. Please check patient email and physician email to cc.';     

                        }

                    }

                    else{

                        $data['err'] .='Invalid request';   

                    }

                    

                }

                else {

                    $data['err'] .='Expired request. Please refresh the page.';

                }



            }



            $data['err'] .=validation_errors(' ',' ');



            return $data;

        }



    }

?>