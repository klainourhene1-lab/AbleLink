<?php
namespace App\Core;

/**
 * Helper utilities for the application
 * These are standalone functions that don't require database access
 */
class Helpers {
    
    /**
     * Estimate reading time in minutes for a text
     */
    public static function estimateReadingMinutes(string $text): int {
        $words = str_word_count(strip_tags($text));
        $minutes = (int)ceil($words / 200);  // Average reading speed: 200 words/minute
        return max(1, $minutes);
    }
    
    /**
     * Extract keywords from text (French language support)
     */
    public static function extractKeywords(string $text, int $topN = 5): array {
        $text = mb_strtolower(strip_tags($text));
        $text = preg_replace('/[^a-zàâäçéèêëîïôöùûüÿ\s-]/u', ' ', $text);
        $parts = preg_split('/\s+/u', $text, -1, PREG_SPLIT_NO_EMPTY);
        
        // French stop words
        $stopWords = ['alors','au','aux','avec','ce','ces','dans','de','des','du','elle','en','et','eux','il','je','la','le','les','leur','lui','ma','mais','me','même','mes','moi','mon','ne','nos','notre','nous','on','ou','par','pas','pour','qu','que','qui','sa','se','ses','son','sur','ta','te','tes','toi','ton','tu','un','une','vos','votre','vous'];
        
        $freq = [];
        foreach ($parts as $word) {
            if (mb_strlen($word) < 4) continue;
            if (in_array($word, $stopWords, true)) continue;
            $freq[$word] = ($freq[$word] ?? 0) + 1;
        }
        
        arsort($freq);
        return array_slice(array_keys($freq), 0, $topN);
    }
}
