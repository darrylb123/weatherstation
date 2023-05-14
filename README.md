# weatherstation
Weatherstation proxy to Weather Underground


Original code posted here: https://community.home-assistant.io/t/weather-station-and-weather-underground-work-around/204443
Modified to use with Fedora Linux

## Install httpd
```
$ sudo dnf install httpd php
```

## Clone the repo
You will need to create the directory /var/www/html/weatherstation and change the owner to yourself
```
$ sudo mkdir /var/www/html/weatherstation
$ sudo chown <you>:<you> /var/www/html/weatherstation
```

## Enable http and https access through firewalld
```
$ sudo firewall-cmd --zone=public --add-service=http
$ sudo firewall-cmd --zone=public --add-service=https
```

## Add PHP MQTT
```
$ composer require php-mqtt/client
```
## Configure weather station 
Configure the ip address of your proxy instead of weatherunderground. This will differ with each weather station YMMV

## Configure SELinux to allow access to the various sockets etc

```
# cd /var/log/audit
# grep php-fpm audit.log  | audit2allow -a -M fix
# semodule -i fix.pp
``` 
You will need to do this several times as the script progresses until all the SELinux restrictions are removed
Run a tail on the PHP error log until the permission denied errors stop
```
$ sudo tail -f /var/log/php-fpm/www-error.log
```


