<?php

namespace App\Services\Icd10;

use App\Models\Icd10Code;
use DOMElement;
use RuntimeException;
use SimpleXMLElement;
use XMLReader;

class ClaMlIcd10Importer
{
    private const CHUNK_SIZE = 500;

    private const MAX_CHAPTER_DEPTH = 10;

    /**
     * Import every diagnosis code from a WHO ClaML-format ICD-10 XML file
     * into icd10_codes, keyed by code. Safe to run repeatedly (e.g. on every
     * container start): existing rows are updated in place and is_active is
     * never touched, so a code an admin has deactivated stays deactivated.
     *
     * @return array{chapters: int, blocks: int, categories: int, imported: int, skipped: int}
     */
    public function import(string $xmlPath): array
    {
        $classes = $this->parse($xmlPath);
        [$rows, $skipped] = $this->buildRows($classes);
        $imported = $this->persist($rows);

        $counts = ['chapter' => 0, 'block' => 0, 'category' => 0];
        foreach ($classes as $class) {
            $counts[$class['kind']] = ($counts[$class['kind']] ?? 0) + 1;
        }

        return [
            'chapters' => $counts['chapter'],
            'blocks' => $counts['block'],
            'categories' => $counts['category'],
            'imported' => $imported,
            'skipped' => $skipped,
        ];
    }

    /**
     * Stream the ClaML file and build an in-memory map of every <Class>
     * element (chapter, block, and category) keyed by its code. Classes
     * reference their parent via <SuperClass code="..."/> rather than XML
     * nesting, so this map lets us walk any category up to its chapter
     * afterwards regardless of the order classes appear in the file.
     *
     * @return array<string, array{kind: string, superclass: ?string, preferred: ?string, preferredLong: ?string}>
     */
    private function parse(string $xmlPath): array
    {
        if (! is_file($xmlPath)) {
            throw new RuntimeException("ICD-10 ClaML file not found at [{$xmlPath}].");
        }

        $reader = new XMLReader;

        if (! $reader->open($xmlPath)) {
            throw new RuntimeException("Unable to open ICD-10 ClaML file at [{$xmlPath}].");
        }

        $classes = [];

        while ($reader->read()) {
            if ($reader->nodeType !== XMLReader::ELEMENT || $reader->name !== 'Class') {
                continue;
            }

            $code = $reader->getAttribute('code');
            $kind = $reader->getAttribute('kind');

            if ($code === null || $kind === null) {
                continue;
            }

            $class = simplexml_load_string($reader->readOuterXML());

            if ($class === false) {
                continue;
            }

            $superclass = isset($class->SuperClass) ? (string) $class->SuperClass['code'] : null;
            [$preferred, $preferredLong] = $this->extractLabels($class);

            $classes[$code] = compact('kind', 'superclass', 'preferred', 'preferredLong');
        }

        $reader->close();

        return $classes;
    }

    /**
     * Pull the "preferred" and "preferredLong" rubric labels off a <Class>,
     * keeping only each Label's own direct text — nested <Reference> markup
     * (the dagger/asterisk cross-reference, e.g. "Amoebic liver
     * abscess†K77.0") is dropped so descriptions stay clean.
     *
     * @return array{0: ?string, 1: ?string}
     */
    private function extractLabels(SimpleXMLElement $class): array
    {
        $preferred = null;
        $preferredLong = null;

        foreach ($class->Rubric as $rubric) {
            $rubricKind = (string) $rubric['kind'];

            if (! isset($rubric->Label) || ! in_array($rubricKind, ['preferred', 'preferredLong'], true)) {
                continue;
            }

            $text = $this->directText($rubric->Label);

            if ($text === '') {
                continue;
            }

            if ($rubricKind === 'preferred') {
                $preferred = $text;
            } else {
                $preferredLong = $text;
            }
        }

        return [$preferred, $preferredLong];
    }

    private function directText(SimpleXMLElement $label): string
    {
        $dom = dom_import_simplexml($label);
        $text = '';

        foreach ($dom->childNodes as $node) {
            if ($node->nodeType === XML_TEXT_NODE) {
                /** @var DOMElement $node */
                $text .= $node->wholeText;
            }
        }

        return trim(preg_replace('/\s+/u', ' ', $text));
    }

    /**
     * @param  array<string, array{kind: string, superclass: ?string, preferred: ?string, preferredLong: ?string}>  $classes
     * @return array{0: list<array<string, mixed>>, 1: int}
     */
    private function buildRows(array $classes): array
    {
        $rows = [];
        $skipped = 0;
        $now = now();

        foreach ($classes as $code => $class) {
            if ($class['kind'] !== 'category') {
                continue;
            }

            $description = $class['preferredLong'] ?? $class['preferred'];

            if (empty($description)) {
                $skipped++;

                continue;
            }

            $rows[] = [
                'code' => $code,
                'description' => $description,
                'category' => $this->resolveChapterTitle($classes, $class['superclass']),
                'is_active' => true,
                'created_at' => $now,
                'updated_at' => $now,
            ];
        }

        return [$rows, $skipped];
    }

    /**
     * Walk a class's SuperClass chain up to its chapter and return the
     * chapter's title, e.g. "I10" -> "IX" -> "Diseases of the circulatory
     * system". Depth-limited as a guard against a malformed/circular file.
     *
     * @param  array<string, array{kind: string, superclass: ?string, preferred: ?string, preferredLong: ?string}>  $classes
     */
    private function resolveChapterTitle(array $classes, ?string $code, int $depth = 0): ?string
    {
        if ($code === null || $depth > self::MAX_CHAPTER_DEPTH || ! isset($classes[$code])) {
            return null;
        }

        $class = $classes[$code];

        if ($class['kind'] === 'chapter') {
            return $class['preferred'] ?? $class['preferredLong'];
        }

        return $this->resolveChapterTitle($classes, $class['superclass'], $depth + 1);
    }

    /** @param  list<array<string, mixed>>  $rows */
    private function persist(array $rows): int
    {
        $imported = 0;

        foreach (array_chunk($rows, self::CHUNK_SIZE) as $chunk) {
            Icd10Code::query()->upsert(
                $chunk,
                uniqueBy: ['code'],
                update: ['description', 'category', 'updated_at'],
            );
            $imported += count($chunk);
        }

        return $imported;
    }
}
