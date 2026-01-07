<?php
defined('BASEPATH') OR exit('No direct script access allowed');


class M_pirani_report_default extends CI_Model
{
	
	function __construct(){ $this->load->database(); }


	private function clinicInfo(){

		$sql = $this->db->query("SELECT * FROM clinics where ID=? LIMIT 1",array($this->session->CLINICID));
		return $sql->row();

	}


	public function Index($id, $return = FALSE){

		$this->load->library('pdf');

		$this->pdf->__construct('L','in','A4');
		$pdf = $this->pdf;

		$margL=0.3;
		$margT=0.3;

		$pdf->SetMargins($margL,$margT,$margL);
		$pdf->SetAutoPageBreak(false);
		$pdf->AddPage();

		$pageNo=0;
		$CH=0.16;
		$CW=0.8;
		$showBorder = 0;

		$clinic = $this->clinicInfo();

		
		$sql = $this->db->query("SELECT P.ID,concat(P.FIRSTNAME,' ',P.MIDDLENAME,' ',P.LASTNAME) as NAME, P.SEX, P.STREETNO,  P.CITY, P.PROVINCE
			FROM patients P
			WHERE P.ID = ? 
			LIMIT 1",array($id));
		$patient = $sql->row();

		$sqlMR = $this->db->query("SELECT MR.ID, MR.CHECKUPDATE, MR.AGE, 
				MR.LATERAL_BORDER_L, MR.LATERAL_BORDER_R, 
				MR.MEDICAL_CREASE_L, MR.MEDICAL_CREASE_R, 
				MR.TALAR_HEAD_L, MR.TALAR_HEAD_R, 
				MR.MIDFOOT_SCORE_L, MR.MIDFOOT_SCORE_R, 
				MR.POSTERIOR_CREASE_L, MR.POSTERIOR_CREASE_R, 
				MR.EMPTY_HEEL_L, MR.EMPTY_HEEL_R, 
				MR.RIGID_EQUINUS_L, MR.RIGID_EQUINUS_R, 
				MR.HINDFOOT_SCORE_L, MR.HINDFOOT_SCORE_R, 
				MR.TOTAL_SCORE_L, MR.TOTAL_SCORE_R, 
				MR.COMPLICATIONS_L, MR.COMPLICATIONS_R,
				MR.TREATMENT_CODE_L, MR.TREATMENT_CODE_R, 
				U.NAME AS DOCTORNAME

			FROM medicalrecords MR
			LEFT JOIN users U ON U.ID = MR.CREATEDBY
			WHERE MR.PATIENTID = ? 
			ORDER BY MR.CHECKUPDATE ",array($id));
		
		
		$records = $sqlMR->result();

		
		$pdf->Image('uploads/reports/miraclefeet.jpg', $margL, $margT, 2, 0.75);
		$pdf->Ln(0.8);

		$CH = 0.25;
		$CW = ($pdf->GetPageWidth() - ($margL*2)) - 1.5;
		
		if( count($records) < 8 ){
			$emptyColumn = 8 - count($records);
			$CW = $CW / 16;
		}
		else{
			$emptyColumn = 0;			
			$CW = $CW /  (count($records) * 2);
		}
				

		if(count($records) > 0){ $age = $records[count($records)-1]->AGE; }
		else{ $age = ''; }

		$pdf->SetFont('Arial','B',10);
		$pdf->Cell( 7, $CH,'Name: '.utf8_decode($patient->NAME), 'LT',0);
		$pdf->Cell( 2, $CH,'Age: '.$age, 'T',0);
		$pdf->Cell( 0, $CH,'Gender: '.$patient->SEX, 'TR',1);

		$pdf->Cell( 0, $CH,'Address: '.utf8_decode($patient->STREETNO.' '.$patient->CITY.', '.$patient->PROVINCE), 'LTR',1);

		$pdf->Cell( 5.1, $CH,'Doctor:', 1,0);
		$pdf->Cell( 3, $CH,'Evaluation Date:', 1,0);
		$pdf->Cell( 0, $CH,'Lateral:', 1,1);
		
		
		$pdf->SetFont('Arial','B', 9);
		$pdf->Cell( 1.5, $CH, 'Date: ',1, 0);		
		$pdf->SetFont('Arial','', 9);
		for($i=0; $i < count($records); $i++){			
			$pdf->Cell( $CW *2 , $CH, date('m/d/Y', strtotime($records[$i]->CHECKUPDATE)), 1, 0, 'C' );
		}
		for($i=0; $i < $emptyColumn; $i++){ $pdf->Cell( $CW *2 , $CH, '', 1, 0); }
		$pdf->Ln();


		
		$pdf->SetFont('Arial','B', 9);
		$pdf->Cell( 1.5, $CH, 'Casting No. ',1, 0);		
		$pdf->SetFont('Arial','', 7);
		$currX = $pdf->GetX();
		$currY = $pdf->GetY();
		for($i=0; $i < count($records); $i++){			
			$pdf->MultiCell( $CW*2, $CH/2, $records[$i]->DOCTORNAME . ' : '. $records[$i]->ID, 'LR', 'C');
			$currX = $currX + ($CW*2);
			$pdf->SetXY($currX, $currY);
		}
		for($i=0; $i < $emptyColumn; $i++){ $pdf->Cell( $CW *2 , $CH, '', 1, 0); }
		$pdf->Ln();


		
		$pdf->SetFont('Arial','B', 9);
		$pdf->Cell( 1.5, $CH, 'PIRANI Scoring ',1, 0);
		for($i=0; $i< count($records); $i++){	
			$pdf->Cell( $CW, $CH, 'L ',1, 0, 'C');		
			$pdf->Cell( $CW, $CH, 'R',1, 0, 'C');		
		}
		for($i=0; $i < $emptyColumn; $i++){ $pdf->Cell( $CW , $CH, 'L', 1, 0, 'C'); $pdf->Cell( $CW , $CH, 'R', 1, 0, 'C'); }
		$pdf->Ln();



		$pdf->SetFont('Arial','', 9);
		$pdf->Cell( 1.5, $CH, 'lateral border',1, 0);		
		for($i=0; $i < count($records); $i++){			
			$pdf->Cell( $CW , $CH, (int)$records[$i]->LATERAL_BORDER_L , 1, 0, 'C' );
			$pdf->Cell( $CW , $CH, (int)$records[$i]->LATERAL_BORDER_R , 1, 0, 'C' );	
		}
		for($i=0; $i < $emptyColumn; $i++){ $pdf->Cell( $CW , $CH, '', 1, 0); $pdf->Cell( $CW , $CH, '', 1, 0); }
		$pdf->Ln();



		$pdf->SetFont('Arial','', 9);
		$pdf->Cell( 1.5, $CH, 'Medical Crease',1, 0);		
		for($i=0; $i < count($records); $i++){			
			$pdf->Cell( $CW , $CH, (int)$records[$i]->MEDICAL_CREASE_L, 1, 0, 'C' );
			$pdf->Cell( $CW , $CH, (int)$records[$i]->MEDICAL_CREASE_R, 1, 0, 'C' );	
		}
		for($i=0; $i < $emptyColumn; $i++){ $pdf->Cell( $CW , $CH, '', 1, 0); $pdf->Cell( $CW , $CH, '', 1, 0); }
		$pdf->Ln();



		$pdf->SetFont('Arial','', 9);
		$pdf->Cell( 1.5, $CH, 'Talar Head',1, 0);		
		for($i=0; $i < count($records); $i++){			
			$pdf->Cell( $CW , $CH, (int)$records[$i]->TALAR_HEAD_L , 1, 0, 'C' );
			$pdf->Cell( $CW , $CH, (int)$records[$i]->TALAR_HEAD_R , 1, 0, 'C' );	
		}
		for($i=0; $i < $emptyColumn; $i++){ $pdf->Cell( $CW , $CH, '', 1, 0); $pdf->Cell( $CW , $CH, '', 1, 0); }
		$pdf->Ln();



		$pdf->SetFont('Arial','B', 9);
		$pdf->Cell( 1.5, $CH, 'MIDFOOT Score',1, 0);		
		for($i=0; $i < count($records); $i++){			
			$pdf->Cell( $CW , $CH, (int)$records[$i]->MIDFOOT_SCORE_L , 1, 0, 'C' );
			$pdf->Cell( $CW , $CH, (int)$records[$i]->MIDFOOT_SCORE_R , 1, 0, 'C' );	
		}
		for($i=0; $i < $emptyColumn; $i++){ $pdf->Cell( $CW , $CH, '', 1, 0); $pdf->Cell( $CW , $CH, '', 1, 0); }
		$pdf->Ln();




		$pdf->SetFont('Arial','', 9);
		$pdf->Cell( 1.5, $CH, 'Posterior Crease', 1, 0);		
		for($i=0; $i < count($records); $i++){			
			$pdf->Cell( $CW , $CH, (int)$records[$i]->POSTERIOR_CREASE_L , 1, 0, 'C' );
			$pdf->Cell( $CW , $CH, (int)$records[$i]->POSTERIOR_CREASE_R , 1, 0, 'C' );	
		}
		for($i=0; $i < $emptyColumn; $i++){ $pdf->Cell( $CW , $CH, '', 1, 0); $pdf->Cell( $CW , $CH, '', 1, 0); }
		$pdf->Ln();



		$pdf->SetFont('Arial','', 9);
		$pdf->Cell( 1.5, $CH,'Empty Heel',1, 0);		
		for($i=0; $i < count($records); $i++){			
			$pdf->Cell( $CW , $CH, (int)$records[$i]->EMPTY_HEEL_L , 1, 0, 'C' );
			$pdf->Cell( $CW , $CH, (int)$records[$i]->EMPTY_HEEL_R , 1, 0, 'C' );	
		}
		for($i=0; $i < $emptyColumn; $i++){ $pdf->Cell( $CW , $CH, '', 1, 0); $pdf->Cell( $CW , $CH, '', 1, 0); }
		$pdf->Ln();



		$pdf->SetFont('Arial','', 9);
		$pdf->Cell( 1.5, $CH, 'Rigid Equinos',1, 0);		
		for($i=0; $i < count($records); $i++){			
			$pdf->Cell( $CW , $CH, (int)$records[$i]->RIGID_EQUINUS_L , 1, 0, 'C' );
			$pdf->Cell( $CW , $CH, (int)$records[$i]->RIGID_EQUINUS_R , 1, 0, 'C' );	
		}
		for($i=0; $i < $emptyColumn; $i++){ $pdf->Cell( $CW , $CH, '', 1, 0); $pdf->Cell( $CW , $CH, '', 1, 0); }
		$pdf->Ln();



		$pdf->SetFont('Arial','B', 9);
		$pdf->Cell( 1.5, $CH, 'HINDFOOT Score',1, 0);		
		for($i=0; $i < count($records); $i++){			
			$pdf->Cell( $CW , $CH, (int)$records[$i]->HINDFOOT_SCORE_L , 1, 0, 'C' );
			$pdf->Cell( $CW , $CH, (int)$records[$i]->HINDFOOT_SCORE_R , 1, 0, 'C' );
		}
		for($i=0; $i < $emptyColumn; $i++){ $pdf->Cell( $CW , $CH, '', 1, 0); $pdf->Cell( $CW , $CH, '', 1, 0); }
		$pdf->Ln();


		$pdf->SetFont('Arial','B', 9);
		$pdf->Cell( 1.5, $CH, 'Total Score',1, 0);		
		for($i=0; $i < count($records); $i++){			
			$pdf->Cell( $CW , $CH, (int)$records[$i]->TOTAL_SCORE_L , 1, 0, 'C' );
			$pdf->Cell( $CW , $CH, (int)$records[$i]->TOTAL_SCORE_R , 1, 0, 'C' );
		}
		for($i=0; $i < $emptyColumn; $i++){ $pdf->Cell( $CW , $CH, '', 1, 0); $pdf->Cell( $CW , $CH, '', 1, 0); }
		$pdf->Ln();


		$pdf->SetFont('Arial','B', 9);
		$pdf->Cell( 1.5, $CH, 'Complications',1, 0);
		$pdf->SetFont('Arial','', 7);		
		for($i=0; $i < count($records); $i++){			
			$pdf->Cell( $CW , $CH, $records[$i]->COMPLICATIONS_L, 1, 0, 'C' );
			$pdf->Cell( $CW , $CH, $records[$i]->COMPLICATIONS_R, 1, 0, 'C' );	
		}
		for($i=0; $i < $emptyColumn; $i++){ $pdf->Cell( $CW , $CH, '', 1, 0); $pdf->Cell( $CW , $CH, '', 1, 0); }
		$pdf->Ln();
		
		
		$pdf->SetFont('Arial','B', 9);
		$pdf->Cell( 1.5, $CH, 'Treatment Code',1, 0);		
		$pdf->SetFont('Arial','', 7);		
		for($i=0; $i < count($records); $i++){			
			$pdf->Cell( $CW , $CH, $records[$i]->TREATMENT_CODE_L, 1, 0, 'C' );
			$pdf->Cell( $CW , $CH, $records[$i]->TREATMENT_CODE_R, 1, 0, 'C' );	
		}
		for($i=0; $i < $emptyColumn; $i++){ $pdf->Cell( $CW , $CH, '', 1, 0); $pdf->Cell( $CW , $CH, '', 1, 0); }
		$pdf->Ln();


		$pdf->MultiCell( 0, $CH, 'Treatment: (C) Casting   (T) Tenotomy   (B) Bracing   Complications/Evaluation: Lower Limb     Upper Limb     Spine' , 1, 'J');
		
		// $pdf->Output('I','Medical Certificate '.utf8_decode($patient->NAME).'.pdf');
		$pdfPath = 'temp_files_pdf/Pirani_Report_'.$id.'_.pdf';
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