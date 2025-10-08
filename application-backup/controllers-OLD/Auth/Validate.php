<?php

class Validate extends MY_Controller
{

    public function __construct()
    {
        parent::__construct();
    }
    
    public function index(){

        if($this->isLoggedIn()) {

            $this->redirectAutnorized();
        }else{
            $this->redirectUnauthorized();
        }
    }
    
}

