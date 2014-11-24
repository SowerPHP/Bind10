<h1>Importar zona</h1>
<p>A través de este formulario podrá importar una zona utilizando un archivo JSON previamente exportado usando este módulo o bien que fue generado manualmente.</p>

<?php
$f = new \sowerphp\general\View_Helper_Form ();
echo $f->begin(array('onsubmit'=>'Form.check()'));
echo $f->input(array(
    'type' => 'file',
    'name' => 'archivo',
    'label' => 'Archivo JSON',
    'check' => 'notempty',
    'help' => 'Archivo JSON previamente exportado usando este módulo o bien que fue generado manualmente.'
));
echo $f->end('Importar zona');
