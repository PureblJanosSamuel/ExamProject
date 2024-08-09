# Home finder

> ### About
> This was our last exam in a Hungarian school,
> so some texts will be shown in Hungarian.
> The exercise was done in groups, the code was rewritten later,
> so there may be logically different parts. Please take this into account!
> 
> We created this project using VS code, we tested the features with xampp,
> but there are features xampp can't use, like the emailing system because
> it was developed after we uploaded the project to a real server.


## How to use it
### Set Xampp
1. Install Xampp (If you haven't got already)
2. In the Xampp directory find htdocs
3. Create a folder here like "otthonkereso" or "homefinder"
4. Download or pull the files here
5. Now set up the Database


### Set the Database
1. Open Xampp and click on Start next to Apache and MySQL
2. Click on the MySQL's Admin button, it will open youar browser.
3. Here on the left section click on "New"
4. Nem your Databease to "otthonkereso" and click "Create"
5. Go to the "Import" tab
6. Click on "Choose File"
7. Select the "otthonkereso.sql" file from the SQL folder
8. Click "Import" down at this section


Now you can open this project in you browser, if you type
"localhost/otthonkereso"
<!-- Ezt nem tudom pontosan mert nálam már nem működik -->
> [!IMPORTANT]
> Never forget to Start Apache and MySQL, with out them you
> can't open the website



### If you want to use virtual host:
1. Open your notepad as administrator
2. Open the "hosts" file from "C:\WINDOWS\system32\drivers\etc\":
3. Create a new line and copy the IP adress press the TAB and name your website. It should looks something like this:
"127.0.0.1  websitename"
4. Sae this file
5. Go to "xampp\apache\conf\extra" and open "httpd-vhosts.conf"
6. Place the following code under everything:
```
<VirtualHost *:80>
    DocumentRoot "C:/xampp/htdocs/websitename"
    ServerName websitename
    ErrorLog "websitename-error.log"
    CustomLog "logs/websitename-access.log" common
</VirtualHost>
```
7. Save this file too
8. Now if your xampp server is running, type "http://" than websitename

## Contributing
This is an open prjoect, we have some new features and updates comming, but feel free to try it out, or use some parts for your project.

## Licence
[MIT](https://choosealicense.com/licenses/mit/)
