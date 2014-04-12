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
 * Funcuión que entrega el nombre de la zona invertido
 * @param zone Nombre de la zona o dominio (subdominio)
 * @return Zona invertida
 * @author Esteban De La Fuente Rubio, DeLaF (esteban[at]delaf.cl)
 * @version 2014-04-05
 */
function rzone ($zone)
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


/**
 * Libidn2 Internationalized Domain Names (IDNA2008) conversion
 * @todo Programar función o bien utilizar extensión o comando de shell
 * @author Esteban De La Fuente Rubio, DeLaF (esteban[at]delaf.cl)
 * @version 2014-04-12
 */
function idn2 ($zone)
{
    return $zone;
}
