FROM mysql:9.1.0

# install 'strings' command for data-at-rest encryption testing
RUN microdnf install binutils

COPY ./init.sql /docker-entrypoint-initdb.d

COPY <<-'EOT' /usr/sbin/mysqld.my
    {
        "components": "file://component_keyring_file"
    }
EOT

COPY <<-'EOT' /usr/lib64/mysql/plugin/component_keyring_file.cnf
    {
        "path": "/var/lib/mysql-keyring/component_keyring_file",
        "read_only": false
    }
EOT

RUN touch /var/lib/mysql-keyring/component_keyring_file \
    && chown mysql:mysql /var/lib/mysql-keyring/component_keyring_file \
    && chmod go= /var/lib/mysql-keyring/component_keyring_file