<?php

/**
 * SowerPHP
 * Copyright (C) SowerPHP (http://sowerphp.org)
 *
 * Este programa es software libre: usted puede redistribuirlo y/o
 * modificarlo bajo los términos de la Licencia Pública General Affero de GNU
 * publicada por la Fundación para el Software Libre, ya sea la versión
 * 3 de la Licencia, o (a su elección) cualquier versión posterior de la
 * misma.
 *
 * Este programa se distribuye con la esperanza de que sea útil, pero
 * SIN GARANTÍA ALGUNA; ni siquiera la garantía implícita
 * MERCANTIL o de APTITUD PARA UN PROPÓSITO DETERMINADO.
 * Consulte los detalles de la Licencia Pública General Affero de GNU para
 * obtener una información más detallada.
 *
 * Debería haber recibido una copia de la Licencia Pública General Affero de GNU
 * junto a este programa.
 * En caso contrario, consulte <http://www.gnu.org/licenses/agpl.html>.
 */

// namespace del controlador
namespace website\Bind10;

/**
 * Controlador para zonas del DNS
 * @author Esteban De La Fuente Rubio, DeLaF (esteban[at]delaf.cl)
 * @version 2014-04-12
 */
class Controller_Zonas extends \Controller_App
{

    /**
     * Acción principal (redireccionará a listar)
     * @author Esteban De La Fuente Rubio, DeLaF (esteban[at]delaf.cl)
     * @version 2014-04-05
     */
    public function index()
    {
        $this->redirect ('/bind10/zonas/listar');
    }

    /**
     * Mostrar el listado de zonas disponibles
     * @author Esteban De La Fuente Rubio, DeLaF (esteban[at]delaf.cl)
     * @version 2014-11-23
     */
    public function listar()
    {
        $bind10 = $this->Auth->User->inGroup(['bind10']);
        if ($bind10)
            $zonas = (new Model_Zonas)->getAll($this->Auth->User->id);
        else
            $zonas = (new Model_Zonas)->getByUser($this->Auth->User->id);
        $this->set([
            'zonas' => $zonas,
            'bind10' => $bind10,
        ]);
    }

    /**
     * Acción para agregar una nueva zona al DNS
     * @todo Reemplazar validación de dominio con una expresión regular
     * @author Esteban De La Fuente Rubio, DeLaF (esteban[at]delaf.cl)
     * @version 2014-11-24
     */
    public function crear()
    {
        if (isset($_POST['submit']) && !empty($_POST['zona'])) {
            $zona = idn2(strtolower($_POST['zona']));
            if ($zona[strlen($zona)-1]!='.') $zona .= '.';
            $chars = count_chars($zona);
            if ($chars[46]<2 or $chars[32]) { // FIXME
                \sowerphp\core\Model_Datasource_Session::message (
                    'Zona <em>'.$_POST['zona'].'</em> es inválida. Debe ser un FQDN.', 'warning'
                );
                $this->redirect ('/bind10/zonas/crear');
            }
            $Zona = new Model_Zona ($zona);
            if (!$Zona->exists()) {
                $Zona->name = $zona;
                $Zona->usuario = $this->Auth->User->id;
                $Zona->save();
                $this->redirect ('/bind10/zonas/editar/'.$zona);
            } else {
                \sowerphp\core\Model_Datasource_Session::message (
                    'Zona <em>'.$Zona->name.'</em> ya existe', 'error'
                );
                $this->redirect ('/bind10/zonas/listar');
            }
        }
    }

    /**
     * Acción para editar una zona del DNS
     * @param id Identificador de la zona o la zona
     * @author Esteban De La Fuente Rubio, DeLaF (esteban[at]delaf.cl)
     * @version 2014-11-23
     */
    public function editar($id)
    {
        // crear zona solicitada y verificar que exista
        $id = idn2($id);
        $Zona = new Model_Zona ($id);
        if (!$Zona->exists()) {
            \sowerphp\core\Model_Datasource_Session::message (
                'Zona('.$id.') no existe', 'error'
            );
            $this->redirect ('/bind10/zonas/listar');
        }
        if (!($Zona->usuario===$this->Auth->User->id or $this->Auth->User->inGroup(['bind10']))) {
            \sowerphp\core\Model_Datasource_Session::message (
                'Usted no es el propietario de la Zona('.$id.')', 'error'
            );
            $this->redirect ('/bind10/zonas/listar');
        }
        // mostrar formulario de edición si no se pidió guardar
        if (!isset($_POST['submit'])) {
            $this->set (array(
                'zona' => $Zona->name,
                'soa' => $Zona->getSoaRecord(),
                'usuario' => $Zona->getUsuario()->usuario,
                'records' => $Zona->getRecords(),
                '_header_extra' => array(
                    'js' => array('/bind10/js/bind10.js'),
                ),
            ));
        }
        // guardar datos de la zona si se envió el formulario
        else {
            $Usuario = new \sowerphp\app\Sistema\Usuarios\Model_Usuario($_POST['usuario']);
            if (!$Usuario->exists()) {
                \sowerphp\core\Model_Datasource_Session::message (
                    'Usuario('.$_POST['usuario'].') no existe', 'error'
                );
                $this->redirect ('/bind10/zonas/editar/'.$id);
            }
            $Zona->name = idn2($_POST['zona']);
            $Zona->usuario = $Usuario->id;
            $Zona->save();
            $Zona->saveSoaRecord(
                $_POST['soa_id'],
                $_POST['soa_ttl'],
                idn2($_POST['soa_host']),
                idn2(str_replace('@', '.', $_POST['soa_email'])),
                $_POST['soa_serial'],
                $_POST['soa_refresh'],
                $_POST['soa_retry'],
                $_POST['soa_expire']
            );
            foreach ($_POST['name'] as &$name) {
                $name = idn2($name);
            }
            $Zona->saveRecords(
                $_POST['id'],
                $_POST['name'],
                $_POST['rdtype'],
                $_POST['rdata'],
                $_POST['soa_ttl']
            );
            // redireccionar
            \sowerphp\core\Model_Datasource_Session::message (
                'Zona <em>'.$Zona->name.'</em> actualizada', 'ok'
            );
            $this->redirect ('/bind10/zonas/listar');
        }
    }

    /**
     * Acción para eliminar una zona del dns
     * @param id Identificador de la zona o la zona
     * @author Esteban De La Fuente Rubio, DeLaF (esteban[at]delaf.cl)
     * @version 2014-11-23
     */
    public function eliminar($id)
    {
        // crear zona solicitada y verificar que exista
        $Zona = new Model_Zona ($id);
        if (!$Zona->exists()) {
            \sowerphp\core\Model_Datasource_Session::message (
                'Zona('.$id.') no existe', 'error'
            );
            $this->redirect ('/bind10/zonas/listar');
        }
        if (!($Zona->usuario===$this->Auth->User->id or $this->Auth->User->inGroup(['bind10']))) {
            \sowerphp\core\Model_Datasource_Session::message (
                'Usted no es el propietario de la Zona('.$id.')', 'error'
            );
            $this->redirect ('/bind10/zonas/listar');
        }
        // eliminar la zona
        $Zona->delete();
        // redireccionar
        \sowerphp\core\Model_Datasource_Session::message (
            'Zona <em>'.$Zona->name.'</em> eliminada', 'ok'
        );
        $this->redirect ('/bind10/zonas/listar');
    }

    /**
     * Acción para exportar una zona del DNS
     * @param id Identificador de la zona o la zona
     * @author Esteban De La Fuente Rubio, DeLaF (esteban[at]delaf.cl)
     * @version 2014-11-23
     */
    private function exportar($id)
    {
        // crear zona solicitada y verificar que exista
        $Zona = new Model_Zona ($id);
        if (!$Zona->exists()) {
            \sowerphp\core\Model_Datasource_Session::message (
                'Zona('.$id.') no existe', 'error'
            );
            $this->redirect ('/bind10/zonas/listar');
        }
        if (!($Zona->usuario===$this->Auth->User->id or $this->Auth->User->inGroup(['bind10']))) {
            \sowerphp\core\Model_Datasource_Session::message (
                'Usted no es el propietario de la Zona('.$id.')', 'error'
            );
            $this->redirect ('/bind10/zonas/listar');
        }
        // crear arreglo con los datos de la zona
        $this->set(array(
            'zona' => $Zona->name,
            'data' => $Zona->data()
        ));
    }

    /**
     * Acción para exportar una zona del DNS a JSON
     * @param id Identificador de la zona o la zona
     * @author Esteban De La Fuente Rubio, DeLaF (esteban[at]delaf.cl)
     * @version 2014-04-05
     */
    public function json($id)
    {
        $this->exportar($id);
    }

    /**
     * Acción para exportar una zona del DNS a un archivo "normal" de bind
     * @param id Identificador de la zona o la zona
     * @author Esteban De La Fuente Rubio, DeLaF (esteban[at]delaf.cl)
     * @version 2014-04-05
     */
    public function descargar($id)
    {
        $this->exportar($id);
    }

    /**
     * Acción para importar una zona del DNS desde un archivo JSON
     * @author Esteban De La Fuente Rubio, DeLaF (esteban[at]delaf.cl)
     * @version 2014-11-23
     */
    public function importar()
    {
        if (isset($_FILES['archivo']) && !$_FILES['archivo']['error']) {
            $data = json_decode(file_get_contents($_FILES['archivo']['tmp_name']), true);
            if ($data===null) {
                \sowerphp\core\Model_Datasource_Session::message (
                    'En el archivo subido no se han encontrado datos válidos para importar', 'error'
                );
                $this->redirect ('/bind10/zonas/importar');
            }
            $Zona = new Model_Zona ($data['zone']['name']);
            if ($Zona->exists()) {
                \sowerphp\core\Model_Datasource_Session::message (
                    'Zona <em>'.$data['zone']['name'].'</em> ya existe, no se puede importar, solo editar', 'warning'
                );
                $this->redirect ('/bind10/zonas/editar/'.$data['zone']['name']);
            }
            $Zona->name = $data['zone']['name'];
            $Zona->rdclass = $data['zone']['rdclass'];
            $Zona->dnssec = $data['zone']['dnssec'];
            $Zona->usuario = $this->Auth->User->id;
            $Zona->save();
            $Zona->importSoaRecord($data['soa']);
            $Zona->importRecords($data['records']);
            \sowerphp\core\Model_Datasource_Session::message (
                'Zona <em>'.$Zona->name.'</em> importada', 'ok'
            );
        }
    }

    /**
     * Función de la API para actualizar los registros de una zona
     * @param zona FQDN de la zona que se está actualizando
     * @author Esteban De La Fuente Rubio, DeLaF (esteban[at]delaf.cl)
     * @version 2014-12-03
     */
    public function _api_crud_PATCH($zona)
    {
        // verificar autenticación, zona y permisos
        $User = $this->Api->getAuthUser();
        if (is_string($User))
            $this->Api->send($User, 401);
        if (substr($zona, -1)!='.')
            $zona .= '.';
        $Zona = new Model_Zona($zona);
        if (!$Zona->exists())
            $this->Api->send('Zona '.$zona.' no existe' , 400);
        if ($Zona->usuario!=$User->id)
            $this->Api->send('Usuario '.$User->usuario.' no es dueño de la zona '.$zona , 403);
        // asignar IP a registros pasados
        $ip = $this->Auth->ip();
        foreach($this->Api->data['registros'] as &$r) {
            if (!isset($r['ip'])) $r['ip'] = $ip;
            if (!isset($r['tipo'])) $r['tipo'] = 'A';
            $Zona->saveRecord($r['registro'].'.'.$Zona->name, $r['ip'], $r['tipo']);
        }
        $this->Api->send('Registro(s) de la zona '.$zona.' modificado(s)');
    }

}
