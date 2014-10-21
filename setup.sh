#!/bin/bash

sudo apt-get update
sudo apt-get install nodejs npm git curl apache2
sudo a2enmod proxy_http
git clone https://github.com/tylerwbell/TBD-GTThriftShop
cd TBD-GTThriftShop/web-client
sudo npm install -g gulp bower
npm install
sudo vi /usr/local/bin/bower
sudo vi /usr/local/bin/gulp
bower install
gulp debug &
cd ../
sudo mv config/apache/000-default.conf /etc/apache2/sites-enabled/000-default.conf
sudo service apache2 restart
