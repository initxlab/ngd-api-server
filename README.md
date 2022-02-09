### PHP8 next generation API Server. Part of the [initxlab/ngd-api](https://github.com/initxlab/ngd-api) project

#### Setup initxlab/ngd-api-server

Download or clone the repo from GitHub
- requirement : <mark>php >= 8.0.2 </mark>
```
git clone https://github.com/initxlab/ngd-api-server.git
```
 
**Download Composer dependencies**

You need [Composer](https://getcomposer.org/download/) complete the process. 
Then inside the newly cloned folder run the below in install dependencies from the composer.lock file :

```
composer install
```

**Configure the .env (or .env.local) File**

Open the `.env` file and edit the entry `DATABASE_URL` with your environment config. 

- `
  DATABASE_URL="mysql://db_user:db_password@127.0.0.1:3306/db_name?serverVersion=5.7"
  `

You can also create a `.env.local` file
and *override* any configuration you need there (instead of changing
`.env` directly).

**Set up the Database**

Once the `.env` configured, you are ready to set the database:

```
php bin/console doctrine:database:create
php bin/console doctrine:migrations:migrate
```
**Load Data Fixtures in database**
```
php bin/console doctrine:fixture:load
```

**Start Symfony built-in web server**
-  Note : Symfony CLI is highly recommended for routine operations. If you don't have it,
   [get it here](https://symfony.com/download/)
```
symfony serve
```

You can also start it on a custom port number

```
symfony server:start --port=PORT_NUMBER
```

Note: You may face this `symfony server:ca:install` message while running the server. 
You can follow instructions or ignore.

Open `https://localhost:8000/api` with your favorite web browser
### API Resources
Given a User #id 2075 (id may divert on your env.) you can preview response body in any format you configured on server side

- json

`https://localhost:8000/api/users/2075.json`
- jsonld

`https://localhost:8000/api/users/2075.jsonld`

- jsonhal

`https://localhost:8000/api/users/2075.jsonhal`

You can also display the entire collection of items following the above logic.
- Collection of Users

`https://localhost:8000/api/users.jsonld`

Ngd-api comes with powerful filtering 
features able to deal with simple to complex submitted request body.

Basic Filtering User by a property. Using jsonld but any format of your pick will make it. As explain above.

`https://localhost:8000/api/users/2075.jsonld?properties[]=username`

Response body

```json
{
  "@context": "/api/contexts/User",
  "@id": "/api/users/2075",
  "@type": "User",
  "username": "jean.m"
}
```

`https://localhost:8000/api/users/2075.jsonld?properties[]=email`

Response body

```json
{
  "@context": "/api/contexts/User",
  "@id": "/api/users/2075",
  "@type": "User",
  "email": "jean.m@example.com"
}
```

You also can retrieve a whole collection.

`https://localhost:8000/api/users.jsonld?properties[]=email`

Response body (limited to 5 items per a page)

```json
{
  "@context": "/api/contexts/User",
  "@id": "/api/users",
  "@type": "hydra:Collection",
  "hydra:member": [
    {
      "@id": "/api/users/1800",
      "@type": "User",
      "email": "reichert.erica@hotmail.com"
    },
    {
      "@id": "/api/users/1801",
      "@type": "User",
      "email": "magnus@initxlab.com"
    },
    {
      "@id": "/api/users/1802",
      "@type": "User",
      "email": "reta20@hotmail.com"
    },
    {
      "@id": "/api/users/1803",
      "@type": "User",
      "email": "camilla.douglas@yahoo.com"
    },
    {
      "@id": "/api/users/1804",
      "@type": "User",
      "email": "kathryne.osinski@gmail.com"
    }
  ],
  "hydra:totalItems": 276,
  "hydra:view": {
    "@id": "/api/users.jsonld?properties%5B%5D=email&page=1",
    "@type": "hydra:PartialCollectionView",
    "hydra:first": "/api/users.jsonld?properties%5B%5D=email&page=1",
    "hydra:last": "/api/users.jsonld?properties%5B%5D=email&page=56",
    "hydra:next": "/api/users.jsonld?properties%5B%5D=email&page=2"
  },
  "hydra:search": {
    "@type": "hydra:IriTemplate",
    "hydra:template": "/api/users.jsonld{?properties[]}",
    "hydra:variableRepresentation": "BasicRepresentation",
    "hydra:mapping": [
      {
        "@type": "IriTemplateMapping",
        "variable": "properties[]",
        "property": null,
        "required": false
      }
    ]
  }
}
```

**OPERATIONS**

Creating new User and new embedded object. Will create the User and Stock with association
- Request
```json
{
  "email": "nemo@example.com",
  "password": "test1",
  "username": "nemo",
  "warehouses": [
    {
      "stock": 1000,
      "item": "/api/products/28ba1040-a73d-4480-825c-5f02bca5250b",
      "label": "The label goes here",
      "description":"great description"
    }
  ]
}
```
Creating new User and assign ownership of an embedded existing array IRIs string
- Request
```json
{
  "email": "martin@example.com",
  "password": "test1",
  "username": "martin",
  "warehouses": [
    "/api/warehouses/004dc05d-c203-4fd4-a3d5-c9818be6213f"
  ]
}
```

GET a product item with an array of stock objects. 
  
- Response body

```json
{
  "@context": "/api/contexts/Product",
  "@id": "/api/products/c1f7b46d-2584-47db-a891-e829c9ee6025",
  "@type": "Product",
  "name": "cars",
  "active": false,
  "stocks": [
    {
      "@id": "/api/warehouses/004dc05d-c203-4fd4-a3d5-c9818be6213f",
      "@type": "Warehouse",
      "stock": 1540,
      "owner": "/api/users/2070",
      "label": "Car new label"
    },
    {
      "@id": "/api/warehouses/09203d75-13f6-4c6d-aea1-4ea749d8f35a",
      "@type": "Warehouse",
      "stock": 42,
      "owner": "/api/users/1811",
      "label": "cars Velit numquam et atque sint sit et."
    },
    {
      "@id": "/api/warehouses/0bf051d2-fbd6-4124-bf03-15d2c4f9dd06",
      "@type": "Warehouse",
      "stock": 57,
      "owner": "/api/users/1806",
      "label": "cars A aut enim dolor. Fugiat ut est exercitationem ad ratione eum. Dolorem blanditiis vel error."
    },
    {
      "@id": "/api/warehouses/1b2e226e-ed61-4001-b2d8-f1e4998f4adb",
      "@type": "Warehouse",
      "stock": 55,
      "owner": "/api/users/1810",
      "label": "cars Voluptates aspernatur nam asperiores aut error."
    },
    {
      "@id": "/api/warehouses/1de75cf1-211a-483d-adc2-8aa6d2f2eef5",
      "@type": "Warehouse",
      "stock": 31,
      "owner": "/api/users/1818",
      "label": "cars Id et ipsa id sit sed repudiandae."
    },
    {
      "@id": "/api/warehouses/25778712-5869-43a2-8186-238417965ec5",
      "@type": "Warehouse",
      "stock": 39,
      "owner": "/api/users/1800",
      "label": "cars Assumenda sed est laborum placeat in voluptas. Ad rerum architecto quia. Rerum quis veritatis cupiditate et."
    },
    {
      "@id": "/api/warehouses/373b10bd-8791-49a8-88f5-e4759ff414da",
      "@type": "Warehouse",
      "stock": 34,
      "owner": "/api/users/1815",
      "label": "cars Ut corporis consequatur adipisci quas."
    },
    {
      "@id": "/api/warehouses/5a19c65f-1743-471d-a431-8f8653aa77bc",
      "@type": "Warehouse",
      "stock": 17,
      "owner": "/api/users/1801",
      "label": "cars Dignissimos eum voluptas quas suscipit architecto eius."
    },
    {
      "@id": "/api/warehouses/6990c073-732b-484b-90ea-d7fd7a50a123",
      "@type": "Warehouse",
      "stock": 25,
      "owner": "/api/users/1816",
      "label": "cars Deleniti deleniti ad neque molestiae. Et dolores quis id saepe labore ipsum dolores. Ipsa soluta est totam modi ut vel ea mollitia."
    },
    {
      "@id": "/api/warehouses/814c3688-d1bc-4a9e-a5e6-82217b301b14",
      "@type": "Warehouse",
      "stock": 43,
      "owner": "/api/users/1807",
      "label": "cars Qui et nobis quia velit reiciendis. Amet voluptatem eos aut aut similique."
    },
    {
      "@id": "/api/warehouses/81deb24c-9e96-4a0e-8612-194af3181437",
      "@type": "Warehouse",
      "stock": 0,
      "owner": "/api/users/1812",
      "label": "cars Cum et aut hic sunt consequatur est. Veniam voluptatem consequatur et."
    },
    {
      "@id": "/api/warehouses/885ff9a3-47e8-4727-8134-cf7b501a1f34",
      "@type": "Warehouse",
      "stock": 8,
      "owner": "/api/users/1817",
      "label": "cars Ratione eaque ducimus aliquid facere. Quia quis tempora voluptas unde quia et eius."
    },
    {
      "@id": "/api/warehouses/8f6506a4-5c31-485b-aca7-a5273c777e0a",
      "@type": "Warehouse",
      "stock": 10,
      "owner": "/api/users/1804",
      "label": "cars Ipsum sapiente autem voluptatem cupiditate iusto culpa."
    },
    {
      "@id": "/api/warehouses/918fb1dc-d890-408e-9094-0f1adad091bb",
      "@type": "Warehouse",
      "stock": 31,
      "owner": "/api/users/1805",
      "label": "cars Voluptatibus omnis voluptate eaque quia possimus ut rem."
    },
    {
      "@id": "/api/warehouses/a566d39e-e243-457d-8c82-787e6aa573dd",
      "@type": "Warehouse",
      "stock": 24,
      "owner": "/api/users/1814",
      "label": "cars Quis quis maiores laborum ut porro error error molestias. Et optio placeat alias non tempore."
    },
    {
      "@id": "/api/warehouses/b473179d-3873-445a-afe3-6b824036df45",
      "@type": "Warehouse",
      "stock": 56,
      "owner": "/api/users/1808",
      "label": "cars Ipsa repellat qui qui in eaque."
    },
    {
      "@id": "/api/warehouses/bc4e6c7e-a4f3-437d-b76f-0da91809703d",
      "@type": "Warehouse",
      "stock": 3,
      "owner": "/api/users/1819",
      "label": "cars Qui eos qui."
    },
    {
      "@id": "/api/warehouses/be391a5c-5f8a-45a0-af2c-6570909a6f1a",
      "@type": "Warehouse",
      "stock": 51,
      "owner": "/api/users/1809",
      "label": "cars Voluptates consectetur dolorem necessitatibus. Sit sunt et quis ipsam."
    },
    {
      "@id": "/api/warehouses/ccecbe58-8062-4aac-9885-089f6150fcbc",
      "@type": "Warehouse",
      "stock": 48,
      "owner": "/api/users/1813",
      "label": "cars Numquam aut nihil quos rem. Aspernatur et tempora qui nostrum. Magnam accusamus a eaque unde et perspiciatis ipsum quis."
    },
    {
      "@id": "/api/warehouses/db53af3f-c112-4b51-98af-9ca703b3d8da",
      "@type": "Warehouse",
      "stock": 43,
      "owner": "/api/users/1802",
      "label": "cars Voluptate vero deserunt."
    }
  ],
  "description": "Stock for product line related to cars",
  "created.at": "1 day ago",
  "countProducts": 20
}
```
**VALIDATIONS**

Embedded Objects validations. Attempt to create a User and new stock entry without a 
mandatory field. Here "label". The form is posted once for both Operations. Validations applied
at both level.

- Request
```json
{
  "email": "jean@example.com",
  "password": "123456",
  "username": "jean",
  "warehouses": [
    {
      "stock": 1000,
      "item": "/api/products/28ba1040-a73d-4480-825c-5f02bca5250b",
      "label": "",
      "description":"great description "
    }
  ]
}
```
- Response
```json
{
  "@context": "/api/contexts/ConstraintViolationList",
  "@type": "ConstraintViolationList",
  "hydra:title": "An error occurred",
  "hydra:description": "warehouses[0].label: This value should not be blank.",
  "violations": [
    {
      "propertyPath": "warehouses[0].label",
      "message": "This value should not be blank.",
      "code": "c1051bb4-d103-4f74-8988-acbcafc7fdc3"
    }
  ]
}
```
Updating User stocks with an array of stock IRIs
- Request (PUT)
```json
{
  "@id": "/api/users/1800",
  "warehouses": [
    "/api/warehouses/25778712-5869-43a2-8186-238417965ec5",
    "/api/warehouses/004dc05d-c203-4fd4-a3d5-c9818be6213f"
  ]
}
```
- User Item Response
```json
{
  "@context": "/api/contexts/User",
  "@id": "/api/users/1800",
  "@type": "User",
  "email": "reichert.erica@hotmail.com",
  "username": "jazmyn.prohaska",
  "warehouses": [
    {
      "@id": "/api/warehouses/004dc05d-c203-4fd4-a3d5-c9818be6213f",
      "@type": "Warehouse",
      "stock": 1540,
      "label": "Car new label",
      "description": "Quibusdam quae aspernatur nihil ullam voluptate sit. Illum temporibus doloremque minima laudantium voluptatem fugit natus. Soluta natus eos quos voluptatem culpa adipisci. Quo similique ipsa ut id cum quas.",
      "picture": null,
      "created.at": "1 day ago"
    },
    {
      "@id": "/api/warehouses/25778712-5869-43a2-8186-238417965ec5",
      "@type": "Warehouse",
      "stock": 39,
      "label": "cars Assumenda sed est laborum placeat in voluptas. Ad rerum architecto quia. Rerum quis veritatis cupiditate et.",
      "description": "Vero corrupti non dolore deleniti provident eum accusamus. Magni est beatae provident adipisci natus. Fuga ad provident voluptatum nesciunt rerum. Dolore vero iste enim voluptates delectus quisquam.",
      "picture": null,
      "created.at": "1 day ago"
    }
  ],
  "countStocks": 2
}
```
Removing stock from User collection
- Request
```json
{
  "@id": "/api/users/1800",
  "warehouses": [
    "/api/warehouses/004dc05d-c203-4fd4-a3d5-c9818be6213f"
  ]
}
```
- Response
```json
{
  "@context": "/api/contexts/User",
  "@id": "/api/users/1800",
  "@type": "User",
  "email": "reichert.erica@hotmail.com",
  "username": "jazmyn.prohaskas",
  "warehouses": [
    {
      "@id": "/api/warehouses/004dc05d-c203-4fd4-a3d5-c9818be6213f",
      "@type": "Warehouse",
      "stock": 1540,
      "label": "Car new label",
      "description": "Quibusdam quae aspernatur nihil ullam voluptate sit. Illum temporibus doloremque minima laudantium voluptatem fugit natus. Soluta natus eos quos voluptatem culpa adipisci. Quo similique ipsa ut id cum quas.",
      "picture": null,
      "created.at": "1 day ago"
    }
  ],
  "countStocks": 1
}
```

You can create a User without stock
- Request
```json
{
  "email": "frank@example.com",
  "password": "123456",
  "username": "frank",
  "warehouses": [
  ]
}
```
- Or simply
```json
{
  "email": "frank@example.com",
  "password": "123456",
  "username": "frank"
}
```

- Response body in both cases
```json
{
  "@context": "/api/contexts/User",
  "@id": "/api/users/2075",
  "@type": "User",
  "email": "frank@example.com",
  "username": "frank",
  "countStocks": 0
}
```
...

Work in progress.

Enjoy !

Initxlab Team