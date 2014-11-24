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

		data/sqlite/default.sqlite3

	**Nota**: tanto el archivo de la base de datos como el directorio padre
	(sqlite) deben ser escribibles por el usuario web.

4.	Se debe cargar en la base de datos de bind10 las tablas para usuario de
	la extensión sowerphp/app módulo Sistema/Usuarios. Esto creará el
	usuario *admin* con contraseña *admin*.

5.	Cargar en la base de datos de bind10 el script ubicado en
	*Model/Sql/bind10.sql*.
