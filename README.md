# Symfony Logger

This image lets you look at the different access logs.

## Locations

### Database

The logs will be stored in the database. To access a database the `DATABASE_URL` env variable must be set :

```shell
DATABASE_URL="mysql://db_user:db_password@127.0.0.1:3306/db_name?serverVersion=5.7&charset=utf8mb4"
```

### Files

The logs will also be stored to two different files.

- A temporary file : `/tmp/local-logs/logs.csv`
- An app directory : `/srv/app/shared-logs/logs.csv` if the directory is shared between multiple pods they will all be
  able to write to it and read the content.

## Run it

From source you can run this locally by adding a `docker-compose.override.yaml` that contains the database container and the env variables for the symfony container :

```yaml
version: '3.8'

services:
  maria:
      ... Set up the maria container
  php:
    networks:
      - internal
    environment:
      - DATABASE_URL=mysql://exploit:exploit@maria:3306/logs?serverVersion=mariadb-10.4.1
      - UID=1000
      - GID=1000
    volumes:
      - ./:/srv/app
      - /srv/app/vendor
      - ./pvc:/pvc

networks:
  internal:
```

Or you can get the image from DockerHub :

```shell
docker run -it codebuds/micro-symfony-webserver
```

Or run the helm chart :

```shell
helm install sf codebuds/sf-log-server
```
