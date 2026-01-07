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

        $this->Image( $this->imagePath.'wm/clinic8_pres_1.png', 0, 0, 0, 2.5, '', '', '', true, 300);
        $this->Image( $this->imagePath.'wm/clinic8_pres_2.png', 0, 2.5, 0, 3, '', '', '', true, 300);
        $this->Image( $this->imagePath.'wm/clinic8_pres_3.png', 0, 5.506, 0, 2.7, '', '', '', true, 300);
    }

 

    
    public function Header() {

        $this->imagePath = dirname(__FILE__).'/images/';

        $this->Image( $this->imagePath.'clinic8_1.png', $this->mlr, 0.67, 0.45, 0);
        $this->Image( $this->imagePath.'clinic8_2.png', $this->getPageWidth() - ($this->mlr + 0.65), 0.75, 0.3, 0);
        $this->Image( $this->imagePath.'clinic8_3.png', $this->getPageWidth() - ($this->mlr + 0.3), 0.75, 0.3, 0);

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
        $this->Cell(2.7, 00.1,$this->patient->ADDRESS, 'B', 0,'C');
        $this->Cell(0.5, 0.1," AGE: ");
        $this->Cell(0, 0.1,$this->patient->AGE, 'B', 1,'C');
    }

    

    public function Footer() {
        
        $this->SetY(-1.3);
        $this->Ln(0.1);
        
        $currY = $this->GetY();
        $this->SetFont('arial', '', 9);  
        
        $this->SetTextColor(15, 15, 15);
        
        
        if( $this->doctor ){
            
            $this->SetFont('arial', 'B', 10); 
            $this->MultiCell(0, 0, $this->doctor->NAME, 'B', 'C', false, 1,  ($this->mlr + 2.3), $currY + 0.14);
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

class M_medical_instruction_wm_8 extends CI_Model {


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
            

            $pdf->SetFont('arial', 'BI', 13);
            $pdf->Cell(0,0, '*** INSTRUCTION ***', 0, 1, 'C');
            $pdf->Ln(0.2);
            $pdf->SetFont('arial', '', 10);

            $pdf->MultiCell(0, 0, $pdf->patient->INSTRUCTION, 0, 'L', false, 1, $mlr, $pdf->GetY() );
            
        }
	
		
        ob_end_clean();
        
        $pdfPath = '/temp_files_pdf/Instruction_'.$id.'_.pdf';
        $pdf->Output( realpath(''). $pdfPath, 'F');
		
        echo base_url($pdfPath);
		// if( $return ){
		// 	return base_url($pdfPath);
		// }
		// else{
		// 	echo base_url($pdfPath);
		// }

	}

}

?>