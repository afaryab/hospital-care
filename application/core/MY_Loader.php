<?php (defined('BASEPATH')) OR exit('No direct script access allowed');

/* load the MX_Loader class */

class MY_Loader extends CI_Loader {
    
    
    public function __construct() {
        
        parent::__construct();
            
    }
    
    protected function _ci_object_to_array($object)
    {
        return is_object($object) ? get_object_vars($object) : $object;
    }
    
    public function makeViewWithOutTemplate($view, $vars = [], $return = FALSE){

        
        $this->_ci_view_paths = array_merge($this->_ci_view_paths, array(APPPATH .'controllers/'. MODULE . '/view/' => TRUE));
        
        return $this->_ci_load(array(
            '_ci_view' => $view,
            '_ci_vars' => $this->_ci_object_to_array($vars),
            '_ci_return' => $return
        ));
    }
}