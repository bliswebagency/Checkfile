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
	'pi_version'	=> '1.0.2 beta',
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
	
	public function file(){
			$file = $this->EE->TMPL->fetch_param('file');
			$remove = $this->EE->TMPL->fetch_param('remove');	
			$path_a = $this->EE->TMPL->fetch_param('path_a');
			$path_b = $this->EE->TMPL->fetch_param('path_b');
			$no_file = $this->EE->TMPL->fetch_param('no_file');
			$remote = $this->EE->TMPL->fetch_param('remote');
			$output = $this->EE->TMPL->fetch_param('output');
						
			//Remove anything we don't want from the file string
			$current_domain = "http://" . $_SERVER['HTTP_HOST'];
			$file = str_replace($remove,"",$file);
			$path_a = str_replace($remove,"",$path_a);
			$path_b = str_replace($remove,"",$path_b);

			//Keep our paths without files
			$path_A = $path_a;
			$path_B = $path_b;
			
			//The lower version gets the file
			$path_a = $path_a . $file;
			$path_b = $path_b . $file;						

    if ($remote == ""){
	        //EVERYTHING HAPPENS ON THE ONE DOMAIN
	
			//If File Exists : Use Path A
			if (file_exists($_SERVER['DOCUMENT_ROOT'].$path_a)){
				if ($output == "true") return $path_a;
				else return "";
			} elseif(file_exists($_SERVER['DOCUMENT_ROOT'].$path_b)) {
				//If File Does NOT Exist : Copy Path B to Path A
				copy($_SERVER['DOCUMENT_ROOT'].$path_b, $_SERVER['DOCUMENT_ROOT'].$path_a);
				
				if ($output == "true") return $path_b;
				else return "";
			} else {			
				//What? Still No File!! : Use No_File
				if ($output == "true") return $no_file;
				else return "";						
			}
	} else {
		
		//OK, WE'RE GOING CROSS DOMAIN		
		$header_response_b = get_headers($path_b, 1);
		if (file_exists($_SERVER['DOCUMENT_ROOT'].$path_a)){
		    // FILE A EXISTS, RUN WITH THAT
		    if ($output == "true") return $path_a;
		    else return "";
		} elseif ( strpos( $header_response_b[0], "404" ) === false ){
			// FILE B EXISTS, USE THIS
			copy($path_b, $_SERVER['DOCUMENT_ROOT'].$path_a);
			if ($output == "true") return $path_b;
			else return "";
			
		}
		else 
		{
		    // NO FILE EXISTS!!
		    if ($output == "true") return $no_file;
	 	    else return "";
		}
		
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

This plugin is designed for people who have a files which are in one place which really need to be in another.



<?php
		$buffer = ob_get_contents();
		ob_end_clean();
		return $buffer;
	}
}


/* End of file pi.checkfile.php */
/* Location: /system/expressionengine/third_party/checkfile/pi.checkfile.php */