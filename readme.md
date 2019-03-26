<p align="center"><img src="https://laravel.com/assets/img/components/logo-laravel.svg"></p>

<p align="center">
<a href="https://travis-ci.org/laravel/framework"><img src="https://travis-ci.org/laravel/framework.svg" alt="Build Status"></a>
<a href="https://packagist.org/packages/laravel/framework"><img src="https://poser.pugx.org/laravel/framework/d/total.svg" alt="Total Downloads"></a>
<a href="https://packagist.org/packages/laravel/framework"><img src="https://poser.pugx.org/laravel/framework/v/stable.svg" alt="Latest Stable Version"></a>
<a href="https://packagist.org/packages/laravel/framework"><img src="https://poser.pugx.org/laravel/framework/license.svg" alt="License"></a>
</p>

## This project

Starting point to create a webapp with:

- [A Laravel 5.8 backend](https://laravel.com/docs/5.8/)
- [Vue frontend](https://vuejs.org/)
- [Vuetify framework](https://vuetifyjs.com/en/)
- [Typescript class](https://github.com/vuejs/vue-class-component) and [property decorators](https://github.com/kaorun343/vue-property-decorator)

Uses Laravel passport to authenticate

## Getting started

Clone the repository and copy the .env.example to .env file to connect to your database. After you have connected to a database run ```php artisan migrate```. 
When you're done migrating run ```php artisan passport:install``` to generate the laravel passport tokens. These should go in your .env file aswell. 

To run the app go type npm run dev or npm run watch. 