<?php 
	function str_rot($s, $n = 13) {
	    static $letters = 'AaBbCcDdEeFfGgHhIiJjKkLlMmNnOoPpQqRrSsTtUuVvWwXxYyZz';
	    $n = (int)$n % 26;
	    if (!$n) return $s;
	    if ($n < 0) $n += 26;
	    if ($n == 13) return str_rot13($s);
	    $rep = substr($letters, $n * 2) . substr($letters, 0, $n * 2);
	    return strtr($s, $letters, $rep);
	}
 ?>