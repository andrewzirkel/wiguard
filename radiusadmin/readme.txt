Make sure ths is in /Library/Preferences/com.deploystudio.server.plist under the security section:
                <key>allow3PClients</key>
                <string>YES</string>

Fixes:
2.5.7
- bug filxes in clients list
2.5.6
- allow self signed certs
2.5.5
- fix deploystudio bugs
2.5.4
- convert to mysqli
2.5.3
- allow use of id in csv import (addmac)
2.5.1
- unset workflows in DS
2.4
- fix some php errors
- add group (filter-id) attribute to radreply table
- fix deploystudio groups
- require computer name
2.3
2.2
- check for existing serial number.
- add search for serial.
2.1
- progress bars for long operations in deploy studio
- variable set errors
2.0.5a
- deploy studio defaults to serial number
1.4
- fixed security checks
