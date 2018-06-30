## install

`composer require ysandreew/queue`

## usage

### 创建连接

创建一个Redis链接,并实例化一个队列实例,第一个参数为队列名称

```php
<?php
use Predis\Client;
use Ysandreew\Queue\Queue;

$redis = new Client([
    'scheme' => 'tcp',
    'host'   => '127.0.0.1',
    'port'   => 6379,
    'password'=>'password'
]);

$queue=new Queue('test',$redis);
```

### 如何分发任务

首先你需要做的只是将你的任务类继承自`Ysandreew\Job`类,在run()方法中编写你的业务逻辑

你可以在Job类中注入任意的其他对象实例并且使用它

最后，确保你的Job能自动加载,否则后续出队操作需要反序列化会出现找不到类

```php
<?php
namespace App;

use Monolog\Logger;
use Ysandreew\Queue\Job;

class TestJob extends Job {
    public $name;
    public $logger;

    public function run()
    {
        $this->logger->info("My Name is {$this->name}");

    }
    public function __construct(string $name,Logger $logger)
    {
        $this->name=$name;
        $this->logger=$logger;
    }
}
```

然后你可以调用`dispatch()`方法将任务实例放入列队

```
<?php
use App\TestJob;
use Monolog\Logger;

$queue->dispatch(new TestJob("Lee",new Logger()));
```

### 执行

只需要调用`start()`方法,然后通过命令行方式运行一个进程来执行队列

请确保你的脚本能够加载你的Job类以及Job类所依赖的类

```
$queue->start();
```



