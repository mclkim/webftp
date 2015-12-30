<?php
namespace Kaiser;

/**
 * @author      Zeki Unal <zekiunal@gmail.com>
 * @description
 *
 * @package     Project\File
 * @name        Info
 * @version     0.1
 */
class Info
{
	/**
	 * @var array
	 */
	protected static $bytes = array(
			array('GB'      => 1073741824),
			array('MB'      => 1048576),
			array('KB'      => 1024),
			array('bytes'   => 2),
			array('byte'    => 1),
	);

	/**
	 * @param  string $file_name
	 * @return string
	*/
	public static function formatSizeUnits($file_name)
	{
		$bytes = 0;
		if (file_exists($file_name)) {
			$bytes = array_filter(self::convert(filesize($file_name)));
		}

		return array_shift($bytes);
	}

	/**
	 * @param $size integer
	 * @return string
	 */
	public static function convert($size)
	{
		$bytes = self::$bytes;

		$format = function ($item) use ($size) {
			if ($size >= $item[key($item)]) {
				return number_format($size/$item[key($item)], 2) . ' ' . key($item);
			}
		};

		return array_map($format, $bytes);
	}
}