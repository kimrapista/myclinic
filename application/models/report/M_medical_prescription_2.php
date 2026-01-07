<?php
defined('BASEPATH') OR exit('No direct script access allowed');


class M_medical_prescription_2 extends CI_Model
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
		$CH = 0.18;
		$CW=0.8;

		$clinic = $this->clinicInfo();

		$sql = $this->db->query("SELECT p.ID, concat(p.FIRSTNAME,' ',p.MIDDLENAME,' ',p.LASTNAME) as NAME, concat(p.STREETNO) as ADDRESS,concat(p.CITY,' ',p.PROVINCE) as ADDRESS1, p.SEX , mr.APPOINTMENT, mr.APPOINTMENTDATE, mr.AGE,c.MOBILENO
			FROM patients p
			INNER JOIN medicalrecords mr ON mr.PATIENTID = p.ID
			INNER JOIN clinics c ON c.ID = mr.CLINICID
			WHERE mr.ID = ?   
			LIMIT 1",array($id));

		$patient = $sql->row();


		$pdf->SetFont('Arial','B',15);
		$pdf->Cell(0,$CH,'BERNARD P. ANTOLIN, M.D.',0,1,'C');

		$pdf->SetFont('Arial','',10);
		$pdf->Cell(0,$CH,'ORTHOPAEDIC SURGEON',0,1,'C');
		$pdf->Cell(0,$CH,'HAND SURGEON',0,1,'C');

		$pdf->SetFont('Arial','B',9);
		$pdf->Cell(0,$CH,'DIPLOMATE, PHIL. BOARD OF ORTHOPAEDICS',0,1,'C');
		$pdf->Cell(0,$CH,'FELLOW, PHIL. ORTHOPAEDIC ASSOCIATION',0,1,'C');

		$pdf->SetFont('Arial','',9);
		$pdf->MultiCell(0,$CH,'CLINIC: Maria Reyna-Xavier University Hospital,Room #63,3rd Floor, New building. Tel #: (088) 882 - 0818');
		$pdf->Cell(0,$CH,'CLINIC SCHEDULE: Mon - Friday (10am - 3pm)',0,1);
		$pdf->Ln(0.05);

		$pdf->Image('assets/css/images/rx.png',$margL,$pdf->GetY(),0.3);

		$pdf->Ln(0.4);

		$CH = 0.2;

		$pdf->SetFont('Arial','',8);
		$pdf->Cell(0.6,$CH,"NAME: ",0);
		$pdf->Cell(2.5,$CH,utf8_decode($patient->NAME),'B',0,'L');
		$pdf->Cell(0.4,$CH,"DATE:",0);
		$pdf->Cell(0,$CH,date('m/d/Y',time()),'B',1,'L');

		// $pdf->Ln(0.05);

		$pdf->Cell(0.6,$CH,"AGE/SEX :",0);
		$pdf->Cell(2.5,$CH,utf8_decode($patient->AGE.' / '.$patient->SEX),'B',1);
		$pdf->Ln(0.15);


		$sql1 = $this->db->query("SELECT m.NAME, mrm.FREQUENCY, mrm.INSTRUCTION , mrm.QUANTITY
			FROM mr_medicines mrm 
			INNER JOIN medicines m ON m.ID = mrm.MEDICINEID
			WHERE mrm.MEDICALRECORDID = ? and mrm.CANCELLED='N' ",array($id));
		$data = $sql1->result();

		if( $data ){

			$pdf->SetFont('Arial','',10);

			foreach ($data as $key => $v) {
				$pdf->Cell(0.2,$CH,'');
				$pdf->Cell(0,$CH,utf8_decode( ($key + 1).'. '.$v->NAME.'   '.($v->QUANTITY > 0 ? '   #'.$v->QUANTITY: '')),0,1);	
				$pdf->Cell(0.4,$CH,'');
				$pdf->MultiCell(0,$CH,utf8_decode($v->INSTRUCTION .'   '.$v->FREQUENCY));	
				$pdf->Ln(0.2);
			}
		}


		$q = $this->db->query("SELECT U.NAME, U.PTR, U.LICENSENO, U.S2NO
			FROM users U
			INNER JOIN medicalrecords MR ON MR.CREATEDBY = U.ID
			WHERE MR.ID = ?   
			LIMIT 1",array($id))->row();

		if( $q ) {

			$CH = 0.15;

			$pdf->SetFont('Arial','',9);

			$pdf->SetXY(3,$pdf->getPageHeight()-1.1);
			$pdf->MultiCell(0,$CH,utf8_decode($q->NAME),0,1);

			$pdf->SetXY(3, $pdf->GetY() + .02);
			$pdf->SetFont('Arial','',9);

			$pdf->MultiCell(0.5,$CH,'LIC. # ');
			$pdf->SetXY(3.45, $pdf->GetY() - $CH);
			$pdf->MultiCell(0,$CH,$q->LICENSENO,0,1);

			$pdf->SetXY(3, $pdf->GetY() + .02);

			$pdf->MultiCell(0.5,$CH,'PTR # ');
			$pdf->SetXY(3.45, $pdf->GetY() - $CH);
			$pdf->MultiCell(0,$CH,$q->PTR,0,1);

		}

		$CH = 0.2;

		$pdf->SetFont('Arial','',9);
		$pdf->Cell(1,$CH,"Next follow-Up:");
		$pdf->Cell(1.1,$CH, $patient->APPOINTMENT == 'Y' ? date('m/d/Y',strtotime($patient->APPOINTMENTDATE)) : '' ,'B',1);

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