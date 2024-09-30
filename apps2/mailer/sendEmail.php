<?php

$from = $_GET['from'];
$to = $_GET['to'];
$subject = $_GET['subject'];
$message = $_GET['message'];

$config = Array(
    'protocol' => 'smtp',
    'smtp_host' => 'smarthost.coxmail.com', // ssl://smtp.googlemail.com
    'smtp_port' => 25,
    'mailtype' => 'html',
    'charset' => 'iso-8859-1',
    'wordwrap' => TRUE
);

//Creamos el Mensaje
$message = $this->createMessage($stamp, $po, $track, $call, $notes);

//Mandamos el correo
$this->load->library('email', $config);
$this->email->set_newline("\r\n");
$this->email->from($from); // change it to yours
$this->email->to($to);
$this->email->subject($subject);
$this->email->message($message);

if ($this->email->send()) 
    echo 'Email sent.';
else 
    show_error($this->email->print_debugger());

return;

?>
