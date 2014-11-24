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

/**
 * @file core.php
 * Configuración del módulo
 */

// Menú para el módulo
\sowerphp\core\Configure::write('nav.module', array(
    '/zonas/listar' => array(
        'name' => 'Zonas',
        'desc' => 'Administrar zonas del DNS',
        'imag' => '/bind10/img/icons/48x48/dns.png',
    ),
    '/zonas/importar' => array(
        'name' => 'Importar zona',
        'desc' => 'Importar una zona a través de un archivo JSON',
        'imag' => '/bind10/img/icons/48x48/dns.png',
    ),
));

// Configuración para la base de datos
\sowerphp\core\Configure::write('database.bind10', array(
    'type' => 'SQLite',
    'file' => DIR_PROJECT.'/data/sqlite/default.sqlite3',
));
