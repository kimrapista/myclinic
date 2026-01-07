<?php
defined('BASEPATH') OR exit('No direct script access allowed');


class M_medical_certificate_4 extends CI_Model
{
	
	function __construct(){ $this->load->database(); }


	private function clinicInfo(){

		$sql = $this->db->query("SELECT * FROM clinics where ID=? LIMIT 1",array($this->session->CLINICID));
		return $sql->row();

	}



	public function Index($id, $return = FALSE){

		$this->load->library('pdf');
		$pageSize=array(5.15,8.20);

		$this->pdf->__construct('P','in',$pageSize);
		$pdf = $this->pdf;

		$margL=0.3;
		$margT=0.3;
		$pdf->SetMargins($margL,$margT,$margL);
		$pdf->SetAutoPageBreak(false);

		$pageNo=0;
		$CH=0.2;
		$CW=0.8;

		$clinic = $this->clinicInfo();

		$sql = $this->db->query("SELECT p.ID,concat(p.FIRSTNAME,' ',p.MIDDLENAME,' ',p.LASTNAME) as NAME, p.SEX,p.OCCUPATION,p.STREETNO,p.CITY,p.PROVINCE, 
			mr.AGE, mr.DIAGNOSIS,mr.CONFINEMENT_DATE_FROM,mr.CONFINEMENT_DATE_TO,mr.PROCEDURE_DONE,mr.ESTIMATED_HEAL_PERIOD, mr.CHECKUPDATE, mr.REMARKS, mr.CONSULTATIONDATES
			FROM patients p 
			INNER JOIN medicalrecords mr ON mr.PATIENTID = p.ID 
			WHERE mr.ID = ? 
			LIMIT 1",array($id));

		$patient = $sql->row();

		$pdf->AddPage();

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


		$pdf->Ln(0.4);
		$pdf->SetFont('Arial','B',13);
		$pdf->Cell(0,$CH,'MEDICAL CERTIFICATE',0,1,'C');
		$pdf->Ln(0.1);

		$CH=0.22;

		$pdf->SetFont('Arial','',10);
		$pdf->Cell( $pdf->GetPageWidth() - ($margL*2) - 1.2 ,$CH,'Date: ',0,0,'R');
		$pdf->Cell(1.2,$CH,date('m/d/Y',time()),'B',1,'C');

		$pdf->Cell(0,$CH,'To Whom It May Concern: ',0,1);
		$pdf->Ln(0.05);
		$pdf->MultiCell(0,$CH,'This is to certify that the person named hereunder has the following record of confinement/ consultation and treatment',0);
		$pdf->Ln(0.05);

		$pdf->Cell( ($pdf->GetPageWidth()/1.6) - ($margL), $CH,'Name: '.utf8_decode($patient->NAME),1,0);
		$pdf->Cell( ($pdf->GetPageWidth()/2.67) - ($margL), $CH,'Age/Sex: '.$patient->AGE.' / '.$patient->SEX,1,1);

		$pdf->Cell( 0, $CH,'Address: '.utf8_decode($patient->STREETNO),1,1);
		$pdf->Ln(0.05);

		$pdf->Cell(1.6,$CH,"Period of Confinement : ",0,0);
		$pdf->Cell(0.9,$CH,date('m/d/Y',strtotime($patient->CHECKUPDATE)),'B',0);
		$pdf->Cell(0.3,$CH," To ",0,0);
		$pdf->Cell(0.9,$CH,date('m/d/Y',strtotime($patient->CHECKUPDATE)),'B',1);

		$pdf->Cell(0.9,$CH, $patient->CONFINEMENT_DATE_FROM != NULL ? date('m/d/Y',strtotime($patient->CONFINEMENT_DATE_FROM)) : '','B',0);
		$pdf->Cell(0.3,$CH," To ",0,0);
		$pdf->Cell(0.9,$CH, $patient->CONFINEMENT_DATE_TO != NULL ? date('m/d/Y',strtotime($patient->CONFINEMENT_DATE_TO)) : '','B',1);

	
		$currentY = $pdf->GetY();

		$pdf->Cell(1.3,$CH,'Date of Consultation: ',0,0);
		$pdf->SetFont('Arial','U',10);
		$pdf->MultiCell( 0, $CH,  $patient->CONSULTATIONDATES,0,'J');

		$pdf->SetFont('Arial','',10);

		$pdf->SetXY($margL,$currentY + $CH);
		$pdf->Cell(0,$CH,'Diagnosis(es)',0,1);

		$currentY = $pdf->GetY();
		$pdf->MultiCell( 0, $CH,utf8_decode($patient->DIAGNOSIS),0,'L');
		
		$pdf->SetXY($margL,$currentY);
		$pdf->Cell(0,$CH,'','B',1);
		$pdf->Cell(0,$CH,'','B',1);
		$pdf->Cell(0,$CH,'','B',1);
		$pdf->Ln(0.05);
		
		$pdf->Cell(0,$CH,'The following procedure(s) was/were done: ',0,1); 

		$currentY = $pdf->GetY();		
		$pdf->MultiCell( 0, $CH,utf8_decode($patient->PROCEDURE_DONE),0,'L');

		$pdf->SetXY($margL,$currentY);
		$pdf->Cell(0,$CH,'','B',1);
		$pdf->Cell(0,$CH,'','B',1);
		$pdf->Cell(0,$CH,'','B',1);
		$pdf->Ln(0.05);

		$pdf->Cell(2.2,$CH,'Healing period is estimated to last ',0,0); 
		$pdf->Cell(0.6,$CH,utf8_decode($patient->ESTIMATED_HEAL_PERIOD),'B',0,'C');	
		$pdf->Cell(2.2,$CH,' Days ',0,1);	
		$pdf->Ln(0.05);

		$currentY = $pdf->GetY();		
		$pdf->Cell(0.7,$CH,'Remarks: ',0,1); 
		
		$pdf->SetXY($margL,$currentY);
		$pdf->MultiCell(0, $CH,''.utf8_decode($patient->REMARKS),0,'L');

		$pdf->SetXY($margL,$currentY);
		$pdf->Cell(0.7,$CH,'',0,0);
		$pdf->Cell(0,$CH,'','B',1);
		$pdf->Cell(0,$CH,'','B',1);
		$pdf->Cell(0,$CH,'','B',1);
		$pdf->Ln(0.05);

		$pdf->SetFont('Arial','',10);
		$pdf->MultiCell(0,0.2,"This certificate is issued upon the request of the patient for whatever purpose it may serve best.",0);
		$pdf->Ln($CH);

		$pdf->Cell(0,$CH,'Sincerely yours,',0,1);
		$pdf->Ln($CH);


		$q = $this->db->query("SELECT U.NAME, U.PTR, U.LICENSENO, U.S2NO
			FROM users U
			INNER JOIN medicalrecords MR ON MR.CREATEDBY = U.ID
			WHERE MR.ID = ?   
			LIMIT 1",array($id))->row();


		if( $q ){

			$CH = 0.18;

			$pdf->SetFont('Arial','B',10);
			$pdf->Cell(0,$CH,utf8_decode($q->NAME),0,1);
			$pdf->SetFont('Arial','',10);
			$pdf->Cell(1.1,$CH,'License Number: ',0,0);
			$pdf->Cell(1,$CH,$q->LICENSENO,0,1);
			$pdf->Cell(0.9,$CH,'PTR Number: ',0,0);
			$pdf->Cell(1,$CH,$q->PTR,0,1);
			$pdf->Cell(0.8,$CH,'S2 Number: ',0,0); 
			$pdf->Cell(0,$CH,$q->S2NO,0,1);

		}
		
		// $pdf->Output('I','Medical Certificate '.utf8_decode($patient->NAME).'.pdf');
		$pdfPath = 'temp_files_pdf/Certificate_'.$id.'_.pdf';
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
