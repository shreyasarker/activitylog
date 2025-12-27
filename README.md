# Activity Log for Laravel

A simple, lightweight activity logging package for Laravel 12.

## Features
- Log custom activities
- Polymorphic causer & subject
- Configurable table name
- Request IP & user-agent capture
- Fully tested with Orchestra Testbench

## Installation

```bash
composer require shreyasarker/activitylog
```

## Usage
```
activity()
    ->event('post.created')
    ->performedOn($post)
    ->causedBy(auth()->user())
    ->withProperties(['title' => $post->title])
    ->log('Post created');
```

## Configuration
```
php artisan vendor:publish --tag=activitylog-config
php artisan vendor:publish --tag=activitylog-migrations
php artisan migrate
```

## Author
Shreya Sarker
📧 shreya@codeboid.com
