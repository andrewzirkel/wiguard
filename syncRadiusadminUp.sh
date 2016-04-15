#!/bin/bash

#ubuntu
rsync -ruv --exclude 'Docs' --exclude 'test' --exclude '.*' --exclude 'nbproject' --exclude '*~' radiusadmin root@wiguard:/var/www/

