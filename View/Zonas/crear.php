<h1>Crear zona</h1>
<p>Para crear una zona especificar el
<a href="http://es.wikipedia.org/wiki/FQDN">FQDN</a> a continuación, en la
pantalla siguiente se pedirán los registros de la zona.</p>

<?php
$f = new \sowerphp\general\View_Helper_Form ();
echo $f->begin (array('onsubmit'=>'Form.check()'));
echo $f->input(array(
    'name'=>'zona',
    'label'=>'Zona (FQDN)',
    'check'=>'notempty',
    'help'=>'Ejemplo: example.com.'
));
echo $f->end ('Siguiente &raquo;');
?>
<div style="text-align:right;margin-bottom:1em;font-size:0.8em">
    <a href="listar">Volver al listado de zonas</a>
</div>
