SowerPHP: Módulo Bind10 
=======================

Módulo para administrar las zonas de un servidor DNS bind10.

Instalación
-----------

1.	Descargar módulo:

		$ cd website/Module
		$ git clone https://github.com/SowerPHP/Bind10.git

2.	Habilitar módulo en *website/Config/core.php*:

		Module::uses (array(
			'Bind10'
		));

3.	El archivo de zonas debe estar ubicado en:

		data/sqlite/zone.sqlite3
