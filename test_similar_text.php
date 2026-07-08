<?php
/**
 * Test script for understanding and testing PHP's similar_text() function.
 * 
 * Formula/Algorithm:
 * similar_text() uses Oliver's Algorithm (1993):
 * 1. Find the longest common substring between String A and String B.
 * 2. Recursively find the longest common substring in the remaining left parts.
 * 3. Recursively find the longest common substring in the remaining right parts.
 * 4. Sum the lengths of all matching parts.
 * 
 * The similarity percentage formula is:
 * Percent = (Total Matching Characters * 200) / (Length of String A + Length of String B)
 * Or: (Total Matching Characters * 2) / (Length of String A + Length of String B) * 100
 */

// Helper class to trace the algorithm step-by-step
class SimilarTextTracer {
    private $steps = [];

    public function trace($str1, $str2) {
        $this->steps = [];
        $matchingChars = $this->calculateSimilarity($str1, strlen($str1), $str2, strlen($str2), "");
        return [
            'matching_chars' => $matchingChars,
            'steps' => $this->steps
        ];
    }

    private function findLongestCommonSubstring($txt1, $txt2, $len1, $len2, &$pos1, &$pos2) {
        $max = 0;
        for ($p = 0; $p < $len1; $p++) {
            for ($q = 0; $q < $len2; $q++) {
                for ($l = 0; ($p + $l < $len1) && ($q + $l < $len2) && ($txt1[$p + $l] === $txt2[$q + $l]); $l++);
                if ($l > $max) {
                    $max = $l;
                    $pos1 = $p;
                    $pos2 = $q;
                }
            }
        }
        return $max;
    }

    private function calculateSimilarity($txt1, $len1, $txt2, $len2, $depthPrefix) {
        if ($len1 <= 0 || $len2 <= 0) {
            return 0;
        }

        $pos1 = 0;
        $pos2 = 0;
        $max = $this->findLongestCommonSubstring($txt1, $txt2, $len1, $len2, $pos1, $pos2);

        if ($max > 0) {
            $matchedSubstring = substr($txt1, $pos1, $max);
            $this->steps[] = "{$depthPrefix}Found match: \"{$matchedSubstring}\" (length $max) at index $pos1 in \"$txt1\" and index $pos2 in \"$txt2\"";

            // Recurse left
            $left1 = substr($txt1, 0, $pos1);
            $left2 = substr($txt2, 0, $pos2);
            $leftMatch = $this->calculateSimilarity($left1, strlen($left1), $left2, strlen($left2), $depthPrefix . "  [Left] ");

            // Recurse right
            $right1 = substr($txt1, $pos1 + $max);
            $right2 = substr($txt2, $pos2 + $max);
            $rightMatch = $this->calculateSimilarity($right1, strlen($right1), $right2, strlen($right2), $depthPrefix . "  [Right] ");

            return $max + $leftMatch + $rightMatch;
        }

        return 0;
    }
}

// Get arguments from CLI if provided, otherwise use default examples
$string1 = isset($argv[1]) ? $argv[1] : "apple";
$string2 = isset($argv[2]) ? $argv[2] : "aple";

echo "=== TESTING similar_text() ===\n";
echo "String 1: \"$string1\" (Length: " . strlen($string1) . ")\n";
echo "String 2: \"$string2\" (Length: " . strlen($string2) . ")\n\n";

// 1. PHP Built-in function
$builtin_chars = similar_text($string1, $string2, $builtin_percent);
echo "1. PHP Built-in similar_text() Result:\n";
echo "   - Matching characters: $builtin_chars\n";
echo "   - Similarity percentage: " . number_format($builtin_percent, 4) . " %\n\n";

// 2. Custom step-by-step tracer
echo "2. Step-by-Step Algorithm Trace:\n";
$tracer = new SimilarTextTracer();
$traceResult = $tracer->trace($string1, $string2);

foreach ($traceResult['steps'] as $step) {
    echo "   $step\n";
}

$matchingChars = $traceResult['matching_chars'];
$lenSum = strlen($string1) + strlen($string2);
$calcPercent = ($lenSum > 0) ? ($matchingChars * 200) / $lenSum : 0;

echo "\n3. Formula Verification:\n";
echo "   - Formula: (Matching Characters * 200) / (Length A + Length B)\n";
echo "   - Calculation: ($matchingChars * 200) / (" . strlen($string1) . " + " . strlen($string2) . ")\n";
echo "   - Calculation: " . ($matchingChars * 200) . " / " . $lenSum . "\n";
echo "   - Result: " . number_format($calcPercent, 4) . " %\n";

echo "\n------------------------------------------------------------\n";
echo "You can run this script with custom strings from terminal:\n";
echo "php test_similar_text.php \"your_first_string\" \"your_second_string\"\n";
echo "------------------------------------------------------------\n";
