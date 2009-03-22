<?
/**
 * @package Helpers
 * @category Concrete
 * @author Andrew Embler <andrew@concrete5.org>
 * @copyright  Copyright (c) 2003-2008 Concrete5. (http://www.concrete5.org)
 * @license    http://www.concrete5.org/license/     MIT License
 */

/**
 * Functions to help with using HTML. Does not include form elements - those have their own helper. 
 * @package Helpers
 * @category Concrete
 * @author Andrew Embler <andrew@concrete5.org>
 * @copyright  Copyright (c) 2003-2008 Concrete5. (http://www.concrete5.org)
 * @license    http://www.concrete5.org/license/     MIT License
 */

defined('C5_EXECUTE') or die(_("Access Denied."));
class HtmlHelper {

	/** 
	 * Includes a CSS file. This function looks in several places. 
	 * First, if the item is either a path or a URL it just returns the link to that item (as XHTML-formatted style tag.) 
	 * Then it checks the currently active theme, then if a package is specified it checks there. Otherwise if nothing is found it
	 * fires off a request to the relative directory CSS directory. If nothing is there, then it checks to the assets directories
	 * @param $file
	 * @return $str
	 */
	public function css($file, $pkgHandle = null) {
		// if the first character is a / then that means we just go right through, it's a direct path
		if (substr($file, 0, 1) == '/' || substr($file, 0, 4) == 'http') {
			return '<style type="text/css">@import "' . $file . '";</style>';
		}
		
		$v = View::getInstance();
		// checking the theme directory for it. It's just in the root.
		if (file_exists($v->getThemeDirectory() . '/' . $file)) {
			$str = '<style type="text/css">@import "' . $v->getThemePath() . '/' . $file . '";</style>';
		} else if ($pkgHandle != null) {
			if (file_exists(DIR_BASE . '/' . DIRNAME_PACKAGES . '/' . $pkgHandle . '/' . DIRNAME_CSS . '/' . $file)) {
				$str = '<style type="text/css">@import "' . DIR_REL . '/' . DIRNAME_PACKAGES . '/' . $pkgHandle . '/' . DIRNAME_CSS . '/' . $file . '";</style>';
			} else if (file_exists(DIR_BASE_CORE . '/' . DIRNAME_PACKAGES . '/' . $pkgHandle . '/' . DIRNAME_CSS . '/' . $file)) {
				$str = '<style type="text/css">@import "' . ASSETS_URL . '/' . DIRNAME_PACKAGES . '/' . $pkgHandle . '/' . DIRNAME_CSS . '/' . $file . '";</style>';
			}
		}
			
		if (!isset($str)) {
			if (file_exists(DIR_BASE . '/' . DIRNAME_CSS . '/' . $file)) {
				$str = '<style type="text/css">@import "' . DIR_REL . '/' . DIRNAME_CSS . '/' . $file . '";</style>';
			} else {
				$str = '<style type="text/css">@import "' . ASSETS_URL_CSS . '/' . $file . '";</style>';
			}
		}
		return $str;
	}
	
	/** 
	 * Includes a JavaScript file. This function looks in several places. 
	 * First, if the item is either a path or a URL it just returns the link to that item (as XHTML-formatted script tag.) 
	 * If a package is specified it checks there. Otherwise if nothing is found it
	 * fires off a request to the relative directory JavaScript directory.
	 * @param $file
	 * @return $str
	 */
	public function javascript($file, $pkgHandle = null) {

		if (substr($file, 0, 1) == '/' || substr($file, 0, 4) == 'http') {
			return '<script type="text/javascript" src="' . $file . '"></script>';
		}

		if ($pkgHandle != null) {
			if (file_exists(DIR_BASE . '/' . DIRNAME_PACKAGES . '/' . $pkgHandle . '/' . DIRNAME_JAVASCRIPT . '/' . $file)) {
				$str = '<script type="text/javascript" src="' . DIR_REL . '/' . DIRNAME_PACKAGES . '/' . $pkgHandle . '/' . DIRNAME_JAVASCRIPT . '/' . $file . '"></script>';
			} else if (file_exists(DIR_BASE_CORE . '/' . DIRNAME_PACKAGES . '/' . $pkgHandle . '/' . DIRNAME_JAVASCRIPT . '/' . $file)) {
				$str = '<script type="text/javascript" src="' . ASSETS_URL . '/' . DIRNAME_PACKAGES . '/' . $pkgHandle . '/' . DIRNAME_JAVASCRIPT . '/' . $file . '"></script>';
			}
		}
			
		if (!isset($str)) {
			if (file_exists(DIR_BASE . '/' . DIRNAME_JAVASCRIPT . '/' . $file)) {
				$str = '<script type="text/javascript" src="' . DIR_REL . '/' . DIRNAME_JAVASCRIPT . '/' . $file . '"></script>';
			} else {
				$str = '<script type="text/javascript" src="' . ASSETS_URL_JAVASCRIPT . '/' . $file . '"></script>';
			}
		}
		return $str;
	}
	
	
	/** 
	 * Includes an image file when given a src, width and height. Optional attribs array specifies style, other properties.
	 * First checks the PATH off the root of the site
	 * Then checks the PATH off the images directory at the root of the site.
	 * @param string $src
	 * @param int $width
	 * @param int $height
	 * @param array $attribs
	 * @return string $html
	 */
	public function image($src, $width = false, $height = false, $attribs = null) {
		$image = parse_url($src);
		$attribsStr = '';
		
		if (is_array($width) && $height == false) {
			$attribs = $width;
			$width = false;
		}
		
		if (is_array($attribs)) {
			foreach($attribs as $key => $at) {
				$attribsStr .= " {$key}=\"{$at}\" ";
			}
		}
		
		if ($width == false && $height == false && (!isset($image['scheme']))) {
			// if our file is not local we DON'T do getimagesize() on it. too slow
			$v = View::getInstance();
			if ($v->getThemeDirectory() != '' && file_exists($v->getThemeDirectory() . '/' . DIRNAME_IMAGES . '/' . $src)) {
				$s = getimagesize($v->getThemeDirectory() . '/' . DIRNAME_IMAGES . '/' . $src);
				$width = $s[0];
				$height = $s[1];
				$src = $v->getThemePath() . '/' . DIRNAME_IMAGES . '/' . $src;
			} else if (file_exists(DIR_BASE . '/' . $src)) {
				$s = getimagesize(DIR_BASE . '/' . $src);
				$width = $s[0];
				$height = $s[1];
			} else if (file_exists(DIR_BASE . '/' . DIRNAME_IMAGES . '/' . $src)) {
				$s = getimagesize(DIR_BASE . '/'  . DIRNAME_IMAGES . '/' . $src);
				$width = $s[0];
				$height = $s[1];
				$src = DIR_REL . '/' . DIRNAME_IMAGES . '/' . $src;
			}
		}
		
		if ($width > 0) {
			$str = '<img src="' . $src . '" width="' . $width . '" border="0" height="' . $height . '" ' . $attribsStr . ' />';
		} else {
			$str = '<img src="' . $src . '" border="0" ' . $attribsStr . ' />';
		}
		return $str;
	}	
	
	
}

?>