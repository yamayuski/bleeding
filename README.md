# Bleeding Edge PHP 8 Framework

## Install

- [clone this repository](https://github.com/il-m-yamagishi/bleeding)
- [Install mkcert](https://github.com/FiloSottile/mkcert)
- Install docker & docker-compose in any style

```
$ mkcert "*.example.com"

Created a new certificate valid for the following names 📜
 - "*.example.com"

Reminder: X.509 wildcards only go one level deep, so this won't match a.b.example.jp ℹ️

The certificate is at "./_wildcard.example.com.pem" and the key at "./_wildcard.example.com-key.pem" ✅

$ cp *.pem container/nginx/
$ cp .env.example .env
$ docker-compose up -d
# add `docker-ip bleeding.example.com` to hosts file
$ vi /etc/hosts
$ curl https://bleeding.example.com
{"Hello":"world"}
```