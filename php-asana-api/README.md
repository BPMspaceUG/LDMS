# Asana API PHP class

A PHP class that acts as wrapper for Asana API.  
Lets make things easy! :)

It is licensed under the Apache 2 license and is Copyrighted 2014 Ajimix

## Installing

Choose your favorite flavour

- Download the php class from github.
- Use [Packagist](https://packagist.org/packages/ajimix/asana-api-php-class) PHP package manager.

Finally require the asana.php file.

## Working with the class

First declare the asana class

```php
$asana = new Asana(array(
    'apiKey' => 'YOUR_COOL_API_KEY'
));
```

*Optionally you can pass an accessToken instead of an apiKey if you use OAuth. Read below for more info.*

#### Creating a task

```php
$asana->createTask(array(
   'workspace' => '176825', // Workspace ID
   'name' => 'Hello World!', // Name of task
   'assignee' => 'bigboss@bigcompany.com', // Assign task to...
   'followers' => array('3714136', '5900783') // We add some followers to the task... (this time by ID)
));
```

#### Adding task to project

```php
$asana->addProjectToTask('THIS_TASK_ID_PLEASE', 'TO_THIS_COOL_PROJECT_ID');
```

#### Commenting on a task

```php
$asana->commentOnTask('MY_BEAUTIFUL_TASK_ID', 'Please please! Don't assign me this task!');
```

#### Getting projects in all workspaces

```php
$asana->getProjects();
```

#### Updating project info

```php
$asana->updateProject('COOL_PROJECT_ID', array(
    'name' => 'This is a new cool project!',
    'notes' => 'At first, it wasn't cool, but after this name change, it is!'
));
```

#### Do more

There are a [lot more methods](https://github.com/ajimix/asana-api-php-class/blob/master/asana.php) to do multiple things with asana.

See the examples [inside examples folder](https://github.com/ajimix/asana-api-php-class/tree/master/examples), read the comments on the [class file]((https://github.com/ajimix/asana-api-php-class/blob/master/asana.php)) for class magic and read [Asana API documentation](http://developer.asana.com/documentation/) if you want to be a master :D

Enjoy ;D

## Using Asana OAuth tokens

To use this API you can also create an App on Asana, in order to get an oAuth access token that gives you the same access as with an API key. Include the class:

```php
require_once('asana-oauth.php');
```

Declare the oAuth class as:

```php
$asanaAuth = new AsanaAuth('YOUR_APP_ID', 'YOUR_APP_SECRET', 'CALLBACK_URL');
$url = $asanaAuth->getAuthorizeUrl();
```

Where YOUR_APP_ID, YOUR_APP_SECRET and CALLBACK_URL you get from your App's details on Asana. Now, redirect the browser to the result held by $url. The user will be asked to login & accept your app, after which the browser will be returned to the CALLBACK_URL, which should process the result:

```php
$code = $_GET['code'];
$asanaAuth->getAccessToken($code);
```

And you will receive an object with the access token and a refresh token
The token expires after one hour so you can refresh it doing the following:

```php
$asanaAuth->refreshAccessToken('ACCESS_TOKEN');
```

For a more detailes instructions on how to make oauth work check the example in examples/oauth.php

## Author

**Twitter:** [@ajimix](http://twitter.com/ajimix)

**GitHub:** [github.com/ajimix](https://github.com/ajimix)

**Contributors:** [view contributors](https://github.com/ajimix/asana-api-php-class/graphs/contributors)


### Copyright and license

Copyright 2014 Ajimix

Licensed under the Apache License, Version 2.0 (the "License");
you may not use this work except in compliance with the License.
You may obtain a copy of the License in the LICENSE file, or at:

   http://www.apache.org/licenses/LICENSE-2.0

Unless required by applicable law or agreed to in writing, software
distributed under the License is distributed on an "AS IS" BASIS,
WITHOUT WARRANTIES OR CONDITIONS OF ANY KIND, either express or implied.
See the License for the specific language governing permissions and
limitations under the License.