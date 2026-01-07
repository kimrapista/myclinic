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

        $this->Image( $this->imagePath.'wm/clinic8_mc_1_1.png', 0, 0, 0, 2.53, '', '', '', true, 300); 
        $this->Image( $this->imagePath.'wm/clinic8_mc_1_2.png', 0, 2.532, 0, 1.91, '', '', '', true, 300);
        $this->Image( $this->imagePath.'wm/clinic8_mc_1_3.png', 0, 4.444, 0, 1.90, '', '', '', true, 300);
        $this->Image( $this->imagePath.'wm/clinic8_mc_1_4.png', 0, 6.346, 0, 1.84, '', '', '', true, 300);

        
        $noDisplay = 71 + (date('Y', time()) - 2022);
        
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
        $this->Ln(0.1);
        
        $this->SetTextColor(15, 15, 15);
        
        $this->SetFont('Arial','B', 13);
        $this->Cell(0, 0, 'CLEARANCE LETTER', 0, 1, 'C');
        $this->Ln(0.2);
    }
    

    public function Footer() {
        
        
    }

}

class M_clearance_letter_8 extends CI_Model
{
	
	function __construct(){ 
		$this->load->database(); 
        $this->imagePath = dirname(__FILE__).'/images/'; 		
	}

	private function clinicInfo(){
		$sql = $this->db->query("SELECT * FROM clinics where ID=? LIMIT 1",array($this->session->CLINICID));
		return $sql->row();
	}


	// ----------------- Medical Prescription Default Report ---------------
	public function Index($id){

		$pageSize = array(5.25, 8.20);

		$pdf = new PDF('P', 'in', $pageSize, true, 'UTF-8', false, true);
        $this->Tpdf = $pdf;

        // set document information
        $pdf->SetTitle('Clearance Letter');
        $pdf->SetSubject('Clearance Letter');
        $pdf->SetKeywords('Clearance Letter');

        $pdf->setPrintHeader(true);
        $pdf->setPrintFooter(false);
        $pdf->SetHeaderMargin(0);
        $pdf->SetFooterMargin(0);

		$mlr = 0.3;
        $mt = 0.2;
		$CH = 0.2; 

        $pdf->mlr = $mlr;
        $pdf->mt = $mt;

        $mt = 1.8;

        $pdf->SetDefaultMonospacedFont(PDF_FONT_MONOSPACED);
        $pdf->SetMargins($mlr, $mt, $mlr);
        $pdf->SetAutoPageBreak(TRUE, 1.3);

        $pdf->setImageScale(PDF_IMAGE_SCALE_RATIO);

        if (@file_exists(dirname(__FILE__).'/lang/eng.php')) {
            require_once(dirname(__FILE__).'/lang/eng.php');
            $pdf->setLanguageArray($l);
        }
 		
		
		$pdf->clinic = $this->clinicInfo();
		$clinic = $pdf->clinic;

		$pdf->patient = $this->db->query("SELECT p.ID, concat(p.FIRSTNAME,' ',p.MIDDLENAME,' ',p.LASTNAME) as NAME, concat(p.STREETNO) as ADDRESS,concat(p.CITY,' ',p.PROVINCE) as ADDRESS1, p.SEX , 
			mr.CLEARANCETO, mr.CLEARANCEMSG, U.NAME AS DOCTORNAME
			FROM patients p
			INNER JOIN medicalrecords mr ON mr.PATIENTID = p.ID
			INNER JOIN clinics c ON c.ID = mr.CLINICID
			LEFT JOIN users U ON U.ID = mr.CREATEDBY
			WHERE mr.ID = ?   
			LIMIT 1",array($id))->row();

		$patient = $pdf->patient;

		$pdf->AddPage();

		if( $id > 0 ){
			
			$pdf->SetFont('Arial','',10);
			$pdf->Cell(0,$CH,'To: '. $patient->CLEARANCETO, 0,1);
			$pdf->MultiCell(0, $CH, $patient->CLEARANCEMSG,0, 'L');

			$pdfPath = '/temp_files_pdf/Clearance_'.$id.'_.pdf';
			$pdf->Output( realpath(''). $pdfPath, 'F');
			echo base_url($pdfPath);

		}
		else{
			$pdf->SetFont('Arial','',10);
			$pdf->Cell(0,$CH,'To: Sample Name',0,1);
			$pdf->MultiCell(0, $CH, $clinic->CLEARANCEDEFAULTTEXT,0, 'L');
			
			$pdfPath = '/temp_files_pdf/Clearance_test'.$clinic->ID.'_.pdf';
			$pdf->Output( realpath(''). $pdfPath, 'F');
			echo base_url($pdfPath);
		}
		
	}

}

?>
