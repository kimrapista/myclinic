<?php

defined('BASEPATH') OR exit('No direct script access allowed');



class M_sales_detail_philhealth extends CI_Model
{


	function __construct(){ 
		$this->load->database(); 
      	$this->load->library('tcpdf/reports/sales_summary/Pdf');
	}



	public function PDF(){ 


		$_POST += json_decode(file_get_contents('php://input'), true);

		$this->load->library('form_validation');

		$DATEFROM = $this->input->post('DATEFROM',TRUE);
		$DATETO = $this->input->post('DATETO',TRUE);	


		$pdf = new Pdf( PDF_PAGE_ORIENTATION, PDF_UNIT, PDF_PAGE_FORMAT, true, 'UTF-8', false);


		$pdf->SetCreator('TCPDF');
		$pdf->SetAuthor('Cerebro Diagnostic System');
		$pdf->SetTitle('PhilHealth Claims Report');
		$pdf->SetSubject('PhilHealth Claims Report');

		$pdf->SetMargins(10, 23, 10, true);
		$pdf->SetAutoPageBreak(TRUE,12);
		
		$pdf->Set_Header_Title('PHILHEALTH CLAIMS REPORT');
		$pdf->setPrintHeader(TRUE);
		$pdf->setPrintFooter(TRUE);
		
		$pdf->SetTextColor(40,40,40);
		$pdf->SetFillColor(220,220,220);
		$pdf->SetDrawColor(210,210,210);
		$pdf->AddPage('P', 'A4');
		

		$clinic = $this->db->query("SELECT * FROM clinics WHERE ID=? LIMIT 1",array($this->session->CLINICID))->row();



		$pdf->SetFont('calibri', '', 9);

		$pdf->Cell(26,0, 'CLINIC',1,0,'L',TRUE);
		$pdf->Cell(100,0, $clinic->CLINICNAME, 1, 0);
		$pdf->Cell(29,0, 'DATE COVERED',1,0,'L',TRUE);
		$pdf->Cell(0, 0,  date('m/d/Y',strtotime($DATEFROM)).' - '.date('m/d/Y',strtotime($DATETO)) , 1, 1,'L');


		$pdf->Cell(26,0, 'DOCTOR',1,0,'L',TRUE);
		$pdf->Cell(100,0, $clinic->DOCTORNAME, 1, 0);
		$pdf->Cell(29,0, 'DATE PRINTED',1,0,'L',TRUE);
		$pdf->Cell(0, 0,  date('m/d/Y H:i A', time()) , 1, 1,'L');


		$pdf->Cell(26,0, 'ADDRESS',1,0,'L',TRUE);
		$pdf->Cell(0,0, $clinic->ADDRESS, 1, 1);
		$pdf->Ln();



		// patients
		$pat = $this->db->query("SELECT M.ID, M.HMOID, M.CHECKUPDATE, P.LASTNAME,P.FIRSTNAME,P.MIDDLENAME, 
			M.NETPAYABLES, M.PAYMODE, M.PAIDAMOUNT, M.AMOUNT,	
		   	H.NAME AS HMONAME, M.HMODATE, M.HMOAMOUNT, M.HMOCHEQUENO, M.HMOCHEQUEDATE, M.HMORECEIVED,
			M.CHEQUEDATE, M.CHEQUEAMOUNT, M.CHEQUENO, M.CHEQUEBANKNAME,	
			M.PHILHEALTH, M.PHILHEALTHRECEIVED, M.PHILHEALTHCHEQUENO, M.PHILHEALTHCHEQUEDATE, M.PHILHEALTHAMOUNT,
			S.NAME AS FROMCLINIC	

			FROM patients P 
			INNER JOIN medicalrecords M ON M.PATIENTID = P.ID
			INNER JOIN subclinic S ON S.ID = M.SUBCLINICID
			LEFT JOIN hmo H ON H.ID = M.HMOID
			WHERE M.CLINICID = ? AND M.PHILHEALTH='Y'
			AND DATE(M.CHECKUPDATE) BETWEEN ? AND ?
			AND M.CANCELLED = 'N'
			ORDER BY M.CHECKUPDATE DESC, S.NAME, P.LASTNAME, P.FIRSTNAME ", 
			array(
				$this->session->CLINICID,
				date('Y-m-d',strtotime($DATEFROM)),
				date('Y-m-d',strtotime($DATETO))
			))->result();


		$pdf->SetFont('calibri', '', 8);		

	
		$totalClaimed = 0;
		$totalNotClaimed = 0;
		$totalClaimedAmount = 0;
		$totalNotClaimedAmount = 0;
		$totalNetPayables = 0;
		

		$borStyle = 'border:1px solid #d1d1d1;';
		$bgStyle= 'background-color:#dcdcdc;';


		$table = '
			<thead>
				<tr style="'.$bgStyle.$borStyle.'">
					<th width="25" align="center" style="'.$borStyle.'">#</th>
					<th width="50" align="center" style="'.$borStyle.'">CHECKUP</th>
					<th width="60" align="center" style="'.$borStyle.'">CLINIC</th>
					<th width="110" align="center" style="'.$borStyle.'">NAME</th>
					<th width="40" align="center" style="'.$borStyle.'">PAYABLE</th>
					<th width="80" align="center" style="'.$borStyle.'">PAYMENT TYPE</th>
					<th width="60" align="center" style="'.$borStyle.'">CHEQUE</th>
					<th width="50" align="center" style="'.$borStyle.'">DATE</th>
					<th width="0" align="center" style="'.$borStyle.'">AMOUNT</th>
				</tr>
			</thead>
		';


      	foreach ($pat as $key => $value) {

			$netPayables = is_numeric($value->NETPAYABLES) ? (float)$value->NETPAYABLES: 0;
			$totalNetPayables += $netPayables;
			
			$payments = array();


			// PHILHEALTH
			if( $value->PHILHEALTH == 'Y' && $value->PHILHEALTHRECEIVED == 'Y'){
				$payments[] = array(
					'TYPE' => 'PhilHealth',
					'CHEQUENO' => $value->PHILHEALTHCHEQUENO,
					'CHEQUEDATE'=> date('m/d/Y',strtotime($value->PHILHEALTHCHEQUEDATE)),
					'AMOUNT' => (float)$value->PHILHEALTHAMOUNT
				);

				$totalClaimed += 1;
				$totalClaimedAmount += (float)$value->PHILHEALTHAMOUNT;
			}
			else if( $value->PHILHEALTH == 'Y' ){
				$payments[] = array(
					'TYPE' => 'PhilHealth',
					'CHEQUENO' => '',
					'CHEQUEDATE'=> '',
					'AMOUNT' => 0
				);

				$totalNotClaimed += 1;
				$totalNotClaimedAmount += $netPayables;
			}

					
		
			$rowspan = count($payments) ? count($payments) : 1;

			$table .= '<tr>';
			$table .= '<td width="25" align="center" rowspan="'.$rowspan.'" style="'.$borStyle.'">'.($key+1).'</td>';
			$table .= '<td width="50" align="center" rowspan="'.$rowspan.'" style="'.$borStyle.'">'.date('m/d/Y',strtotime($value->CHECKUPDATE)).'</td>';
			$table .= '<td width="60" align="center" rowspan="'.$rowspan.'" style="'.$borStyle.'">'.$value->FROMCLINIC.'</td>';
			$table .= '<td width="110" rowspan="'.$rowspan.'" style="'.$borStyle.'">'.($value->LASTNAME.', '.$value->FIRSTNAME.' '.$value->MIDDLENAME).'</td>';
			$table .= '<td width="40" rowspan="'.$rowspan.'" align="right" style="'.$borStyle.'">'. number_format($value->NETPAYABLES,2).'</td>';


			$table .= '<td width="80" align="center" style="'.$borStyle.'">'.$payments[0]['TYPE'].'</td>';
			$table .= '<td width="60"  align="center" style="'.$borStyle.'">'.$payments[0]['CHEQUENO'].'</td>';
			$table .= '<td width="50" align="center" style="'.$borStyle.'">'.$payments[0]['CHEQUEDATE'].'</td>';
			$table .= '<td width="0" align="right" style="'.$borStyle.'">'.($payments[0]['AMOUNT'] > 0 ? number_format($payments[0]['AMOUNT'],2) : '').'</td>';
			

			$table .= '</tr>';

			
		}


		$table .= '
			<tfoot>
				<tr style="'.$bgStyle.$borStyle.'">
					<th width="0" align="center">-- End --</th>
				</tr>
			</tfoot>
		';


		$table = '<table cellpadding="2"  border="0">'.$table.'</table>';
		$pdf->writeHTML($table, true);


		$pdf->SetTextColor(10,10,10);
      	$pdf->SetFont('calibri', '', 9);
		
	
		$pdf->Cell(165,0, 'Claimed ('.$totalClaimed.') :', 0,0,'R');
		$pdf->Cell(0,0, number_format($totalClaimedAmount,2) , 0,1,'R');	

		$pdf->Cell(165,0, 'Un-Claimed ('.$totalNotClaimed.') :', 0,0,'R');
		$pdf->Cell(0,0, number_format($totalNotClaimedAmount,2) ,'B',1,'R');	
		$pdf->Ln(1);

		
		$pdf->SetFont('calibri', '', 10);
		$pdf->Cell(165,0, 'TOTAL:' , 0, 0,'R');
		$pdf->Cell(0,0, number_format($totalClaimedAmount +  $totalNotClaimedAmount,2) , 0,1,'R');
		$pdf->Ln(1);
		

       	$pdfName = 'temp_files_pdf/Sales_Philhealth_'.$this->session->CLINICID.'_'.date('Y_m_d',strtotime($DATEFROM)).'_'.date('Y_m_d',strtotime($DATETO)).'.pdf';
      	
		$pdf->Output( getcwd().'/'.$pdfName,'F');

		echo base_url($pdfName);
	}



}

?>