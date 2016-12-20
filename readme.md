# Notes API

A Laravel Lumen framework based API

## Features

 - Lumen Passport ([Source](https://github.com/dusterio/lumen-passport))
 - AES Encryption support
 - RESTful API
 
## Setup

 1. Clone or download repository
 2. Cd into that directory
 3. Run `composer install`
 4. Duplicate `.env.example` to `.env`
 5. Provide the `APP_KEY` value with some random string, like:
 `base64:8Y6coT87VMVS5lfWa9ZwbgpWH5YcKhWgzkR/YiJZCNs=`
 6. Provide the `LUMEN_PASSPORT_TOKEN_PERIOD` value with appropriate number of seconds, like `60`.
 6. Set the database settings according to your system's database.
 7. Run `php artisan migrate --seed`
 8. Run `php artisan passport:install`
 9. Copy the output value from `Password grant client` to your `.env`, like:
 
     ```
     APP_CLIENT_ID=2
     APP_CLIENT_SECRET=eRlUMINSSgmqXOUUJIISDQPFpfGODLiPTJ6wUKXQ
     ```
 10. To run the application, issue this command 
 
    `php -S localhost:9000 -t public`.

## Usage

 1. Make a `POST` request to `http://localhost:9000/oauth/token`. An example body content would be like,
 
    ```
    {
        "username": "lumen.api.admin@gmail.com",
        "password": "12345678"
        "client_id": 2,
        "client_secret": "eRlUMINSSgmqXOUUJIISDQPFpfGODLiPTJ6wUKXQ"
        "grant_type": "password"
    }
    ```
 2. The returned `access_token` and `refresh_token` needs to be kept referenced.
 3. To make any further requests to defined routes add the `access_token` as Authorization header.
 4. To refresh a token once expired, make a similar request as in step one, only the body content would be like,
 
     ```
        {
            "client_id": 2,
            "client_secret": "eRlUMINSSgmqXOUUJIISDQPFpfGODLiPTJ6wUKXQ"
            "grant_type": "refresh_token",
            "refresh_token": "VN3LVe+knMmNcS4tf9J3rPAorWGrkgriYdtbMXcvzo53loo0A1la9jJYxrtWfQBMq8Lr2QwT2JL/VeTT6fPysa+MZAbtd14McXqK++1diZuTmNNU/YbuhmSWwudcTFAv+JLYB65v9uL5Evc9DTCS1DNNFN/Nt+6QyM0RLRJgNxCSDtIx0donJ0dfAl1qtKsRkSdbFJB+g1DVm6SSHwLBjwIPavoXBxOcK1maLQj1wd4P8SXU/m1aNYLLQL9fQdFO/mqkKpJXoOcfc3U4ALJ0mMYfnEXsz0tpeR+u6JeX/HNn2MTj4EStHEqm1g8GLqXQLnAj+HVNgMLfoDWrDv+3scPxXAryXn5B1HF2Ysv9pQ8n3xMMGB9hzcdsJNQ60cTRufSGwWLdkRlF4eO3ZYm9pZo6jiFCtApmE2/ARgJonefPc9tiJ+27ji+u+GmEE9mP3Csy5Ud2xdrERMN9MDEg9JQwWAox8TGI5RjQVgoYceuQHcB6eHt1UF69AN3WN3lnegr0U3k7lGBQpRzgrRmoyiVhBoAcbi+KrO7FbZDcOkVL8RUXXVlalY+OitJIdpmGmJEp6z7qOONh3VRgknJf46Q8mHDaH+4Z/+3mpVqIiJQ7YhVo3xIIJYVZFMYECMfaro+HRJ80uhQikIN8c9GTo4DZqqU6X0hP2lU6W7Kx3uY="
        }
     