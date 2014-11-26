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

Amazon S3 Server
----------------
Test suite for S3 adapter uses a local fake S3 server. It provides by ruby gem called **FakeS3**.
For more information check the following link: [https://github.com/jubos/fake-s3](https://github.com/jubos/fake-s3)

After you install the Fake-S3 server:

Setup wildcard. Follow the instruction: [https://help.ubuntu.com/community/Dnsmasq](https://help.ubuntu.com/community/Dnsmasq)

Add the following line into **/etc/hosts**

```
127.0.0.1       s3.amazonaws.com
```

Add the following line into **/etc/dnsmasq.conf** file:

```
address=/s3.amazonaws.com/127.0.0.1
```

Restart server

```
sudo service dnsmasq restart
```

Ensure that subdomains of s3.amazonaws.com are pointed to localhost

```
ping test.s3.amazonaws.com
```

Prepare S3 server

```
curl -H"Host:test.s3.amazonaws.com:4567" -H"Content-Length:0" -H"Content-Type:application/octet-stream" -H"Date: Sat, 17 May 2014 17:10:23GMT" -H"Authorization:AWS <AWSAccessKey/>:bgdmYRMpfSeBdiItZCUdHXV/wrM=" -X PUT http://test.s3.amazonaws.com:4567 -v
```

Start fake-s3 server

```
sudo fakes3 server --root=/mnt/s3.vegas.com --port=4567
```


Now you can run tests:

```
./vendor/bin/phpunit
```
