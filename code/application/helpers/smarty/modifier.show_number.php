<?php
/**
 * Вывод числел с поразрядным разделителем
 *
 * @author Sinner
 * @create 11.03.2010
 **/
function smarty_modifier_show_number($string) {

	return _calculate_substr($string);
}

function _calculate_substr($string){
    $len = utf8::strlen($string);
	if ($len <= 3) {
	    return $string;
	} else {
	    return _calculate_substr(substr($string, 0, $len-3)) . ' ' . substr($string, $len-3, 3);
	}
}

?>
