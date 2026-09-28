<?php

namespace App\Helpers;

use Dompdf\Canvas;
use Dompdf\FontMetrics;

/**
 * "Page x of y" in the letterhead footer's right-hand corner. dompdf cannot
 * resolve the CSS `counter(pages)` total (it printed "1 / 0"), so the text is
 * drawn onto each page after rendering, when the page count is known.
 */
class PdfPageNumbers
{
    /**
     * @return array<int, array{event: string, f: callable}>
     */
    public static function callbacks(): array
    {
        return [[
            'event' => 'end_document',
            'f' => function (int $pageNumber, int $pageCount, Canvas $canvas, FontMetrics $fontMetrics): void {
                $text = "Page {$pageNumber} of {$pageCount}";
                $font = $fontMetrics->getFont('DejaVu Sans');
                $size = 6.8;
                $width = $fontMetrics->getTextWidth($text, $font, $size);

                // Matches #letterhead-footer: 34px bottom band, 6px top padding, ~15px side margin.
                $canvas->text($canvas->get_width() - 12 - $width, $canvas->get_height() - 25, $text, $font, $size, [0.42, 0.45, 0.5]);
            },
        ]];
    }
}
