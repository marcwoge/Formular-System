<?php

class SimplePdfGenerator
{
    private $pageWidth = 595.28;
    private $pageHeight = 841.89;
    private $marginLeft = 50;
    private $marginRight = 50;
    private $marginTop = 50;
    private $marginBottom = 50;
    private $currentY = 0.0;
    private $pages = [];
    private $currentCommands = [];
    private $logoAsset = null;
    private $footerLines = [];

    public function renderSubmissionDocument(array $document): string
    {
        $this->pages = [];
        $this->currentCommands = [];
        $this->logoAsset = $this->loadLogoAsset($document['logo_path'] ?? null);
        $this->footerLines = is_array($document['footer_lines'] ?? null) ? $document['footer_lines'] : [];

        $this->startPage();

        $this->renderSummary($document['summary_left'] ?? [], $document['summary_right'] ?? []);
        $this->currentY -= 16;

        $rows = $document['rows'] ?? [];
        $this->renderTable($rows);

        $this->finishPage();

        return $this->buildPdf();
    }

    private function renderSummary(array $leftLines, array $rightLines): void
    {
        $leftX = $this->marginLeft;
        $rightX = 300;
        $lineHeight = 18;
        $topY = $this->pageHeight - $this->marginTop;
        $maxLines = max(count($leftLines), count($rightLines));
        $logoBottomY = $topY;

        if ($this->logoAsset !== null) {
            $logoPlacement = $this->calculateLogoPlacement($this->logoAsset);
            $this->addImage($logoPlacement['x'], $logoPlacement['y'], $logoPlacement['width'], $logoPlacement['height'], 'ImLogo');
            $logoBottomY = $logoPlacement['y'];
        }

        foreach ($leftLines as $index => $line) {
            $font = $index === 0 ? 'F2' : 'F1';
            $fontSize = $index === 0 ? 15 : 11;
            $this->addText($leftX, $topY - ($index * $lineHeight), $line, $font, $fontSize);
        }

        foreach ($rightLines as $index => $line) {
            $this->addText($rightX, $topY - ($index * $lineHeight), $line, 'F1', 11);
        }

        $summaryBottomY = $topY - ($maxLines * $lineHeight);
        $this->currentY = min($summaryBottomY, $logoBottomY) - 10;
        $this->addLine($this->marginLeft, $this->currentY, $this->pageWidth - $this->marginRight, $this->currentY);
        $this->currentY -= 20;
    }

    private function renderTable(array $rows): void
    {
        if (empty($rows)) {
            $rows = [
                ['label' => 'Hinweis', 'value' => 'Keine Formulardaten vorhanden.'],
            ];
        }

        $tableX = $this->marginLeft;
        $tableWidth = $this->pageWidth - $this->marginLeft - $this->marginRight;
        $labelWidth = 170;
        $valueWidth = $tableWidth - $labelWidth;

        $this->drawTableHeader($tableX, $labelWidth, $valueWidth);

        foreach ($rows as $row) {
            $labelLines = $this->wrapText((string) ($row['label'] ?? ''), 26);
            $valueLines = $this->wrapText((string) ($row['value'] ?? ''), 58);
            $lineCount = max(count($labelLines), count($valueLines), 1);
            $rowHeight = 8 + ($lineCount * 14);

            if (($this->currentY - $rowHeight) < $this->marginBottom) {
                $this->finishPage();
                $this->startPage();
                $this->drawTableHeader($tableX, $labelWidth, $valueWidth);
            }

            $topY = $this->currentY;
            $bottomY = $topY - $rowHeight;
            $dividerX = $tableX + $labelWidth;
            $tableEndX = $tableX + $labelWidth + $valueWidth;

            $this->addLine($tableX, $topY, $tableEndX, $topY);
            $this->addLine($tableX, $bottomY, $tableEndX, $bottomY);
            $this->addLine($tableX, $topY, $tableX, $bottomY);
            $this->addLine($dividerX, $topY, $dividerX, $bottomY);
            $this->addLine($tableEndX, $topY, $tableEndX, $bottomY);

            foreach ($labelLines as $index => $line) {
                $this->addText($tableX + 5, $topY - 14 - ($index * 14), $line, 'F1', 10);
            }

            foreach ($valueLines as $index => $line) {
                $this->addText($dividerX + 5, $topY - 14 - ($index * 14), $line, 'F1', 10);
            }

            $this->currentY = $bottomY;
        }
    }

    private function drawTableHeader(float $tableX, float $labelWidth, float $valueWidth): void
    {
        $headerHeight = 22;
        $topY = $this->currentY;
        $bottomY = $topY - $headerHeight;
        $dividerX = $tableX + $labelWidth;
        $tableEndX = $tableX + $labelWidth + $valueWidth;

        $this->addLine($tableX, $topY, $tableEndX, $topY);
        $this->addLine($tableX, $bottomY, $tableEndX, $bottomY);
        $this->addLine($tableX, $topY, $tableX, $bottomY);
        $this->addLine($dividerX, $topY, $dividerX, $bottomY);
        $this->addLine($tableEndX, $topY, $tableEndX, $bottomY);

        $this->addText($tableX + 5, $topY - 15, 'Feld', 'F2', 10);
        $this->addText($dividerX + 5, $topY - 15, 'Wert', 'F2', 10);

        $this->currentY = $bottomY;
    }

    private function wrapText(string $text, int $maxChars): array
    {
        $text = trim((string) preg_replace('/\s+/', ' ', $text));
        if ($text === '') {
            return ['-'];
        }

        $wrapped = wordwrap($text, $maxChars, "\n", true);
        $lines = preg_split('/\r\n|\r|\n/', $wrapped);

        return $lines === false ? [$text] : $lines;
    }

    private function startPage(): void
    {
        $this->currentCommands = [];
        $this->currentY = $this->pageHeight - $this->marginTop;
    }

    private function finishPage(): void
    {
        if (!empty($this->currentCommands)) {
            $this->renderFooter();
            $this->pages[] = implode("\n", $this->currentCommands);
        }
    }

    private function renderFooter(): void
    {
        if (empty($this->footerLines)) {
            return;
        }

        $lineY = $this->marginBottom - 10;
        $textY = $lineY - 12;
        $centerX = $this->pageWidth / 2;
        $lineHeight = 11;

        $this->addLine($this->marginLeft, $lineY, $this->pageWidth - $this->marginRight, $lineY);

        foreach ($this->footerLines as $index => $line) {
            $estimatedWidth = strlen($this->convertToPdfEncoding($line)) * 2.7;
            $x = max($this->marginLeft, $centerX - ($estimatedWidth / 2));
            $this->addText($x, $textY - ($index * $lineHeight), $line, 'F1', 8);
        }
    }

    private function calculateLogoPlacement(array $asset): array
    {
        $maxWidth = 80.0;
        $maxHeight = 40.0;
        $scale = min($maxWidth / $asset['width'], $maxHeight / $asset['height'], 1.0);
        $displayWidth = round($asset['width'] * $scale, 2);
        $displayHeight = round($asset['height'] * $scale, 2);

        return [
            'x' => round($this->pageWidth - $this->marginRight - $displayWidth, 2),
            'y' => round($this->pageHeight - $this->marginTop - $displayHeight + 4, 2),
            'width' => $displayWidth,
            'height' => $displayHeight,
        ];
    }

    private function addText(float $x, float $y, string $text, string $fontName = 'F1', int $fontSize = 11): void
    {
        $text = $this->convertToPdfEncoding($text);
        $text = $this->escapePdfText($text);

        $this->currentCommands[] = "BT /{$fontName} {$fontSize} Tf 1 0 0 1 {$x} {$y} Tm ({$text}) Tj ET";
    }

    private function addLine(float $x1, float $y1, float $x2, float $y2): void
    {
        $this->currentCommands[] = "{$x1} {$y1} m {$x2} {$y2} l S";
    }

    private function addImage(float $x, float $y, float $width, float $height, string $imageName): void
    {
        $this->currentCommands[] = "q {$width} 0 0 {$height} {$x} {$y} cm /{$imageName} Do Q";
    }

    private function buildPdf(): string
    {
        if (empty($this->pages)) {
            $this->pages[] = '';
        }

        $objects = [];
        $pageObjectIds = [];
        $fontRegularObjectId = 3;
        $fontBoldObjectId = 4;
        $nextObjectId = 5;
        $logoObjectId = null;
        $logoMaskObjectId = null;

        if ($this->logoAsset !== null) {
            if (isset($this->logoAsset['smask'])) {
                $logoMaskObjectId = $nextObjectId++;
                $objects[$logoMaskObjectId] = $this->buildImageObject($this->logoAsset['smask']);
            }

            $logoObjectId = $nextObjectId++;
            $objects[$logoObjectId] = $this->buildImageObject($this->logoAsset, $logoMaskObjectId);
        }

        foreach ($this->pages as $pageContent) {
            $contentObjectId = $nextObjectId++;
            $pageObjectId = $nextObjectId++;

            $objects[$contentObjectId] = "<< /Length " . strlen($pageContent) . " >>\nstream\n" . $pageContent . "\nendstream";

            $resourceDictionary = "/Font << /F1 {$fontRegularObjectId} 0 R /F2 {$fontBoldObjectId} 0 R >>";
            if ($logoObjectId !== null) {
                $resourceDictionary .= " /XObject << /ImLogo {$logoObjectId} 0 R >>";
            }

            $objects[$pageObjectId] = "<< /Type /Page /Parent 2 0 R /MediaBox [0 0 {$this->pageWidth} {$this->pageHeight}] /Resources << {$resourceDictionary} >> /Contents {$contentObjectId} 0 R >>";
            $pageObjectIds[] = $pageObjectId;
        }

        $kids = implode(' ', array_map(static function ($id) {
            return $id . ' 0 R';
        }, $pageObjectIds));

        $objects[1] = '<< /Type /Catalog /Pages 2 0 R >>';
        $objects[2] = '<< /Type /Pages /Count ' . count($pageObjectIds) . " /Kids [ {$kids} ] >>";
        $objects[3] = '<< /Type /Font /Subtype /Type1 /BaseFont /Helvetica /Encoding /WinAnsiEncoding >>';
        $objects[4] = '<< /Type /Font /Subtype /Type1 /BaseFont /Helvetica-Bold /Encoding /WinAnsiEncoding >>';

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
        $pdf .= '%%EOF';

        return $pdf;
    }

    private function buildImageObject(array $asset, ?int $smaskObjectId = null): string
    {
        $dictionary = [
            '/Type /XObject',
            '/Subtype /Image',
            '/Width ' . $asset['width'],
            '/Height ' . $asset['height'],
            '/ColorSpace /' . $asset['color_space'],
            '/BitsPerComponent ' . $asset['bits_per_component'],
            '/Filter /' . $asset['filter'],
        ];

        if (!empty($asset['decode_params'])) {
            $dictionary[] = '/DecodeParms << ' . $asset['decode_params'] . ' >>';
        }

        if ($smaskObjectId !== null) {
            $dictionary[] = '/SMask ' . $smaskObjectId . ' 0 R';
        }

        $dictionary[] = '/Length ' . strlen($asset['data']);

        return '<< ' . implode(' ', $dictionary) . " >>\nstream\n" . $asset['data'] . "\nendstream";
    }

    private function loadLogoAsset(?string $path): ?array
    {
        if ($path === null || !file_exists($path)) {
            return null;
        }

        $imageInfo = @getimagesize($path);
        if ($imageInfo === false) {
            return null;
        }

        if ($imageInfo['mime'] === 'image/jpeg') {
            return $this->loadJpegAsset($path, $imageInfo);
        }

        if ($imageInfo['mime'] === 'image/png') {
            return $this->loadPngAsset($path);
        }

        return null;
    }

    private function loadJpegAsset(string $path, array $imageInfo): ?array
    {
        $data = @file_get_contents($path);
        if ($data === false) {
            return null;
        }

        return [
            'width' => (int) $imageInfo[0],
            'height' => (int) $imageInfo[1],
            'color_space' => 'DeviceRGB',
            'bits_per_component' => 8,
            'filter' => 'DCTDecode',
            'decode_params' => null,
            'data' => $data,
        ];
    }

    private function loadPngAsset(string $path): ?array
    {
        $data = @file_get_contents($path);
        if ($data === false || substr($data, 0, 8) !== "\x89PNG\x0D\x0A\x1A\x0A") {
            return null;
        }

        $offset = 8;
        $width = 0;
        $height = 0;
        $bitDepth = 8;
        $colorType = null;
        $idat = '';

        while ($offset < strlen($data)) {
            $length = unpack('N', substr($data, $offset, 4))[1];
            $offset += 4;
            $chunkType = substr($data, $offset, 4);
            $offset += 4;
            $chunkData = substr($data, $offset, $length);
            $offset += $length + 4;

            if ($chunkType === 'IHDR') {
                $header = unpack('Nwidth/Nheight/CbitDepth/CcolorType/Ccompression/Cfilter/Cinterlace', $chunkData);
                $width = (int) $header['width'];
                $height = (int) $header['height'];
                $bitDepth = (int) $header['bitDepth'];
                $colorType = (int) $header['colorType'];
            } elseif ($chunkType === 'IDAT') {
                $idat .= $chunkData;
            } elseif ($chunkType === 'IEND') {
                break;
            }
        }

        if ($width <= 0 || $height <= 0 || $bitDepth !== 8 || $idat === '' || !in_array($colorType, [2, 6], true)) {
            return null;
        }

        if ($colorType === 2) {
            return [
                'width' => $width,
                'height' => $height,
                'color_space' => 'DeviceRGB',
                'bits_per_component' => 8,
                'filter' => 'FlateDecode',
                'decode_params' => '/Predictor 15 /Colors 3 /BitsPerComponent 8 /Columns ' . $width,
                'data' => $idat,
            ];
        }

        $decoded = function_exists('zlib_decode') ? @zlib_decode($idat) : @gzuncompress($idat);
        if ($decoded === false) {
            return null;
        }

        $bytesPerPixel = 4;
        $rowLength = $width * $bytesPerPixel;
        $rgbStream = '';
        $alphaStream = '';
        $offset = 0;
        $previousLine = str_repeat("\x00", $rowLength);

        for ($row = 0; $row < $height; $row++) {
            $filterType = ord($decoded[$offset]);
            $offset++;

            $scanline = substr($decoded, $offset, $rowLength);
            $offset += $rowLength;

            $unfiltered = $this->unfilterPngScanline($filterType, $scanline, $previousLine, $bytesPerPixel);
            $previousLine = $unfiltered;

            $rgbLine = "\x00";
            $alphaLine = "\x00";

            for ($i = 0; $i < $rowLength; $i += 4) {
                $rgbLine .= $unfiltered[$i] . $unfiltered[$i + 1] . $unfiltered[$i + 2];
                $alphaLine .= $unfiltered[$i + 3];
            }

            $rgbStream .= $rgbLine;
            $alphaStream .= $alphaLine;
        }

        return [
            'width' => $width,
            'height' => $height,
            'color_space' => 'DeviceRGB',
            'bits_per_component' => 8,
            'filter' => 'FlateDecode',
            'decode_params' => '/Predictor 15 /Colors 3 /BitsPerComponent 8 /Columns ' . $width,
            'data' => gzcompress($rgbStream),
            'smask' => [
                'width' => $width,
                'height' => $height,
                'color_space' => 'DeviceGray',
                'bits_per_component' => 8,
                'filter' => 'FlateDecode',
                'decode_params' => '/Predictor 15 /Colors 1 /BitsPerComponent 8 /Columns ' . $width,
                'data' => gzcompress($alphaStream),
            ],
        ];
    }

    private function unfilterPngScanline(int $filterType, string $scanline, string $previousLine, int $bytesPerPixel): string
    {
        $length = strlen($scanline);
        $result = '';

        for ($i = 0; $i < $length; $i++) {
            $raw = ord($scanline[$i]);
            $left = $i >= $bytesPerPixel ? ord($result[$i - $bytesPerPixel]) : 0;
            $up = isset($previousLine[$i]) ? ord($previousLine[$i]) : 0;
            $upperLeft = $i >= $bytesPerPixel && isset($previousLine[$i - $bytesPerPixel]) ? ord($previousLine[$i - $bytesPerPixel]) : 0;

            switch ($filterType) {
                case 0:
                    $value = $raw;
                    break;
                case 1:
                    $value = ($raw + $left) & 0xFF;
                    break;
                case 2:
                    $value = ($raw + $up) & 0xFF;
                    break;
                case 3:
                    $value = ($raw + intdiv($left + $up, 2)) & 0xFF;
                    break;
                case 4:
                    $value = ($raw + $this->paethPredictor($left, $up, $upperLeft)) & 0xFF;
                    break;
                default:
                    $value = $raw;
                    break;
            }

            $result .= chr($value);
        }

        return $result;
    }

    private function paethPredictor(int $left, int $up, int $upperLeft): int
    {
        $p = $left + $up - $upperLeft;
        $pa = abs($p - $left);
        $pb = abs($p - $up);
        $pc = abs($p - $upperLeft);

        if ($pa <= $pb && $pa <= $pc) {
            return $left;
        }

        if ($pb <= $pc) {
            return $up;
        }

        return $upperLeft;
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
