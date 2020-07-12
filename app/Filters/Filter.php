<?php


namespace App\Filters;

class Filter
{
    /**
     * Highlight of string with <b> element
     * @param int $position position of first occurrence of substring in string
     * @param int $end end of loop
     * @param string $string string
     * @param int $inputLength length of input from filter
     * @return string string with highlighted part of string
     */
    public static function highlightString(int $position, int $end, string $string, int $inputLength)
    {
        $concat = $newName = "";
        for ($i = 0; $i < $end; $i++) {
            $letter = $string[$i];
            $isHex = 0;

            if (preg_match('/[\x80-\xff]/', $letter)) {
                $concat = $letter.$string[$i + 1];
                $isHex = 1;
                $i++;
                $position++;
            }

            /* compare each letter from value and db */
            if ($i == $position) {
                $newName .= "<b>";

                if ($isHex) {
                    $newName .= $concat;
                }

                for ($j = $position; $j < $inputLength + $position; $j++) {
                    if (!$isHex || $j != $position) {
                        $letter = $string[$j];
                        if (preg_match('/[\x80-\xff]/', $letter)) {
                            $concat = $letter.$string[$j + 1];
                            $isHex = 1;
                            $j++;
                            $position++;
                        }
                        $newName .= $isHex ? $concat : $letter;
                        $isHex = 0;
                    }
                }

                $i = $j - 1;
                $newName .= "</b>";
            } else {
                $newName .= $isHex ? $concat : $letter;
            }
        }

        return $newName;
    }

    /**
     * Remove all punctuation from string
     * @param string $string
     * @return string string without punctuation
     */
    public static function removeAccents(string $string)
    {
        if (!preg_match('/[\x80-\xff]/', $string))
            return $string;


        $chars = array(
            /* decompositions for Latin-1 Supplement */
            chr(195).chr(129) => 'A',
            chr(195).chr(137) => 'E',
            chr(195).chr(141) => 'I',
            chr(195).chr(145) => 'N',
            chr(195).chr(147) => 'O',
            chr(195).chr(154) => 'U',
            chr(195).chr(157) => 'Y',
            chr(195).chr(161) => 'a',
            chr(195).chr(164) => 'a',
            chr(195).chr(169) => 'e',
            chr(195).chr(170) => 'e',
            chr(195).chr(173) => 'i',
            chr(195).chr(179) => 'o',
            chr(195).chr(189) => 'y',

            /* decompositions for Latin Extended-A */
            chr(196).chr(140) => 'C', chr(196).chr(141) => 'c',
            chr(196).chr(142) => 'D', chr(196).chr(143) => 'd',
            chr(196).chr(154) => 'E', chr(196).chr(155) => 'e',
            chr(197).chr(135) => 'N', chr(197).chr(136) => 'n',
            chr(197).chr(152) => 'R',chr(197).chr(153) => 'r',
            chr(197).chr(160) => 'S', chr(197).chr(161) => 's',
            chr(197).chr(164) => 'T', chr(197).chr(165) => 't',
            chr(197).chr(166) => 'T', chr(197).chr(167) => 't',
            chr(197).chr(174) => 'U', chr(197).chr(175) => 'u',
            chr(197).chr(189) => 'Z', chr(197).chr(190) => 'z',
        );

        $string = strtr($string, $chars);

        return $string;
    }
}