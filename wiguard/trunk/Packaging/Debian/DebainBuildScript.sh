#!/bin/bash

export PATH=$PATH:/usr/bin:/usr/local/bin

cd ../../../
mkdir -p build/debian/wiguard
cd build/debian

#control files
mkdir -p wiguard/DEBIAN
#rsync -ruv --exclude '.*' ../../trunk/Packaging/Debian/ wiguard/DEBIAN
cp ../../trunk/Packaging/Debian/config wiguard/DEBIAN/
cp ../../trunk/Packaging/Debian/control wiguard/DEBIAN/
cp ../../trunk/Packaging/Debian/postinst wiguard/DEBIAN/
cp ../../trunk/Packaging/Debian/templates wiguard/DEBIAN/
chmod a+rx wiguard/DEBIAN/config
chmod a+rx wiguard/DEBIAN/postinst

#site source
mkdir -p wiguard/var/www/radiusadmin
rsync -rupv --exclude 'Docs' --exclude 'test' --exclude '.*' --exclude 'nbproject' --exclude '*~' ../../trunk/radiusadmin/ wiguard/var/www/radiusadmin
#set permissions
chmod -R a+rX wiguard/var/www/radiusadmin

#init files
mkdir -p wiguard/tmp/WiGuard
cp ../../trunk/SQL/createradiusdb.sql wiguard/tmp/WiGuard/createradiusdb.sql
cp ../../trunk/SQL/initialValues.sql wiguard/tmp/WiGuard/initialValues.sql
cp ../../trunk/SQL/users.sql wiguard/tmp/WiGuard/users.sql
cp ../../trunk/SQL/wiguardSchema.sql wiguard/tmp/WiGuard/wiguardSchema.sql

#create package
dpkg-deb -b wiguard
