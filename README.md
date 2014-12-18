Vegas CMF Filesystem Manager
============================


Tests
-----


Ftp server
----------

Test suite for FTP adapter uses a local FTP server. In the example we show how to setup local ftp server based on **vsftpd** service, which you can easily install in your system.
In the following steps we show how to install it in the Ubuntu system.

```
sudo apt-get install vsftpd
sudo vim /etc/vsftpd.conf
```

Find and setup the following lines:

```
anonymous_enable=YES
write_enable=YES
local_enable=YES
ftpd_banner=Welcome to Vegas Ftp Test Server
```

Restart FTP server

```
sudo service vsftpd restart
```

Now we should create an example user

```
sudo groupadd ftp-users
sudo mkdir /home/ftp-user
sudo chmod 775 /home/ftp-user
sudo chown ftp-user:ftp-users /home/ftp-user
sudo useradd -g ftp-users -d /home/ftp-user ftp-user
sudo passwd ftp-user
#(enter the password: test1234)
touch /home/ftp-user/hello.txt
echo "Hello Vegas" > /home/ftp-user/hello.txt
```

When everything is done, try to login to your local server using ftp-user account.

```
ftp localhost
```

Now you can run tests:

```
./vendor/bin/phpunit
```
