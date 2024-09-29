# Hyperf Payment

This is a simple project to demonstrate Hyperf capabilities to create a payments API, with email/sms notifications and
external transaction validation.

## Getting started

Once you clone the repository, you can run the server immediately using the command below.

### Docker

This application is based on docker, so you can run it using the following command:

```bash
docker-compose up
```

This will start all the necessary tools, and start the hyperf container running the cli-server on port `9501`.

#### Accessing the container

If you need to access the container, you can use the following command:

```bash
docker-compose exec php bash
```

`php` here means the name of the service in the `docker-compose.yml` file.

### Testing

By accessing the container you can then run the tests by using the following command:

```bash
composer test
```

## Accessing the API

You can then reach the api at `http://localhost:9501/`

### Docs

You can access the swagger
docs [by clicking here](https://andeen171.github.io/hyperf-payment/)
