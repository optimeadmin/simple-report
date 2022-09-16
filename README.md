# Simple Report Bundle

Un bundle que permite la creación de reportes a partir de un query o un servicio

## Instalacion

    composer require optimeconsulting/simple-report
## Configuración
Una vez instalado el bundle si en el proceso de instalación no se registro automaticamente debemos hacerlo nosotros en el config/bundles.php 

    <?php
    
    return [
        ...
        Optime\Acl\Bundle\OptimeAclBundle::class => ['all' => true],
    ];

Y debemos agregar en siguiente código en el config/router.yaml

    simplereport:  
      resource: "@OptimeSimpleReportBundle/src/Controller"  
      type: annotation
Por último debemos crear la tabla donde vamos a registrar los reportes, para ellos podemos usar el comando

    symfony console doctrine:schema:update -f
Ya con esto estaremos listos para crear nuestro primero reporte

## Uso
Para crear los reportes podemos acceder a la ruta /simplereport donde contaremos con un crud que nos ayudará en esta tarea. Los campos que debemos completar son los siguientes
**Name:** nombre del reporte
**Slug:** identificador único del reporte. Es lo que se usará para crear la ruta del mismo
**Query String:** Query que usaremos para generar el reporte, por ejemplo

    SELECT firstname, lastname, email FROM users
En caso de que el reporte sea de tipo `service` no hace falta llenar este campo
**Active:** lo activamos si deseamos que el reporte salga en el listo y se puede descargar
**Type:** podemos escoger entre `query_string` y `service`

Si queremos crear un reporte de tipo `service `solo debemos crear una clase que implemente `Optime\SimpleReport\Bundle\Service\ReportInterface` donde `getDataArray()` será donde incluiremos la lógica de nuestro reporte devolviendo un arreglo y en `getSlug()` colocaremos el mismo slug que registramos en el dashboard
