<?php
defined('BASEPATH') OR exit('No direct script access allowed');

require_once getcwd() .'/application/libraries/tpdf8/tcpdf.php';
require_once(APPPATH.'libraries/tcpdf/tcpdf_barcodes_2d.php');

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
        
        $this->SetFont('Arial','B',13);
        $this->Cell(0, 0, 'MEDICAL CERTIFICATE', 0, 1, 'C');
        $this->Ln(0.2);
        
        $this->SetFont('Arial','',10);
        $this->Cell(3.5, 0, 'Date: ', 0, 0,'R');
        $this->Cell(0, 0, date('m/d/Y',time()), 'B', 1, 'C');
        
        $this->MultiCell(0,0, "To Whom It May Concern:\n\nThis is to certify that the person named hereunder has the following record of confinement/consultation and treatment.", 0, 'L', false, 1);

        $this->Ln(0.1);

        $this->Cell(3, 0.1, ' Name: '.$this->patient->NAME, 1, 0);
        $this->Cell(0, 0.1, ' Age/Sex: '.$this->patient->AGE.' / '.$this->patient->SEX, 1, 1);
        $this->Cell(0, 0.1, ' Address: '.$this->patient->STREETNO.' '.$this->patient->CITY, 1, 1);
    }

    public function Footer() {
        
        $this->SetY(-1.3);
        // $this->Ln(0.1);
        $currY = $this->GetY();
        $this->SetFont('arial', '', 9);  
        $this->SetTextColor(15, 15, 15);
        
        if( $this->doctor ){
            
            $this->SetFont('arial', 'B', 10); 
            $this->MultiCell(2, 0, $this->doctor->NAME, 0, 'L', false, 1, $this->mlr, $currY + 0.14);
            $this->Ln(0.05);

            if( $this->doctor->ISSIG == 'Y' && !is_null($this->doctor->ESIGNATURE) && !empty($this->doctor->ESIGNATURE) ){
                $sig = str_replace('data:image/png;base64,','', $this->doctor->ESIGNATURE);
                $sigData = base64_decode($sig);
                // $this->Image('@'.$sigData,  $this->mlr, $this->GetY() - 0.5, 1.5, 0, 'PNG');
                $this->Image('@'.$sigData,  0.1, $this->GetY() - 0.7, 2, 0, 'PNG');
            }

            $this->SetFont('arial', '', 9);        
            $this->MultiCell( 0, 0,"License No.: ".$this->doctor->LICENSENO."\nPTR No.: ".$this->doctor->PTR."\nS2 No.: ".$this->doctor->S2NO, 0, 'L', false, 1, $this->mlr);
        }


        $this->SetFont('arial', '', 6);
        $this->SetTextColor(80, 80, 80);
        $this->MultiCell(0, 0, 'Tampering of any part of this document will invalidate its contents. Falsification of this document is a serious offense.', 0, 'J', false, 0, $this->mlr, $this->GetPageHeight() - 0.35);

        //QR CODE IMAGE
		if (!empty($this->qrImage)) {
            $this->Image($this->qrImage, $this->getPageWidth() - 0.8, $currY + 0.3, 0.5, 0.5, 'PNG');
        }

        $this->Watermark();
    }


}

class M_medical_certificate_wm_8 extends CI_Model {


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
        $pdf->SetTitle('Medical Certificate');
        $pdf->SetSubject('Medical Certificate');
        $pdf->SetKeywords('Medical Certificate');

        $pdf->setPrintHeader(true);
        $pdf->setPrintFooter(true);
        $pdf->SetHeaderMargin(0);
        $pdf->SetFooterMargin(0);


        $mlr = 0.3;
        $mt = 0.2;

        $pdf->mlr = $mlr;
        $pdf->mt = $mt;

        $mt = 3.2;

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

        $pdf->patient = $this->db->query("SELECT p.ID,concat(p.FIRSTNAME,' ',p.MIDDLENAME,' ',p.LASTNAME) as NAME, p.SEX,p.OCCUPATION,p.STREETNO,p.CITY,p.PROVINCE, 
                mr.AGE, mr.DIAGNOSIS, mr.CHECKUPDATE, mr.REMARKS,.mr.CONFINEMENT_DATE_FROM, mr.CONFINEMENT_DATE_TO, mr.CONSULTATIONDATES
            FROM patients p 
            INNER JOIN medicalrecords mr ON mr.PATIENTID = p.ID 
            WHERE mr.ID = ? 
            LIMIT 1",array($id))->row();
        
        //QR CODE GENERATION
		$pdfPath = '/temp_files_pdf/Certificate_'.$id.'_.pdf';
        $qrData = base_url(trim($pdfPath,'/'));
        $barcodeobj = new TCPDF2DBarcode($qrData, 'QRCODE,H');
        $barcodeImage = $barcodeobj->getBarcodePngData(4, 4, [0,0,0]);
        $tmpFile = tempnam(sys_get_temp_dir(), 'qr_') . '.png';
        file_put_contents($tmpFile, $barcodeImage);
        $pdf->qrImage = $tmpFile;

        $patient = $pdf->patient;
        
        if( $patient ){
            
            $pdf->AddPage();
            $pdf->SetFont('Arial','',10);
            $pdf->SetY(3.2);

            $pdf->Cell(1.6, 0,"Period of Confinement : ");
            $pdf->Cell(0.9, 0, $patient->CONFINEMENT_DATE_FROM != NULL ? date('m/d/Y',strtotime($patient->CONFINEMENT_DATE_FROM)) : '','B',0);
            $pdf->Cell(0.3, 0," To ");
            $pdf->Cell(0.9, 0, $patient->CONFINEMENT_DATE_TO != NULL ? date('m/d/Y',strtotime($patient->CONFINEMENT_DATE_TO)) : '','B',1);
            $pdf->Ln(0.03);
            
            $pdf->Cell(1.35, 0,'Date of Consultation: ');
            
            $pdf->SetFont('Arial','U',10);
            $pdf->MultiCell(0, 0, $patient->CONSULTATIONDATES, 0, 'L', false, 1);
            
            $pdf->SetY(3.6);
            $pdf->SetFont('Arial','',10);
            $pdf->Cell(0, 0,'Diagnosis:',0,1);

            $currentY = $pdf->GetY();
            $pdf->MultiCell( 0, 0,$patient->DIAGNOSIS,0,'L');
            
            $pdf->SetY($currentY);
            $pdf->Cell(0,0,'','B',1);
            $pdf->Cell(0,0,'','B',1);
            $pdf->Cell(0,0,'','B',1);
            $pdf->Cell(0,0,'','B',1);
            $pdf->Cell(0,0,'','B',1);
            $pdf->Cell(0,0,'','B',1);
            $pdf->Cell(0,0,'','B',1);
            $pdf->Cell(0,0,'','B',1);
            $pdf->Ln(0.2);

            $currentY = $pdf->GetY();	
            $pdf->Cell(0.7, 0,'Remarks: ',0,1); 
            
            $pdf->SetY($currentY);
            $pdf->MultiCell(0, 0, str_repeat(' ', 17).$patient->REMARKS,0,'L');

            $pdf->SetY($currentY);
            $pdf->Cell(0.7, 0,'',0,0);
            $pdf->Cell(0, 0,'','B',1);
            $pdf->Cell(0, 0,'','B',1);
            $pdf->Cell(0, 0,'','B',1);
            $pdf->Cell(0, 0,'','B',1);
            $pdf->Cell(0, 0,'','B',1);
            $pdf->Ln(0.2);


            $pdf->SetFont('Arial','',10);
            $pdf->MultiCell(0, 0,"This medical certificate is issued per patient's request but it is not valid for any medico-legal purposes.",0, 'L');
            $pdf->Ln(0.5);
        }
	
		
        ob_end_clean();
        
        $pdfPath = '/temp_files_pdf/Certificate_'.$id.'_.pdf';
        $pdf->Output( realpath(''). $pdfPath, 'F');
		
        //QR CODE CLEANUP
        if(file_exists($tmpFile)) unlink($tmpFile);
		

		if( $return ){
			return base_url($pdfPath);
		}
		else{
			echo base_url($pdfPath);
		}

	}

}

?>
