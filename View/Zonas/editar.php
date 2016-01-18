<h1>Editar zona: <?=$zona?></h1>
<?php
$f = new \sowerphp\general\View_Helper_Form ();
echo $f->begin (array('onsubmit'=>'Form.check()'));
echo $f->input (array(
    'type' => 'hidden',
    'name' => 'soa_id',
    'value' => $soa['soa_id'],
));
echo $f->input (array(
    'name' => 'zona',
    'label' => 'Zona',
    'value' => $zona,
    'check' => 'notempty',
    'help' => 'Ejemplo: example.com.',
));
echo $f->input (array(
    'name' => 'soa_host',
    'label' => 'Host',
    'value' => $soa['soa_host'],
    'check' => 'notempty',
    'help' => 'Ejemplo: equipo.example.com.',
));
echo $f->input (array(
    'name' => 'soa_email',
    'label' => 'Email',
    'value' => $soa['soa_email'],
    'check' => 'notempty',
    'help' => 'Ejemplo: usuario.example.com.',
));
echo $f->input (array(
    'name' => 'soa_serial_old',
    'label' => 'Serial actual',
    'value' => $soa['soa_serial'],
    'check' => 'notempty integer',
));
echo $f->input (array(
    'name' => 'soa_serial',
    'label' => 'Serial nuevo',
    'value' => date('YmdH'),
    'check' => 'notempty integer',
    'help' => 'Ejemplo: '.($soa['soa_serial']+1),
));
echo $f->input (array(
    'name' => 'soa_refresh',
    'label' => 'Refresh',
    'value' => $soa['soa_refresh'],
    'check' => 'notempty integer',
    'help' => 'Ejemplo: 172800',
));
echo $f->input (array(
    'name' => 'soa_retry',
    'label' => 'Retry',
    'value' => $soa['soa_retry'],
    'check' => 'notempty integer',
    'help' => 'Ejemplo: 900',
));
echo $f->input (array(
    'name' => 'soa_expire',
    'label' => 'Expire',
    'value' => $soa['soa_expire'],
    'check' => 'notempty integer',
    'help' => 'Ejemplo: 1209600',
));
echo $f->input (array(
    'name' => 'soa_ttl',
    'label' => 'TTL',
    'value' => $soa['soa_ttl'],
    'check' => 'notempty integer',
    'help' => 'Ejemplo: 3600',
));
echo $f->input (array(
    'name' => 'usuario',
    'label' => 'Usuario',
    'value' => $usuario,
    'check' => 'notempty',
    'help' => 'Usuario de la aplicación que es propietario del dominio en el DNS',
));
echo $f->input(array(
    'type' => 'js',
    'id' => 'records',
    'label' => 'Registros',
    'titles' => array ('Nombre', 'Tipo', 'Dato'),
    'inputs' => array (
        array('type'=>'hidden', 'name'=>'id'),
        array('name'=>'name', 'check'=>'notempty'),
        array('name'=>'rdtype', 'check'=>'notempty'),
        array('name'=>'rdata', 'check'=>'notempty'),
    ),
    'values' => $records,
));
echo $f->end('Guardar zona: '.$zona);
?>
<div style="text-align:right;margin-bottom:1em;font-size:0.8em">
    <a href="" onclick="zona_addGoogleApps(); return false" accesskey="g">Agregar registros para Google Apps</a> |
    <a href="../listar" accesskey="l">Volver al listado de zonas</a>
</div>
