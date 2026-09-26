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

    protected function labelMarkup($item, $currency)
    {
        if (!$item || !is_array($item)) {
            return '<div class="label-cell-inner">&nbsp;</div>';
        }
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

        $barcode = '';
        if ($code !== '') {
            $barcode =
                '<table class="barcode-table" align="center">' .
                    '<tr>' .
                        '<td align="center" valign="middle">' .
                            '<barcode code="'.htmlspecialchars($code, ENT_QUOTES, 'UTF-8').'" type="C128B" size="0.33" height="6.5" text="0" padding_left="0.5" padding_right="0.5" padding_top="0" padding_bottom="0" />' .
                        '</td>' .
                    '</tr>' .
                '</table>';
        }

        return
            '<table class="label-table">' .
                '<tr>' .
                    '<td class="label-cell-inner">' .
                        '<div class="label-store">THOK KI DUKAN</div>'.
                        $barcode.
                        '<div class="label-code">'.htmlspecialchars($code).'</div>'.
                        '<div class="label-name">'.htmlspecialchars($name).' ('.$net_text.')</div>'.
                        '<div class="label-meta">' .
                            '<div><strong>Price:</strong> '.$price_text.'</div>'.
                            '<div><strong>Seller:</strong> '.$seller_display.'</div>'.
                            '<div><strong>FSSAI No:</strong> 22624030001319</div>'.
                        '</div>'.
                    '</td>' .
                '</tr>' .
            '</table>';
    }

    public function generate($items, $filename = 'labels.pdf')
    {
        $currency = (string)$this->CI->config->item('currency');

        $items = is_array($items) ? array_values($items) : [];
        $total = count($items);
        if ($total <= 0) {
            $items = [null];
            $total = 1;
        }
        if ($total > 10000) {
            $items = array_slice($items, 0, 10000);
            $total = 10000;
        }
        if ($total % 2 !== 0) {
            $items[] = null;
            $total++;
        }

        $mpdf = $this->CI->pdf->load([
            'mode'          => 'utf-8',
            'format'        => [100, 50],
            'margin_left'   => 0,
            'margin_right'  => 0,
            'margin_top'    => 0,
            'margin_bottom' => 0
        ]);

        $headCss = '<style>
            @page {
                sheet-size: 100mm 50mm;
                margin: 0;
            }
            html, body {
                width: 100mm;
                height: 50mm;
                margin: 0;
                padding: 0;
            }
            .labels-table {
                border-collapse: collapse;
                table-layout: fixed;
                width: 100mm;
                height: 50mm;
                margin: 0;
                padding: 0;
                page-break-inside: avoid;
            }
            .labels-table > tbody > tr,
            .labels-table > tr {
                page-break-inside: avoid;
                height: 50mm;
                margin: 0;
                padding: 0;
            }
            .label-cell {
                width: 50mm;
                height: 50mm;
                border: none;
                margin: 0;
                padding: 1.5mm 1mm 1.5mm 1mm;
                vertical-align: middle;
                text-align: center;
            }
            .label-table {
                border-collapse: collapse;
                table-layout: fixed;
                width: 48mm;
                height: 47mm;
                margin: 0 auto;
                padding: 0;
                page-break-inside: avoid;
            }
            .label-table > tbody > tr > td,
            .label-table > tr > td {
                vertical-align: middle;
                text-align: center;
                margin: 0;
                padding: 0.5mm 1mm 0.5mm 1mm;
            }
            .label-cell-inner {
                width: 100%;
                height: 100%;
                margin: 0 auto;
                padding: 0;
                box-sizing: border-box;
                overflow: auto;
                page-break-inside: avoid;
                display: block;
                vertical-align: middle;
            }
            .label-store {
                width: 100%;
                font-size: 7.5pt;
                font-weight: bold;
                margin: 0 0 0.6mm 0;
                padding: 0;
                line-height: 1;
                text-align: center;
                display: block;
                clear: both;
            }
            .barcode-table {
                border-collapse: collapse;
                table-layout: fixed;
                width: 100%;
                max-width: 38mm;
                height: 8mm;
                margin: 0.3mm auto 0.4mm auto;
                padding: 0;
            }
            .barcode-table > tbody > tr > td,
            .barcode-table > tr > td {
                width: 100%;
                height: 8mm;
                margin: 0;
                padding: 0;
                border: none;
                text-align: center;
                vertical-align: middle;
                overflow: hidden;
            }
            .label-code {
                width: 100%;
                font-size: 6.5pt;
                font-weight: bold;
                margin: 0 0 0.5mm 0;
                padding: 0;
                line-height: 1;
                letter-spacing: 0.3px;
                text-align: center;
                display: block;
                clear: both;
            }
            .label-name {
                width: 100%;
                font-size: 6.4pt;
                margin: 0 0 0.5mm 0;
                padding: 0 0.5mm;
                line-height: 1.2;
                text-align: center;
                display: block;
                clear: both;
                box-sizing: border-box;
            }
            .label-meta {
                width: 100%;
                font-size: 5.5pt;
                margin: 0.2mm auto 0 auto;
                padding: 0 0.5mm;
                line-height: 1.25;
                text-align: left;
                display: block;
                clear: both;
                box-sizing: border-box;
            }
            .label-meta div {
                width: 100%;
                margin: 0;
                padding: 0;
                display: block;
                clear: both;
                text-align: left;
            }
            .label-meta strong { font-weight: bold; }
        </style>';

        $mpdf->WriteHTML($headCss);

        $pageCount = (int)ceil($total / 2);
        for ($p = 0; $p < $pageCount; $p++) {
            $left = $items[2 * $p] ?? null;
            $right = $items[2 * $p + 1] ?? null;

            if ($p > 0) {
                $mpdf->AddPage();
            }

            $html = '<table class="labels-table"><tr>';
            $html .= '<td class="label-cell">' . $this->labelMarkup($left, $currency) . '</td>';
            $html .= '<td class="label-cell">' . $this->labelMarkup($right, $currency) . '</td>';
            $html .= '</tr></table>';
            $mpdf->WriteHTML($html);
        }

        $mpdf->Output($filename,'I');
        exit;
    }
}
