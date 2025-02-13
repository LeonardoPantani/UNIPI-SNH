# UNIPI-SNH
Progetto per il corso di System and Network Hacking del Corso di Cybersecurity dell'Università di Pisa.

## Docker
Al primo avvio, inserire il file `db.env` nella cartella `database`:
```
MYSQL_ROOT_PASSWORD=<root_password>
MYSQL_DATABASE=snh_db
MYSQL_USER=<non_root_user>
MYSQL_PASSWORD=<non_root_password>
```
Ad esempio, `MYSQL_ROOT_PASSWORD=root`, `DB_USER=root` e `DB_PASSWORD=root`. Per `MYSQL_USER` e `MYSQL_PASSWORD` inserire stinghe a piacere. 

Inserire anche il file `app.env` nella cartella `app`:
```
DB_HOST=db
DB_NAME=snh_db
DB_USER=<user>
DB_PASSWORD=<password>

MAIL_HOST=<mail_server_address>
MAIL_PORT=<mail_server_port>
MAIL_ADDRESS=<mail_address>
MAIL_PASSWORD=<mail_address_password>
```
Successivamente, eseguire
```
docker compose up --build
```
In fase di development, è possibile utilizzare la funzionalità di `watch`, i.e.
```
docker compose up --build -w
```
Per stoppare i container, eseguire
```
docker compose down
```

Di seguito sono riportati i comandi principali per interagire con i container docker.
```
docker compose up --build                       # per mandare in esecuzione i container ed eseguire il build delle immagini
docker compose up -d                            # per mandare in esecuzione i container in detach mode
docker compose up -w                            # per mandare in esecuzione i container con docker watch attivo
docker compose down                             # per stoppare e rimuovere i container
docker compose down -v                          # per stoppare e rimuovere i container cancellando i volumi
docker compose stop                             # per stoppare i container senza rimuoverli
docker compose start                            # per mandare nuovamente in esecuzione i container stoppati
docker compose ps -a                            # per controllare i container in esecuzione/stoppati
docker exec -it <container_name> /bin/bash      # per aprire una shell su un container
docker exec -it snh_db mysql -u root -p         # per accedere al database (vedi MYSQL_ROOT_PASSWORD in compose.yml per la password)
```

## Database
Di seguito sono riportati alcuni comandi utili per interagire con il database.

> [!NOTE]
> Ogni comando deve terminare con `;`

```
show databases; 
use snh_db; 
show tables; 
select * from user; 
exit
```
### Database Encryption
Il contenuto del database `snh_db` è cifrato (**su disco**) tramite la funzionalità `InnoDB Data-At-Rest Encryption` di `MySQL`.
Per verificare se il keyring è attivo, eseguire
```
SELECT * FROM performance_schema.keyring_component_status;
```
Se `Component_status` è indicato come `Active`, allora la componente `component_keyring_file` è attiva.
Per testare il funzionamento della cifratura, eseguire i seguenti comandi.
- Entrare nel container `snh_db` tramite una shell `/bin/bash` (i.e. `just exec /bin/bash`) ed eseguire il client `mysql` (i.e. `mysql -u root -proot`).
    ```
    CREATE DATABASE snh_testing;
    USE snh_testing;
    CREATE TABLE user ( name varchar(255) NOT NULL );
    INSERT INTO user (name) VALUES ('topkek');
    ```
- Uscire da `mysql` (i.e. `\q`) e verificare che i dati su disco siano in chiaro (`grep` restituisce la stringa `topkek`).
    ```
    strings /var/lib/mysql/snh_testing/user.ibd | grep topkek
    ```
- Eseguire nuovamente il client `mysql` e attivare la cifratura.
    ```
    USE snh_testing;
    ALTER TABLE user encryption='Y';
    ```
- Uscire da `mysql` e verificare che i dati siano cifrati (`grep` deve restituire una stringa vuota) e che il `component_keyring_file` non sia vuoto.
    ```
    cat /var/lib/mysql-keyring/component_keyring_file
    strings /var/lib/mysql/snh_testing/user.ibd | grep topkek
    ```

### Debug
In caso di errore, è possibile modificare le impostazioni del database ed usare il comando
```
ALTER INSTANCE RELOAD KEYRING;
```
per eseguire a runtime il reload della componente `component_keyring_file`.  

## Logs
Le operazioni eseguite dall'applicazione sono monitorate attraverso un sistema di logging. Per estrarre dal container
i file di log, eseguire
```
docker cp snh_app:/var/www/html/logs .
```
Per visualizzare in tempo reale il contenuto di un file di log (e.g. `file.log`), eseguire
```
docker exec -it snh_app less --follow-name +F logs/file.log
```
È possibile inoltre sfruttare il comando `less` per aprire tutti i file di log e navigare tra essi. Eseguire
```
docker exec -it snh_app less --follow-name +F logs/*
```
ed usare i seguenti comandi per controllare `less`.
```
:n   # file successivo
:p   # file precedente
F    # contenuto aggiornato in tempo reale (i.e. follow) 
g    # vai all'inizio
G    # vai alla fine
h    # help
```

## justfile
I comandi di Docker possono essere eseguiti anche attraverso [just](https://github.com/casey/just). 
Il `justfile` presente nella repo permette di eseguire i seguenti comandi.
```
just           # mostra tutti i recipes
just up        # docker compose up --build -d
just up w      # docker compose up -w
just up -      # docker compose up
just rs        # docker compose down    && docker compose up --build -d
just rs v      # docker compose down -v && docker compose up --build -d
just rs b      # docker compose down    && docker compose up --build
just rs d      # docker compose down    && docker compose up -d
just rs w      # docker compose down    && docker compose up -w
just rs v b    # docker compose down -v && docker compose up --build
just rs v d    # docker compose down -v && docker compose up -d
just rs v b w  # docker compose down -v && docker compose up --build -w
just rs -      # docker compose down    && docker compose up
just w         # docker compose watch --no-up
just ps        # docker compose ps -a
just ps d      # docker ps -a
just logs      # scegli un container ed esegui docker compose logs
just run       # scegli un recipe ed eseguilo
```
Notare che `just up`, `just down` e `just exec` accettano un numero **illimitato** di argomenti. In particolare,
`just up` sovrascrive i suoi argomenti di default, i.e.
```
just up              # docker compose up --build -d
just up -d           # docker compose up -d
just up -w           # docker compose up -w
just up --build -w   # docker compose up --build -w
```

### just exec
Il recipe `just exec` esegue i seguenti comandi.
```
mysql -u root -proot  # snh_db
/bin/bash             # snh_app
```
Esattamente come `just up`, è possibile modificare i parametri di default. Per esempio,
```
just exec /bin/bash
```
esegue una shell indipendentemente dal container scelto. Inoltre, `just exec` permette di scegliere un container
attraverso `select` (i.e. il comando built-in di `bash` per la generazione di prompt). Alternativamente, è possibile 
scegliere il comando `fzf` creando un file `.just.env` e definendo la variabile d'ambiente `EXEC_RECIPE_CHOOSER`, i.e. 
```
EXEC_RECIPE_CHOOSER='fzf --tmux 75%,75% --exact --reverse --border --header-first --header "ESC. quit"'
```
Il recipe `just run` fornisce la stessa possibilità attraverso la variabile `JUST_CHOOSER`, i.e.
```
JUST_CHOOSER='fzf --tmux 75%,75% --exact --reverse --border --header-first --header "ESC. quit; SPACE. jump mode" --bind="space:jump-accept" --preview "just --unstable --color always --show {}"'
```