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
 * Modelo Zonas (para trabajar con varios registros de la tabla)
 * @author Esteban De La Fuente Rubio, DeLaF (esteban[at]delaf.cl)
 * @version 2014-11-23
 */
class Model_Zonas
{

    /**
     * Constructor del modelo
     * @author Esteban De La Fuente Rubio, DeLaF (esteban[at]delaf.cl)
     * @version 2014-03-30
     */
    public function __construct()
    {
        $this->db = &\sowerphp\core\Model_Datasource_Database::get('bind10');
    }

    /**
     * Método que entrega el listado de todas las zonas
     * @author Esteban De La Fuente Rubio, DeLaF (esteban[at]delaf.cl)
     * @version 2014-11-23
     */
    public function getAll($user)
    {
        return $this->db->getTable('
            SELECT * FROM (
                SELECT u.usuario, z.id, z.name, z.rdclass, z.dnssec, COUNT(*) AS records
                FROM zones AS z LEFT JOIN records AS r ON z.id = r.zone_id, usuario AS u
                WHERE z.usuario = u.id AND u.id = :user
                GROUP BY z.id, z.name, z.rdclass, z.dnssec
                ORDER BY z.name
            ) AS t1
            UNION
            SELECT * FROM (
                SELECT u.usuario, z.id, z.name, z.rdclass, z.dnssec, COUNT(*) AS records
                FROM zones AS z LEFT JOIN records AS r ON z.id = r.zone_id, usuario AS u
                WHERE z.usuario = u.id AND u.id != :user
                GROUP BY z.id, z.name, z.rdclass, z.dnssec
                ORDER BY u.usuario, z.name
            ) AS t2
        ', [':user'=>$user]);
    }

    /**
     * Método que entrega el listado de las zonas de un usuario
     * @author Esteban De La Fuente Rubio, DeLaF (esteban[at]delaf.cl)
     * @version 2014-11-23
     */
    public function getByUser($user)
    {
        return $this->db->getTable('
            SELECT z.id, z.name, z.rdclass, z.dnssec, COUNT(*) AS records
            FROM zones AS z LEFT JOIN records AS r ON z.id = r.zone_id
            WHERE z.usuario = :user
            GROUP BY z.id, z.name, z.rdclass, z.dnssec
            ORDER BY z.name
        ', [':user'=>$user]);
    }

}
