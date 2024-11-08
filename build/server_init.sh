#!/bin/bash
# Swarmサーバー初回セットアップ 項目指定オプションで自動化可
# -H で設定する値 Swarm-Host値は Perforceサーバーの P4.Swarm.URL値に利用されている。(http(s)://~は省略して)
# こちらが正しく指定のSwarmサーバーサイトに設定されていないとP4Vの**GUI上での**Swarmの操作ができなくなってしまうので注意。
# -c で新しいswarm管理アカウントを作成、-gで管理用グループを作成
/opt/perforce/swarm/sbin/configure-swarm.sh -n \
-p ssl:XXX:1666 -u swarm -w initialpasswd -U super -W Passw0rd -H serveraddress -e localhost
wait
cat << EOF
############
swarm initialize log....
############
EOF
cat /opt/perforce/swarm/data/configure-swarm.log

# apache起動中に構成ファイルをいじるとバグるのでプロセスを停止させてから行う
apache2ctl stop
# swarmのvirtualhost名を指定のサーバーIPにしておく
cp /swarm_init/apache_site/perforce-swarm-site.conf /etc/apache2/sites-available/perforce-swarm-site.conf

# swarmサーバー内でcronの設定を行う
cp /swarm_init/cron/swarm-cron-hosts.conf /opt/perforce/etc/swarm-cron-hosts.conf

# メール送信動作時のエラー対策
cp /swarm_init/Address.php /opt/perforce/swarm/vendor/laminas/laminas-mail/src/Address.php

# Swarm側で初期設定で作成されたログインチケット値を取得しないとエラーになるので書き換える
config_passwd=$(cat /opt/perforce/swarm/data/config.php | grep \'password\')
sed -i -e "s|'password' => '\${after_inject}',|${config_passwd}|g" /swarm_init/config_sso_mail.php
cp /swarm_init/config_sso_mail.php /opt/perforce/swarm/data/config.php

# サブミット時メモリ枯渇による動作失敗防止のためメモリ上限を増やす
sed -i -e "s|memory_limit = 128M|memory_limit = -1|g" /etc/php/7.4/apache2/php.ini
