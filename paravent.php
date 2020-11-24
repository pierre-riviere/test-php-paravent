<?php

const MIN_WIDTH = 1;
const MAX_WIDTH = 100000;
const MIN_HEIGHT = 0;
const MAX_HEIGHT = 100000;
const NB_CHARS = 10000;

$width = null;
$heights = null;

/**
 * Check width
 * @param String $width
 * @return Boolean
 */
function validateWidth($width): bool {
    if (!preg_match("/^[0-9]+$/",$width)) { 
        return false;
    }
    $width = (int)$width;
    return $width >= MIN_WIDTH && $width <= MAX_WIDTH;
}

/**
 * Replace multiple spaces by single space into the given values string then return values in array
 * @param String $value
 * @return String
 */
function explodeHelper($value): array {
    return explode(' ', preg_replace("/\s+/", " ", trim($value)));
}

/**
 * Check heights
 * @param String $heights
 * @return Boolean
 */
function validateHeights($heights): bool {
    return preg_match("/^[0-9 ]+$/",$heights);
}

/**
 * Get microtime float
 * @return Float
 */
function getMicrotimeFloat()
{
    list($usec, $sec) = explode(" ", microtime());
    return ((float)$usec + (float)$sec);
}

/**
 * Paravent function
 * @param String $width
 * @param String $heights
 * @return Integer
 */
function paravent($width, $heights) {

    while(!validateWidth($width)) {
        // input example : 10
        $width = trim(readline(sprintf("Veuillez renseigner la largeur du continent (%d ≤ n ≤ %d) :", MIN_WIDTH, MAX_WIDTH)));
    }

    while(!validateHeights($heights)) {
        // input example : 30 27 17 42 29 12 14 41 42 42
        $heights = trim(readline(sprintf("Veuillez renseigner les altitudes du terrain (%d ≤ h ≤ %d) :", MIN_HEIGHT, MAX_HEIGHT)));
    }

    // to check exec duration
    $startTime = getMicrotimeFloat();

    // compute the available shelters
    $width = (int)$width;
    $heightsStrLen = mb_strlen($heights);
    $maxHeight = 0;
    $countShelter = 0;
    $countHeight = 0;
    $nbChars = min(NB_CHARS, $width);

    while($heightsStrLen && ($countShelter + $countHeight) < $width) {
        $heightsPartial = substr($heights, 0, $nbChars);
        $heights = substr($heights, $nbChars);
        $heightsStrLen = mb_strlen($heights);
        $heightsPartial = preg_replace("/\s+/", " ", $heightsPartial); 

        if (isset($lastHeight) && $lastHeight !== " ") {
            $heightsPartial = $lastHeight . $heightsPartial;
        }

        $heightArr = explode(' ', $heightsPartial);
        $heightArrLen = count($heightArr);

        $lastHeight = null;

        for($i=0; $i<$heightArrLen; $i++) {
            $height = $heightArr[$i];

            if ($i === ($heightArrLen - 1) && $height !== " " && $heightsStrLen) {
                $lastHeight = $height;
            } 
            else if ((int)$height > MAX_HEIGHT){
                // @TODO : throw error because altitude must be <= MAX_HEIGHT
                continue;
            }
            else if ((int)$height < $maxHeight) {
                $countShelter++;
            } 
            else {
                $maxHeight = (int)$height;
                $countHeight++;
            }

            if ( ($countShelter + $countHeight) >= $width ) {
                break;
            }
        }
    }

    // Get exec duration
    $endTime = getMicrotimeFloat();
    echo "\nTotal exec duration (seconds) : " . ($endTime - $startTime);

    return $countShelter;
}

// call paravent function
echo "\nAvailable shelters : " . paravent($width, $heights);

// check memory peak usage
echo "\nMemory peak usage (Ko) : " . memory_get_peak_usage() / 1024;

echo "\n";