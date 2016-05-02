#!/usr/bin/expect

spawn sftp katesomerville@feeds.comparemetrics.com
expect "password:"
send "JQwtB#ohJ23:i(Q\n"
expect "sftp>"
send "cd data\n"
expect "sftp>"
send "put /var/vhosts/katesomerville.com/feeds/comparemetrics.csv\n"
expect "sftp>"
send "exit\n"
interact
