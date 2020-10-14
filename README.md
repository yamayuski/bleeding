# Bleeding Edge PHP 8 Framework

## Introduction

- PHP 8 Ready
- Ultra thin framework
    - No [nikic/fast-route](https://github.com/nikic/FastRoute)
    - No route parameters
    - GET & POST HTTP method only(RESTful is too complex)
    - Functional controller(No Instance or state needed)
    - Controller attributes
    - Accepts `application/json` first, `multipart/form-data` second
    - returns only `application/json`
- PHP Standard Recommendation(PSR) first
    - PSR-3 Log ready, powered by [monolog](https://github.com/Seldaek/monolog)
    - PSR-4 Autoload ready, powered by [composer](https://getcomposer.org/)
    - PSR-7, PSR-17 HTTP ready, powered by [laminas-diactoros](https://docs.laminas.dev/laminas-diactoros/)
    - PSR-11 DI ready, powered by [PHP-DI 6](https://php-di.org/)
    - PSR-12 ready, powered by [PHP CodeSniffer](https://github.com/squizlabs/PHP_CodeSniffer)
    - PSR-15 RequestHandler, Middleware ready, powered by [Relay](http://relayphp.com/)

## Developer Install

- clone this repository
- [Install mkcert](https://github.com/FiloSottile/mkcert)
- Install docker & docker-compose in any style

```
# installs TLS certification
$ mkcert "*.example.com"

Created a new certificate valid for the following names 📜
 - "*.example.com"

Reminder: X.509 wildcards only go one level deep, so this won't match a.b.example.com ℹ️

The certificate is at "./_wildcard.example.com.pem" and the key at "./_wildcard.example.com-key.pem" ✅

$ cp *.pem container/nginx/
$ cp .env.example .env
$ docker-compose up -d
$ docker-compose exec workspace composer install --ignore-platform-req=php

# add `docker-ip bleeding.example.com` to hosts file
$ vi /etc/hosts

$ curl https://bleeding.example.com
{"Hello":"world"}
```
