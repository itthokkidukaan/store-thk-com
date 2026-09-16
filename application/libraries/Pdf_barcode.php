<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Pdf_barcode
{
    protected $CI;
    public function __construct()
    {
        $this->CI =& get_instance();
        $this->CI->load->library('pdf'); // mPDF wrapper
    }

    public function generate($items, $filename = 'labels.pdf')
    {
        $currency = (string)$this->CI->config->item('currency');
        $mpdf = $this->CI->pdf->load([
            'mode'          => 'utf-8',
            'format'        => [50, 50],
            'margin_left'   => 0,
            'margin_right'  => 0,
            'margin_top'    => 0,
            'margin_bottom' => 0
        ]);

        $html = '<style>
            .label {
                width: 50mm;
                height: 50mm;
                border: 0.4mm solid #000;
                text-align: center;
                vertical-align: middle;
                padding: 2mm;
                font-size: 8pt;
                page-break-after: always;
                box-sizing: border-box;
            }
            .label:last-child { page-break-after: auto; }
            .company { font-size: 9.5pt; font-weight: bold; margin-bottom: 0.5mm; }
            .barcode { margin: 0.5mm 0; }
            .product-line { font-size: 8pt; font-weight: bold; margin-bottom: 1mm; color: #333; }
            .meta { display: inline-block; font-size: 8pt; line-height: 1.2; text-align: left; margin-top: 1mm; }
            .meta div { margin: 0.5mm 0; }
        </style>';

        foreach ($items as $item) {
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

            $html .= "<div class='label'>
                        <div style='font-size: 9.5pt; font-weight: bold; margin-bottom: 0.5mm;'>THOK KI DUKAN</div>
                        <div class='barcode' style='margin: 0.5mm 0;'>
                          <barcode code='".htmlspecialchars($code, ENT_QUOTES, 'UTF-8')."' type='C128B' size='0.85' height='0.9' />
                        </div>
                        <div style='font-size: 8pt; font-weight: bold; margin-bottom: 1mm; color: #333;'>
                          ".htmlspecialchars($code).", ".htmlspecialchars($name)." (".$net_text.")
                        </div>
                        <div class='meta' style='font-size: 8pt; text-align: left;'>
                          <div style='margin-bottom: 0.5mm;'><strong>Price:</strong> ".$price_text."</div>
                          <div style='margin-bottom: 0.5mm;'><strong>Seller:</strong> ".$seller_display."</div>
                          <div style='margin-bottom: 0.5mm;'><strong>FSSAI No-</strong>22624030001319</div>
                        </div>
                      </div>";
        }

        $mpdf->WriteHTML($html);
        $mpdf->Output($filename,'I');
        exit;
    }
}
