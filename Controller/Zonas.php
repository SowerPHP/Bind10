<?php

/**
 * SowerPHP: Minimalist Framework for PHP
 * Copyright (C) SowerPHP (http://sowerphp.org)
 *
 * Este programa es software libre: usted puede redistribuirlo y/o
 * modificarlo bajo los términos de la Licencia Pública General GNU
 * publicada por la Fundación para el Software Libre, ya sea la versión
 * 3 de la Licencia, o (a su elección) cualquier versión posterior de la
 * misma.
 *
 * Este programa se distribuye con la esperanza de que sea útil, pero
 * SIN GARANTÍA ALGUNA; ni siquiera la garantía implícita
 * MERCANTIL o de APTITUD PARA UN PROPÓSITO DETERMINADO.
 * Consulte los detalles de la Licencia Pública General GNU para obtener
 * una información más detallada.
 *
 * Debería haber recibido una copia de la Licencia Pública General GNU
 * junto a este programa.
 * En caso contrario, consulte <http://www.gnu.org/licenses/gpl.html>.
 */

// namespace del controlador
namespace website\Bind10;

/**
 * Controlador para zonas del DNS
 * @author Esteban De La Fuente Rubio, DeLaF (esteban[at]delaf.cl)
 * @version 2014-03-30
 */
class Controller_Zonas extends \Controller_App
{

    /**
     * Mostrar el listado de zonas disponibles
     * @author Esteban De La Fuente Rubio, DeLaF (esteban[at]delaf.cl)
     * @version 2014-03-30
     */
    public function listar ()
    {
        $this->set(array(
            'zonas' => (new Model_Zonas)->listado()
        ));
    }

    /**
     * Acción para agregar una nueva zona al DNS
     * @author Esteban De La Fuente Rubio, DeLaF (esteban[at]delaf.cl)
     * @version 2014-03-30
     */
    public function crear ()
    {
        if (isset($_POST['submit']) && !empty($_POST['zona'])) {
            $Zona = new Model_Zona ($_POST['zona']);
            if (!$Zona->exists()) {
                $Zona->name = $_POST['zona'];
                $Zona->save();
                $this->redirect ('/bind10/zonas/editar/'.$Zona->id);
            } else {
                \sowerphp\core\Model_Datasource_Session::message (
                    'Zona <em>'.$Zona->name.'</em> ya existe'
                );
                $this->redirect ('/bind10/zonas/listar');
            }
        }
    }

    /**
     * Acción para editar una zona del DNS
     * @param id Identificador de la zona o la zona
     * @author Esteban De La Fuente Rubio, DeLaF (esteban[at]delaf.cl)
     * @version 2014-03-30
     */
    public function editar ($id)
    {
        // crear zona solicitada y verificar que exista
        $Zona = new Model_Zona ($id);
        if (!$Zona->exists()) {
            \sowerphp\core\Model_Datasource_Session::message (
                'Zona('.$id.') no existe'
            );
            $this->redirect ('/bind10/zonas/listar');
        }
        // mostrar formulario de edición si no se pidió guardar
        if (!isset($_POST['submit'])) {
            $this->set (array(
                'zona' => $Zona->name,
                'soa' => $Zona->getSoaRecord(),
                'records' => $Zona->getRecords()
            ));
        }
        // guardar datos de la zona si se envió el formulario
        else {
            $Zona->name = $_POST['zona'];
            $Zona->save();
            $Zona->saveSoaRecord(
                $_POST['soa_id'],
                $_POST['soa_ttl'],
                $_POST['soa_host'],
                $_POST['soa_email'],
                $_POST['soa_serial'],
                $_POST['soa_refresh'],
                $_POST['soa_retry'],
                $_POST['soa_expire']
            );
            $Zona->saveRecords(
                $_POST['id'],
                $_POST['name'],
                $_POST['rdtype'],
                $_POST['rdata'],
                $_POST['soa_ttl']
            );
            // redireccionar
            \sowerphp\core\Model_Datasource_Session::message (
                'Zona <em>'.$Zona->name.'</em> actualizada'
            );
            $this->redirect ('/bind10/zonas/listar');
        }
    }

}
