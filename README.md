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

Using symfony maker bundle, you can create new entities.

```bash
# execute in /api directory
bin/console make:entity --api-resource
```

To generate new Entities using structured data (currently `yaml` or `json`), you can use the implemented `mae:entity-from-json` maker command:

```bash
php bin/console make:entity-from-json --from=../path/to/datefile
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

Created by [Kévin Dunglas](https://dunglas.fr). Commercial support is available at [Les-Tilleuls.coop](https://les-tilleuls.coop).
