<?php
/**
 * Created by PhpStorm.
 * User: youmy
 * Date: 16/09/25
 * Time: 14:51
 */

namespace Apine\Utility;


class Files
{

	/**
	 * Verify if exec is disabled
	 *
	 * @author Daniel Convissor
	 *
	 * @see http://stackoverflow.com/questions/3938120/check-if-exec-is-disabled
	 */
	public static function is_exec_available () {

		static $available;

		if (!isset($available)) {
			$available = true;
			if (ini_get('safe_mode')) {
				$available = false;
			} else {
				$d = ini_get('disable_functions');
				$s = ini_get('suhosin.executor.func.blacklist');
				if ("$d$s") {
					$array = preg_split('/,\s*/', "$d,$s");
					if (in_array('exec', $array)) {
						$available = false;
					}
				}
			}
		}

		return $available;

	}

	/**
	 * Recursive file copy
	 *
	 * @param string $src
	 *            Source directory
	 * @param string $dst
	 *            Destination directory
	 */
	public static function recurse_copy ($src, $dst) {

		$dir = opendir($src);
		@mkdir($dst, 0777);
		@chmod($dst, 0777);

		while (false !== ($file = readdir($dir))) {
			if (($file != '.') && ($file != '..')) {
				if (is_dir($src . '/' . $file)) {
					recurse_copy($src . '/' . $file, $dst . '/' . $file);
				} else {
					copy($src . '/' . $file, $dst . '/' . $file);
					chmod($dst . '/' . $file, 0777);
				}
			}
		}

		closedir($dir);


	}

	/**
	 * Return the extension from a file name
	 *
	 * @param string $a_file_path
	 * @return string
	 */
	public static function file_extension ($a_file_path) {

		$name = basename($a_file_path);
		$dot_pos = strpos($name, ".");

		if ($dot_pos > 0) {
			$extension = substr($name, $dot_pos + 1);
		} else {
			$extension = $name;
		}

		return $extension;

	}

	/**
	 * Write strings in a configuration file in INI format
	 *
	 * Source:
	 * http://stackoverflow.com/questions/1268378/create-ini-file-write-values-in-php
	 *
	 * @param array $assoc_arr
	 * @param string $path
	 * @param boolean $has_sections
	 *
	 * @return boolean
	 */
	public static function write_ini_file ($assoc_arr, $path, $has_sections = FALSE) {

		$content = "";

		if ($has_sections) {
			foreach ($assoc_arr as $key=>$elem) {
				$content .= "[" . $key . "]\n";

				foreach ($elem as $key2=>$elem2) {
					if (is_array($elem2)) {
						for ($i = 0;$i < count($elem2);$i++) {
							$content .= "\t" . $key2 . "[] = \"" . $elem2[$i] . "\"\n";
						}
					} else if ($elem2 == "") {
						$content .= "\t" . $key2 . " = \n";
					} else {
						$content .= "\t" . $key2 . " = \"" . $elem2 . "\"\n";
					}
				}
			}
		} else {
			foreach ($assoc_arr as $key=>$elem) {
				if (is_array($elem)) {
					for ($i = 0;$i < count($elem);$i++) {
						$content .= "\t" . $key . "[] = \"" . $elem[$i] . "\"\n";
					}
				} else if ($elem == "") {
					$content .= "\t" . $key . " = \n";
				} else {
					$content .= "\t" . $key . " = \"" . $elem . "\"\n";
				}
			}
		}

		if (!$handle = fopen($path, 'w')) {
			return false;
		}

		$success = fwrite($handle, $content);
		fclose($handle);
		return $success;

	}

}