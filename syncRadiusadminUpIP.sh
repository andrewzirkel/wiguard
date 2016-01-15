#!/bin/bash

ip="192.168.211.128"

#ubuntu
rsync -ruv --exclude 'Docs' --exclude 'test' --exclude '.*' --exclude 'nbproject' --exclude '*~' radiusadmin root@$ip:/var/www/
#rsync -ruv --exclude '.*' radiusadmin root@dori.academic.exeter.k12.pa.us:/var/www/localhost/htdocs
