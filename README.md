wdCalendar
==========

wdCalendar is a jquery based google calendar clone. Based on http://www.web-delicious.com/jquery-plugins-demo/wdCalendar/docs/index.htm

1. Introduction
This is wdCalendar version2 and allowed to use freely (LGPL).

2. Browsers Supported
FireFox2.0+ IE6+ Opera9+ Safari3+ Chrome

3. Installation & Usage
3.1 docker image pull

```
$ docker pull php:5.6.31-apache 
```

3.2 Download wdCalendar

```
$ git clone https://github.com/ir-shin1/wdCalendar.git
```

3.3 Copy dbconfig.php, index.php, datafeed.php file

```
$ cd wdCalendar
$ cp -p php/dbconfig.php.sqlite php/dbconfig.php
$ cp -p sample-ja.php index.php
$ cp -p php/datafeed.db.php php/datafeed.php
```

3.4 start docker container and PHP timezone setup

```
$ mkdir -pm 777 db
$ chcon -Rt svirt_sandbox_file_t wdCalendar db
$ docker run -d --restart=always --name calendar_sv -v $PWD/db:/var/lib/db -v $PWD/wdCalendar:/var/www/html -p 9080:80 php:5.6.31-apache
$ docker exec -ti calendar_sv bash -c "echo -e \"[Date]\ndate.timezone ='Asia/Tokyo'\" >> /usr/local/etc/php/php.ini"
```

3.5 create SQLite DB

```
$ docker exec -ti -u 33 calendar_sv php -f /var/www/html/setup-sqlite.php
```

3.6 container restart
PHP timezone up

```
docker restart calendar_sv
```

3.7 Web access http://localhost:9080/

4. About web-delicious.com
We are an IT outsourcing company location in Shanghai, China.
We provide end-to-end solutions in web development (Web 2.0, PHP, ASP.NET, ASP, JSP, XML, Flash),
application development and IT consulting services at very reasonable price.
www.web-delicious.com


5.Credits
jQuery is a new kind of JavaScript Library. http://jquery.com/
wdCalendar Library. base script from http://www.web-delicious.com


## Nexts Steps

A code update is needed in order to comply with current versions of php, jQuery and browsers

