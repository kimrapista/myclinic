<?php
defined('BASEPATH') OR exit('No direct script access allowed');


class M_sales_hmo extends CI_Model
{

	function __construct(){ 

		$this->load->database(); 
		$this->load->library('tcpdf/reports/sales_summary/Pdf');
	} 

	public function PDF(){

		$_POST += json_decode(file_get_contents('php://input'), true);
		$this->load->library('form_validation');

		$HMOID = $this->input->post('HMOID',TRUE);
		$PAIDTYPE = $this->input->post('PAIDTYPE',TRUE);
		$DATEFROM = $this->input->post('DATEFROM',TRUE);
		$DATETO = $this->input->post('DATETO',TRUE);
		

		$pdf = new Pdf( PDF_PAGE_ORIENTATION, PDF_UNIT, PDF_PAGE_FORMAT, true, 'UTF-8', false);

		$pdf->SetCreator('TCPDF');
		$pdf->SetAuthor('Cerebro Diagnostic System');
		$pdf->SetTitle('HMO Report');
		$pdf->SetSubject('HMO Report');

		$pdf->SetMargins(10, 23, 10, true);
		$pdf->SetAutoPageBreak(TRUE,12);
		
		$pdf->Set_Header_Title('HMO REPORT');
		$pdf->setPrintHeader(TRUE);
		$pdf->setPrintFooter(TRUE);
		
		$pdf->SetTextColor(40,40,40);
		$pdf->SetFillColor(220,220,220);
		$pdf->SetDrawColor(210,210,210);
		
		$pdf->AddPage('P', 'A4');
		
		$clinic = $this->db->query("SELECT * FROM clinics WHERE ID=? LIMIT 1",array($this->session->CLINICID))->row();
		$hmo = $this->db->query("SELECT * FROM hmo WHERE ID=? LIMIT 1",array($HMOID))->row();
		
		$pdf->SetFont('calibri', '', 9);

		$pdf->Cell(25,0, 'CLINIC',1,0,'L',TRUE);
		$pdf->Cell(100,0, $clinic->CLINICNAME, 1, 0);
		$pdf->Cell(25,0, 'DATE COVERED',1,0,'L',TRUE);
		$pdf->Cell(0, 0,  date('m/d/Y',strtotime($DATEFROM)).' - '.date('m/d/Y',strtotime($DATETO)) , 1, 1, 'C');

		$pdf->Cell(25,0, 'DOCTOR',1,0,'L',TRUE);
		$pdf->Cell(100,0, $clinic->DOCTORNAME, 1, 0);
		$pdf->Cell(25,0, 'DATE PRINTED',1,0,'L',TRUE);
		$pdf->Cell(0, 0,  date('m/d/Y H:i A', time()) , 1, 1,'C');
        
		$pdf->Cell(25,0, 'ADDRESS', 1, 0,'L',TRUE);
		$pdf->Cell(100,0, $clinic->ADDRESS, 1, 0);
		$pdf->Cell(25,0, 'HMO',1,0,'L',TRUE);
		$pdf->Cell(0,0, $hmo->NAME, 1, 1, 'C');
        
		$pdf->Cell(125, 0, '',1,0,'L',TRUE);
		$pdf->Cell(25, 0, 'STATUS',1,0,'L',TRUE);
		$pdf->Cell(0, 0, $PAIDTYPE == 1 ? 'PAID' : ($PAIDTYPE == 0 ? 'UNPAID' : 'ALL') , 1, 1, 'C');        
		$pdf->Ln();


        // ======================= PAID
        if( $PAIDTYPE == 1 || $PAIDTYPE == 2 ){

            $pat = $this->db->query("SELECT M.CHECKUPDATE, P.LASTNAME,P.FIRSTNAME,P.MIDDLENAME, 
                M.NETPAYABLES, 	
                M.HMOAMOUNT, M.HMORECEIVED,
                M.HMOCHEQUEDATE, M.HMOAMOUNT, M.HMOCHEQUENO,		
                S.NAME AS FROMCLINIC			

                FROM patients P 
                INNER JOIN medicalrecords M ON M.PATIENTID = P.ID
                INNER JOIN subclinic S ON S.ID = M.SUBCLINICID
                WHERE M.HMOID=? AND M.HMORECEIVED ='Y'
                AND DATE(M.CHECKUPDATE) BETWEEN ? AND ?
                AND M.CANCELLED = 'N'
                ORDER BY P.LASTNAME, P.FIRSTNAME, M.CHECKUPDATE",array( 
                    $HMOID,
                    date('Y-m-d',strtotime($DATEFROM)),
                    date('Y-m-d',strtotime($DATETO))
                ))->result();



            $pdf->SetFont('calibri', '', 8);

            $pdf->Cell(50, 0, 'PATIENT', 1, 0, 'C',TRUE);
            $pdf->Cell(20, 0, 'CHECKUP DATE', 1, 0, 'C',TRUE);
            $pdf->Cell(30, 0, 'CLINIC', 1, 0, 'C',TRUE);
            $pdf->Cell(40, 0, 'CHEQUE NO.', 1, 0, 'C',TRUE);
            $pdf->Cell(20, 0, 'CHEQUE DATE', 1, 0, 'C',TRUE);
            $pdf->Cell(0, 0, 'PAID AMOUNT', 1, 1, 'C',TRUE);
            
            $totalHMOAmount = 0;

            foreach ($pat as $key => $value) {

                $hmoAmount = is_numeric($value->HMOAMOUNT) ? (float)$value->HMOAMOUNT: 0;

                $pdf->Cell(50, 0, ($key+1).'. '. $value->LASTNAME.', '. $value->FIRSTNAME .' '. $value->MIDDLENAME, 1);
                $pdf->Cell(20, 0, is_null($value->CHECKUPDATE) ? '' : date('m/d/Y',strtotime($value->CHECKUPDATE)), 1, 0, 'C');
                $pdf->Cell(30, 0, $value->FROMCLINIC, 1);
                $pdf->Cell(40, 0, $value->HMOCHEQUENO, 1, 0, 'C');            
                $pdf->Cell(20, 0, is_null($value->HMOCHEQUEDATE) ? '' : date('m/d/Y',strtotime($value->HMOCHEQUEDATE)), 1, 0, 'C');
                $pdf->Cell(0, 0, number_format($hmoAmount, 2), 1, 1,'R');

                $totalHMOAmount += $hmoAmount;
            }

            $pdf->SetFont('calibri', '', 8);
            $pdf->Cell(50, 0, 'TOTAL PATIENT(S): '. count($pat) , 1, 0, 'L',TRUE);
            $pdf->Cell(110, 0, 'TOTAL ', 1,0,'R',TRUE);
            $pdf->Cell(0, 0, number_format($totalHMOAmount, 2) , 1,1,'R');
            $pdf->Ln();
        }

        // ============= UNPAID
        if( $PAIDTYPE == 0 || $PAIDTYPE == 2 ){

            $pat = $this->db->query("SELECT M.CHECKUPDATE, P.LASTNAME,P.FIRSTNAME,P.MIDDLENAME, 
                S.NAME AS FROMCLINIC,
                GROUP_CONCAT(SS.NAME) AS SERVICES_USE			

                FROM patients P 
                INNER JOIN medicalrecords M ON M.PATIENTID = P.ID
                INNER JOIN subclinic S ON S.ID = M.SUBCLINICID
                LEFT JOIN mr_services MS ON MS.MEDICALRECORDID = M.ID AND MS.CANCELLED='N'
                INNER JOIN services SS ON SS.ID = MS.SERVICEID
                WHERE M.HMOID=? AND M.HMORECEIVED !='Y'
                AND DATE(M.CHECKUPDATE) BETWEEN ? AND ?
                AND M.CANCELLED = 'N'
                GROUP BY M.ID
                ORDER BY P.LASTNAME, P.FIRSTNAME, M.CHECKUPDATE",array( 
                    $HMOID,
                    date('Y-m-d',strtotime($DATEFROM)),
                    date('Y-m-d',strtotime($DATETO))
                ))->result();


            $pdf->SetFont('calibri', '', 8);

            $pdf->Cell(50, 0, 'PATIENT', 1, 0, 'C',TRUE);
            $pdf->Cell(20, 0, 'CHECKUP DATE', 1, 0, 'C',TRUE);
            $pdf->Cell(30, 0, 'CLINIC', 1, 0, 'C',TRUE);
            $pdf->Cell(0, 0, 'SERVICES', 1, 1, 'C',TRUE);
            

            foreach ($pat as $key => $value) {
                $pdf->Cell(50, 0, ($key+1).'. '. $value->LASTNAME.', '. $value->FIRSTNAME .' '. $value->MIDDLENAME, 1);
                $pdf->Cell(20, 0, is_null($value->CHECKUPDATE) ? '' : date('m/d/Y',strtotime($value->CHECKUPDATE)), 1, 0, 'C');
                $pdf->Cell(30, 0, $value->FROMCLINIC, 1);
                $pdf->MultiCell(0, 0, $value->SERVICES_USE, 1, 'L');
            }

            $pdf->SetFont('calibri', '', 8);
            $pdf->Cell(0, 0, 'TOTAL PATIENT(S): '. count($pat) , 1, 0, 'L',TRUE);
            $pdf->Ln();
        }

        $pdf->Ln(15);

        $pdf->Cell(20, 0, '');
        $pdf->Cell(50, 0, 'Prepared By', 'T', 0, 'C');
        $pdf->Cell(40, 0, '');
        $pdf->Cell(50, 0, 'Received By', 'T', 1, 'C');


        $pdfName = 'temp_files_pdf/Sales_HMO_Report_'.$this->session->CLINICID.'_'.date('Y_m_d',strtotime($DATEFROM)).'_'.date('Y_m_d',strtotime($DATETO)).'.pdf';
        $pdf->Output( getcwd().'/'.$pdfName,'F');
		
        echo base_url($pdfName);
	}


  public function PDF1(){

    $_POST += json_decode(file_get_contents('php://input'), true);
    $this->load->library('form_validation');

    $HMOID = $this->input->post('HMOID',TRUE);
    $DATEFROM = $this->input->post('DATEFROM',TRUE);
    $DATETO = $this->input->post('DATETO',TRUE);
    

    $pdf = new Pdf( PDF_PAGE_ORIENTATION, PDF_UNIT, PDF_PAGE_FORMAT, true, 'UTF-8', false);

    $pdf->SetCreator('TCPDF');
    $pdf->SetAuthor('Cerebro Diagnostic System');
    $pdf->SetTitle('HMO POSTING REPORT');
    $pdf->SetSubject('HMO POSTING REPORT');

    $pdf->SetMargins(10, 23, 10, true);
    $pdf->SetAutoPageBreak(TRUE,12);
    
    $pdf->Set_Header_Title('HMO POSTING REPORT');
    $pdf->setPrintHeader(TRUE);
    $pdf->setPrintFooter(TRUE);
    
    $pdf->SetTextColor(40,40,40);
    $pdf->SetFillColor(220,220,220);
    $pdf->SetDrawColor(210,210,210);
    
    $pdf->AddPage('P', 'A4');
    
    $clinic = $this->db->query("SELECT * FROM clinics WHERE ID=? LIMIT 1",array($this->session->CLINICID))->row();
    $hmo = $this->db->query("SELECT * FROM hmo WHERE ID=? LIMIT 1",array($HMOID))->row();
    
    $pdf->SetFont('calibri', '', 9);

    $pdf->Cell(25,0, 'CLINIC',1,0,'L',TRUE);
    $pdf->Cell(100,0, $clinic->CLINICNAME, 1, 0);
    $pdf->Cell(25,0, 'DATE COVERED',1,0,'L',TRUE);
    $pdf->Cell(0, 0,  date('m/d/Y',strtotime($DATEFROM)).' - '.date('m/d/Y',strtotime($DATETO)) , 1, 1, 'C');

    $pdf->Cell(25,0, 'DOCTOR',1,0,'L',TRUE);
    $pdf->Cell(100,0, $clinic->DOCTORNAME, 1, 0);
    $pdf->Cell(25,0, 'DATE PRINTED',1,0,'L',TRUE);
    $pdf->Cell(0, 0,  date('m/d/Y H:i A', time()) , 1, 1,'C');
    
    $pdf->Cell(25,0, 'ADDRESS', 1, 0,'L',TRUE);
    $pdf->Cell(100,0, $clinic->ADDRESS, 1, 0);

    $pdf->Cell(25, 0, 'STATUS',1,0,'L',TRUE);
    $pdf->Cell(0, 0, 'PAID'  , 1, 1, 'C');        
    $pdf->Ln();

    // Fetch all records
    $allRecords = $this->db->query("SELECT M.CHECKUPDATE, P.LASTNAME,P.FIRSTNAME,P.MIDDLENAME, 
        M.NETPAYABLES, 	
        M.HMOAMOUNT, M.HMORECEIVED,M.HMODATE,
        M.HMOCHEQUEDATE, M.HMOAMOUNT, M.HMOCHEQUENO,		
        S.NAME AS FROMCLINIC,H.NAME, H.ID AS HMOID			

        FROM patients P 
        INNER JOIN medicalrecords M ON M.PATIENTID = P.ID
        INNER JOIN subclinic S ON S.ID = M.SUBCLINICID
        INNER JOIN hmo H ON H.ID = M.HMOID
        WHERE  M.HMORECEIVED ='Y'
        AND DATE(M.HMODATE) BETWEEN ? AND ?
        AND M.CANCELLED = 'N'
        ORDER BY H.NAME, P.LASTNAME, P.FIRSTNAME, M.HMODATE",array( 
            date('Y-m-d',strtotime($DATEFROM)),
            date('Y-m-d',strtotime($DATETO))
        ))->result();

    // Group records by HMO
    $groupedByHMO = array();
    foreach ($allRecords as $record) {
        $hmoName = $record->NAME;
        if (!isset($groupedByHMO[$hmoName])) {
            $groupedByHMO[$hmoName] = array();
        }
        $groupedByHMO[$hmoName][] = $record;
    }

    $pdf->SetFont('calibri', '', 8);
    
    $grandTotalAmount = 0;
    $grandTotalPatients = 0;

    // Loop through each HMO group
    foreach ($groupedByHMO as $hmoName => $patients) {
        
        // HMO Header
        $pdf->SetFont('calibri', 'B', 10);
        $pdf->SetFillColor(180,180,180);
        $pdf->Cell(0, 6, 'HMO: ' . $hmoName, 1, 1, 'L', TRUE);
        $pdf->SetFillColor(220,220,220);
        
        // Column Headers
        $pdf->SetFont('calibri', '', 8);
        $pdf->Cell(50, 0, 'PATIENT', 1, 0, 'C',TRUE);
        $pdf->Cell(20, 0, 'RECEIVED DATE', 1, 0, 'C',TRUE);
        $pdf->Cell(30, 0, 'CLINIC', 1, 0, 'C',TRUE);
        $pdf->Cell(40, 0, 'CHEQUE NO.', 1, 0, 'C',TRUE);
        $pdf->Cell(20, 0, 'CHEQUE DATE', 1, 0, 'C',TRUE);
        $pdf->Cell(0, 0, 'PAID AMOUNT', 1, 1, 'C',TRUE);
        
        $hmoTotalAmount = 0;
        $patientCounter = 1;

        // Print patient records for this HMO
        foreach ($patients as $value) {
            $hmoAmount = is_numeric($value->HMOAMOUNT) ? (float)$value->HMOAMOUNT : 0;
            
            $pdf->Cell(50, 0, $patientCounter.'. '. $value->LASTNAME.', '. $value->FIRSTNAME .' '. $value->MIDDLENAME, 1);
            $pdf->Cell(20, 0, is_null($value->HMODATE) ? '' : date('m/d/Y',strtotime($value->HMODATE)), 1, 0, 'C');
            $pdf->Cell(30, 0, $value->FROMCLINIC, 1);
            $pdf->Cell(40, 0, $value->HMOCHEQUENO, 1, 0, 'C');            
            $pdf->Cell(20, 0, is_null($value->HMOCHEQUEDATE) ? '' : date('m/d/Y',strtotime($value->HMOCHEQUEDATE)), 1, 0, 'C');
            $pdf->Cell(0, 0, number_format($hmoAmount, 2), 1, 1,'R');

            $hmoTotalAmount += $hmoAmount;
            $patientCounter++;
        }

        // HMO Subtotal
        $pdf->SetFont('calibri', 'B', 8);
        $pdf->Cell(50, 0, 'TOTAL PATIENT(S): '. count($patients) , 1, 0, 'L',TRUE);
        $pdf->Cell(110, 0, 'SUBTOTAL ', 1,0,'R',TRUE);
        $pdf->Cell(0, 0, number_format($hmoTotalAmount, 2) , 1,1,'R');
        $pdf->Ln(3);
        
        $grandTotalAmount += $hmoTotalAmount;
        $grandTotalPatients += count($patients);
    }

    // Grand Total
    $pdf->SetFont('calibri', 'B', 9);
    $pdf->SetFillColor(180,180,180);
    $pdf->Cell(50, 0, 'GRAND TOTAL PATIENT(S): '. $grandTotalPatients , 1, 0, 'L',TRUE);
    $pdf->Cell(110, 0, 'GRAND TOTAL ', 1,0,'R',TRUE);
    $pdf->Cell(0, 0, number_format($grandTotalAmount, 2) , 1,1,'R');
    $pdf->Ln();

    $pdf->Ln(15);

    $pdf->SetFont('calibri', '', 9);
    $pdf->Cell(20, 0, '');
    $pdf->Cell(50, 0, 'Prepared By', 'T', 0, 'C');
    $pdf->Cell(40, 0, '');
    $pdf->Cell(50, 0, 'Received By', 'T', 1, 'C');

    $pdfName = 'temp_files_pdf/Sales_HMO_Report_'.$this->session->CLINICID.'_'.date('Y_m_d',strtotime($DATEFROM)).'_'.date('Y_m_d',strtotime($DATETO)).'.pdf';
    $pdf->Output( getcwd().'/'.$pdfName,'F');
    
    echo base_url($pdfName);
}



	

}



?>
