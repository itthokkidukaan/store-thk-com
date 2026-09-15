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

    public function generate($items, $filename = 'labels.pdf', $columns = 2)
    {
        $columns = ($columns == 1) ? 1 : 2;
        $currency = (string)$this->CI->config->item('currency');
        $mpdf = $this->CI->pdf->load([
            'mode'          => 'utf-8',
            'format'        => [50 * $columns, 50],   // one 50mm sticker per column
            'margin_left'   => 0,
            'margin_right'  => 0,
            'margin_top'    => 0,
            'margin_bottom' => 0
        ]);

        $html = '<style>
            table { border-collapse: collapse; width: 100%; }
            td {
                width: 50mm;
                height: 50mm;
                border: 0.4mm solid #000;
                text-align: center;
                vertical-align: top;
                padding: 2mm;
                font-size: 8pt;
            }
            .product-name { font-weight: bold; font-size: 9pt; margin-bottom: 1mm; }
            .barcode { margin: 1mm 0; }
            .code { font-size: 8pt; margin: 1mm 0; }
            .meta { font-size: 7pt; line-height: 1.2; text-align: left; margin-top: 1mm; }
            .meta div { margin: 0.5mm 0; }
        </style>';

        $html .= '<table>';
        $count = 0;

        foreach ($items as $item) {
            if ($count % $columns == 0) $html .= '<tr>'; // हर $columns stickers के बाद नई row
            $name = isset($item['name']) ? (string)$item['name'] : '';
            $code = isset($item['code']) ? (string)$item['code'] : '';
            $uom = isset($item['uom']) ? trim((string)$item['uom']) : '';
            $weight = isset($item['weight']) ? trim((string)$item['weight']) : '';
            $net = $uom !== '' ? $uom : $weight;
            
            $price = isset($item['price']) ? (float)$item['price'] : 0.0;
            $seller = isset($item['seller']) ? trim((string)$item['seller']) : '';
            
            $price_text = $price > 0 ? htmlspecialchars($currency) . ' ' . number_format($price, 2) : 'N/A';
            $net_text = ($net !== '' && $net !== '0' && $net !== '0.00') ? htmlspecialchars($net) : 'N/A';
            $seller_display = ($seller !== '') ? htmlspecialchars($seller) : 'THOK KI DUKAN';

            $html .= "<td>
                        <!-- 1. Company Name -->
                        <div style='font-size: 9.5pt; font-weight: bold; margin-bottom: 0.5mm;'>THOK KI DUKAN</div>
                        
                        <!-- 2. Barcode -->
                        <div class='barcode' style='margin: 0.5mm 0;'>
                          <barcode code='".htmlspecialchars($code, ENT_QUOTES, 'UTF-8')."' type='C128B' size='0.85' height='0.9' />
                        </div>
                        
                        <!-- 3. Product SKU, Variant Name -->
                        <div style='font-size: 8pt; font-weight: bold; margin-bottom: 1mm; color: #333;'>
                          ".htmlspecialchars($code).", ".htmlspecialchars($name)." (".$net_text.")
                        </div>
                        
                        <!-- Meta Details -->
                        <div class='meta' style='font-size: 8pt; text-align: left; padding-left: 2mm;'>
                          <!-- 4. Price -->
                          <div style='margin-bottom: 0.5mm;'><strong>Price:</strong> ".$price_text."</div>
                          <!-- 5. Seller -->
                          <div style='margin-bottom: 0.5mm;'><strong>Seller:</strong> ".$seller_display."</div>
                          <!-- 6. FSSAI No (Fsi Code) -->
                          <div style='margin-bottom: 0.5mm;'><strong>FSSAI No-</strong>22624030001319</div>
                        </div>
                       </td>";

            if ($count % $columns == $columns - 1) $html .= '</tr>';
            $count++;
        }

        if ($count % $columns != 0) {
            $html .= str_repeat('<td></td>', $columns - ($count % $columns)) . '</tr>';
        }

        $html .= '</table>';

        $mpdf->WriteHTML($html);
        $mpdf->Output($filename,'I');
        exit;
    }
}
