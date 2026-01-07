<?php
defined('BASEPATH') OR exit('No direct script access allowed');

require_once getcwd() .'/application/libraries/tpdf8/tcpdf.php';

class PDF extends TCPDF{ 

    public $clinic;
    public $patient;
    public $doctor;

    public $imagePath;
    public $mlr;
    public $mt;

    private function Watermark(){

        $this->SetXY(0,0);
        $this->setJPEGQuality(0);  

        $this->Image( $this->imagePath.'wm/clinic8_pres_1_1.png', 0, 0, 0, 2.53, '', '', '', true, 300); 
        $this->Image( $this->imagePath.'wm/clinic8_pres_1_2.png', 0, 2.532, 0, 1.91, '', '', '', true, 300);
        $this->Image( $this->imagePath.'wm/clinic8_pres_1_3.png', 0, 4.444, 0, 1.90, '', '', '', true, 300);
        $this->Image( $this->imagePath.'wm/clinic8_pres_1_4.png', 0, 6.346, 0, 1.84, '', '', '', true, 300);

        
        $noDisplay = 71 + (date('Y', time()) - 2022);
        $noDisplay = 250;
        
        $this->SetXY(3.98, 4.8);
        $this->StartTransform();
        $this->Rotate(-60);
        
        $this->SetFont('Arial', 'B', 18);
        $this->SetTextColor(190, 190, 190);
        $this->setAlpha(0.2);
        $this->Cell(1, 1, $noDisplay, 0, 0, 'C');
        $this->setAlpha(1);
        
        $this->StopTransform();
    }



    
    public function Header() {

        $this->imagePath = dirname(__FILE__).'/images/';

        $this->Image( $this->imagePath.'clinic8_1.png', $this->mlr, 0.67, 0.45, 0);
        $this->Image( $this->imagePath.'clinic8_2.png', $this->getPageWidth() - ($this->mlr + 0.65), 0.75, 0.3, 0);
        $this->Image( $this->imagePath.'clinic8_3.png', $this->getPageWidth() - ($this->mlr + 0.3), 0.75, 0.3, 0);

        // $this->Image( $this->imagePath.'clinic8_2.png', $this->getPageWidth() - ($this->mlr + 0.44), 0.55, 0.3, 0);
        // $this->Image( $this->imagePath.'clinic8_3.png', $this->getPageWidth() - ($this->mlr + 0.44), 0.88, 0.3, 0);
    
        $this->Ln($this->mt);
        $this->SetDrawColor(30, 30, 30);

        $this->SetFont('Times','B', 23);
        $this->Cell(0, 0,$this->clinic->CLINICNAME, 0, 1, 'C');

        $this->SetTextColor(40, 40, 40);
        $this->SetFont('Arial','B', 11);
        $this->Cell(0, 0,$this->clinic->CLINICSUBNAME,0,1,'C');

        $this->SetFont('Arial','B',11);
        $this->Cell(0, 0,$this->clinic->CLINICSUBNAME1,0,1,'C');

        $this->SetFont('Arial','B',11);
        $this->Cell(0, 0,$this->clinic->CLINICSUBNAME2,0,1,'C');
        $this->Ln(0.07);

        $this->SetTextColor(15, 15, 15);

        $widthCell = ($this->getPageWidth() - ($this->mlr * 2)) / 3;

        $this->SetFont('Arial', 'B', 7.7);
        $this->Cell($widthCell, 0.1,"CDO Polymedic General Hospital");
        $this->Cell($widthCell, 0.1," ");
        $this->Cell($widthCell, 0.1,"CDO Polymedic Medical Plaza",0,1);

        $this->SetFont('Arial','',7);
        $this->Cell($widthCell, 0.1,"Don A. Velez St.,CDOC");
        $this->Cell($widthCell, 0.1," ");
        $this->Cell($widthCell, 0.1,"Kauswagan Highway, CDOC",0,1);

        $this->Cell($widthCell, 0.1,"Ground Floor");
        $this->Cell($widthCell, 0.1," ");
        $this->Cell($widthCell, 0.1,"Room 241",0,1);

        $this->Cell($widthCell, 0.1,"856447 local 1014");
        $this->Cell($widthCell, 0.1," ");
        $this->Cell($widthCell, 0.1,"8585241 local 2241", 0, 1);
        $this->SetY($this->GetY() - 0.1);

        $this->Cell(0, 0, '', 'B',1);
        $this->Ln(0);

        $this->SetFont('Arial','',9);
        $this->Cell(1.1, 0.1,"PATIENT'S NAME: ");
        $this->Cell(2.3, 0.1,$this->patient->NAME, 'B', 0,'C');
        $this->Cell(0.5, 0.1," DATE: ");
        $this->Cell(0, 0.1,date('m/d/Y',time()),'B',1,'C');

        $this->Cell(0.7, 0.1,"ADDRESS: ");
        $this->Cell(2.7, 0.1,$this->patient->ADDRESS, 'B', 0,'C');
        $this->Cell(0.5, 0.1," AGE: ");
        $this->Cell(0, 0.1,$this->patient->AGE, 'B', 1,'C');
        

        $this->Image( $this->imagePath.'rx.png', $this->GetX(), $this->GetY() + 0.1, 0.4);
    }

    

    public function Footer() {
        
        $this->SetY(-1.3);
        $this->Ln(0.1);
        
        $currY = $this->GetY();
        $this->SetFont('arial', '', 9);
        
        $this->SetTextColor(15, 15, 15);
        
        if($this->patient->APPOINTMENT == 'N' || $this->patient->APPOINTMENT == NULL){
            $this->MultiCell(1.8, 0,"Your next appointment will :\n ",'B', 'J', false, 1);
            
        } else{
            $this->SetFillColor(252, 215, 246);
            $this->MultiCell(1.8, 0,"Your next appointment will :", 0, 1, false, 1);
            $this->MultiCell(1.8, 0,date('F d, Y',strtotime($this->patient->APPOINTMENTDATE)), 'B', 'C', true, 1);
        }

        
        $this->Ln(0.05);
        $currX1 = $this->GetX();
        $currY1 = $this->GetY();
        
        $this->SetTextColor(15, 15, 15);
        $this->MultiCell(1.8, 0, "Clinic No.:", 0, 'L');
        
        
        $this->SetFillColor(249, 250, 185);
        $this->SetXY($currX1 + 0.6, $currY1);
        $this->MultiCell(1.2, 0, $this->clinic->MOBILENO, 0, 'C', true);

        $this->SetTextColor(15, 15, 15);
        $this->SetX($currX1);
        $this->MultiCell(1.8, 0, "(7:30am to 7:00pm)", 0, 'L');
        
        $this->SetTextColor(15, 15, 15);
        
        if( $this->doctor ){
            
            $this->SetFont('arial', 'B', 10); 
            $this->MultiCell(0, 0, $this->doctor->NAME, 'B', 'C', false, 1, ($this->mlr + 2.3), $currY + 0.14);
            $this->Ln(0.05);
            
            if( $this->doctor->ISSIG == 'Y' && !is_null($this->doctor->ESIGNATURE) && !empty($this->doctor->ESIGNATURE) ){
                $sig = str_replace('data:image/png;base64,','', $this->doctor->ESIGNATURE);
                $sigData = base64_decode($sig);
                $this->Image('@'.$sigData,  ($this->mlr + 2.5), $this->GetY() - 0.7, 2, 0, 'PNG');
            }

            $this->SetFont('arial', '', 9);        
            $this->MultiCell( 0, 0,"License No.: ".$this->doctor->LICENSENO."\nPTR No.: ".$this->doctor->PTR."\nS2 No.: ".$this->doctor->S2NO, 0, 'L', false, 1, ($this->mlr + 2.3));
        }


        $this->SetFont('arial', '', 6.4);
        $this->SetTextColor(80, 80, 80);
        $this->MultiCell(0, 0, 'Tampering of any part of this document will invalidate its contents. Falsification of this document is a serious offense.', 0, 'J', false, 0, $this->mlr, $this->GetPageHeight() - 0.35);

        $this->Watermark();
    }


}

class M_medical_prescription_wm_8 extends CI_Model {


	function __construct(){ 
		$this->load->database(); 
        $this->imagePath = dirname(__FILE__).'/images/'; 		
	}


	private function clinicInfo(){
		$sql = $this->db->query("SELECT * FROM clinics where ID=? LIMIT 1",array($this->session->CLINICID));
		return $sql->row();
	}


	public function Index($id, $return = FALSE){
        
		$pageSize = array(5.25, 8.20);

        $pdf = new PDF('P', 'in', $pageSize, true, 'UTF-8', false, true);
        $this->Tpdf = $pdf;

        // set document information
        $pdf->SetTitle('Prescription');
        $pdf->SetSubject('Prescription');
        $pdf->SetKeywords('Prescription');

        $pdf->setPrintHeader(true);
        $pdf->setPrintFooter(true);
        $pdf->SetHeaderMargin(0);
        $pdf->SetFooterMargin(0);


        $mlr = 0.3;
        $mt = 0.2;

        $pdf->mlr = $mlr;
        $pdf->mt = $mt;

        $mt = 2.3;

        $pdf->SetDefaultMonospacedFont(PDF_FONT_MONOSPACED);
        $pdf->SetMargins($mlr, $mt, $mlr);
        $pdf->SetAutoPageBreak(TRUE, 1.3);

        $pdf->setImageScale(PDF_IMAGE_SCALE_RATIO);

        if (@file_exists(dirname(__FILE__).'/lang/eng.php')) {
            require_once(dirname(__FILE__).'/lang/eng.php');
            $pdf->setLanguageArray($l);
        }
    

        $pdf->clinic = $this->clinicInfo();
        
        $pdf->doctor = $this->db->query("SELECT U.NAME, U.PTR, U.LICENSENO, U.S2NO, U.ESIGNATURE, S.ISSIG
				FROM users U
				INNER JOIN medicalrecords MR ON MR.CREATEDBY = U.ID
                LEFT JOIN subclinic S ON S.ID = MR.SUBCLINICID
				WHERE MR.ID = ?   
				LIMIT 1",array($id))->row();

		$pdf->patient = $this->db->query("SELECT p.ID, concat(p.FIRSTNAME,' ',p.MIDDLENAME,' ',p.LASTNAME) as NAME, concat(p.STREETNO) as ADDRESS,concat(p.CITY,' ',p.PROVINCE) as ADDRESS1, p.SEX ,
                    mr.APPOINTMENT, mr.APPOINTMENTDATE, mr.AGE, mr.INSTRUCTION,
                    c.MOBILENO

			FROM patients p
			INNER JOIN medicalrecords mr ON mr.PATIENTID = p.ID
			INNER JOIN clinics c ON c.ID = mr.CLINICID
			WHERE mr.ID = ?   
			LIMIT 1",array($id))->row();
        
        if( $pdf->patient ){
            
            $pdf->AddPage();
            
            $meds = $this->db->query("SELECT m.NAME, mrm.FREQUENCY, mrm.INSTRUCTION, mrm.QUANTITY
                FROM mr_medicines mrm 
                INNER JOIN medicines m ON m.ID = mrm.MEDICINEID
                WHERE mrm.MEDICALRECORDID = ? and mrm.CANCELLED='N' ",array($id))->result();

            $pdf->SetFont('arial', '', 10);

            $pdf->SetTextColor(15, 15, 15);
            
            
            foreach ($meds as $key => $v) {

                $pdf->Cell(0.7, 0, ($key + 1).'.', 0, 0, 'R');
                $pdf->MultiCell(4, 0,
                    trim($v->NAME). ($v->QUANTITY > 0 ? ", #".$v->QUANTITY: "").
                    "\nSig: ".trim($v->INSTRUCTION).
                    ($v->FREQUENCY ? ',  '.trim($v->FREQUENCY) : '') 
                , 0, 'L');	
                
                if( $key < count($meds) )
                $pdf->Ln(0.175);
            }

            
        }
	 
		ob_end_clean();
        if( $return ){
            $pdf->Output('Prescription_'.$id.'.pdf', 'I');
		}
		else{
            
            $pdfPath = '/temp_files_pdf/Prescription_'.$id.'_.pdf';
            $pdf->Output( realpath(''). $pdfPath, 'F');
			echo base_url($pdfPath);
		}
	}

}

?>