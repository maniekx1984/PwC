# PwC

git clone https://github.com/maniekx1984/PwC.git

.env


composer install --no-dev --optimize-autoloader


query_user
Haslohaslo1

#<VirtualHost *:80>
#    ServerAdmin jakis@email.com.pl
#    DocumentRoot "C:/xampp/htdocs/PwC/PwC/public"
#    ServerName PwC-task.local
#    ErrorLog "logs/pwc-error.log"
#    CustomLog "logs/pwc-access.log" common
#</VirtualHost>

php bin\console doctrine:migrations:migrate

php bin\console doctrine:query:sql "INSERT INTO `user` (`id`, `username`, `password`) VALUES (1, 'query_user', '$2y$13$zCLuOLa5rqH8b5P3gHWuP.o2EiOv9QBp2cRD54hQWh.hAAqOgR40S');"