<?php  if ( ! defined('BASEPATH')) exit('No direct script access allowed');

/**
 * ExpressionEngine - by EllisLab
 *
 * @package		ExpressionEngine
 * @author		ExpressionEngine Dev Team
 * @copyright	Copyright (c) 2003 - 2011, EllisLab, Inc.
 * @license		http://expressionengine.com/user_guide/license.html
 * @link		http://expressionengine.com
 * @since		Version 2.0
 * @filesource
 */
 
// ------------------------------------------------------------------------

/**
 * Checkfile Plugin
 *
 * @package		ExpressionEngine
 * @subpackage	Addons
 * @category	Plugin
 * @author		Blis Web Agency
 * @link		http://blis.net.au
 */

$plugin_info = array(
	'pi_name'		=> 'Checkfile',
	'pi_version'	=> '1.0',
	'pi_author'		=> 'Blis Web Agency',
	'pi_author_url'	=> 'http://blis.net.au',
	'pi_description'=> 'Checks if a file exists, then falls back on a remote, then gives up!',
	'pi_usage'		=> Checkfile::usage()
);


class Checkfile {

	public $return_data;
    
	/**
	 * Constructor
	 */
	public function __construct()
	{
		$this->EE =& get_instance();
	}
	
	function url_exists($url){
        $url = str_replace("http://", "", $url);
        if (strstr($url, "/")) {
            $url = explode("/", $url, 2);
            $url[1] = "/".$url[1];
        } else {
            $url = array($url, "/");
        }

        $fh = fsockopen($url[0], 80);
        if ($fh) {
            fputs($fh,"GET ".$url[1]." HTTP/1.1\nHost:".$url[0]."\n\n");
            if (fread($fh, 22) == "HTTP/1.1 404 Not Found") { return FALSE; }
            else { return TRUE;    }

        } else { return FALSE;}
    }
	
	public function file(){
			$file = $this->EE->TMPL->fetch_param('file');
			$remove = $this->EE->TMPL->fetch_param('remove');	
			$path_a = $this->EE->TMPL->fetch_param('path_a');
			$path_b = $this->EE->TMPL->fetch_param('path_b');
			$no_file = $this->EE->TMPL->fetch_param('no_file');
			
			//Remove anything we don't want from the file string
			$current_domain = "http://" . $_SERVER['HTTP_HOST'];
			$file = str_replace($remove,"",$file);
			/* REMOTE MODE
			if (!stristr($path_a,"http://")){
				$path_a = $current_domain . $path_a;
			}
			if (!stristr($path_b,"http://")){
				$path_b = $current_domain . $path_b;
			}
			*/
			//Keep our paths without files
			$path_A = $path_a;
			$path_B = $path_b;
			
			//The lower version gets the file
			$path_a = $path_a . $file;
			$path_b = $path_b . $file;						

			//If File Exists : Use Path A
			if (file_exists($_SERVER['DOCUMENT_ROOT'].$path_a)){
				#return $path_a;
				return "";
			} elseif($this->url_exists($path_b)) {
				//If File Does NOT Exist : Copy Path B to Path A


				$content = file_get_contents($path_b);
				$dir = dirname($_SERVER['SCRIPT_FILENAME']);
				$fp = fopen($_SERVER['DOCUMENT_ROOT'].$path_a, 'w');
				fwrite($fp, $content);
				fclose($fp);
				
				#return $path_b;
				return "";
			} else {			
				//What? Still No File!! : Use No_File
				return $no_file;						
			}
	}
	
	
	
	// ----------------------------------------------------------------
	
	/**
	 * Plugin Usage
	 */
	public static function usage()
	{
		ob_start();
?>

This plugin is designed for people who have a files which are in one place which really need to be in another. Here's how it works.

First, it checks if the files are where you want them on Server A.
If not, it checks Server B. If it finds them there, it copies them to where they need to be on Server A.

Failing this, will then either serve a default file (e.g. "no-image.jpg") or nothing at all.
<?php
		$buffer = ob_get_contents();
		ob_end_clean();
		return $buffer;
	}
}


/* End of file pi.checkfile.php */
/* Location: /system/expressionengine/third_party/checkfile/pi.checkfile.php */