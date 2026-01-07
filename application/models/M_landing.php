<?php
defined('BASEPATH') OR exit('No direct script access allowed');


class M_landing extends CI_Model
{

   function __construct(){ $this->load->database(); }
   


   public function Search_Doctor(){

      
      $_POST += json_decode(file_get_contents('php://input'), true);

      $data = array();

      $this->load->library('form_validation');
      $this->form_validation->set_rules('TOKEN','token', 'trim|required');

      if ($this->form_validation->run()){

			if( $this->m_utility->tokenCheck($this->input->post('TOKEN')) ){

            $NAME = $_POST['NAME'];
            $FROM = $_POST['FROM'];
            $TO = $_POST['TO'];
            
            $data = $this->db->query("SELECT U.ID, U.CLINICID, U.NAME, U.JOBTITLE, U.AVATAR, U.LINK, U.MOTTO, 
                  S.SPECIALTY,
                  SUM(SC.MAXPATIENT) AS MAXPATIENT,
                  SUM((SELECT COUNT(P.ID) FROM pre_appoint P WHERE P.SCHEDULEID = SC.ID AND P.ACKNOWLEDGED='Y' AND P.CANCELLED='N' LIMIT 1)) as TOTAL_ACKNOWLEDGED

               FROM users U
               INNER JOIN specialist S ON S.ID = U.SPECIALISTID
               LEFT JOIN schedules SC ON SC.USERID = U.ID  AND SC.SDATETIME > ? AND SC.CANCELLED='N'

               WHERE CONCAT(U.NAME,S.SPECIALTY) LIKE ?
               AND U.ISONLINE = 'Y'
               AND U.CANCELLED = 'N'
               GROUP BY U.ID
               ORDER BY MAXPATIENT DESC, U.ID
               LIMIT ?,?",
               array(
                  date('Y-m-d H:i:s',time()),
                  '%'.$NAME.'%',
                  $FROM, 
                  $TO
               ))->result();


            foreach ($data as $key => $value) {

               $value->LOCATIONS =  $this->db->query("SELECT S.ID, COORDLONG_0, COORDLONG_1, COORDSHORT_0, COORDSHORT_1
                  FROM subclinic S
                  WHERE CLINICID=? AND ISONLINE='Y' AND COORDLONG_0 > 0 ",
                  array($value->CLINICID))->result();
            }

         }
		}
           
      return $data;
   }
 

   public function Clinics_Location(){

      $sql =  $this->db->query("SELECT S.ID, COORDLONG_0, COORDLONG_1, COORDSHORT_0, COORDSHORT_1
         FROM subclinic S
         WHERE ISONLINE='Y' AND COORDLONG_0 > 0 ")->result();

      return $sql;
   }


   public function Map_Clinic_Schedules(){

      
      $_POST += json_decode(file_get_contents('php://input'), true);

      $data = array(
         'err' => '',
         'suc' => []
      );

      $this->load->library('form_validation');
      $this->form_validation->set_rules('TOKEN','token', 'trim|required');
      $this->form_validation->set_rules('SUBCLINIC','Clinic', 'trim|required');

      if ($this->form_validation->run()){

			if( $this->m_utility->tokenCheck($this->input->post('TOKEN')) ){

            $SUBCLINICID = preg_replace('/\D/', '', $this->input->post('SUBCLINIC'));

            $clinic = $this->db->query("SELECT C.ID, C.CLINICNAME, C.CLINICSUBNAME, C.CLINICSUBNAME1, C.CONTACTNO, C.MOBILENO, C.EMAIL,
                  S.MONTIME, S.TUETIME, S.WEDTIME, S.THUTIME, S.FRITIME, S.SATTIME, S.SUNTIME,
                  U.ID AS DOCTORID, U.LINK
               FROM clinics C
               INNER JOIN subclinic S ON S.CLINICID = C.ID
               LEFT JOIN users U ON U.CLINICID = C.ID AND U.POSITION = 'BRANCH ADMINISTRATOR' AND U.ISONLINE = 'Y'
               WHERE S.ID = ? 
               LIMIT 1", array($SUBCLINICID))->row();

            if( $clinic ){
            
               $clinic->CLINICSUBNAME = is_null($clinic->CLINICSUBNAME) ? '' : $clinic->CLINICSUBNAME;
               $clinic->CLINICSUBNAME1 = is_null($clinic->CLINICSUBNAME1) ? '' : $clinic->CLINICSUBNAME1;

               $clinic->CONTACTNO = is_null($clinic->CONTACTNO) ? '' : $clinic->CONTACTNO;
               $clinic->MOBILENO = is_null($clinic->MOBILENO) ? '' : $clinic->MOBILENO;
               $clinic->EMAIL = is_null($clinic->EMAIL) ? '' : $clinic->EMAIL;

               if( $clinic->DOCTORID )
               $clinic->PROFILE = is_null($clinic->LINK) || empty($clinic->LINK) ? base_url($clinic->DOCTORID) : base_url($clinic->LINK);

                           
               $data['suc']['CLINIC'] = $clinic;

               $sqlSched = $this->db->query("SELECT S.SDATETIME, S.MAXPATIENT,
                     U.ID AS DOCTORID, U.LINK,
                     (SELECT COUNT(P.ID) FROM pre_appoint P WHERE P.SCHEDULEID = S.ID AND P.ACKNOWLEDGED='Y' AND P.CANCELLED='N' LIMIT 1) as TOTAL_ACKNOWLEDGED

                  FROM schedules S 
                  LEFT JOIN users U ON U.ID = S.USERID
                  WHERE S.SUBCLINICID=? AND DATE(S.SDATETIME) >= ?
                  ORDER BY S.SDATETIME
                  LIMIT 5", array(
                     $SUBCLINICID,
                     date('Y-m-d', time())
                  ))->result();

               foreach ($sqlSched as $key => $value) {
                  
                  $sqlSched[$key]->PROFILE = is_null($value->LINK) || empty($value->LINK) ? base_url($value->DOCTORID) : base_url($value->LINK);
               }

               $data['suc']['SCHEDULES'] = $sqlSched;
            }
         }
		}
           
      return $data;
   }


   public function Doctor_Page_Info($link){

      $sql = $this->db->query("SELECT U.ID, U.NAME, U.AVATAR, U.LINK, U.MOTTO, S.SPECIALTY
         FROM users U
         LEFT JOIN specialist S ON S.ID = U.SPECIALISTID
         WHERE (U.ID=? OR U.LINK=?) AND U.ISONLINE = 'Y'
         LIMIT 1",array($link,$link))->row();

      return $sql;
   }



   public function Doctor_Profile(){


      $_POST += json_decode(file_get_contents('php://input'), true);

      $data = array(
         'err' => '',
         'suc' => array()
      );
      
      $this->load->library('form_validation');
      $this->form_validation->set_rules('TOKEN','token', 'trim|required');

      if ($this->form_validation->run()){

         if( $this->m_utility->tokenCheck($this->input->post('TOKEN')) ){

            $link = $this->m_utility->tokenRetrieve($this->input->post('TOKEN'));  
            
            if( $link ){

               $sql = $this->db->query("SELECT U.ID, U.CLINICID, U.NAME, U.JOBTITLE, U.AVATAR, U.BACKGROUNDIMG, U.LINK, U.MOTTO, 
                  S.SPECIALTY
                  FROM users U
                  LEFT JOIN specialist S ON S.ID = U.SPECIALISTID
                  WHERE U.ID=? OR U.LINK=?
                  LIMIT 1",array($link,$link))->row();

               if( $sql ){
                  
                  $sql->TOKEN =  $this->m_utility->tokenRequest($sql->ID);

                  $sql->CLINIC = $this->db->query("SELECT CLINICNAME, CLINICSUBNAME, CLINICSUBNAME1, ADDRESS, CONTACTNO, MOBILENO, EMAIL 
                     FROM clinics 
                     WHERE ID=? 
                     LIMIT 1 ",array($sql->CLINICID))->row();

                  $sql->SUBCLINIC = $this->db->query("SELECT S.LOCATION, H.NAME
                     FROM subclinic S
                     LEFT JOIN hospitals H ON H.ID = S.HOSPITALID
                     WHERE S.CLINICID=? AND S.ISONLINE='Y' ",array($sql->CLINICID))->result();


                  $sql->MEMBERS = $this->db->query("SELECT U.ID, U.NAME, U.JOBTITLE, U.POSITION, U.LINK, U.AVATAR,
                     S.SPECIALTY
                     FROM users U 
                     LEFT JOIN specialist S ON S.ID = U.SPECIALISTID
                     WHERE U.ID != ? AND U.CLINICID=? AND U.CANCELLED='N' AND U.POSITION = 'BRANCH ASSISTANT'
                     ORDER BY U.POSITION DESC, U.NAME", array($sql->ID,$sql->CLINICID))->result();

                  $data['suc'] = $sql;
               }
               else{
                  $data['err'] = 'Invalid Request';    
               }
            }
            else{
               $data['err'] = 'Invalid Request'; 
            }
         }
      }
         
      return $data;
   }


   public function Doctor_Schedules(){

      
      $_POST += json_decode(file_get_contents('php://input'), true);

      $data = array();

      $this->load->library('form_validation');
      $this->form_validation->set_rules('TOKEN','token', 'trim|required');

      if ($this->form_validation->run()){

			if( $this->m_utility->tokenCheck($this->input->post('TOKEN')) ){

            $USERID = $this->m_utility->tokenRetrieve($this->input->post('TOKEN'));  
            
            if( $USERID ){

               $data = $this->db->query("SELECT S.ID, S.CLINICID, S.SDATETIME, S.MAXPATIENT, S.REMARKS,
                  SC.NAME,  SC.LOCATION, H.NAME AS HOSPITALNAME,
                  (SELECT COUNT(P.ID) FROM pre_appoint P WHERE P.SCHEDULEID = S.ID AND P.VERIFIED='N' AND P.ACKNOWLEDGED='N' AND CANCELLED='N' LIMIT 1) as TOTAL_UNVERIFIED,
                  (SELECT COUNT(P.ID) FROM pre_appoint P WHERE P.SCHEDULEID = S.ID AND P.ACKNOWLEDGED='Y' AND CANCELLED='N' LIMIT 1) as TOTAL_ACKNOWLEDGED

                  FROM schedules S
                  INNER JOIN users U ON U.ID = S.USERID
                  INNER JOIN subclinic SC ON SC.ID = S.SUBCLINICID
                  LEFT JOIN hospitals H ON H.ID = SC.HOSPITALID
                  
                  WHERE S.USERID=? 
                  AND S.SDATETIME >= ?
                  AND S.CANCELLED='N'
                  ORDER BY S.SDATETIME " , 
                  array(
                     $USERID,
                     date('Y-m-d H:i:s', time()),
                  ))->result();

               foreach ($data as $key => $value) {
                  $value->TOKEN = $this->m_utility->tokenRequest($value->ID);
               }
            }
         }
		}
           
      return $data;
   }


   public function Submit_Pre_Appointment(){

      $_POST += json_decode(file_get_contents('php://input'), true);

      $data = array(
         'err' => '',
         'suc' => array()
      );

      $this->load->library('form_validation');
      $this->form_validation->set_rules('TOKEN','token', 'trim|required');
      $this->form_validation->set_rules('CLINICID','Clinic ID', 'trim|required');
      $this->form_validation->set_rules('FIRSTNAME','First Name', 'trim|required|max_length[50]');
      $this->form_validation->set_rules('MIDDLENAME','Middle Name', 'trim|required|max_length[50]');
      $this->form_validation->set_rules('LASTNAME','Last Name', 'trim|required|max_length[50]');
      $this->form_validation->set_rules('DOB','Date of Birth', 'trim|required');
      $this->form_validation->set_rules('SEX','Sex', 'trim|required');
      $this->form_validation->set_rules('CIVILSTATUS','Civil Status', 'trim|required|max_length[20]');
      $this->form_validation->set_rules('STREETNO','Street No.', 'trim|required|max_length[50]');
      $this->form_validation->set_rules('CITY','City', 'trim|required|max_length[50]');
      $this->form_validation->set_rules('PROVINCE','Province', 'trim|max_length[50]');
      $this->form_validation->set_rules('MOBILENO','Mobile No', 'trim|required|max_length[20]');
      $this->form_validation->set_rules('EMAIL','Email', 'trim|max_length[50]');
      $this->form_validation->set_rules('COMPLAINT','Complaint', 'trim|required|max_length[200]');


      if ($this->form_validation->run()){

         if( $this->m_utility->tokenCheck($this->input->post('TOKEN')) ){

            $SCHEDULEID = $this->m_utility->tokenRetrieve($this->input->post('TOKEN'));  


            $sql = $this->db->query("SELECT ID
               FROM block_mobile 
               WHERE MOBILENO=? AND CANCELLED='N'
               LIMIT 1",array($this->input->post('MOBILENO')))->row();

            if( $sql ){
               $data['err'] .= 'Sorry the mobile number you entered is blocked.';
            }


            $sql = $this->db->query("SELECT P.ID
               FROM pre_appoint P 
               WHERE P.MOBILENO=? AND P.VERIFIED='N' AND P.ACKNOWLEDGED='N'
               LIMIT 1",array($this->input->post('MOBILENO')))->row();

            if( $sql ){
               $data['err'] .= 'Sorry the mobile number you given still need to verify or acknowledge.';
            }


            $sql = $this->db->query("SELECT COUNT(P.ID) AS TOTAL, S.MAXPATIENT
               FROM pre_appoint P 
               INNER JOIN schedules S ON S.ID = P.SCHEDULEID
               WHERE S.ID=? AND P.ACKNOWLEDGED='Y' AND P.CANCELLED='N'
               GROUP BY S.ID
               LIMIT 1",array($SCHEDULEID))->row();

            if( $sql ){

               if( $sql->TOTAL >= $sql->MAXPATIENT ){
                  $data['err'] .= 'Sorry the schedule you selected is already full.';
               }

               $data['suc']['TOTAL_ACKNOWLEDGED'] = $sql->TOTAL;
            }


            if( empty($data['err'])) {

               $code = '';

               while ( empty($code)) {
                  
                  $tempCode = $this->m_utility->Num_Generator();

                  if( strlen($tempCode) >= 4 ){

                     $sql = $this->db->query("SELECT ID FROM pre_appoint where VERIFIEDCODE=? AND VERIFIED='N' ",array($tempCode))->row();
                     if( ! $sql ){
                        $code = $tempCode;
                     }
                  }
               }

               $this->db->insert('pre_appoint',array(
                  'CLINICID' => $this->input->post('CLINICID'),
                  'SCHEDULEID' => $SCHEDULEID,
                  'FIRSTNAME' => ucwords(strtolower($this->input->post('FIRSTNAME'))),
                  'MIDDLENAME' => ucwords(strtolower($this->input->post('MIDDLENAME'))),
                  'LASTNAME' => ucwords(strtolower($this->input->post('LASTNAME'))),
                  'DOB' => date('Y-m-d',strtotime($this->input->post('DOB'))),
                  'SEX' => $this->input->post('SEX'),
                  'CIVILSTATUS' => $this->input->post('CIVILSTATUS'),
                  'MOBILENO' => $this->input->post('MOBILENO'),
                  'EMAIL' => $this->input->post('EMAIL'),
                  'STREETNO' => $this->input->post('STREETNO'),
                  'CITY' => $this->input->post('CITY'),
                  'PROVINCE' => $this->input->post('PROVINCE'),
                  'COMPLAINT' => $this->input->post('COMPLAINT'),
                  'VERIFIED' => 'N',
                  'VERIFIEDCODE' => $code,
                  'ACKNOWLEDGED' => 'N',
                  'CANCELLED' => 'N',
                  'BLOCKED' => 'N',
                  'CREATEDTIME' => date('Y-m-d H:i:s', time())
               )); 

               $ID = $this->db->insert_id();

               $this->load->model('m_text'); 
               $this->m_text->Online_Appointment_Code($ID);
            }
  
         }
			else {
            $data['err'] .='Invalid Request.';
			}
      }

      $data['err'] .= validation_errors(' ',' ');

      return $data;
   }


   
   public function Submit_Verify_Code(){

      
      $_POST += json_decode(file_get_contents('php://input'), true);

      $data = array(
         'err' => '',
         'suc' => array()
      );

      $this->load->library('form_validation');
      $this->form_validation->set_rules('TOKEN','token', 'trim|required');
      $this->form_validation->set_rules('CODE','Code', 'trim|required');

      if ($this->form_validation->run()){

			if( $this->m_utility->tokenCheck($this->input->post('TOKEN')) ){

            $USERID = $this->m_utility->tokenRetrieve($this->input->post('TOKEN'));  
           
            $sql = $this->db->query("SELECT P.ID, P.VERIFIED
               FROM schedules S 
               INNER JOIN pre_appoint P ON P.SCHEDULEID = S.ID
               WHERE S.USERID=? AND P.VERIFIEDCODE=? AND P.CANCELLED='N' ",array(
                  $USERID, 
                  $this->input->post('CODE')
               ))->row();

            if( $sql ){

               if( $sql->VERIFIED == 'N' ){
                  $this->db->update('pre_appoint',array(
                     'VERIFIED' => 'Y',
                     'VERIFIEDTIME' => date('Y-m-d H:i:s', time())
                  ),array('ID' => $sql->ID));
               }
               else{
                  $data['err'] .= 'The code you entered is already verified. If the clinic has not yet contacted you, please call them for assistance on the number you provided.';
               }
               
               
            }
            else{
               $data['err'] .='Invalid Code. Please check the physician you select and RESEND CODE.'; 
            }
         }
			else {
            $data['err'] .='Invalid Request.';
			}
      }

      $data['err'] .= validation_errors(' ',' ');
           
      return $data;
   }



   public function Submit_Resend_Code(){

      
      $_POST += json_decode(file_get_contents('php://input'), true);

      $data = array(
         'err' => '',
         'suc' => array()
      );

      $this->load->library('form_validation');
      $this->form_validation->set_rules('TOKEN','token', 'trim|required');
      $this->form_validation->set_rules('MOBILENO','Mobile No', 'trim|required');

      if ($this->form_validation->run()){

			if( $this->m_utility->tokenCheck($this->input->post('TOKEN')) ){

            $USERID = $this->m_utility->tokenRetrieve($this->input->post('TOKEN'));  
           
            $sql = $this->db->query("SELECT P.ID, P.VERIFIED
               FROM schedules S 
               INNER JOIN pre_appoint P ON P.SCHEDULEID = S.ID
               WHERE S.USERID=? AND P.MOBILENO=? AND P.CANCELLED='N' 
               ORDER BY P.VERIFIED ",array(
                  $USERID, 
                  $this->input->post('MOBILENO')
               ))->row();

            if( $sql ){

               if( $sql->VERIFIED == 'N' ){
                 
                  $this->load->model('m_text'); 
                  $this->m_text->Online_Appointment_Code($sql->ID);
               }
               else{
                  $data['err'] .= 'The code is already verified. If the clinic has not yet contacted you, please call them for assistance on the number you provided.';
               }
            }
            else {
               $data['err'] .='Invalid Code or Mobile No. Please check the physician info and RESEND CODE.'; 
            }
         }
			else {
            $data['err'] .='Invalid Request.';
			}
      }

      $data['err'] .= validation_errors(' ',' ');
           
      return $data;
   }



   public function Submit_Jitsi_Check_Patient(){

      
      $_POST += json_decode(file_get_contents('php://input'), true);

      $data = array(
         'err' => '',
         'suc' => array()
      );

      $this->load->library('form_validation');
      $this->form_validation->set_rules('DOCTORID','Doctor', 'trim|required');
      $this->form_validation->set_rules('LASTNAME','Lastname', 'trim|required');
      $this->form_validation->set_rules('DOB','Date of Birth', 'trim|required');

      if ($this->form_validation->run()){

         $sql = $this->db->query("SELECT P.ID 
            FROM patients P 
            INNER JOIN clinics C ON C.ID = P.CLINICID
            INNER JOIN users U ON U.CLINICID = C.ID
            WHERE U.ID=? AND UPPER(P.LASTNAME)=? AND DATE(P.DOB)=? 
            LIMIT 1",array(
               $this->input->post('DOCTORID'),
               strtoupper($this->input->post('LASTNAME')),
               date('Y-m-d', strtotime($this->input->post('DOB')))
            ))->row();

         if( $sql ){
            $data['suc']['PATIENTID'] = $sql->ID;
         }
         else{
            $data['err'] .= 'Patient info not found! Please check the info or incorrect physician.';
         }
      }

      $data['err'] .= validation_errors(' ',' ');
           
      return $data;
   }

}
?>