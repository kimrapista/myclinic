<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class M_medical_prescription_4 extends CI_Model
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

		$margL=0.4;
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

      $pdf->Image('uploads/reports/clinic'.$clinic->ID.'_1.png',$pdf->GetX(),$pdf->GetY(),0.6);

		$pdf->SetFont('Arial','B',13);
		$pdf->Cell(0,$CH,utf8_decode($clinic->CLINICNAME),0,1,'C');

		if( ! empty($clinic->CLINICSUBNAME) ){
			$pdf->SetFont('Arial','B',10);
			$pdf->Cell(0,$CH,utf8_decode($clinic->CLINICSUBNAME),0,1,'C');
		}

		if( ! empty($clinic->CLINICSUBNAME1) ){
			$pdf->SetFont('Arial','B',10);
			$pdf->Cell(0,$CH,utf8_decode($clinic->CLINICSUBNAME1),0,1,'C');
		}

		$pdf->SetFont('Arial','',9);
		$pdf->MultiCell(0,$CH - 0.05,$clinic->ADDRESS,0,'C');
		$pdf->Ln(0.2);

		$pdf->SetFont('Arial','',9);

		$pdf->Cell(0,$CH,$clinic->EMAIL,'B',1,'R');
		$pdf->Ln(0.01);
		$pdf->Cell(0,0,'','T',1,'R');	

		$pdf->Ln(0.14);

		$CH=0.12;
		$pdf->SetFont('Arial','',9);
		$pdf->Cell(0.5,$CH,"Name: ",0);
		$pdf->Cell(2.5,$CH,utf8_decode($patient->NAME),'B',0,'C');
		$pdf->Cell(0.3,$CH,"Age:",0);
		$pdf->Cell(0.2,$CH,$patient->AGE,'B',0,'C');

		$pdf->Cell(0.5,$CH," Gender:",0);
		$pdf->Cell(0,$CH,$patient->SEX,'B',1,'C');
		$pdf->Ln(0.05);

		$pdf->Cell(0.5,$CH,"Address:",0);
		$pdf->Cell(2.5,$CH,utf8_decode($patient->ADDRESS),'B',0);

		$pdf->Cell(0.5,$CH,"Date:",0,0,'C');
		$pdf->Cell(0,$CH,date('m/d/Y',time()),'B',1,'C');
		$pdf->Ln(0.15);

		$pdf->Image('assets/css/images/rx.png',$pdf->GetX(),$pdf->GetY(),0.4);

		$pdf->SetFillColor(220,220,220);

		$sql1 = $this->db->query("SELECT m.NAME, mrm.FREQUENCY, mrm.INSTRUCTION , mrm.QUANTITY
			FROM mr_medicines mrm 
			INNER JOIN medicines m ON m.ID = mrm.MEDICINEID
			WHERE mrm.MEDICALRECORDID = ? and mrm.CANCELLED='N' ",array($id));
		$data = $sql1->result();

		if( $data ){

			$pdf->SetFont('Arial','',10);

			foreach ($data as $key => $v) {
				$pdf->Cell(0.5,$CH,'');
				$pdf->Cell(0,$CH,utf8_decode( ($key + 1).'.  '.$v->NAME.'  '.($v->QUANTITY > 0 ? ' #'.$v->QUANTITY: '')),0,1);	
				$pdf->Ln(0.03);
				$pdf->Cell(0.8,$CH,'');
				$pdf->MultiCell(0,$CH,utf8_decode($v->INSTRUCTION ."   ".$v->FREQUENCY));	
				$pdf->Ln(0.1);
			}
		}


		$CH=0.15;

		$currentX = $pdf->getX();
		$currentY = $pdf->getPageHeight()-1;

		$pdf->SetFont('Arial','',9);
		$pdf->SetXY($margL+0,$currentY);

		if($patient->APPOINTMENT == 'N' || $patient->APPOINTMENT == NULL){
			$pdf->MultiCell(2,$CH,"Your next appointment will :\n\r\n\r",'B');

		} else{
			$pdf->MultiCell(2,$CH,"Your next appointment will : ".date('F d, Y',strtotime($patient->APPOINTMENTDATE)),'B',1);
		}

		$pdf->Ln(0.05);
		$pdf->MultiCell(2,$CH,"Tel No.:".$clinic->CONTACTNO."\r\nMobile No.:".$clinic->MOBILENO,0,1);

		$pdf->SetFont('Arial','B',9);
		$pdf->SetXY($margL+2.5,$currentY);

		$q = $this->db->query("SELECT U.NAME, U.PTR, U.LICENSENO, U.S2NO
			FROM users U
			INNER JOIN medicalrecords MR ON MR.CREATEDBY = U.ID
			WHERE MR.ID = ?   
			LIMIT 1",array($id))->row();


		if( $q ){

			$pdf->MultiCell(3,$CH,utf8_decode($q->NAME)."\n\rLicense No :  ".$q->LICENSENO."\n\rPTR No :  ".$q->PTR."\n\rS2 No :  ".$q->S2NO,0);

		}

		// $pdf->Output('I','Medical Prescription '.utf8_decode($patient->NAME).'.pdf');
		$pdfPath = 'temp_files_pdf/Prescription_'.$id.'_.pdf';
		$pdf->Output('F', $pdfPath);
		// echo base_url($pdfPath);

		if( $return ){
			return base_url($pdfPath);
		}
		else{
			echo base_url($pdfPath);
		}

	}

}

?>