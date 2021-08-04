<?php

namespace App\Services;

include_once(app_path('Services/tcpdf/config/lang/eng.php'));
require_once(app_path('Services/tcpdf/tcpdf.php'));

class Tcpdf
{
    public $TCPDF;

    public function __construct(array $config=[])
    {
        return $this->init($config);
    }

    public function init(array $config=[]) 
    {
        error_reporting(0);
        $this->TCPDF = new \TCPDF(PDF_PAGE_ORIENTATION, PDF_UNIT, PDF_PAGE_FORMAT, true, 'UTF-8', false);		

		// set document information
		$this->TCPDF->SetCreator(PDF_CREATOR);
		$this->TCPDF->SetAuthor($config['author']??'');
		$this->TCPDF->SetTitle($config['title']??'');
		$this->TCPDF->SetSubject($config['subject']??'');
		$this->TCPDF->SetKeywords($config['keywords']??'');
		
		// remove default header/footer
		$this->TCPDF->setPrintHeader(false);
		$this->TCPDF->setPrintFooter(true);
		
		$this->TCPDF->getPageSizeFromFormat($config['page_size']??'A4');
		
		// set default monospaced font
		$this->TCPDF->SetDefaultMonospacedFont(PDF_FONT_MONOSPACED);

		//set margins left, top, right, button
		$this->TCPDF->SetMargins(20,12,20,true);
		
		$this->TCPDF->SetHeaderMargin(0);
		$this->TCPDF->SetFooterMargin(10);

		//set auto page breaks
		$marginbottom = 15;
		// $datasurattugas = $this->system->s_surat->surat_get('tugas',$this->system->url->urlsegment[2]);
		// if(count($datasurattugas['pegawai_ditugaskan'])<=6)$marginbottom = 0;
		$this->TCPDF->SetAutoPageBreak(TRUE, $marginbottom );

		//set image scale factor
		$this->TCPDF->setImageScale(PDF_IMAGE_SCALE_RATIO);
		if(!isset($l))
            $l = [
                'a_meta_charset'=>'UTF-8',
                'a_meta_dir'=>'ltr',
                'a_meta_language'=>'id',
                'w_page'=>'halaman'
            ];
		//set some language-dependent strings
		$this->TCPDF->setLanguageArray($l);

		// ---------------------------------------------------------

		// set font
		$this->TCPDF->SetFont('helvetica', '', 12);

        $this->TCPDF->AddPage();
        // return $this;
    }

    public function download($html='', $outputFIleName='file.pdf'){
		$this->TCPDF->writeHTML($html, false, false, true, false, '');		
		//=========================================================================================
		$this->TCPDF->lastPage();
		//Close and output PDF document
		$this->TCPDF->Output($outputFIleName, 'I');
		die();
    }

    
    // Page footer
    public function SetFooter($footerText='') {
        // Position at 15 mm from bottom
        $this->TCPDF->SetY(-15);
        // Set font
        $this->TCPDF->SetFont('helvetica', '', 8);
        // Page number
        $this->TCPDF->Cell(0, 10, $footerText, 0, false, 'C', 0, '', 0, false, 'T', 'M');
    }

}