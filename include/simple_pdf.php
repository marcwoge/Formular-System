<?php

class SimplePdfGenerator
{
    private $pageWidth = 595.28;
    private $pageHeight = 841.89;
    private $margin = 50;
    private $lineHeight = 16;
    private $fontSize = 11;
    private $maxCharsPerLine = 95;

    public function render(array $lines): string
    {
        $normalizedLines = $this->normalizeLines($lines);
        if (empty($normalizedLines)) {
            $normalizedLines = ['Keine Formulardaten vorhanden.'];
        }

        $maxLinesPerPage = max(1, (int) floor(($this->pageHeight - (2 * $this->margin)) / $this->lineHeight));
        $pages = array_chunk($normalizedLines, $maxLinesPerPage);

        $objects = [];
        $pageObjectIds = [];
        $fontObjectId = 3;
        $nextObjectId = 4;

        foreach ($pages as $pageLines) {
            $content = $this->buildPageContent($pageLines);
            $contentObjectId = $nextObjectId++;
            $pageObjectId = $nextObjectId++;

            $objects[$contentObjectId] = "<< /Length " . strlen($content) . " >>\nstream\n" . $content . "\nendstream";
            $objects[$pageObjectId] = "<< /Type /Page /Parent 2 0 R /MediaBox [0 0 {$this->pageWidth} {$this->pageHeight}] /Resources << /Font << /F1 {$fontObjectId} 0 R >> >> /Contents {$contentObjectId} 0 R >>";
            $pageObjectIds[] = $pageObjectId;
        }

        $kids = implode(' ', array_map(static function ($id) {
            return $id . ' 0 R';
        }, $pageObjectIds));

        $objects[1] = "<< /Type /Catalog /Pages 2 0 R >>";
        $objects[2] = "<< /Type /Pages /Count " . count($pageObjectIds) . " /Kids [ {$kids} ] >>";
        $objects[3] = "<< /Type /Font /Subtype /Type1 /BaseFont /Helvetica >>";

        ksort($objects);

        $pdf = "%PDF-1.4\n";
        $offsets = [0];

        foreach ($objects as $id => $objectContent) {
            $offsets[$id] = strlen($pdf);
            $pdf .= $id . " 0 obj\n" . $objectContent . "\nendobj\n";
        }

        $xrefOffset = strlen($pdf);
        $maxObjectId = max(array_keys($objects));

        $pdf .= "xref\n";
        $pdf .= "0 " . ($maxObjectId + 1) . "\n";
        $pdf .= "0000000000 65535 f \n";

        for ($i = 1; $i <= $maxObjectId; $i++) {
            $offset = $offsets[$i] ?? 0;
            $pdf .= sprintf("%010d 00000 n \n", $offset);
        }

        $pdf .= "trailer\n";
        $pdf .= "<< /Size " . ($maxObjectId + 1) . " /Root 1 0 R >>\n";
        $pdf .= "startxref\n";
        $pdf .= $xrefOffset . "\n";
        $pdf .= "%%EOF";

        return $pdf;
    }

    private function normalizeLines(array $lines): array
    {
        $result = [];

        foreach ($lines as $line) {
            $line = trim((string) $line);
            if ($line === '') {
                $result[] = '';
                continue;
            }

            $line = preg_replace('/\s+/', ' ', $line);
            $wrappedLines = preg_split('/\r\n|\r|\n/', wordwrap($line, $this->maxCharsPerLine, "\n", true));

            foreach ($wrappedLines as $wrappedLine) {
                $result[] = $wrappedLine;
            }
        }

        return $result;
    }

    private function buildPageContent(array $lines): string
    {
        $startY = $this->pageHeight - $this->margin;
        $commands = [
            'BT',
            '/F1 ' . $this->fontSize . ' Tf',
            '1 0 0 1 ' . $this->margin . ' ' . $startY . ' Tm',
        ];

        foreach ($lines as $index => $line) {
            if ($index > 0) {
                $commands[] = '0 -' . $this->lineHeight . ' Td';
            }
            $commands[] = '(' . $this->escapePdfText($this->convertToPdfEncoding($line)) . ') Tj';
        }

        $commands[] = 'ET';

        return implode("\n", $commands);
    }

    private function convertToPdfEncoding(string $text): string
    {
        $converted = iconv('UTF-8', 'Windows-1252//TRANSLIT', $text);

        if ($converted === false) {
            return utf8_decode($text);
        }

        return $converted;
    }

    private function escapePdfText(string $text): string
    {
        return str_replace(
            ['\\', '(', ')'],
            ['\\\\', '\(', '\)'],
            $text
        );
    }
}
