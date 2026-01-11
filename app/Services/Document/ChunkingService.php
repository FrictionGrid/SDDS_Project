<?php

namespace App\Services\Document;

class ChunkingService
{
    public function makeChunks(string $text, int $maxChars = 1200, int $overlapChars = 200): array
    {
        $text = trim($text);
        if ($text === '') return [];

        // Split by paragraph-ish blocks
        $blocks = preg_split("/\n\s*\n/u", $text) ?: [$text];
        $blocks = array_values(array_filter(array_map('trim', $blocks), fn ($b) => $b !== ''));

        $chunks = [];
        $buffer = '';

        foreach ($blocks as $block) {
            // If a single block is huge, split further by sentences
            if (mb_strlen($block) > $maxChars * 2) {
                $sentences = preg_split("/(?<=[\.\!\?\u0E2F\u0E46])\s+/u", $block) ?: [$block];
                foreach ($sentences as $s) {
                    $s = trim($s);
                    if ($s === '') continue;

                    $candidate = $buffer === '' ? $s : ($buffer . "\n" . $s);
                    if (mb_strlen($candidate) <= $maxChars) {
                        $buffer = $candidate;
                    } else {
                        if ($buffer !== '') {
                            $chunks[] = $buffer;
                            $buffer = $this->withOverlap($buffer, $s, $overlapChars);
                        } else {
                            // worst case: cut hard
                            $chunks[] = mb_substr($s, 0, $maxChars);
                            $buffer = mb_substr($s, max(0, $maxChars - $overlapChars));
                        }
                    }
                }
                continue;
            }

            $candidate = $buffer === '' ? $block : ($buffer . "\n\n" . $block);

            if (mb_strlen($candidate) <= $maxChars) {
                $buffer = $candidate;
                continue;
            }

            if ($buffer !== '') {
                $chunks[] = $buffer;
                $buffer = $this->withOverlap($buffer, $block, $overlapChars);
            } else {
                // single block larger than maxChars
                $chunks[] = mb_substr($block, 0, $maxChars);
                $buffer = mb_substr($block, max(0, $maxChars - $overlapChars));
            }
        }

        if (trim($buffer) !== '') {
            $chunks[] = trim($buffer);
        }

        // return structured chunks
        return array_values(array_map(fn ($c, $i) => [
            'chunk_index' => $i,
            'content' => trim($c),
        ], $chunks, array_keys($chunks)));
    }

    private function withOverlap(string $prevChunk, string $nextBlock, int $overlapChars): string
    {
        $tail = mb_substr($prevChunk, max(0, mb_strlen($prevChunk) - $overlapChars));
        $tail = trim($tail);

        $combined = $tail === '' ? $nextBlock : ($tail . "\n\n" . $nextBlock);
        return trim($combined);
    }
}
