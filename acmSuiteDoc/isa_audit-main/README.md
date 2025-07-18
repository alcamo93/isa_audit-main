<p align="center"><img src="/public/logos/acm_suite_logo_h.png" width="400"></p>

## EHS ACM Suite Audit and Compliance Management

Software desarrollado para la auditoria ambiental.

***Tecnologías y Librerias***

**Frontend:**  
- *HTML5*
- *Sass*
- *Boostrap 4 ~ 4.6.0*
- *Boostrap-vue 2.21.2*
- *JQuery*
- *Vue.js 2.5*
- *Moment 2.29.4*
- *Laravel Echo*
- *Vee validate 3.4.14*
- *Vue select 3.20.0*
- *Pusher.js*

**Backend:**  
- *PHP 7.4*
- *Laravel 8.0*
- *Laravel Excel 3.1*
- *Carbon*
- *Pusher server*
- *Intervention Image*
- *SDK para PHP de AWS*

## Ramas de Git

- Producción: **master**
- Desarrollo: **isa_development**

## Deploy para Desarrollo

**Instalación de paquetes de PHP y Node**  
`composer install`  
`npm install`  

**Configuración de variables en archivo .env**  
`cp .env.example .env`  

**Notificaciones**  

`BROADCAST_DRIVER=pusher`  

Copiar los siguientes valores:  

`DEV_PUSHER_APP_ID=1270110`  
`DEV_PUSHER_APP_KEY=64c3cd45350497a6e159`  
`DEV_PUSHER_APP_SECRET=c0d183b7175625677c67`  
`DEV_PUSHER_APP_CLUSTER=us2`  

a las claves:  

`PUSHER_APP_ID=`  
`PUSHER_APP_KEY=`  
`PUSHER_APP_SECRET=`  
`PUSHER_APP_CLUSTER=`  

**Especificar claves de motor de Base de datos**  

`DB_CONNECTION=mysql`  
`DB_HOST=127.0.0.1`  
`DB_PORT=3306`  
`DB_DATABASE=tu_base_de_datos`  
`DB_USERNAME=tu_usuario`  
`DB_PASSWORD=tu_password`  

**Especificar claves para envio de correos**  
`MAIL_DRIVER=smtp`  
`MAIL_HOST=smtp.gmail.com`  
`MAIL_PORT=465`  
`MAIL_USERNAME=your_account@gmail.com`  
`MAIL_PASSWORD=password`  
`MAIL_ENCRYPTION=ssl` 

**Especificar zona horaria para cronjobs y calculos de fechas**  
*America/Mexico_City or America/Chihuahua*  
`TIME_ZONE_CARBON="America/Mexico_City"`  

**Especificar modo de almacenamiento de archivos**  

`PRODUCTION_STORAGE=false`  
- *Colocar VIEW_IMAGE_PRODUCTION_IN_DEV=true si desea solo ver los archivos almacenados en AWS S3 para desarrollo*  
`VIEW_IMAGE_PRODUCTION_IN_DEV=false`  

**Especificar driver de Queues para procesos en segundo planno**  
`QUEUE_CONNECTION=database`

**Ejecutar Queues en entorno local**  
`php artisan queue:work database --sleep=3 --tries=2 --delay=5`  

**Últimos ajustes**  
`php artisan key:genarate`  
`composer dump-autoload`  
`php artisan storage:link` 

**Ajustes para frontend**  
`npm run dev`  
`npm run watch`  

**Ajustes para backend**  
*copiar base de datos de producción y ejecutar el siguiente comando*   
`php artisan db:seed --class=DatabaseProductionTruncate && php artisan db:seed --class=DatabaseProductionStep1 && php artisan migrate && php artisan db:seed --class=DatabaseProductionStep2`  

## Deploy para Producción

**Instalación de paquetes de PHP y Node**  
`composer install`  
*npm se debe ejecutar dentro de local junto con el comando 'npm run prod'*  
`npm install`  

**Configuración de variables en archivo .env**  
`cp .env.example .env`  

**Notificaciones**  

`BROADCAST_DRIVER=pusher`  

Copiar los siguientes valores:  

`PRODUCTION_PUSHER_APP_ID=1023672`  
`PRODUCTION_PUSHER_APP_KEY=109bae0517205c2dae55`  
`PRODUCTION_PUSHER_APP_SECRET=7324c2de4a55f2dd0d8e`  
`PRODUCTION_PUSHER_APP_CLUSTER=us2`  

a las claves:  

`PUSHER_APP_ID=`  
`PUSHER_APP_KEY=`  
`PUSHER_APP_SECRET=`  
`PUSHER_APP_CLUSTER=`  

**Especificar claves de motor de Base de datos**  

`DB_CONNECTION=mysql`  
`DB_HOST=127.0.0.1`  
`DB_PORT=3306`  
`DB_DATABASE=tu_base_de_datos`  
`DB_USERNAME=tu_usuario`  
`DB_PASSWORD=tu_password`  

**Especificar claves para envio de correos**  
*Verificar que siga proyecto hospedado en AWS*  
`MAIL_DRIVER=smtp`  
`MAIL_HOST=email-smtp.us-east-2.amazonaws.com`  
`MAIL_PORT=587`  
`MAIL_USERNAME=AKIA6NHV6B4GNXWY7AUY`  
`MAIL_FROM_ADDRESS=delivery@ehs-acmsuite.com`  
`MAIL_PASSWORD=BAzuQyN3Ky5iABv1c8o3zlkELdj6G1Nfcfp5QS/MmEtv`  
`MAIL_ENCRYPTION=tls`  

`AWS_DEFAULT_REGION=us-east-2`  

**Especificar zona horaria para cronjobs y calculos de fechas**  
*America/Mexico_City or America/Chihuahua*  
`TIME_ZONE_CARBON="America/Mexico_City"`  

**Especificar modo de almacenamiento de archivos**  

`PRODUCTION_STORAGE=true`  
`VIEW_IMAGE_PRODUCTION_IN_DEV=false`  

`AWS_ACCESS_KEY_ID=AKIAZKRYU7VPB6KXJNGZ`  
`AWS_SECRET_ACCESS_KEY=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`  
`AWS_DEFAULT_REGION=us-east-2`  
`AWS_BUCKET=ehs-acmsuite-bucket-v1`  
`AWS_URL=ehs-acmsuite-bucket-v1.s3.us-east-2.amazonaws.com`  
`AWS_USE_PATH_STYLE_ENDPOINT=true`  

**Especificar driver de Queues para procesos en segundo planno**  
`QUEUE_CONNECTION=database`

**Configuración de Supervisor para manejo de Queues**  

*Instalar supervisor en VPS*  
`sudo apt install supervisor`  

*Creación de configuración por proceso*  
`cd /etc/supervisor/conf.d`  
`nano pro-ehs-acmsuite.conf`  

*Contenido de la confifuración de proceso*  
`[program:pro-ehs-acmsuite]`  
`process_name=%(program_name)s_%(process_num)02d`  
`command=php /opt/bitnami/projects/production-ehs-acmsuite/artisan queue:work database --sleep=3 --tries=2 --delay=5`  
`autostart=true`  
`autorestart=true`  
`stopasgroup=true`  
`killasgroup=true`  
`;user=forge`  
`numprocs=8`  
`redirect_stderr=true`  
`stdout_logfile= /opt/bitnami/projects/scripts/pro-ehs-acmsuite-queue.log`  
`stopwaitsecs=3600`  
`startsecs=0`  

*Ejecuntando procesos en Supervisor*  
`sudo supervisorctl reread`  
`sudo supervisorctl update`  
`sudo supervisorctl start pro-ehs-acmsuite:*`  

**Últimos ajustes**  
`php artisan key:genarate`  
`composer dump-autoload`  
`php artisan storage:link` 

**Ajustes para frontend**  
*Ejecutar localmente*  
`npm run prod`  

**Ajustes para backend**  
`php artisan migrate`  

**Programar cronjobs**  
*Dentro de la terminal del servidor de producción*  
`root@ehs-acmsuite:~#crontab -e`  
*Escribir el siguiente cron*  
`* * * * * cd /path-to-your-project && php artisan schedule:run >> /dev/null 2>&1` 

**Optimización de mapeo de recursos del framework**  
`composer install --optimize-autoloader --no-dev`  
*Borra de carpeta vendor los servicios que no se usan solo mantiene librerias especificadas en composer.json en la propiedad 'require'*  

## Consideraciones  

- Si necesita visualizar la consola de **Pusher** crear un app con una cuenta de pusher y especificar sus credenciales en el archivo **.env**  
- La cuenta de correo especificado en este documento es una cuenta de correo dentro del servidor de producción verificar que aún siga vigente  
- Para probar los cronjobs por separado en desarrollo ejecutar el comando **php artisan** donde se mostraran los comandos de cada uno de los cronjobs  
