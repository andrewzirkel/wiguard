#!/bin/bash

export PATH=$PATH:/usr/bin:/usr/local/bin

cd ../../../
mkdir -p wiguard-build/debian/wiguard
cd wiguard-build/debian

#control files
mkdir -p wiguard/DEBIAN
#rsync -ruv --exclude '.*' ../../trunk/Packaging/Debian/ wiguard/DEBIAN
cp ../../wiguard/Packaging/Debian/config wiguard/DEBIAN/
cp ../../wiguard/Packaging/Debian/control wiguard/DEBIAN/
cp ../../wiguard/Packaging/Debian/postinst wiguard/DEBIAN/
cp ../../wiguard/Packaging/Debian/templates wiguard/DEBIAN/
chmod a+rx wiguard/DEBIAN/config
chmod a+rx wiguard/DEBIAN/postinst

#site source
mkdir -p wiguard/var/www/radiusadmin
rsync -rupv --exclude 'Docs' --exclude 'test' --exclude '.*' --exclude 'nbproject' --exclude '*~' ../../wiguard/radiusadmin/ wiguard/var/www/radiusadmin
#set permissions
chmod -R a+rX wiguard/var/www/radiusadmin

#init files
mkdir -p wiguard/tmp/WiGuard
cp ../../wiguard/SQL/createradiusdb.sql wiguard/tmp/WiGuard/createradiusdb.sql
cp ../../wiguard/SQL/initialValues.sql wiguard/tmp/WiGuard/initialValues.sql
cp ../../wiguard/SQL/users.sql wiguard/tmp/WiGuard/users.sql
cp ../../wiguard/SQL/wiguardSchema.sql wiguard/tmp/WiGuard/wiguardSchema.sql

#create package
dpkg-deb -b wiguard
