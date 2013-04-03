gps-track
=========

GPS Tracking

# Installation

Cài đặt trên Windows

1.Sử dụng [XAMPP](http://www.apachefriends.org/en/xampp.html)

2.Dùng Git để clone project
    
    cd /path/to/xampp/htdocs/
    git clone https://github.com/chnghia/gps-track.git

3.Thiết lập vhosts cho gps-track

    <VirtualHost *:80>
      ServerAdmin master@gps-track.mycloud.me
      DocumentRoot "/path/to/xampp/htdocs/gps-track"
      ServerName gps-track.mycloud.me
      ServerAlias gps-track.mycloud.me
      ErrorLog "logs/gps-track.mycloud-error.log"
      CustomLog "logs/gps-track.mycloud-access.log" combined
    </VirtualHost>

4.Chỉnh sửa hosts file

    127.0.0.1            gps-track.mycloud.me

# License
(The MIT License)

Copyright © 2013 Nghia Chung

Permission is hereby granted, free of charge, to any person obtaining a copy of this software and associated documentation files (the ‘Software’), to deal in the Software without restriction, including without limitation the rights to use, copy, modify, merge, publish, distribute, sublicense, and/or sell copies of the Software, and to permit persons to whom the Software is furnished to do so, subject to the following conditions:

The above copyright notice and this permission notice shall be included in all copies or substantial portions of the Software.

THE SOFTWARE IS PROVIDED ‘AS IS’, WITHOUT WARRANTY OF ANY KIND, EXPRESS OR IMPLIED, INCLUDING BUT NOT LIMITED TO THE WARRANTIES OF MERCHANTABILITY, FITNESS FOR A PARTICULAR PURPOSE AND NONINFRINGEMENT. IN NO EVENT SHALL THE AUTHORS OR COPYRIGHT HOLDERS BE LIABLE FOR ANY CLAIM, DAMAGES OR OTHER LIABILITY, WHETHER IN AN ACTION OF CONTRACT, TORT OR OTHERWISE, ARISING FROM, OUT OF OR IN CONNECTION WITH THE SOFTWARE OR THE USE OR OTHER DEALINGS IN THE SOFTWARE.