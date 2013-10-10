## Background

We often need to implement a long running script in background to do house keeping tasks for our application.

There are couple of techniques that we can use as listed bellow :

1. With cron by default there are no process control mechanism. If we set a script to run every minute, then it is very that there will be a race condition between process execution when every process of script takes more time than the scheduled time to finish.

2. When we use one script to execute a collection of tasks in an array using loop. The tasks will be executed one after another. This will make the last task in the collection have to wait for earlier tasks to finish.

## What this library do

This library provides basic threaded process control to run our tasks but we want to prevent race condition by running each tasks in order on forked child process using PHP **pcntl** extension.

## Minimum Requirements

1. **PHP >= 5.3.6**
2. Installed and enabled **pcntl extension**

## Installing

- Just clone the repository to your project
```
git clone git@github.org:ncrypthic/daemon
```

## Running
- Autoload the project

```
<?php

spl_autoload_register();
```

- Create process(es) classes which implements **ProcessInterface** interface

```
<?php
use Ncrypthic\Daemon\Process\ProcessInterface;

class AliceProcess implements ProcessInterface
{
    public function execute()
    {
        sleep(3);
        echo 'Alice done'.PHP_EOL;
        exit;
    }    
}

class BobProcess implements ProcessInterface
{
    public function execute()
    {
        sleep(3);
        echo 'Bob done'.PHP_EOL;
        exit;
    }    
}
```

- Create default manager instance and register the tasks

```
<?php

// ... Other commands

$manager = new \Ncrypthic\Daemon\Manager\DefaultManager();
$manager->addProcess(new AliceProcess());
$manager->addProcess(new BobProcess());
```

- Now daemonize the manager

```
<?php

try {
    $manager->daemonize();
} catch (\Ncrypthic\Daemon\Exception\ChildProcessException $exc) {
    echo $exc->getMessage();
}
```

## Output

- Console output

```
bash-4.2$ php <your_script>.php 
Alice done
Bob done
        No child processes
Alice done
Bob done
        No child processes
Alice done
Alice done
Bob done
        No child processes
Alice done
Bob done
        No child processes
Alice done
```

- Process manager output

```
php
   `+-php
    +-php
```

#### Todo

- Implements process events
- Support composser and packagist
