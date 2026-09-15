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
            'format'        => [100, 50],   // 2 stickers (50mm left + 50mm right)
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
            if ($count % 2 == 0) $html .= '<tr>'; // हर 2 stickers के बाद नई row
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

    // One label per page, sized for the TVS LP46 Dlite's 4" x 4" (101.6mm) default label.
    // Used by chalan/print_single_barcode so single and multi-quantity prints both come out
    // as one correctly-sized label per page instead of being cropped to a half-width sticker.
    public function generate_one_per_page($items, $filename = 'labels.pdf')
    {
        require_once APPPATH . 'third_party/vendor/autoload.php';

        $currency = (string)$this->CI->config->item('currency');

        $mpdf = new \Mpdf\Mpdf([
            'tempDir'       => FCPATH . 'userfiles/temp/pdf',
            'mode'          => 'utf-8',
            'format'        => [101.6, 101.6],
            'margin_left'   => 0,
            'margin_right'  => 0,
            'margin_top'    => 0,
            'margin_bottom' => 0,
            'margin_header' => 0,
            'margin_footer' => 0
        ]);

        $html = '<style>
            .label {
                width: 101.6mm;
                height: 101.6mm;
                box-sizing: border-box;
                border: 0.4mm solid #000;
                text-align: center;
                padding: 4mm;
                font-size: 10pt;
            }
            .company { font-size: 14pt; font-weight: bold; margin-bottom: 2mm; }
            .barcode { margin: 2mm 0; }
            .sku { font-size: 12pt; font-weight: bold; margin: 2mm 0; color: #333; }
            .meta { font-size: 10.5pt; line-height: 1.4; text-align: left; padding-left: 4mm; margin-top: 2mm; }
            .meta div { margin: 1mm 0; }
        </style>';

        $count = 0;
        $total = count($items);

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
            $seller_display = ($seller !== '') ? htmlspecialchars($seller) : 'THOK KI DUKAN';

            $html .= "<div class='label'>
                        <div class='company'>THOK KI DUKAN</div>
                        <div class='barcode'>
                          <barcode code='".htmlspecialchars($code, ENT_QUOTES, 'UTF-8')."' type='C128B' size='1.3' height='1.4' />
                        </div>
                        <div class='sku'>".htmlspecialchars($code).", ".htmlspecialchars($name)." (".$net_text.")</div>
                        <div class='meta'>
                          <div><strong>Price:</strong> ".$price_text."</div>
                          <div><strong>Seller:</strong> ".$seller_display."</div>
                          <div><strong>FSSAI No-</strong>22624030001319</div>
                        </div>
                       </div>";

            $count++;
            if ($count < $total) {
                $html .= '<pagebreak />';
            }
        }

        $mpdf->WriteHTML($html);
        $mpdf->Output($filename, 'I');
        exit;
    }
}
