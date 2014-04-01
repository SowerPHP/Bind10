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

// namespace del modelo
namespace website\Bind10;

/**
 * Modelo Zona (para trabajar con un registro de la tabla)
 * @author Esteban De La Fuente Rubio, DeLaF (esteban[at]delaf.cl)
 * @version 2014-03-30
 */
class Model_Zona extends \Model_App
{

    protected $_database = 'bind10'; ///< Nombre de la configuración de BD
    public $id; ///< Identificador de la zona (ID incremental)
    public $name; ///< Nombre de la zona
    public $rdclass; ///< ¿?
    public $dnssec; ///< Si utiliza o no DNSSEC

    /**
     * Constructor del modelo
     * @param id Identificador de la zona o la zona
     * @author Esteban De La Fuente Rubio, DeLaF (esteban[at]delaf.cl)
     * @version 2014-03-30
     */
    public function __construct ($id = null)
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
     * @version 2014-03-30
     */
    private function get ($id = null)
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
            $this->_set ($this->db->getRow('
                SELECT *
                FROM zones
                WHERE id = \''.$this->db->sanitize($id).'\'
            '));
        } else {
            $this->_set ($this->db->getRow('
                SELECT *
                FROM zones
                WHERE name = \''.$this->db->sanitize($id).'\'
            '));
        }
    }

    /**
     * Método para determinar si la zona existe o no
     * @return =true si la zona existe o =false si no existe
     * @author Esteban De La Fuente Rubio, DeLaF (esteban[at]delaf.cl)
     * @version 2014-03-30
     */
    public function exists ()
    {
        return (boolean) $this->id;
    }

    /**
     * Métoddo que guarda la zona, se ebe haber asignado el nombre a la misma
     * @author Esteban De La Fuente Rubio, DeLaF (esteban[at]delaf.cl)
     * @version 2014-03-30
     */
    public function save ()
    {
        if (!$this->exists()) {
            $this->db->query ('
                INSERT INTO zones (name) VALUES (
                    \''.$this->db->sanitize($this->name).'\'
                )
            ');
        } else {
            $this->db->query ('
                UPDATE zones
                SET name = \''.$this->db->sanitize($this->name).'\'
                WHERE id = '.$this->db->sanitize($this->id)
            );
        }
        $this->get ();
    }

    /**
     * Método que obtiene el registro SOA de la zona
     * @return Arreglo con los datos del registro SOA
     * @author Esteban De La Fuente Rubio, DeLaF (esteban[at]delaf.cl)
     * @version 2014-03-30
     */
    public function getSoaRecord ()
    {
        $aux1 = $this->db->getRow ('
            SELECT id, rdata
            FROM records
            WHERE zone_id = '.$this->db->sanitize($this->id).' AND rdtype = \'SOA\'
        ');
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
     * @version 2014-03-30
     */
    public function getRecords ()
    {
        return $this->db->getTable ('
            SELECT id, name, rdtype, rdata
            FROM records
            WHERE
                zone_id = '.$this->db->sanitize($this->id).'
                AND rdtype != \'SOA\'
        ');
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
     * @version 2014-04-01
     */
    public function saveSoaRecord ($id, $ttl, $host, $email, $serial, $refresh, $retry, $expire)
    {
        // registro SOA nuevo
        if (empty($id)) {
            $this->db->query ('
                INSERT INTO records (zone_id, name, rname, ttl, rdtype, rdata) VALUES (
                    '.$this->db->sanitize($this->id).',
                    \''.$this->db->sanitize($this->name).'\',
                    \''.$this->db->sanitize($this->rzone($this->name)).'\',
                    '.$this->db->sanitize($ttl).',
                    \'SOA\',
                    \''.$this->db->sanitize($host).' '.$this->db->sanitize($email).' '.$this->db->sanitize($serial).' '.$this->db->sanitize($refresh).' '.$this->db->sanitize($retry).' '.$this->db->sanitize($expire).' '.$this->db->sanitize($ttl).'\'
                )
            ');
        }
        // actualizar registro SOA
        else {
            $this->db->query ('
                UPDATE records SET
                    name = \''.$this->db->sanitize($this->name).'\',
                    rname = \''.$this->rzone($this->db->sanitize($this->name)).'\',
                    rdata = \''.$this->db->sanitize($host).' '.$this->db->sanitize($email).' '.$this->db->sanitize($serial).' '.$this->db->sanitize($refresh).' '.$this->db->sanitize($retry).' '.$this->db->sanitize($expire).' '.$this->db->sanitize($ttl).'\'
                WHERE
                    id = '.$this->db->sanitize($id).'
            ');
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
     * @version 2014-03-30
     */
    public function saveRecords ($id, $name, $rdtype, $rdata, $ttl)
    {
        // borrar registros que no se salvaron
        $ids = [];
        foreach ($id as $i) {
            if (!empty($i)) $ids[] = $this->db->sanitize($i);
        }
        $this->db->query ('
            DELETE FROM records
            WHERE
                zone_id = '.$this->db->sanitize($this->id).'
                AND id NOT IN ('.implode(', ', $ids).')
                AND rdtype != \'SOA\'
        ');
        // iterar registros
        $n_records = count($id);
        for ($i=0; $i<$n_records; ++$i) {
            // si el registro existía se actualiza
            if ($id[$i]) {
                $this->db->query ('
                    UPDATE records SET
                        name = \''.$this->db->sanitize($name[$i]).'\',
                        rname = \''.$this->rzone($this->db->sanitize($name[$i])).'\',
                        rdtype = \''.$this->db->sanitize($rdtype[$i]).'\',
                        rdata = \''.$this->db->sanitize($rdata[$i]).'\'
                    WHERE id = '.$this->db->sanitize($id[$i]).'
                ');
            }
            // si el registro no existía se inserta
            else {
                $this->db->query ('
                    INSERT INTO records (zone_id, name, rname, ttl, rdtype, rdata) VALUES (
                        '.$this->db->sanitize($this->id).',
                        \''.$this->db->sanitize($name[$i]).'\',
                        \''.$this->rzone($this->db->sanitize($name[$i])).'\',
                        '.$this->db->sanitize($ttl).',
                        \''.$this->db->sanitize($rdtype[$i]).'\',
                        \''.$this->db->sanitize($rdata[$i]).'\'
                    )
                ');
            }
        }
    }

    /**
     * Método que entrega el nombre de la zona invertido
     * @param zone Nombre de la zona o dominio (subdominio)
     * @return Zona invertida
     * @author Esteban De La Fuente Rubio, DeLaF (esteban[at]delaf.cl)
     * @version 2014-03-30
     */
    private function rzone ($zone)
    {
        $rzona = '';
        $aux = explode ('.', $zone);
        $n = count ($aux);
        for ($i=$n-1; $i>=0; --$i) {
            if (isset($aux[$i][0]))
                $rzona .= $aux[$i].'.';
        }
        return $rzona;
    }

}
