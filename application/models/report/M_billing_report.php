<?php
defined('BASEPATH') OR exit('No direct script access allowed');


class M_billing_report extends CI_Model
{

    function __construct(){ 
        $this->load->database();
        
    }


    public function Mini_Billing($MRID, $USERID){
        
        

        $pat = $this->db->query("SELECT M.ID, M.CLINICID, M.CHEIFCOMPLAINT, M.GROSSAMOUNT, M.DISCOUNTAMOUNT, M.NETPAYABLES,
            P.FIRSTNAME, P.MIDDLENAME, P.LASTNAME, P.DOB
        FROM medicalrecords M
        INNER JOIN patients P ON P.ID = M.PATIENTID
        WHERE M.ID=? AND M.CANCELLED=0 LIMIT 1", array($MRID))->row();

        $services = $this->db->query("SELECT S.NAME, M.AMOUNT
        FROM mr_services M
        INNER JOIN services S ON S.ID = M.SERVICEID
        WHERE M.MEDICALRECORDID=? AND M.CANCELLED=0 ", array($MRID))->result();

        $discounts = $this->db->query("SELECT D.NAME, M.AMOUNT
        FROM mr_discounts M
        INNER JOIN discounts D ON D.ID = M.DISCOUNTID
        WHERE M.MEDICALRECORDID=? AND M.CANCELLED=0 ", array($MRID))->result();

        if( $pat ){

            $clinic = $this->db->query("SELECT * FROM clinics where ID=? LIMIT 1",array($pat->CLINICID))->row();
            $user = $this->db->query("SELECT * FROM users where ID=? LIMIT 1",array($USERID))->row();
            
            $a = array();
    
    
            $a[] = array(
                'type' => 0,
                'content' => $clinic->CLINICNAME,
                'bold' => 1,
                'align' => 1,
                'format' => 4
            );

            // if( !empty($clinic->CLINICSUBNAME) ){
            //     $a[] = array(
            //         'type' => 0,
            //         'content' => $clinic->CLINICSUBNAME,
            //         'bold' => 0,
            //         'align' => 1,
            //         'format' => 4
            //     );
            // }

            // if( !empty($clinic->CLINICSUBNAME1) ){
            //     $a[] = array(
            //         'type' => 0,
            //         'content' => $clinic->CLINICSUBNAME1,
            //         'bold' => 0,
            //         'align' => 1,
            //         'format' => 4
            //     );
            // }

            // if( !empty($clinic->CLINICSUBNAME2) ){
            //     $a[] = array(
            //         'type' => 0,
            //         'content' => $clinic->CLINICSUBNAME2,
            //         'bold' => 0,
            //         'align' => 1,
            //         'format' => 4
            //     );
            // }


            // $a[] = array(
            //     'type' => 0,
            //     'content' => '-------------------------------------',
            //     'bold' => 1,
            //     'align' => 1,
            //     'format' => 4
            // );

    
    
            $a[] = array(
                'type' => 0,
                'content' => 'Patient: '. $pat->LASTNAME.', '. $pat->FIRSTNAME,
                'bold' => 0,
                'align' => 0,
                'format' => 4                
            );

            $a[] = array(
                'type' => 0,
                'content' => 'Date of Birth: '. date('m/d/Y', strtotime($pat->DOB)),
                'bold' => 0,
                'align' => 0,
                'format' => 4               
            );

            $a[] = array(
                'type' => 0,
                'content' => 'MR No: '. $pat->ID,
                'bold' => 0,
                'align' => 0,
                'format' => 4                
            );


            $a[] = array(
                'type' => 0,
                'content' => '-------------------------------------',
                'bold' => 1,
                'align' => 1,
                'format' => 4
            );

        
            foreach ($services as $key => $value) {
                $a[] = array(
                    'type' => 0,
                    'content' => $value->NAME." P".number_format($value->AMOUNT,2),
                    'bold' => 0,
                    'align' => 0,
                    'format' => 4
                );
            }

           

            if( count($discounts) > 0 ){
 
                foreach ($discounts as $key => $value) {
                    $a[] = array(
                        'type' => 0,
                        'content' => $value->NAME." P".number_format($value->AMOUNT,2),
                        'bold' => 0,
                        'align' => 0,
                        'format' => 4
                    );
                }
            }

            $a[] = array(
                'type' => 0,
                'content' => '----------------------',
                'bold' => 0,
                'align' => 2,
                'format' => 4
            );


            $a[] = array(
                'type' => 0,
                'content' => 'Subtotal: P'. number_format($pat->GROSSAMOUNT,2),
                'bold' => 0,
                'align' => 2,
                'format' => 4
            );

            $a[] = array(
                'type' => 0,
                'content' => 'Less: P'. number_format($pat->DISCOUNTAMOUNT,2),
                'bold' => 0,
                'align' => 2,
                'format' => 4
            );


            $a[] = array(
                'type' => 0,
                'content' => '----------------------',
                'bold' => 0,
                'align' => 2,
                'format' => 4
            );
            
            
            $a[] = array(
                'type' => 0,
                'content' => 'TOTAL: P'. number_format($pat->NETPAYABLES,2),
                'bold' => 1,
                'align' => 2,
                'format' => 4
            );


            $a[] = array(
                'type' => 0,
                'content' => '',
                'bold' => 0,
                'align' => 0,
                'format' => 4
            );

               
            
            if( $user ){
                $a[] = array(
                    'type' => 0,
                    'content' => "Printed By: ". $user->NAME,
                    'bold' => 0,
                    'align' => 0,
                    'format' => 4
                );
            }

            $a[] = array(
                'type' => 0,
                'content' => 'Date Printed: '. date('Y-m-d H:i:s a', time()),
                'bold' => 0,
                'align' => 0,
                'format' => 4
            );

            $a[] = array(
                'type' => 0,
                'content' => '*** THIS IS NOT AN OFFICIAL RECEIPT ***',
                'bold' => 0,
                'align' => 1,
                'format' => 4
            );


            $a[] = array(
                'type' => 0,
                'content' => '',
                'bold' => 0,
                'align' => 0,
                'format' => 4
            );
            
            
            // $a[] = array(
            //     'type' => 0,
            //     'content' => '*** END ***',
            //     'bold' => 1,
            //     'align' => 1,
            //     'format' => 0
            // );
            
            // $a[] = array(
            //     'type' => 0,
            //     'content' => '',
            //     'bold' => 0,
            //     'align' => 1,
            //     'format' => 0
            // );

            echo json_encode($a, JSON_FORCE_OBJECT);
        }
        else{
            echo 'INVALID DATA';
        }
        
    }

}

?>