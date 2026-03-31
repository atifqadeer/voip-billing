<?php

namespace App;

use TCPDF;

class CustomTCPDF extends TCPDF
{
    // Override the Footer method
    public function Footer()
    {
        // Position at 15 mm from the bottom
        $this->SetY(-15);

        // Set font
        $this->SetFont('helvetica', 'R', 8);

        // Footer content
        $footerText = 'Tranzcript Ltd (T/A iBSTEC ) with registered address at 124 City Road, London, EC1V 2NX. Registered in England 06613739';
        $this->writeHTMLCell(0, 0, '', '', $footerText, 0, 1, false, true, 'C', true);
    }
}
