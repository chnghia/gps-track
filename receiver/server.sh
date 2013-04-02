#!/bin/bash

while [ 1 ]; do
  /opt/lampp/bin/php -q /srv/www/GPStrack.com/htdocs/receiver/server.php;
  sleep 1;
done