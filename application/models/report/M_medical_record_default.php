<?php
defined('BASEPATH') OR exit('No direct script access allowed');


class M_medical_record_default extends CI_Model
{
	
	function __construct(){ $this->load->database(); }


	private function clinicInfo(){

		$sql = $this->db->query("SELECT * FROM clinics where ID=? LIMIT 1",array($this->session->CLINICID));
		return $sql->row();

	}


	// ----------------- Dr khu ---------------
	public function Index($id){

		$this->load->library('pdf');


		$this->pdf->__construct('P','in','A4');
		$pdf = $this->pdf;

		$margL=0.4;
		$margT=0.3;
		$pdf->SetMargins($margL,$margT,$margL);
		$pdf->SetAutoPageBreak(false);

		$pdf->AddPage();

		$pageNo=0;

		$CH=0.2;
		$CW=0.8;

		$pdf->SetTextColor(100,100,100);
		$pdf->SetFillColor(240,240,240);
		$pdf->SetDrawColor(210,210,210);
		
		$clinic = $this->clinicInfo();


		if( file_exists('uploads/reports/clinic'.$clinic->ID.'_1.png') ){

			$pdf->Image('uploads/reports/clinic'.$clinic->ID.'_1.png', $margL, $margT, 0.45);	
			$pdf->SetXY($margL + 0.5, $margT );
		}

		$pdf->SetFont('Arial','',9);
		$pdf->MultiCell(0,0.15,"$clinic->CLINICNAME \r\n$clinic->CLINICSUBNAME". (!empty($clinic->CLINICSUBNAME1) ? "\r\n$clinic->CLINICSUBNAME1" : "")."\r\nTel No. $clinic->CONTACTNO $clinic->MOBILENO");
		// $pdf->SetTextColor(50,50,50);

		$pdf->SetFont('Arial','B',14);
		$pdf->SetXY($margL,$margT);
		$pdf->MultiCell(0,$CH,"Medical Record Report\r\nNo. $id",0,'R');

		$pdf->Ln(0.25);

		$sql = $this->db->query("SELECT p.ID, concat(p.FIRSTNAME,' ',p.MIDDLENAME,' ',p.LASTNAME) as NAME, p.DOB, p.SEX, p.CIVILSTATUS, p.NATIONALITY, p.RELIGION, p.OCCUPATION, p.PHONENO, p.MOBILENO, concat(p.STREETNO,' ',p.CITY,' ',p.PROVINCE) as ADDRESS , 
			mr.*, S.NAME AS FROMCLINIC
			FROM patients p
			INNER JOIN medicalrecords mr ON mr.PATIENTID = p.ID
			INNER JOIN clinics c ON c.ID = mr.CLINICID
			LEFT JOIN subclinic S ON S.ID = mr.SUBCLINICID
			WHERE mr.ID = ?   
			LIMIT 1",array($id));

		$patient = $sql->row();


		$pdf->SetFont('Arial','B',10);
		$pdf->Cell(0,$CH*1.5,'PATIENT INFORMATION',1,1,'L',true);

		$pdf->SetFont('Arial','',9);
		$pdf->Cell(1,$CH,'Name',1,0,'L',true);
		$pdf->Cell(2.5,$CH,utf8_decode($patient->NAME),1,0);
		$pdf->Cell(1.5,$CH,'Date Printed',1,0,'C',true);
		$pdf->Cell(0,$CH,date('m/d/Y h:i A',time()),1,1,'C');

		$pdf->Cell(1,$CH,'Date of Birth',1,0,'L',true);
		$pdf->Cell(1,$CH,date('m/d/Y',strtotime($patient->DOB)),1,0);
		$pdf->Cell(0.5,$CH,'Sex',1,0,'C',true);
		// $pdf->Cell(1,$CH,$patient->SEX,1,0,'C');
		$pdf->Cell(1.5,$CH,'Nationality / Religion',1,0,'C',true);
		$pdf->Cell(0,$CH,utf8_decode($patient->NATIONALITY.' '.$patient->RELIGION),1,1,'C');

		$pdf->Cell(1,$CH,'Civil Status',1,0,'L',true);
		$pdf->Cell(2.5,$CH,$patient->CIVILSTATUS,1,0);
		$pdf->Cell(1.5,$CH,'Tel / Mobile No.',1,0,'C',true);
		$pdf->Cell(0,$CH,$patient->PHONENO.' '.$patient->MOBILENO,1,1,'C');

		$pdf->Cell(1,$CH,'Address',1,0,'L',true);
		$pdf->Cell(0,$CH,utf8_decode($patient->ADDRESS),1,1);

		$pdf->SetFont('Arial','B',10);
		$pdf->Cell(0,$CH*1.5,'MEDICAL INFORMATION',1,1,'L',true);

		$pdf->SetFont('Arial','',9);

		$pdf->Cell(1,$CH,'Checkup Date',1,0,'L',true);
		$pdf->Cell(1,$CH,date('m/d/Y',strtotime($patient->CHECKUPDATE)),1,0);
		$pdf->Cell(0.5,$CH,'Age',1,0,'C',true);
		$pdf->Cell(1,$CH,$patient->AGE,1,0,'C');
		$pdf->Cell(1.5,$CH,'Clinic location',1,0,'C',true);
		$pdf->Cell(0,$CH,utf8_decode($patient->FROMCLINIC),1,1,'C');

		$pdf->MultiCell(0,$CH + 0.05,'Chief Complaint',1,'L',TRUE);
		$pdf->MultiCell(0,$CH,utf8_decode($patient->CHEIFCOMPLAINT),0,'L');
		$pdf->Ln();

		$pdf->MultiCell(0,$CH + 0.05,'History Present Illness',1,'L',TRUE);
		$pdf->MultiCell(0,$CH,utf8_decode($patient->PRESENTILLNESS),0,'L');
		$pdf->Ln();

		$pdf->MultiCell(0,$CH + 0.05,'CO-Morbidities',1,'L',TRUE);
		$pdf->MultiCell(0,$CH,utf8_decode($patient->COMORBIDITIES),0,'L');
		$pdf->Ln();

		$pdf->MultiCell(0,$CH + 0.05,'Vital Signs',1,'L',TRUE);
		$pdf->Cell(0.5,$CH,'BP',1,0,'C',true);
		$pdf->Cell(0.7,$CH,$patient->BP_SYSTOLIC.'/'.$patient->BP_DIASTOLIC,1,0,'C');
		$pdf->Cell(0.5,$CH,'HR',1,0,'C',true);
		$pdf->Cell(0.5,$CH,$patient->HEART_RATE,1,0,'C');
		$pdf->Cell(0.5,$CH,'RR',1,0,'C',true);
		$pdf->Cell(0.5,$CH,$patient->RESPIRATORY,1,0,'C');
		$pdf->Cell(0.5,$CH,'Temp.',1,0,'C',true);
		$pdf->Cell(0.5,$CH,$patient->TEMPERATURE,1,0,'C');
		$pdf->Cell(0.5,$CH,'Weight',1,0,'C',true);
		$pdf->Cell(0.5,$CH,$patient->WEIGHT,1,0,'C');
		$pdf->Cell(0.5,$CH,'Height',1,0,'C',true);
		$pdf->Cell(0.5,$CH,$patient->HEIGHT,1,0,'C');
		$pdf->Cell(0,$CH,'',1,1,'C',true);
		$pdf->Ln();

		$pdf->MultiCell(0,$CH + 0.05,'Findings',1,'L',TRUE);
		$pdf->MultiCell(0,$CH,utf8_decode($patient->FINDINGS),0,'L');
		$pdf->Ln();

		$pdf->MultiCell(0,$CH + 0.05,'Diagnosis',1,'L',TRUE);
		$pdf->MultiCell(0,$CH,utf8_decode($patient->DIAGNOSIS),0,'L');
		$pdf->Ln();

		$pdf->MultiCell(0,$CH + 0.05,'Procedure/s Done',1,'L',TRUE);
		$pdf->MultiCell(0,$CH,utf8_decode($patient->PROCEDURE_DONE),0,'L');
		$pdf->Ln();


		$meds1 = '';

		$pres = $this->db->query("SELECT m.NAME, mrm.FREQUENCY, mrm.INSTRUCTION , mrm.QUANTITY
			FROM mr_medicines mrm 
			INNER JOIN medicines m ON m.ID = mrm.MEDICINEID
			WHERE mrm.MEDICALRECORDID = ? and mrm.CANCELLED='N' ",array($id))->result();

		foreach ($pres as $key => $v) {
			$meds1 .= $v->NAME.($v->QUANTITY == 0 ? '' : ' '.$v->QUANTITY).' '.$v->INSTRUCTION.( empty($v->FREQUENCY) ? '' : $v->FREQUENCY )."\r\n";
		}
		
		$pdf->MultiCell(0,$CH + 0.05,'Medication',1,'L',TRUE);
		
		if( !empty($meds1) )
			$pdf->MultiCell(0,$CH,utf8_decode($meds1),0,'L');

		$pdf->MultiCell(0,$CH,utf8_decode($patient->MEDICATION),0,'L');
		$pdf->Ln();
		


		$q = $this->db->query("SELECT U.NAME, U.PTR, U.LICENSENO, U.S2NO
			FROM users U
			INNER JOIN medicalrecords MR ON MR.CREATEDBY = U.ID
			WHERE MR.ID = ?   
			LIMIT 1",array($id))->row();

		$pdf->SetFont('Arial','B',8);

		if( $q ){

			$pdf->SetDrawColor(50,50,50);

			$pdf->SetXY($margL,$pdf->getPageHeight()-1);

			$pdf->SetFont('Arial','',9);
			$pdf->Cell(4.5, $CH,'');
			$pdf->Cell(2.5,$CH,utf8_decode($q->NAME),'B',1,'C');
			$pdf->Cell(4.5, $CH,'');
			$pdf->Cell(2.5,$CH,'CONSULTING DOCTOR/SIGNATURE',0,1,'C');
			

		}

		// $pdf->Output('I','Medical Record '.utf8_decode($patient->NAME).'.pdf');
		$pdfPath = 'temp_files_pdf/Medical_Record_default_'.$id.'_.pdf';
		$pdf->Output('F', $pdfPath);
		echo base_url($pdfPath);

	}

}

?>
