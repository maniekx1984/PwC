# PwC task

### Server specyfication + framework
XAMPP: Apache + MariaDB + PHP 7.2 + Perl on Windows 10: paths with backslashes
Symfony 4.1

## Installation steps

### GIT + Composer

Clone this repository to C:/xampp/htdocs
```
git clone https://github.com/maniekx1984/PwC.git
```

Then, go to PwC directory and install dependencies with Composer

```
composer install
```

### vhost
Create vhost in C:\xampp\apache\conf\extra\httpd-vhosts.conf

Example:
```
<VirtualHost *:80>
    ServerAdmin jakis@email.com.pl
    DocumentRoot "C:/xampp/htdocs/PwC/public"
    ServerName PwC-task.local
    ErrorLog "logs/pwc-error.log"
    CustomLog "logs/pwc-access.log" common
</VirtualHost>
```
Note that DocumentRoot must ends with "public"


### .env
Configure your .env file, you can create it by renaming .env.dist file.

Provide or update:
```
DATABASE_URL=mysql://db_user:db_password@127.0.0.1:3306/db_name
GOOGLE_CX=
GOOGLE_KEY=
```

CX can be created here: https://support.google.com/customsearch/answer/2649143?hl=en
- remember to change Sites to search value to Search the entire web...

KEY can be created here: https://console.developers.google.com/apis/library/customsearch.googleapis.com

## database
In the application root directory, not in public, run fallowing commands:
```
php bin\console doctrine:migrations:migrate
```
```
php bin\console doctrine:query:sql "INSERT INTO `user` (`id`, `username`, `password`) VALUES (1, 'query_user', '$2y$13$zCLuOLa5rqH8b5P3gHWuP.o2EiOv9QBp2cRD54hQWh.hAAqOgR40S');"
```

## Usage


1. Go to http://PwC-task.local
2. Login with username: query_user and password: Haslohaslo1
3. Edit websites in public\assets\sites.yml
4. Run commands in application root directory:
4.a. This will read sites.yml file and store sites and keywords in database
```
php bin\console app:read-file
```
4.b. This will ask Google API for results, and store them in database
```
php bin\console app:read-api
```

Note: in order to run these commands you need to set up e.g. cron jobs (on linux server) or run them manually via command line
