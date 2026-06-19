<?php

namespace App\Services;

use App\Models\Quotation;

class QuotationPdfService
{
    public function render(Quotation $quotation): string
    {
        $quotation->loadMissing(['lead', 'items']);

        $lines = [
            'Wooden Dad Design',
            'ใบเสนอราคาเฟอร์นิเจอร์ไม้สน',
            'เลขที่ใบเสนอราคา: '.$quotation->quotation_number,
            'สถานะ: '.$quotation->status_label,
            'วันที่: '.$quotation->created_at->format('Y-m-d'),
            '',
            'ลูกค้า: '.$quotation->lead->name,
            'เบอร์โทร: '.$quotation->lead->phone,
            'จังหวัด: '.$quotation->lead->province,
            'ขนาดห้อง: '.$quotation->lead->room_width.' x '.$quotation->lead->room_length.' เมตร',
            '',
            'รายการสินค้า',
        ];

        foreach ($quotation->items as $index => $item) {
            $lines[] = ($index + 1).'. '.$item->product_name;
            $lines[] = '   จำนวน: '.$item->quantity.'  ราคาต่อหน่วย: '.number_format((float) $item->unit_price, 2).'  ยอดรวม: '.number_format((float) $item->subtotal, 2);
        }

        $lines[] = '';
        $lines[] = 'ยอดรวมใบเสนอราคา: ฿'.number_format((float) $quotation->subtotal, 2);

        if ($quotation->notes) {
            $lines[] = '';
            $lines[] = 'หมายเหตุ: '.$quotation->notes;
        }

        $content = "BT\n/F1 18 Tf\n50 790 Td\n".$this->pdfText($lines[0])." Tj\n/F1 10 Tf\n0 -24 Td\n";

        foreach (array_slice($lines, 1) as $line) {
            $content .= $this->pdfText($line)." Tj\n0 -16 Td\n";
        }

        $content .= "ET";

        return $this->buildPdf($content);
    }

    private function pdfText(string $text): string
    {
        $text = str_replace(["\\", '(', ')'], ["\\\\", "\\(", "\\)"], $text);
        $text = preg_replace('/[^\x20-\x7E]/', '', $text) ?? '';

        return '('.$text.')';
    }

    private function buildPdf(string $content): string
    {
        $objects = [
            "1 0 obj\n<< /Type /Catalog /Pages 2 0 R >>\nendobj\n",
            "2 0 obj\n<< /Type /Pages /Kids [3 0 R] /Count 1 >>\nendobj\n",
            "3 0 obj\n<< /Type /Page /Parent 2 0 R /MediaBox [0 0 595 842] /Resources << /Font << /F1 4 0 R >> >> /Contents 5 0 R >>\nendobj\n",
            "4 0 obj\n<< /Type /Font /Subtype /Type1 /BaseFont /Helvetica >>\nendobj\n",
            "5 0 obj\n<< /Length ".strlen($content)." >>\nstream\n".$content."\nendstream\nendobj\n",
        ];

        $pdf = "%PDF-1.4\n";
        $offsets = [0];

        foreach ($objects as $object) {
            $offsets[] = strlen($pdf);
            $pdf .= $object;
        }

        $xref = strlen($pdf);
        $pdf .= "xref\n0 ".(count($objects) + 1)."\n";
        $pdf .= "0000000000 65535 f \n";

        for ($i = 1; $i <= count($objects); $i++) {
            $pdf .= str_pad((string) $offsets[$i], 10, '0', STR_PAD_LEFT)." 00000 n \n";
        }

        $pdf .= "trailer\n<< /Size ".(count($objects) + 1)." /Root 1 0 R >>\n";
        $pdf .= "startxref\n{$xref}\n%%EOF";

        return $pdf;
    }
}
