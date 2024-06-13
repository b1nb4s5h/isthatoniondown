Is That Onion Down?
=====================

A simple web application to check the status of an onion URL from the clearnet.

Description
-----------

This application allows users to enter an onion URL and check its status. The application uses a SQLite database to store the search history, and a simple captcha system to prevent abuse.

This can be run from clearnet with no need to go through TOR proxies, as the back end will handle this

Features
--------

1. Front End - Single PHP file
   ---------------------------
  * Requirements
      (1xDebian for FE, PHP8+, Nginx) 
  * Capture url check from user.
  * Store in search history SQLite database
  * Captcha system to prevent abuse
  * Display last 10 search history
  * 2 API's with tokens - show pending and update with status

2. Back End - Single Python script
   -------------------------------
  * Requirements
      (1xDebian for BE, Python3, TOR, cURL, toriptables3) 
  * toriptables3 to route all traffic through TOR
  * Py script runs every minute via cron
  * Gets pending urls from API
  * Does a curl check
  * Post url status update to FE server
  

Installation
------------
Self explanitory based on requirements

Note
----

* This application is for educational purposes only.
* The application does not guarantee the accuracy of the onion URL status.
* The application does not store any sensitive information.
* Use at own risk and perril
* No support will be provided

License
-------

This application is licensed under the MIT License.

Acknowledgments
---------------
* The terminal-like design is inspired by [TTY-UI](https://github.com/jcubic/tty-ui)
* The tori[ptables3 system is based on [toriptables3](https://github.com/ruped24/toriptables3)

Author
------
https://github.com/b1nb4s5h

