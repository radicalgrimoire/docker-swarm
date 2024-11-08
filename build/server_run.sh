#!/bin/bash
#Swarm Site Standby
apache2ctl start
#Redis Cache Server Standby
/opt/perforce/swarm/sbin/redis-server-swarm --daemonize yes
#swarm syncronized server trigger(cron) Standby
/etc/init.d/cron start

less -f /dev/null