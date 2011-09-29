#!/bin/bash

#ubuntu
rsync -ruv --exclude 'Docs' --exclude 'test' --exclude '.*' --exclude 'nbproject' --exclude '*~' radiusadmin root@dori:/var/www/
#rsync -ruv --exclude '.*' radiusadmin root@dori.academic.exeter.k12.pa.us:/var/www/localhost/htdocs
