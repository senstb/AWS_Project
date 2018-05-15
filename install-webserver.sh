#!/bin/bash

sudo apt-get update -y
sudo apt-get install -y apache2 git php5 php5-curl mysql-client curl php5-mysql
sudo apt-get install -y php5-imagick
git clone https://github.com/bsenst/environment-setup.git

mv ./Miniproject/images /var/www/html/images
mv ./Miniproject/index.html /var/www/html
mv ./Miniproject/gallery.php /var/www/html
mv ./Miniproject/index.php /var/www/html
mv ./Miniproject/setup.php /var/www/html
mv ./Miniproject/submit.php /var/www/html

curl -sS https://getcomposer.org/installer | sudo php &> getcomp.txt

sudo php composer.phar require aws/aws-sdk-php &> comp.txt

sudo mv vendor /var/www/html &> mvVen.txt
sudo php /var/www/html/setup.php &> /tmp/setupthing.txt
sudo chmod 600 /var/www/html/setup.php
#sudo php /var/www/html/dbcreate.php &> /tmp/tablecre.txt

echo "Installation complete!" > results.txt



