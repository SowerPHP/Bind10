<h1>Zonas</h1>
<p>Se muestra el listado de zonas disponibles en el DNS.</p>
<?php
foreach ($zonas as &$zona) {
    $zona[] = '<a href="editar/'.$zona['id'].'"><img src="'.$_base.'/img/icons/16x16/actions/edit.png" alt="" /></a>';
}
array_unshift ($zonas, array('ID', 'Zona', 'RD Class', 'DNSSEC', 'Acciones'));
$t = new \sowerphp\app\View_Helper_Maintainer (array('link'=>$_base.'/bind10/zonas'), false);
$t->setId ('zonas');
echo $t->listar($zonas);
