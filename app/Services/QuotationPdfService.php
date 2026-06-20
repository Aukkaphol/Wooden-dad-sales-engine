<?php

namespace App\Services;

use App\Models\Quotation;

class QuotationPdfService
{
    public function render(Quotation $quotation): string
    {
        $quotation->loadMissing(['lead', 'items']);
        $company = company();

        $lines = [
            $company->display_name,
            'Professional Custom Pine Wood Furniture Quotation',
            'Phone: '.($company->phone ?: '-'),
            'LINE: '.($company->line_oa_id ?: '-'),
            'Website: '.$company->website_display,
            'Address: '.($company->address ?: '-'),
            'Logo: '.($company->logo ?: '-'),
            '',
            'Quotation No: '.$quotation->display_number,
            'Status: '.$quotation->status_label,
            'Date: '.$quotation->created_at->format('Y-m-d'),
            'Valid Until: '.($quotation->valid_until?->format('Y-m-d') ?? '-'),
            '',
            'Customer: '.($quotation->customer_name ?: $quotation->lead->name),
            'Phone: '.($quotation->phone ?: $quotation->lead->phone),
            'Province: '.($quotation->province ?: $quotation->lead->province),
            'Project: '.($quotation->project_name ?: '-'),
            '',
            'Items',
        ];

        foreach ($quotation->items as $index => $item) {
            $lines[] = ($index + 1).'. '.$item->display_name;
            if ($item->description) {
                $lines[] = '   Description: '.$item->description;
            }
            $lines[] = '   Qty: '.number_format($item->display_quantity, 2).' '.$item->unit
                .' | Unit Price: '.number_format((float) $item->unit_price, 2)
                .' | Amount: '.number_format($item->display_total, 2);
        }

        $lines[] = '';
        $lines[] = 'Subtotal: '.number_format((float) $quotation->subtotal, 2);
        $lines[] = 'Discount: '.number_format((float) $quotation->discount, 2);
        $lines[] = 'Shipping: '.number_format((float) $quotation->shipping_cost, 2);
        $lines[] = 'Deposit: '.number_format((float) $quotation->deposit_amount, 2);
        $lines[] = 'Grand Total: '.number_format((float) ($quotation->grand_total ?: $quotation->subtotal), 2);
        $lines[] = 'Balance: '.number_format($quotation->balance, 2);

        if ($quotation->remark || $quotation->notes) {
            $lines[] = '';
            $lines[] = 'Remark: '.($quotation->remark ?: $quotation->notes);
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
