#!/bin/bash

ip="192.168.211.135"

#ubuntu
rsync -ruv --exclude 'Docs' --exclude 'test' --exclude '.*' --exclude 'nbproject' --exclude '*~' radiusadmin root@$ip:/var/www/

