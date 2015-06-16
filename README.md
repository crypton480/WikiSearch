WikiSearch
==========

> A Symfony project created on June 14, 2015, 4:30 pm.

Sample app in Symfony2 to search Wikipedia and store favourites in database using MySQL.

Deployment Instructions
----
This assumes that php (>= version 5.4) is installed on your machine. The app uses the built in php webserver.  
```
git clone https://github.com/crypton480/WikiSearch
cd /path/to/WikiSearch/
php app/console server:run
```
Navigate to http://localhost:8000/config.php and follow instructions to configure local MySQL user and password.
```
cd /path/to/WikiSearch
php app/console doctrine:database:create
php app/console doctrine:generate:entities AppBundle
php app/console server:run
```
Navigate to http://localhost:8000/ to run application. Enter search terms. Click on star to add/remove to favorites. Click hyperlink on the bottom of the page to see all favorites. 


