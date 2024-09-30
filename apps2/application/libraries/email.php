<?php

        $config['protocol'] = 'smtp';
        $config['smtp_host'] = 'smarthost.coxmail.com';
        $config['smtp_port'] = '25'; // 8025, 587 and 25 can also be used. Use Port 465 for SSL.
        $config['smtp_user'] = 'gabriel.macias@mitechnologiesinc.com';
        $config['smtp_pass'] = 'Tenken23';
        $config['charset'] = 'iso-8859-1';
        $config['mailtype'] = 'html';
        $config['newline'] = "\r\n";    

        $this->email->initialize($config);

        $this->email->from('autobot@mitechnologiesinc.com', 'autobot');
        $this->email->to('gabriel.macias@mitechnologiesinc.com'); 

        $this->email->subject('Email Test');
        $this->email->message('<strong>Testing the email class.</strong>');  

        $this->email->send();

        //echo $this->email->print_debugger();

        $this->load->view('email_view');
?>