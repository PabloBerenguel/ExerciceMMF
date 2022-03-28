# ExerciceMMF

## Front

The front is made in ReactJs with the template: `https://github.com/minimal-ui-kit/material-kit-react`

### Installation

1 - run `yarn install` inside the folder ReactJs
2 - run `yarn start`

## Back

The back is made in Symfony from `https://symfony.com/doc/current/setup.html`

### Installation
1 - install ddev https://ddev.readthedocs.io/en/stable/
2 - run `ddev config` inside the folder Symfony6 press enter until the end of the process
3 - run `ddev start` wait for the container to start
4 - run `ddev ssh`
5 - Install symfony and required packages: `composer install && composer update`
6 - Run migrations: `php bin/console doctrine:schema:update --force`

### Routes

#### Auth

<details>
<summary>Login</summary>

- URL:  .../api/auth/login
- Method: POST
- Action: Logs in
- Authorization: none

|key             |type    |mandatory|boundaries|
|----------------|--------|---------|----------|
|email           |string  |yes      |          | 
|password        |string  |yes      |          | 

- Returns:
```
{
  "access_token": {
    "token": "763689021e2a3586f7ebeb3d7f756cb4",
    "valid_until": "2022-03-28T21:11:25.735541+00:00"
  },
  "refresh_token": {
    "token": "3657ee2bf037fc5dd66e5d4da0a9d1cd",
    "valid_until": "2022-03-29T21:11:25.729092+00:00"
  }
}
```
</details>
<details>
<summary>Me</summary>

- URL:  .../api/auth/me
- Method: POST
- Action: Get user data from the bearer token
- Authorization: Bearer

|key             |type    |mandatory|boundaries|
|----------------|--------|---------|----------|

- Returns:
```
{
  "id": 1,
  "email": "user@email.com",
  "firstName": "Hello",
  "lastName": "World"
}
```
</details>
<details>
<summary>Logout</summary>

- URL:  .../api/auth/logout
- Method: POST
- Action: Delete access and refresh token
- Authorization: Bearer

|key             |type    |mandatory|boundaries|
|----------------|--------|---------|----------|

- Returns:
```
```
</details>

#### User
<details>
<summary>Create</summary>

- URL:  .../api/users
- Method: POST
- Action: Delete access and refresh token
- Authorization: none

|key             |type    |mandatory|boundaries|
|----------------|--------|---------|----------|
|email|string|yes||
|password|string|yes||
|PasswordConfirm|string|yes||
|firstName|string|yes||
|lastName|string|yes||

- Returns:
```
{
  "id": 13,
  "email": "user@email.com",
  "first_name": "hello",
  "last_name": "world"
}
```
</details>
<details>
<summary>Get</summary>

- URL:  .../api/users
- Method: GET
- Action: Get all users, no pagination due to time limitation
- Authorization: Bearer

|key             |type    |mandatory|boundaries|
|----------------|--------|---------|----------|

- Returns:
```

  {
    "id": 1,
    "email": "user1@test.com",
    "first_name": "Hello",
    "last_name": "World"
  },
  {
    "id": 2,
    "email": "user2@test.com",
    "first_name": "Hello",
    "last_name": "World"
  }
]
```
</details>
