<?php

namespace Thunderhawk;

final class Utils {
	
	public static function camelize($scored) {
		return lcfirst ( implode ( '', array_map ( 'ucfirst', array_map ( 'strtolower', explode ( '_', $scored ) ) ) ) );
	}
	public static function underscore($cameled) {
		return implode ( '_', array_map ( 'strtolower', preg_split ( '/([A-Z]{1}[^A-Z]*)/', $cameled, - 1, PREG_SPLIT_DELIM_CAPTURE | PREG_SPLIT_NO_EMPTY ) ) );
	}
}