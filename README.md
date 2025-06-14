# crudle

Create CRUD applications powered by API Platform with ease. 
It exposes a description in the OpenAPI format and integrates a by api-platfrom customized version of Swagger UI. 


<h1 align="center"><a href="https://api-platform.com"><img src="https://api-platform.com/images/logos/Logo_Circle%20webby%20text%20blue.png" alt="API Platform" width="100" height="100"></a></h1>

The official project documentation is available **[on the API Platform website](https://api-platform.com)**.

## Install

[Read the official "Getting Started" guide](https://api-platform.com/docs/distribution/).

### Build

```bash
docker compose build --no-cache
```

### Run

```bash
docker compose up
```

### Debug

```bash
docker compose logs -f
```

### Create new Entities 

Using symfony maker bundle, you can create new entities that will be automatically exposed as 
API resources and documentation will be generated for them.

```bash
# execute in /api directory
bin/console make:entity --api-resource
```


## Credits

Created by [Kévin Dunglas](https://dunglas.fr). Commercial support is available at [Les-Tilleuls.coop](https://les-tilleuls.coop).
