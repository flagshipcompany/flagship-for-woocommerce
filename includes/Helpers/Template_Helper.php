<?php
namespace FlagshipWoocommerce\Helpers;

class Template_Helper {

	public static $placeholderPattern = '/{{(\s?)(%s)(\s?)}}/i';

    public static function render_php($filePath, $data = array()) {
				if ($data) {
					extract($data);
				}

				include(self::get_file_path($filePath));
    }

    public static function render_embedded_php($filePath, $data = array()) {
				if ($data) {
					extract($data);
				}

        ob_start();
        include(self::get_file_path($filePath));
        $content = ob_get_contents();

        return ob_get_clean();
    }

    public static function render_html($filePath, $data) {
    	$content = file_get_contents(self::get_file_path($filePath));
        $matched = preg_match_all(sprintf(self::$placeholderPattern, '\S+'), $content, $matches);

        if (!$matched) {
            $a = [1, 2,3, 4];
        	echo $content;

            return;
        }

        foreach ($matches[0] as $key => $value) {
        	$content = self::replaceVar($content, $value, $data);
        }

        echo $content;
    }

    public static function replaceVar($content, $match, $data) {
	    $varName = preg_replace(sprintf(self::$placeholderPattern, '\S+'), '${2}', $match);

	    if (!$varName) {
	    	return $content;
	    }

	    if (!isset($data[$varName])) {
	    	throw new \Exception(sprintf('Variable %s is undefined!', $varName));
	    }

        return preg_replace(sprintf(self::$placeholderPattern, $varName), $data[$varName], $content);
    }

    public static function get_file_path($file_path) {
        return realpath(__DIR__ . '/../../templates').'/'.$file_path;
    }
}
