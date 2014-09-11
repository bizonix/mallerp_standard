<?php
/* 
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */

/**
 * Description of MY_Input
 *
 * @author lion
 */
class MY_Input extends CI_Input {
    public function __construct()
    {
        parent::__construct();
    }
    
    public function is_post()
    {
        if (isset($_SERVER['REQUEST_METHOD']))
        {
            return $_SERVER['REQUEST_METHOD'] === 'POST';
        }
        
        return FALSE;
    }
}
?>
