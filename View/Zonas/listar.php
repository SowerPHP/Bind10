<h1>Zonas</h1>
<p>Se muestra el listado de zonas disponibles en el DNS.</p>
<?php
foreach ($zonas as &$zona) {
    $actions = '<a href="descargar/'.$zona['name'].'" title="Descargar archivo de la zona '.$zona['name'].'"><span class="fa fa-download btn btn-default" aria-hidden="true"></span></a> ';
    $actions .= '<a href="json/'.$zona['name'].'" title="Exportar zona '.$zona['name'].' a archivo JSON"><span class="fa fa-file-code-o btn btn-default" aria-hidden="true"></span></a> ';
    $actions .= '<a href="editar/'.$zona['name'].'" title="Editar la zona '.$zona['name'].'"><span class="fa fa-edit btn btn-default" aria-hidden="true"></span></a> ';
    $actions .= '<a href="eliminar/'.$zona['name'].'" onclick="return eliminar(\'Zona\', \''.$zona['name'].'\')" title="Eliminar la zona '.$zona['name'].'"><span class="fa fa-remove btn btn-default" aria-hidden="true"></span></a>';
    $zona[] = $actions;
    unset ($zona['id']);
    if (!$bind10) unset ($zona['usuario']);
}
$t = new \sowerphp\app\View_Helper_Maintainer(['link'=>$_base.'/bind10/zonas'], false);
if ($bind10) {
    array_unshift ($zonas, ['Usuario', 'Zona', 'RD Class', 'DNSSEC', 'Registros', 'Acciones']);
    $t->setId ('zonas');
} else {
    array_unshift ($zonas, ['Zona', 'RD Class', 'DNSSEC', 'Registros', 'Acciones']);
    $t->setId ('zonas_'.$_Auth->User->usuario);
}
echo $t->listar($zonas);
