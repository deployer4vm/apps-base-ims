<?php

require_once(dirname(__FILE__).'/tcpdf.php');

class MYPDF extends TCPDF {

    // Page footer
    public function Footer() {
        // Position at 15 mm from bottom
        $this->SetY(-15);
        // Set font
        $this->SetFont('helvetica', '', 8);
        // Page number
        $this->Cell(0, 10, 'SOME FOOTER TEXT', 0, false, 'C', 0, '', 0, false, 'T', 'M');
    }

}
