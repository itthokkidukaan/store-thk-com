<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Pdf_barcode
{
    protected $CI;
    public function __construct()
    {
        $this->CI =& get_instance();
        $this->CI->load->library('pdf');
    }

    public function generate($items, $filename = 'labels.pdf')
    {
        $currency = (string)$this->CI->config->item('currency');
        $mpdf = $this->CI->pdf->load([
            'mode'          => 'utf-8',
            'format'        => [100, 50],
            'margin_left'   => 0,
            'margin_right'  => 0,
            'margin_top'    => 0,
            'margin_bottom' => 0
        ]);

        $html = '<style>
            @page { margin: 0; }
            table { border-collapse: collapse; width: 100%; table-layout: fixed; }
            tr { page-break-after: always; }
            tr:last-child { page-break-after: auto; }
            td {
                width: 50mm;
                height: 50mm;
                border: none;
                text-align: center;
                vertical-align: top;
                padding: 2mm 2mm 2mm 2mm;
                margin: 0;
                overflow: hidden;
            }
            .label-store { font-size: 9pt; font-weight: bold; margin: 0 0 1mm 0; line-height: 1; }
            .label-barcode { margin: 1mm 0; }
            .label-code { font-size: 8pt; font-weight: bold; margin: 0.5mm 0 0.5mm 0; line-height: 1; letter-spacing: 0.5px; }
            .label-name { font-size: 7.5pt; margin: 0.3mm 0; line-height: 1.1; }
            .label-meta { font-size: 6.5pt; text-align: left; margin-top: 0.8mm; line-height: 1.25; }
            .label-meta div { margin: 0.2mm 0; }
            .label-meta strong { font-weight: bold; }
        </style>';

        $html .= '<table>';
        $count = 0;

        foreach ($items as $item) {
            if ($count % 2 == 0) $html .= '<tr>';

            $name = isset($item['name']) ? (string)$item['name'] : '';
            $code = isset($item['code']) ? (string)$item['code'] : '';
            $uom = isset($item['uom']) ? trim((string)$item['uom']) : '';
            $weight = isset($item['weight']) ? trim((string)$item['weight']) : '';
            $net = $uom !== '' ? $uom : $weight;

            $price = isset($item['price']) ? (float)$item['price'] : 0.0;
            $seller = isset($item['seller']) ? trim((string)$item['seller']) : '';

            $price_text = $price > 0 ? htmlspecialchars($currency) . ' ' . number_format($price, 2) : 'N/A';
            $net_text = ($net !== '' && $net !== '0' && $net !== '0.00') ? htmlspecialchars($net) : 'N/A';
            $seller_display = ($seller !== '') ? htmlspecialchars($seller) : 'THOK KI DUKAN VEGETABLE STORE';

            $html .= "<td>";
            $html .= "<div class='label-store'>THOK KI DUKAN</div>";
            $html .= "<div class='label-barcode'>";
            $html .= "<barcode code='".htmlspecialchars($code, ENT_QUOTES, 'UTF-8')."' type='C128B' size='1.0' height='14' />";
            $html .= "</div>";
            $html .= "<div class='label-code'>".htmlspecialchars($code)."</div>";
            $html .= "<div class='label-name'>".htmlspecialchars($name)." (".$net_text.")</div>";
            $html .= "<div class='label-meta'>";
            $html .= "<div><strong>Price:</strong> ".$price_text."</div>";
            $html .= "<div><strong>Seller:</strong> ".$seller_display."</div>";
            $html .= "<div><strong>FSSAI No:</strong> 22624030001319</div>";
            $html .= "</div>";
            $html .= "</td>";

            if ($count % 2 == 1) $html .= '</tr>';
            $count++;
        }

        if ($count % 2 != 0) {
            $html .= '<td></td></tr>';
        }

        $html .= '</table>';

        $mpdf->WriteHTML($html);
        $mpdf->Output($filename,'I');
        exit;
    }
}
