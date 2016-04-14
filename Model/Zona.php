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

// namespace del modelo
namespace website\Bind10;

/**
 * Modelo Zona (para trabajar con un registro de la tabla)
 * @author Esteban De La Fuente Rubio, DeLaF (esteban[at]delaf.cl)
 * @version 2014-12-03
 */
class Model_Zona extends \Model_App
{

    protected $_database = 'bind10'; ///< Nombre de la configuración de BD
    public $id; ///< Identificador de la zona (ID incremental)
    public $name; ///< Nombre de la zona
    public $rdclass = 'IN'; ///< ¿?
    public $dnssec = 0; ///< Si utiliza o no DNSSEC
    public $usuario; ///< Propietario del dominio en el DNS

    public static $fkNamespace = array(
        'Model_Usuario' => 'sowerphp\app\Sistema\Usuarios',
    ); ///< Namespaces que utiliza esta clase

    /**
     * Constructor del modelo
     * @param id Identificador de la zona o la zona
     * @author Esteban De La Fuente Rubio, DeLaF (esteban[at]delaf.cl)
     * @version 2014-03-30
     */
    public function __construct($id = null)
    {
        parent::__construct ();
        if ($id) {
            $this->get ($id);
        }
    }

    /**
     * Método para obtener los atributos de la zona
     * @param id Identificador de la zona o la zona
     * @author Esteban De La Fuente Rubio, DeLaF (esteban[at]delaf.cl)
     * @version 2014-07-01
     */
    public function get($id = null)
    {
        // si el ID es nulo se busca a través del ID o del nombre
        if (!$id) {
            if ($this->id || $this->name) {
                $id = $this->id ? $this->id : $this->name;
            } else {
                return;
            }
        }
        // obtener atributos de la zona
        if (is_numeric($id)) {
            $this->set ($this->db->getRow('
                SELECT *
                FROM zones
                WHERE id = :id
            ', [':id'=>$id]));
        } else {
            $this->set ($this->db->getRow('
                SELECT *
                FROM zones
                WHERE name = :name
            ', [':name'=>$id]));
        }
    }

    /**
     * Método para determinar si la zona existe o no
     * @return =true si la zona existe o =false si no existe
     * @author Esteban De La Fuente Rubio, DeLaF (esteban[at]delaf.cl)
     * @version 2014-03-30
     */
    public function exists()
    {
        return (boolean) $this->id;
    }

    /**
     * Métoddo que guarda la zona, se ebe haber asignado el nombre a la misma
     * @author Esteban De La Fuente Rubio, DeLaF (esteban[at]delaf.cl)
     * @version 2014-11-23
     */
    public function save()
    {
        if (!$this->exists()) {
            $this->db->query ('
                INSERT INTO zones (name, rdclass, dnssec, usuario) VALUES (
                    :name, :rdclass, :dnssec, :usuario
                )
            ', [
                ':name' => $this->name,
                ':rdclass' => $this->rdclass,
                ':dnssec' =>  $this->dnssec,
                ':usuario' => $this->usuario,
            ]);
        } else {
            $this->db->query ('
                UPDATE zones
                SET name = :name, rdclass = :rdclass, dnssec = :dnssec, usuario = :usuario
                WHERE id = :id
            ', [
                ':id' => $this->id,
                ':name' => $this->name,
                ':rdclass' => $this->rdclass,
                ':dnssec' =>  $this->dnssec,
                ':usuario' => $this->usuario,
            ]);
        }
        $this->get ();
    }

     /**
     * Método que borra la zona y sus registros
     * @author Esteban De La Fuente Rubio, DeLaF (esteban[at]delaf.cl)
     * @version 2014-04-28
     */
    public function delete()
    {
        $this->db->query('DELETE FROM records WHERE zone_id = :id', [':id'=>$this->id]);
        $this->db->query('DELETE FROM zones WHERE id = :id', [':id'=>$this->id]);
    }

    /**
     * Método que obtiene el registro SOA de la zona
     * @return Arreglo con los datos del registro SOA
     * @author Esteban De La Fuente Rubio, DeLaF (esteban[at]delaf.cl)
     * @version 2014-04-28
     */
    public function getSoaRecord()
    {
        $aux1 = $this->db->getRow ('
            SELECT id, rdata
            FROM records
            WHERE zone_id = :id AND rdtype = \'SOA\'
        ', [':id'=>$this->id]);
        if (isset($aux1['rdata'])) {
            $aux2 = explode (' ', $aux1['rdata']);
            $soa = array (
                'soa_id' => $aux1['id'],
                'soa_host' => $aux2[0],
                'soa_email' => $aux2[1],
                'soa_serial' => $aux2[2],
                'soa_refresh' => $aux2[3],
                'soa_retry' => $aux2[4],
                'soa_expire' => $aux2[5],
                'soa_ttl' => $aux2[6],
            );
        } else {
            $soa = array (
                'soa_id' => '',
                'soa_host' => '',
                'soa_email' => '',
                'soa_serial' => date('YmdH'),
                'soa_refresh' => 172800,
                'soa_retry' => 900,
                'soa_expire' => 1209600,
                'soa_ttl' => 3600,
            );
        }
        return $soa;
    }

    /**
     * Método que obtiene todos los registros de una zona, excepto el SOA
     * @return Arreglo con los registros de la zona
     * @author Esteban De La Fuente Rubio, DeLaF (esteban[at]delaf.cl)
     * @version 2014-04-28
     */
    public function getRecords()
    {
        return $this->db->getTable ('
            SELECT id, name, rdtype, rdata
            FROM records
            WHERE zone_id = :id AND rdtype != \'SOA\'
        ', [':id'=>$this->id]);
    }

    /**
     * Método que guarda el registro SOA
     * @param id Identificador del registro SOA
     * @param ttl
     * @param host
     * @param email
     * @param serial
     * @param refresh
     * @param retry
     * @param expire
     * @author Esteban De La Fuente Rubio, DeLaF (esteban[at]delaf.cl)
     * @version 2014-04-28
     */
    public function saveSoaRecord($id, $ttl, $host, $email, $serial, $refresh, $retry, $expire)
    {
        // registro SOA nuevo
        if (empty($id)) {
            $this->db->query ('
                INSERT INTO records (zone_id, name, rname, ttl, rdtype, rdata) VALUES (
                    :zone_id,
                    :name,
                    :rname,
                    :ttl,
                    :rdtype,
                    :rdata
                )
            ', [
                ':zone_id' => $this->id,
                ':name' => $this->name,
                ':rname' => rzone($this->name),
                ':ttl' => $ttl,
                ':rdtype' => 'SOA',
                ':rdata' => $host.' '.$email.' '.$serial.' '.$refresh.' '.$retry.' '.$expire.' '.$ttl
            ]);
        }
        // actualizar registro SOA
        else {
            $this->db->query ('
                UPDATE records SET
                    name = :name,
                    rname = :rname,
                    rdata = :rdata
                WHERE id = :id
            ', [
                ':id' => $id,
                ':name' => $this->name,
                ':rname' => rzone($this->name),
                ':rdata' => $host.' '.$email.' '.$serial.' '.$refresh.' '.$retry.' '.$expire.' '.$ttl
            ]);
        }
    }

    /**
     * Método que guarda los registros
     * @param id Arreglo con los identificadores de los registros
     * @param name Arreglo con los nombres (dominios/zona) de los registros
     * @param rdtype Arreglo con los tipos de registro
     * @param rdata Arreglo con los datos de cada registro
     * @param ttl
     * @author Esteban De La Fuente Rubio, DeLaF (esteban[at]delaf.cl)
     * @version 2014-04-28
     */
    public function saveRecords($id, $name, $rdtype, $rdata, $ttl)
    {
        // borrar registros que no se salvaron
        $this->db->query ('
            DELETE FROM records
            WHERE
                zone_id = :zone_id
                AND id NOT IN ('.implode(', ', array_map('intval', $id)).')
                AND rdtype != \'SOA\'
        ', [
            ':zone_id' => $this->id
        ]);
        // iterar registros
        $n_records = count($id);
        for ($i=0; $i<$n_records; ++$i) {
            // si el registro existía se actualiza
            if ($id[$i]) {
                $this->db->query ('
                    UPDATE records SET
                        name = :name,
                        rname = :rname,
                        rdtype = :rdtype,
                        rdata = :rdata
                    WHERE id = :id
                ', [
                    ':name' => $name[$i],
                    ':rname' => rzone($name[$i]),
                    ':rdtype' => $rdtype[$i],
                    ':rdata' => $rdata[$i],
                    ':id' => $id[$i],
                ]);
            }
            // si el registro no existía se inserta
            else {
                $this->db->query ('
                    INSERT INTO records (zone_id, name, rname, ttl, rdtype, rdata) VALUES (
                        :zone_id, :name, :rname, :ttl, :rdtype, :rdata
                    )
                ', [
                    ':zone_id' => $this->id,
                    ':name' => $name[$i],
                    ':rname' => rzone($name[$i]),
                    ':ttl' => $ttl,
                    ':rdtype' => $rdtype[$i],
                    ':rdata' => $rdata[$i]
                ]);
            }
        }
    }

    /**
     * Método para actualizar un registro de la zona
     * @param name Nombre del registro en formato FQDN
     * @param data Datos o valor para el registro
     * @param type Tipo de registro (A, AAAA, NS, etc)
     * @author Esteban De La Fuente Rubio, DeLaF (esteban[at]delaf.cl)
     * @version 2014-12-03
     */
    public function saveRecord($name, $data, $type = 'A')
    {
        $this->db->query('
            UPDATE records
            SET rdata = :data
            WHERE zone_id = :zone AND name = :name AND rdtype = :type
        ', ['zone'=>$this->id, ':name'=>$name, ':data'=>$data, ':type'=>$type]);
    }

    /**
     * Método que obtiene todos los registros de una zona (con todos sus datos)
     * @return Arreglo con los registros de la zona
     * @author Esteban De La Fuente Rubio, DeLaF (esteban[at]delaf.cl)
     * @version 2014-04-28
     */
    public function data()
    {
        return [
            'zone' => [
                'name'    => $this->name,
                'rdclass' => $this->rdclass,
                'dnssec'  => $this->dnssec
            ],
            'soa'         => $this->db->getRow ('
                                SELECT name, ttl, rdtype, sigtype, rdata
                                FROM records
                                WHERE
                                    zone_id = :zone_id
                                    AND rdtype = \'SOA\'
                            ', [':zone_id'=>$this->id]),
            'records'     => $this->db->getTable ('
                                SELECT name, ttl, rdtype, sigtype, rdata
                                FROM records
                                WHERE
                                    zone_id = :zone_id
                                    AND rdtype != \'SOA\'
                                ORDER BY rname, id ASC
                            ', [':zone_id'=>$this->id]),
        ];
    }

    /**
     * Método que importa un registro SOA
     * @param data Arreglo con los datos exportados del registro SOA
     * @author Esteban De La Fuente Rubio, DeLaF (esteban[at]delaf.cl)
     * @version 2014-04-28
     */
    public function importSoaRecord($data)
    {
        $this->db->query ('
            INSERT INTO records (zone_id, name, rname, ttl, rdtype, sigtype, rdata) VALUES (
                :zone_id,
                :name,
                :rname,
                :ttl,
                :rdtype,
                :sigtype,
                :rdata
            )
        ', [
            ':zone_id' => $this->id,
            ':name' => $this->name,
            ':rname' => rzone($this->name),
            ':ttl' => $data['ttl'],
            ':rdtype' => 'SOA',
            ':sigtype' => $data['sigtype'],
            ':rdata' => $data['rdata']
        ]);
    }

    /**
     * Método que importa múltiples registros
     * @param records Arreglo con los datos exportados de los registros
     * @author Esteban De La Fuente Rubio, DeLaF (esteban[at]delaf.cl)
     * @version 2014-04-28
     */
    public function importRecords($records)
    {
        foreach ($records as &$record) {
             $this->db->query ('
                INSERT INTO records (zone_id, name, rname, ttl, rdtype, sigtype, rdata) VALUES (
                    :zone_id,
                    :name,
                    :rname,
                    :ttl,
                    :rdtype,
                    :sigtype,
                    :rdata
                )
            ', [
                ':zone_id' => $this->id,
                ':name' => $record['name'],
                ':rname' => rzone($record['name']),
                ':ttl' => $record['ttl'],
                ':rdtype' => $record['rdtype'],
                ':sigtype' => $record['sigtype'],
                ':rdata' => $record['rdata']
            ]);
        }
    }

}
