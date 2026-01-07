<?php
defined('BASEPATH') OR exit('No direct script access allowed');


class M_medical_prescription_8 extends CI_Model
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

		$margL=0.3;
		$margT=0.3;
		$pdf->SetMargins($margL,$margT,$margL);
		$pdf->SetAutoPageBreak(true);

		

		$clinic = $this->clinicInfo();

		$patient = $this->db->query("SELECT p.ID, concat(p.FIRSTNAME,' ',p.MIDDLENAME,' ',p.LASTNAME) as NAME, concat(p.STREETNO) as ADDRESS,concat(p.CITY,' ',p.PROVINCE) as ADDRESS1, p.SEX , mr.APPOINTMENT, mr.APPOINTMENTDATE, mr.AGE,c.MOBILENO
			FROM patients p
			INNER JOIN medicalrecords mr ON mr.PATIENTID = p.ID
			INNER JOIN clinics c ON c.ID = mr.CLINICID
			WHERE mr.ID = ?   
			LIMIT 1",array($id))->row();

		$meds = $this->db->query("SELECT m.NAME, mrm.FREQUENCY, mrm.INSTRUCTION, mrm.QUANTITY
			FROM mr_medicines mrm 
			INNER JOIN medicines m ON m.ID = mrm.MEDICINEID
			WHERE mrm.MEDICALRECORDID = ? and mrm.CANCELLED='N' ",array($id))->result();

		
		$loop = (count($meds) % 9);

		if( count($meds) > 0 ){
			$loop = ceil(count($meds) / 10);
		}
		else{
			$loop = 1;
		}
		

		for ($i = 1; $i <= $loop ; $i++) { 

			$pdf->AddPage();

			$pageNo=0;
			$CH=0.2;
			$CW=0.8;


			
			$pdf->Image('uploads/reports/clinic'.$clinic->ID.'_1.png', $pdf->GetX(),$pdf->GetY() - 0.05,0.45);
			$pdf->Image('uploads/reports/clinic'.$clinic->ID.'_2.png', $pdf->getPageWidth() - ($margL+0.3),$pdf->GetY()-0.05,0.35);
			$pdf->Image('uploads/reports/clinic'.$clinic->ID.'_3.png', $pdf->getPageWidth() - ($margL+0.3),$pdf->GetY() + 0.37, 0.35);

			//$pdf->SetDrawColor(244, 164, 96);
			//$pdf->SetDrawColor(189, 137, 93);
			$pdf->SetDrawColor(30, 30, 30);
			//$pdf->SetTextColor(244, 164, 96);
			//$pdf->SetTextColor(189, 137, 93);

			$pdf->SetFont('Arial','B',13);
			$pdf->Cell(0,$CH,utf8_decode($clinic->CLINICNAME),0,1,'C');

			$pdf->SetTextColor(40, 40, 40);
			$pdf->SetFont('Arial','B',11);
			$pdf->Cell(0,$CH,utf8_decode($clinic->CLINICSUBNAME),0,1,'C');

			$pdf->SetFont('Arial','B',11);
			$pdf->Cell(0,$CH,utf8_decode($clinic->CLINICSUBNAME1),0,1,'C');
			$pdf->Ln(0.1);


			$pdf->SetTextColor(15, 15, 15);
			$pdf->SetFont('Arial','',9);

			$CH= 0.15; 

			$widthCell = ($pdf->getPageWidth() - ($margL*2)) / 3;

			$pdf->SetFont('Arial','B',7);
			$pdf->Cell($widthCell,$CH,"CDO Polymedic General Hospital",0,0);
			$pdf->Cell($widthCell,$CH,"       Polymedic Limketkai",0,0);
			$pdf->Cell($widthCell,$CH,"CDO Polymedic Medical Plaza",0,1);

			$pdf->SetFont('Arial','',7);
			$pdf->Cell($widthCell,$CH,"Don A. Velez St.,CDOC",0,0);
			$pdf->Cell($widthCell,$CH,"       2nd floor, near PRC",0,0);
			$pdf->Cell($widthCell,$CH,"Kauswagan Highway, CDOC",0,1);

			$pdf->Cell($widthCell,$CH,"Ground Floor",0,0);
			$pdf->Cell($widthCell,$CH,"       0917-706-3594",0,0);
			$pdf->Cell($widthCell,$CH,"Room 241",0,1);

			$pdf->Cell($widthCell,$CH,"856447 local 1014",0,0);
			$pdf->Cell($widthCell,$CH," ",0,0);
			$pdf->Cell($widthCell,$CH,"8585241 local 2241",0,1);


			$pdf->Cell(0,0.01,'','B',1,'R');
			$pdf->Ln(0.15);


			$CH=0.12;
			$pdf->SetFont('Arial','',9);
			$pdf->Cell(1.1,$CH,"PATIENT'S NAME: ",0);
			$pdf->Cell(2.3,$CH,utf8_decode($patient->NAME),'B',0,'C');
			$pdf->Cell(0.5,$CH," DATE: ",0);
			$pdf->Cell(0,$CH,date('m/d/Y',time()),'B',1,'C');
			$pdf->Ln(0.05);

			$pdf->Cell(0.7,$CH,"ADDRESS: ",0);
			$pdf->Cell(2.7,$CH,utf8_decode($patient->ADDRESS),'B',0,'C');

			$pdf->Cell(0.5,$CH," AGE: ",0);
			$pdf->Cell(0,$CH,$patient->AGE,'B',1,'C');
			$pdf->Ln(0.1);

			$pdf->Image('assets/css/images/rx.png',$pdf->GetX(),$pdf->GetY(),0.4);

			$pdf->Ln(0.1);

			$pdf->SetFillColor(220,220,220);


			if( $meds ){

				$pdf->SetFont('Times','',11);
				$pdf->Ln(0.005);
				$CH += 0.05;

				foreach ($meds as $key => $v) {

					if(  ($key+1) >= ((($i-1)*10)+1) && ($key+1) <= ($i*10) ) {

						$pdf->Cell(0.5,$CH,'');
						$pdf->Cell(0,$CH, utf8_decode(($key + 1).'.    '.$v->NAME.'     '.($v->QUANTITY > 0 ? '    #'.$v->QUANTITY: '')),0,1);	
						$pdf->Ln(0.03);
						$pdf->Cell(0.8,$CH,'');
						$pdf->MultiCell(0,$CH,'Sig:    '.utf8_decode($v->INSTRUCTION .'     '.$v->FREQUENCY));	
						$pdf->Ln(0.1);
					}
				}
			}

			

			$CH=0.15;

			$currentX = $pdf->getX();
			$currentY = $pdf->getPageHeight()-1;

			$pdf->SetFont('Times','B',9);
			$pdf->SetXY($margL+0,$currentY);

			if($patient->APPOINTMENT == 'N' || $patient->APPOINTMENT == NULL){
				$pdf->MultiCell(1.8,$CH,"Your next appointment will :\n\r\n\r",'B');

			} else{
				$pdf->MultiCell(1.8,$CH,"Your next appointment will : ".date('F d, Y',strtotime($patient->APPOINTMENTDATE)),'B',1);
			}

			$pdf->Ln(0.05);
			$pdf->MultiCell(1.5,$CH,"Clinic No :\n\r".$clinic->MOBILENO,0,1);

			$pdf->SetFont('Times','B',9);
			$pdf->SetXY($margL+3.2,$currentY);

			$q = $this->db->query("SELECT U.NAME, U.PTR, U.LICENSENO, U.S2NO
				FROM users U
				INNER JOIN medicalrecords MR ON MR.CREATEDBY = U.ID
				WHERE MR.ID = ?   
				LIMIT 1",array($id))->row();


			if( $q ){
				$pdf->Ln();
				$pdf->SetFont('Times','B',10);
				$pdf->SetX(3);
				$pdf->MultiCell(2,$CH,utf8_decode($q->NAME),'B','L');
				
				$pdf->SetFont('Times','B',9);
				$pdf->SetXY(3, $pdf->getY() + 0.05);
				$pdf->MultiCell(2,$CH,"License No :  ".$q->LICENSENO."\n\rPTR No :  ".$q->PTR."\n\rS2 No :  ".$q->S2NO,0);

			}

		} 

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