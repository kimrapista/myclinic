<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class M_medical_prescription_16 extends CI_Model
{
	
	function __construct(){ $this->load->database(); }


	private function clinicInfo(){

		$sql = $this->db->query("SELECT * FROM clinics where ID=? LIMIT 1",array($this->session->CLINICID));
		return $sql->row();

	}


	public function Index($id, $return = FALSE){

		$this->load->library('pdf');
		$pageSize=array(5.25,8.20);

		$this->pdf->__construct('P','in',$pageSize);
		$pdf = $this->pdf;

		$margL=0.25;
		$margT=0.3;
		$pdf->SetMargins($margL,$margT,$margL);
		$pdf->SetAutoPageBreak(true);
		$pdf->AddPage();

		$pageNo=0;
		$CH=0.2;
		$CW=0.8;

		$clinic = $this->clinicInfo();

		$sql = $this->db->query("SELECT p.ID, concat(p.FIRSTNAME,' ',p.MIDDLENAME,' ',p.LASTNAME) as NAME, concat(p.STREETNO) as ADDRESS,concat(p.CITY,' ',p.PROVINCE) as ADDRESS1, p.SEX , mr.APPOINTMENT, mr.APPOINTMENTDATE, mr.AGE,c.MOBILENO
			FROM patients p
			INNER JOIN medicalrecords mr ON mr.PATIENTID = p.ID
			INNER JOIN clinics c ON c.ID = mr.CLINICID
			WHERE mr.ID = ?   
			LIMIT 1",array($id));

		$patient = $sql->row();

		
		$pdf->SetFont('times','B',12);
		$pdf->Cell(0,$CH,utf8_decode($clinic->CLINICNAME),0,1,'C');

        $pdf->SetFont('times','B',11);
		$pdf->Cell(0,$CH,utf8_decode($clinic->CLINICSUBNAME).'/'.utf8_decode($clinic->CLINICSUBNAME1),0,1,'C');

        $pdf->SetFont('times','B',9);
		$pdf->Cell(0,$CH, 'Adult Diseases, Hypertension, Dialysis, Kidney Transplant',0,1,'C');
        $pdf->Ln(0.05);
        

        $pdf->SetFont('Arial','B',7);
        $pdf->Cell(2.6, 0.15,"CDO POLYMEDIC MEDICAL PLAZA");
        $pdf->Cell(0, 0.15,"CDO POLYMEDIC GENERAL HOSPITAL",0,1);

        $currY = $pdf->GetY();
        $pdf->SetFont('Arial','',7);

        $pdf->MultiCell(2.5, 0.15,"NATIONAL HIGHWAY KAUSWAGAN, CDOC\r\nCLINIC HRS: 9:00 AM - 1:00PM MON TO SAT\r\n2nd FLR. ROOM 241 Tel. #858-5842 local 236");
        
        $pdf->SetXY( $margL + 2.6, $currY );
        $pdf->MultiCell(2.5, 0.15,"DON Apolinar Velez St. CDOC\r\nCLINIC HRS: 2:00PM - 4:00PM M.T.W.TH.\r\n2nd FLR. Room 219 Te;. # 856-7232 local 1219");
        $pdf->Ln(0.1);

		$pdf->Cell(0,0,'','T',1);	

        $pdf->Image('assets/css/images/rx.png',$pdf->GetX(),$pdf->GetY() + 0.1, 0.3);

        $pdf->Ln(0.25);
                

		$CH=0.12;
		$pdf->SetFont('Arial','',10);
		$pdf->Cell(0.9,$CH,"Name: ",0, 0 ,'R');
		$pdf->Cell(2.7,$CH,utf8_decode($patient->NAME),'B',0,'L');
		$pdf->Cell(0.35,$CH,"Age:",0);
		$pdf->Cell(0,$CH,$patient->AGE,'B',1,'C');
		$pdf->Ln(0.05);

		$pdf->Cell(1,$CH,"Address:",0, 0,'R');
		$pdf->Cell(2.6,$CH,utf8_decode($patient->ADDRESS).' '.utf8_decode($patient->ADDRESS1),'B',0);

		$pdf->Cell(0.35,$CH,"Date:",0,0);
		$pdf->Cell(0,$CH,date('m/d/Y',time()),'B',1,'C');
		$pdf->Ln(0.2);

		

		$pdf->SetFillColor(220,220,220);

		$sql1 = $this->db->query("SELECT m.NAME, mrm.FREQUENCY, mrm.INSTRUCTION , mrm.QUANTITY
			FROM mr_medicines mrm 
			INNER JOIN medicines m ON m.ID = mrm.MEDICINEID
			WHERE mrm.MEDICALRECORDID = ? and mrm.CANCELLED='N' ",array($id));
		$data = $sql1->result();

		if( $data ){

			$pdf->SetFont('Arial','',11);

			foreach ($data as $key => $v) {
				$pdf->Cell(0.4,$CH,'');
				$pdf->Cell(0,$CH,utf8_decode( ($key + 1).'. '.$v->NAME.' '.($v->QUANTITY > 0 ? '#'.$v->QUANTITY: '')),0,1);	
				$pdf->Ln(0.05);
				$pdf->Cell(0.8,$CH,'');
				$pdf->MultiCell(0,$CH,utf8_decode($v->INSTRUCTION .' '.$v->FREQUENCY));	
				$pdf->Ln(0.1);
			}
		}


		$CH=0.15;

		$currentX = $pdf->getX();
		$currentY = $pdf->getPageHeight();

		$pdf->SetFont('Arial','',9);
		$pdf->SetXY($margL+0, $currentY - 1.05);

        $pdf->Cell(0.8,$CH,"Follow-up on:");

		if($patient->APPOINTMENT == 'N' || $patient->APPOINTMENT == NULL){
			$pdf->Cell(1,$CH,"",'B',1);
		} 
        else{
            $pdf->Cell(1,$CH, date('m/d/Y',strtotime($patient->APPOINTMENTDATE)),'B',1, 'C');
		}
		
		$pdf->Ln(0.45);
		$pdf->Cell(1,$CH,"For appointment pls contact: ANN 09177065872",0,1);

		
		$pdf->SetFont('Arial','', 9);
		$pdf->SetXY($margL + 2, $currentY - 1);

		$q = $this->db->query("SELECT U.NAME, U.PTR, U.LICENSENO, U.S2NO
			FROM users U
			INNER JOIN medicalrecords MR ON MR.CREATEDBY = U.ID
			WHERE MR.ID = ?   
			LIMIT 1",array($id))->row();

		if( $q ){
			$pdf->MultiCell(0,$CH,utf8_decode($q->NAME)."\n\rLicense No :  ".$q->LICENSENO."\n\rPTR No :  ".$q->PTR,'T', 'L');
		}

		$pdfPath = 'temp_files_pdf/Prescription_default_'.$id.'_.pdf';
		$pdf->Output('F', $pdfPath);

		if( $return ){
			return base_url($pdfPath);
		}
		else{
			echo base_url($pdfPath);
		}


	}

}

?>