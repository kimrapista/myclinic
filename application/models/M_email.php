<?php

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\SMTP;
use PHPMailer\PHPMailer\Exception;

require_once getcwd()."/application/libraries/mailer/src/PHPMailer.php";
require_once getcwd()."/application/libraries/mailer/src/SMTP.php";
require_once getcwd()."/application/libraries/mailer/src/Exception.php"; 

defined('BASEPATH') OR exit('No direct script access allowed');


class M_email extends CI_Model
{
	

	function __construct(){ 
        $this->load->database(); 
    }


    public function Send_Email_Prescription(){

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
                    U.NAME AS DOCTORNAME, U.EMAIL AS USEREMAIL,
                    C.CLINICNAME

                    FROM medicalrecords M
                    INNER JOIN patients P ON P.ID = M.PATIENTID
                    INNER JOIN clinics C ON C.ID = M.CLINICID
                    INNER JOIN users U ON U.ID = M.CREATEDBY
                    WHERE M.ID=?",array($MEDID))->row();

                if( $sql ){


                    if( filter_var( $sql->PATIENTEMAIL, FILTER_VALIDATE_EMAIL) && filter_var($sql->USEREMAIL, FILTER_VALIDATE_EMAIL) ) {


                        $this->load->model('report/m_medical_prescription');

                        $pdfPath = $this->m_medical_prescription->index($sql->ID, TRUE);                        

                        if( $pdfPath ){

                            try{

                                $mail = new PHPMailer(true);
                                $mail->isSMTP();                                           
                                $mail->SMTPAuth   = true;
                                $mail->XMailer    = "cerebrodiagnostics.com"; 

                                $mail->SMTPSecure = 'ssl';            
                                $mail->Host       = 'cpanel32698811.vultr.com';                    
                                $mail->Port       = 465;
                                $mail->Username   = 'admin@cerebrodiagnostics.com';                     
                                $mail->Password   = 'Alp65230071';                              
            
                                $mail->SMTPOptions = array(
                                    'ssl' => array(
                                        'verify_peer' => true,
                                        'verify_depth' => 3,
                                        'allow_self_signed' => true
                                    )
                                );


                                $mail->DKIM_domain = 'cerebrodiagnostics.com';
                                $mail->DKIM_private = getcwd().'/application/libraries/mailer/src/others/private.key';
                                $mail->DKIM_selector = 'default';
                                $mail->DKIM_passphrase = '';
                                $mail->DKIM_identity = $mail->From;


                                $mail->setFrom('admin@cerebrodiagnostics.com', 'MyClinic');
                                $mail->addAddress(trim($sql->PATIENTEMAIL)); 
                                $mail->addReplyTo('no-reply@cerebrodiagnostics.com', 'No-reply');
                                $mail->addCC($sql->USEREMAIL);


                                $mail->isHTML(true);
                                $mail->Subject = $sql->CLINICNAME. ': Medical Prescription';

                                $msg = 'Hi Mr/Ms '.$sql->LASTNAME.', '.$sql->FIRSTNAME.' '.$sql->MIDDLENAME.'<br>';
                                $msg .= 'Please see attached file for your electronic medical prescription from '.$sql->DOCTORNAME.'. This document is strictly private, confidential and personal to its recipients and should not be copied, distributed or reproduced in whole or in part, nor passed to any third party.';

                                $mail->Body = $msg;
                                $mail->AltBody = $msg;

                                $pdfPath = str_replace(base_url(), '', $pdfPath);

                                $pdfPath = getcwd().'/'.$pdfPath;

                                $mail->addAttachment($pdfPath);
                                $mail->send();


                            }
                            catch (Exception $e) {
                                $removeStr = 'https://github.com/PHPMailer/PHPMailer/wiki/Troubleshooting';
                                $data['err'] = str_replace($removeStr,'',$e->getMessage()); 
                            }
                            catch (\Exception $e) { 
                                $data['err'] = $e->getMessage(); 
                            }
                                                     
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

    
    public function Send_Email_Certificate(){

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
                    U.NAME AS DOCTORNAME, U.EMAIL AS USEREMAIL,
                    C.CLINICNAME

                    FROM medicalrecords M
                    INNER JOIN patients P ON P.ID = M.PATIENTID
                    INNER JOIN clinics C ON C.ID = M.CLINICID
                    INNER JOIN users U ON U.ID = M.CREATEDBY
                    WHERE M.ID=?",array($MEDID))->row();

                if( $sql ){


                    if( filter_var( $sql->PATIENTEMAIL, FILTER_VALIDATE_EMAIL) && filter_var($sql->USEREMAIL, FILTER_VALIDATE_EMAIL) ) {


                        $this->load->model('report/M_medical_certificate');

                        $pdfPath = $this->M_medical_certificate->index($sql->ID, TRUE);                        

                        if( $pdfPath ){

                            try{

                                $mail = new PHPMailer(true);
                                $mail->isSMTP();                                           
                                $mail->SMTPAuth   = true;
                                $mail->XMailer    = "cerebrodiagnostics.com"; 

                                $mail->SMTPSecure = 'ssl';            
                                $mail->Host       = 'cpanel32698811.vultr.com';                    
                                $mail->Port       = 465;
                                $mail->Username   = 'admin@cerebrodiagnostics.com';                     
                                $mail->Password   = 'Alp65230071';                              
            
                                $mail->SMTPOptions = array(
                                    'ssl' => array(
                                        'verify_peer' => true,
                                        'verify_depth' => 3,
                                        'allow_self_signed' => true
                                    )
                                );


                                $mail->DKIM_domain = 'cerebrodiagnostics.com';
                                $mail->DKIM_private = getcwd().'/application/libraries/mailer/src/others/private.key';
                                $mail->DKIM_selector = 'default';
                                $mail->DKIM_passphrase = '';
                                $mail->DKIM_identity = $mail->From;


                                $mail->setFrom('admin@cerebrodiagnostics.com', 'MyClinic');
                                $mail->addAddress(trim($sql->PATIENTEMAIL)); 
                                $mail->addReplyTo('no-reply@cerebrodiagnostics.com', 'No-reply');
                                $mail->addCC($sql->USEREMAIL);


                                $mail->isHTML(true);
                                $mail->Subject = $sql->CLINICNAME. ': Medical Certificate';

                                $msg = 'Hi Mr/Ms '.$sql->LASTNAME.', '.$sql->FIRSTNAME.' '.$sql->MIDDLENAME.'<br>';
                                $msg .= 'Please see attached file for your electronic medical certificate from '.$sql->DOCTORNAME.'. This document is strictly private, confidential and personal to its recipients and should not be copied, distributed or reproduced in whole or in part, nor passed to any third party.';

                                $mail->Body = $msg;
                                $mail->AltBody = $msg;

                                $pdfPath = str_replace(base_url(), '', $pdfPath);

                                $pdfPath = getcwd().'/'.$pdfPath;

                                $mail->addAttachment($pdfPath);
                                $mail->send();


                            }
                            catch (Exception $e) {
                                $removeStr = 'https://github.com/PHPMailer/PHPMailer/wiki/Troubleshooting';
                                $data['err'] = str_replace($removeStr,'',$e->getMessage()); 
                            }
                            catch (\Exception $e) { 
                                $data['err'] = $e->getMessage(); 
                            }
                                                     
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