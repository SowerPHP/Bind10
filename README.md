SowerPHP: Módulo Bind10
=======================

Módulo para administrar las zonas de un servidor DNS bind10.

Este módulo es usado por la aplicación [Bind10 de SASCO SpA](https://github.com/sascocl/bind10-webapp).

**Recomendación**: usar [Cloudflare](https://www.cloudflare.com) para administrar los DNS.

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

Extensión PHP: idn2
-------------------

**Esto nunca se probó**

Se recomienda tener instalada la extensión idn2 para PHP, de esta forma los
dominios con caracteres internacionales (por ejemplo eñes) serán convertidos al
formato estándar requerido por el servidor DNS.

La extensión se encuentra disponible en <http://pecl.p4.net/download.htm>.

	# apt-get install libidn2-0
	# wget -c http://pecl.p4.net/files/phpext/idn2.so
	# mv idn2.so /usr/lib/php5/20121212/
	# echo "extension=idn2.so" > /etc/php5/mods-available/idn2.ini
	# ln -s /etc/php5/mods-available/idn2.ini \
	    /etc/php5/apache2/conf.d/30-idn2.ini

El directorio de extensiones real (en este caso 20121212) puede ser verificado a
través del comando:

	# php -r "@phpinfo();" | grep ^extension_dir | awk '{print $3}'
