<?php
class Common {

    /**
     * Cuts string from the end and appends three dots
     * @access public static
     * @param string $str string to cut
     * @param int $maxlength maximal length of string after cutting
     * @param array $chars characters after which can be string cut
     * @return string cut string
     */
    public static function cut($str, $maxlength, $chars = array(" ",",",".","?","!")) {
        $str = $str2 = substr(trim($str), 0, $maxlength+1);
        while(!in_array(substr($str2, -1), $chars) && strlen($str2) > 0) {
            $str2 = substr($str2, 0, strlen($str2)-1);
        }
        $str = $str2 ? $str2 : $str;
        $str = trim($str)."&hellip;";
        return $str;
    }

    /**
     * Cuts string from the beginning
     * @access public static
     * @param string $str string to cut
     * @param int $maxlength maximal length of string after cutting
     * @param array $chars characters after which can be string cut
     * @return string cut string
     */
    public static function lcut($str, $maxlength, $chars = array(" ",",",".","?","!")) {
        $str2 = $str;
        while(!in_array(substr($str2, 0, 1), $chars) && strlen($str2) > 0) {
            $str2 = substr($str2, 1);
        }
        $str = $str2 ? $str2 : $str;
        return trim($str);
    }

    /**
     * Method for conversion bytes to higher units
     * $unit = 0 - result in auto unit; default
     * $unit = 1 - result in bytes
     * $unit = 2 - result in kilobytes
     * $unit = 3 - result in megabytes
     * $unit = 4 - result in gigabytes
     * @access public static
     * @param int $size in bytes
     * @param int $unit modifier for manual select of units
     * @return string size in chosen unit
     */
    public static function getPCSize($size, $unit=0) {
        $p = 1024;
        switch($unit) {
            case 1: $return = $size." B"; break;
            case 2: $return = round($size/$p)." kB"; break;
            case 3: $return = round($size/($p*$p), 2)." MB"; break;
            case 4: $return = round($size/($p*$p*$p), 2)." GB"; break;
            default:
                if($size < $p) {
                    $return = $size." B"; break;
                } elseif($size < ($p*$p)) {
                    $return = round($size/$p)." kB"; break;
                } elseif($size < ($p*$p*$p)) {
                    $return = round($size/($p*$p), 2)." MB"; break;
                } else {
                    $return = round($size/($p*$p*$p), 2)." GB"; break;
                }
        }
        return $return;
    }

    /**
     * Method for conversion milimetres to higher length units
     * $unit = 0 - result in auto unit; default
     * $unit = 1 - result in mm
     * $unit = 2 - result in cm
     * $unit = 3 - result in dm
     * $unit = 4 - result in m
     * $unit = 5 - result in km
     * @access public static
     * @param int $size in mm
     * @param int $unit modifier for manual select of units
     * @return string size in chosen unit
     */
    public static function getLengthSize($size, $unit=0, $showUnits=true) {
        $p = 10;
        switch($unit) {
            case 1: $return = $size.($showUnits ? " mm" : ""); break;
            case 2: $return = flooer($size/$p).($showUnits ? " cm" : ""); break;
            case 3: $return = floor($size/($p*$p)).($showUnits ? " dm" : ""); break;
            case 4: $return = floor($size/($p*$p*$p)).($showUnits ? " m" : ""); break;
            case 5: $return = floor($size/($p*$p*$p*1000)).($showUnits ? " km" : ""); break;
            default:
                if($size < $p) {
                    $return = $size.($showUnits ? " mm" : ""); break;
                } elseif($size < ($p*$p)) {
                    $return = floor($size/$p).($showUnits ? " cm" : ""); break;
                } elseif($size < ($p*$p*$p)) {
                    $return = floor($size/($p*$p)).($showUnits ? " dm" : ""); break;
                } elseif($size < ($p*$p*$p*1000)) {
                    $return = floor($size/($p*$p*$p)).($showUnits ? " m" : ""); break;
                } else {
                    $return = floor($size/($p*$p*$p*1000)).($showUnits ? " km" : ""); break;
                }
        }
        return $return;
    }

    /**
     * Generates random string of chosen length
     * @access public static
     * @param int $length length of the result string
     * @param boolean $specialChars appends non-alphanumeric characters to character set
     * @return string
     */
    public static function generateString($length, $specialChars = false) {
        $return = "";
        $chars = "abcdefghijklmnoqrstuvwxyzABCDEFGHIJKLMNOQRSTUVWXYZ1234567890";
        //$chars = "ABCDEFGHIJKLMNOPQRSTUVWXYZ";
        if($specialChars === true) {
            $chars.= "!@#$%^&*()[]?§";
        }
        for($i = 0; $i < $length; $i++) {
            $return.= substr($chars, rand(0, strlen($chars) - 1), 1);
        }
        return $return;
    }

    /**
     * Modifies input array into array of requested keys and values
     * @access public static
     * @param array $array array to be changed
     * @param string $key column name of input array row which is supposed to be a key of result array
     * @param string $value optional column name of input array row which is supposed to be a value of result array; whole row if empty
     * @return array
     */
    public static function transformArray($array, $key, $value='') {
        $return = array();
        foreach($array as $k => $row) {
            if($value) {
                $return[$row[$key]] = $row[$value];
            } else {
                $return[$row[$key]] = $array[$k];
            }
        }
        return $return;
    }

    /**
     * General method for redirection
     * @param string $url url where to redirect
     * @param int $code http error code: 404, 301
     * @access private
     */
    public static function redirect($url="", $code=0) {
        if($code == 301) {
            header("HTTP/1.1 301 Moved Permanently");
        } elseif($code == 404) {
            header("HTTP/1.1 404 Not Found");
        }
        if(!$url) {
            if(isset($_SERVER['HTTP_REFERER'])) {
                $url = $_SERVER['HTTP_REFERER'];
            } else {
                $url = dirname($_SERVER['SCRIPT_NAME']);
            }
        }
        header("Location: ".$url); exit;
    }

    public static function clean(&$str) {
        $str = trim(addslashes($str));
        return $str;
    }

	public static function resize($image_source, $thumb_x, $thumb_y, $output_file) {
	    $image_properties = getimagesize($image_source);
	    if (!in_array($image_properties[2], array(1,2,3))) {
	        return false;
	    } else {
	        if ($image_properties[2]==1) {
	            $src_image = imagecreatefromgif($image_source);
	        } elseif ($image_properties[2]==2) {
	            $src_image = imagecreatefromjpeg($image_source);
	        } elseif ($image_properties[2]==3) {
	            $src_image = imagecreatefrompng($image_source);
	        }
	        $src_x = imagesx($src_image);
	        $src_y = imagesy($src_image);
	        if($src_x > $thumb_x || $src_y > $thumb_y) {
				if($thumb_x > 0 && $thumb_y > 0 && $thumb_x <> $thumb_y) {
					$orig_x = (int)$thumb_x;
					$orig_y = (int)$thumb_y;
					if($thumb_x > $thumb_y) {
						$thumb_y = 0;
					} else {
						$thumb_x = 0;
					}
					$crop = true;
				} else {
					$crop = false;
				}
	            if($src_x > $src_y && $thumb_x > 0) {
	                $thumb_y = 0;
	            } elseif($src_x < $src_y && $thumb_y > 0) {
	                $thumb_x = 0;
	            }
	            if (($thumb_y == "0") && ($thumb_x == "0")) {
	                return false;
	            } elseif($thumb_y == "0") {
	                $scalex = $thumb_x/$src_x;
	                $thumb_y = $src_y*$scalex;
	            } elseif($thumb_x == "0") {
	                $scaley = $thumb_y/$src_y;
	                $thumb_x = $src_x*$scaley;
	            }
	            $thumb_x = (int)($thumb_x);
	            $thumb_y = (int)($thumb_y);

	            $coords_x = 0;
	            $coords_y = 0;
	            if($crop) {
					if($orig_x > $orig_y) {
						$coords_y = (($thumb_y - $orig_y) / 2) * ($src_y / $thumb_y);
						$coords_y = (int)$coords_y;
					} else {
						$coords_x = (($thumb_x - $orig_x) / 2) * ($src_x / $thumb_x);
						$coords_x = (int)$coords_x;
					}
					$width = $orig_x;
					$height = $orig_y;
				} else {
					$width = $thumb_x;
					$height = $thumb_y;
				}

	            $dest_image = imagecreatetruecolor($width, $height);
	            if (!imagecopyresampled($dest_image, $src_image, 0, 0, $coords_x, $coords_y, $thumb_x, $thumb_y, $src_x, $src_y)) {
	                imagedestroy($src_image);
	                imagedestroy($dest_image);
	                return false;
	            } else {
	                imagedestroy($src_image);
	                if (imagejpeg($dest_image,$output_file, 100)) {
	                    imagedestroy($dest_image);
	                    return true;
	                }
	                imagedestroy($dest_image);
	            }
	        } else {
	            copy($image_source,$output_file);
	        }
	        return false;
	    }
	}

	public static function pinToBirthdate($pin) {
		list($y, $m, $d) = str_split($pin, 2);
		$d = (int)$d;
		$m = $m > 50 ? $m-50 : (int)$m;
		$y = $y < date('y') ? $y + 2000 : $y + 1900;
		//return $d.'.'.$m.'.'.$y;
		return $y;
	}

    public static function friendlyUrl($str) {
        $url = $str;
		$url = preg_replace('~[^\\pL0-9]+~u', '-', $url);
        $url = trim($url, "-");
        $url = iconv("utf-8", "us-ascii//TRANSLIT", $url);
        $url = strtolower($url);
        $url = preg_replace('~[^-a-z0-9]+~', '', $url);
        return $url;
    }
/*
    public static function friendlyUrl($str) {
        $url = $str;
        $url = preg_replace('~[^\\pL0-9_]+~u', '-', $url);
        $url = trim($url, "-");
        $url = mb_strtolower($url);

        $from = array("á","ä","č","ď","é","ě","ë","í","ľ","ň","ó","ö","ř","š","ť","ú","ü","ů","ý","ž");
        $to   = array("a","a","c","d","e","e","e","i","l","n","o","o","r","s","t","u","u","u","y","z");
        $url = str_replace($from, $to, $url);

        return $url;
    }
*/
}
