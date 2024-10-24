# UNIPI-SNH
Progetto per il corso di System and Network Hacking del Corso di Cybersecurity dell'Università di Pisa.

## Docker
Al primo avvio, creare la cartella `db` ed eseguire
```
docker compose build
```
Di seguito sono riportati i comandi principali per interagire con i container docker.

```
docker compose up -d                            # per mandare in esecuzione i container
docker compose down                             # per stoppare e rimuovere i container
docker compose stop                             # per stoppare i container senza rimuoverli
docker compose start                            # per mandare nuovamente in esecuzione i container stoppati
docker compose ps -a                            # per controllare i container in esecuzione/stoppati
docker exec -it <container_name> /bin/bash      # per aprire una shell su un container
docker exec -it php_snh_db mysql -u root -p     # per accedere al database (vedi MYSQL_ROOT_PASSWORD in compose.yml per la password)
```

Di seguito sono riportati alcuni comandi utili per interagire con il database (NOTA: ogni comando deve terminare con `;`)
```
show databases; 
use snh_db; 
show tables; 
select * from user; 
exit
```

## justfile
I comandi di Docker possono essere eseguiti anche attraverso [just](https://github.com/casey/just). 
Il `justfile` presente nella repo permette di eseguire i seguenti comandi.
```
just on        # manda in esecuzione il Docker deamon
just off       # termina il Docker deamon
just up        # docker compose up -d
just down      # docker compose down
just start     # docker compose start
just stop      # docker compose stop
just ps        # docker compose ps -a
just app       # apre una shell sul container php_snh_app
just db        # apre una shell sul container php_snh_db (usare comando mysql per acceder al db)
```
