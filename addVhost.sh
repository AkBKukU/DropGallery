#!/bin/bash
if [[ $EUID -ne 0 ]] 
then
		echo "You need to run this as root to be able to make system changes"
	echo -e "Run \"sudo $0\" next time"
	exit 1
fi

cp galleryTest.conf /etc/apache2/sites-available/

a2ensite
a2disite




service apache2 restart

echo "
127.0.0.1 local.akbkuku.org" >> /etc/hosts
