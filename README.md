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

### Running Multiple Instances

To run multiple instances of this project on the same host, you need to configure different ports for each instance to avoid port conflicts. The application uses the following environment variables to configure ports:

- `HTTP_PORT`: The HTTP port (default: 80)
- `HTTPS_PORT`: The HTTPS port (default: 443)
- `HTTP3_PORT`: The HTTP/3 port (default: 443)
- `DATABASE_PORT`: The PostgreSQL database port (default: 5432)

Example of running a second instance on different ports:

```bash
# First instance (default ports)
docker compose up -d

# Second instance (custom ports)
HTTP_PORT=50000 HTTPS_PORT=50001 HTTP3_PORT=50002 DATABASE_PORT=50003 docker compose up -d
```

You can also create a `.env` file for each instance with different port configurations.


### Create new Entities 

Using symfony maker bundle, you can create new entities.

```bash
# execute in /api directory
bin/console make:entity --api-resource
```

To generate new Entities using structured data (currently `yaml` or `json`), you can use the implemented `make:entity-from-json` maker command:

```bash
# provide path to a JSON or YAML file
php bin/console make:entity-from-json --file=../path/to/datefile

# or provide JSON data directly
php bin/console make:entity-from-yaml --data='{...}'
```

Here are two examples of structured data files you can use:

### Example JSON file

```json
{
    "name": "Book",
    "apiResource": true,
    "fields": [
        {
            "name": "title",
            "type": "string",
            "nullable": false
        },
        {
            "name": "publishedAt",
            "type": "datetime_immutable",
            "nullable": true
        },
        {
            "name": "isbn",
            "type": "string",
            "nullable": true
        },
        {
            "name": "pages",
            "type": "integer",
            "nullable": true
        }
    ]
}
```

```yaml
name: Article
apiResource: true
fields:
    - name: title
      type: string
      nullable: false
    - name: author
      type: string
      nullable: false
    - name: publishedAt
      type: datetime_immutable
      nullable: true
    - name: content
      type: text
      nullable: true
    - name: views
      type: integer
      nullable: false
```


## Credits

Yannick Fenz'l, [Kévin Dunglas](https://dunglas.fr), and contributors.
