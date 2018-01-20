# simple-repository
> simple-repository 是一个为laravel5提供的数据库抽象层，目的是为了将应用的数据库操作和核心的业务逻辑分离开，保证controller的精致。

## 简介
> 这是一个简易版的 repository 模式 

## 安装
```
composer request ykaej/simple-repository
```
然后运行这个命令来发布资产和配置
```
php artisan vendor:publish --provider "Ykaej\Repository\Providers\RepositoryProvider"
```

## 使用
首先，创建你的repository类，你可以在命令行中使用如下命令自动生成该类

```
php artisan make:repository Post --model=Post
```
其中，--model 是可选的，用来指定 repository 中 model 的名称 ，默认情况下，会根据 repository 的名称自动生成 model 名，生成的文件如下：

```php
<?php
namespace App\Repositories\Eloquent;

use Ykaej\Repository\Eloquent\BaseRepository;
use App\Models\Post;

class PostRepository extends BaseRepository
{
    public function model()
    {
        return Post::class;
    }
}
```

当然，你也可以手动创建 repository 类，该类必须继承 `Ykaej\Repository\Eloquent\BaseRepository` ，并实现 `model()` 方法,
该方法用来指定该repository对应的数据模型。

最后，在你的 controller 中使用 repository

```php
<?php
namespace App\Http\Controllers;

use App\Repositories\Eloquent\PostRepository;

class PostController extends Controller
{
    protected $post;
    
    public function __construct(PostRepository $post)
    {
        $this->post = $post;
    }

    public function index()
    {
        $this->post->all();
    }
}
```

### 可用方法

`Ykaej\Repository\Contracts\RepositoryInterface`

```php
    public function all($columns = ['*']);  //获取所有记录
    
    public function paginate($limit = null, $columns = ['*']);  //分页, 默认可以再 `repository.php` 中调 
        
    public function find($id, $columns = ['*']);    //按id查找
    
    public function findOrFail($id, $columns = ['*']); //按id查找
    
    public function findByField($field, $value = null, $columns = ['*']);   //按指定字段查找
    
    public function findWhere(array $where, $columns = ['*']);  //按多个条件查找
    
    public function findWhereIn($field, array $values, $columns = ['*']);   
    
    public function findWhereNotIn($field, array $values, $columns = ['*']);
    
    public function create(array $attributes);  //创建一条数据
    
    public function update(array $attributes, $id); //修改一条数据
    
    public function updateBatch(array $multipleData);   //批量修改
    
    public function delete($id);    //删除
    
    public function deleteWhere(array $where);  //按条件删除
    
    public function has($relation); //数据关联
    
    public function with($relations);   //数据关联
    
    public function withCount($relations);  //关联数据计数
    
    public function whereHas($relation, $closure);  //关联查找
    
    public function orderBy($column, $direction = 'asc');
    
    public function where($field, $condition, $value = null, $boolean = 'and');
```

### 简单例子
创建一条记录:
```php
$this->post->create(Input::all());
```

修改记录:
```php
$this->post->create(Input::all(),$id);
```

批量修改,默认以id为条件更新，如果没有ID则以第一个字段为条件:
```php
$data =[
 ['id' => 1, 'name' => '张三', 'email' => 'zhansan@qq.com'],
 ['id' => 2, 'name' => '李四', 'email' => 'lisi@qq.com'],
 ...
];
$this->post->updateBatch($data);
```

按id查找，可以指定查找字段:
```php
$this->post->find($id,['id','title','body']);
```

按指定字段查找:
```php
$this->post->findByField('title',$title);
```

多个条件查找:
```php
$this->post->findWhere([
    'author' => $author_id,
    ['year','>',$year]
]);
```

## Criteria

> Criteria 是一个让你可以根据具体或者一系列复杂的条件来向你的 repository 发起查询的方式，
你可以将一些可能会重复出现多次的查询条件放到这里，达到复用的目的，将复杂的条件查询从 controller 当中抽离出来。

> 你的 Criteria 类必须继承 `Ykaej\Repository\Criteria\Criteria`

### 创建Criteria

```php
php artisan make:criteria Post
```

一个简单的例子，简单查 没有隐藏的文章
```php
<?php
namespace App\Repositories\Criteria;

use Ykaej\Repository\Contracts\RepositoryInterface;
use Ykaej\Repository\Criteria\Criteria;

class UnHiddenCriteria extends Criteria
{
    public function apply($model, RepositoryInterface $repository)
    {
        return $model->where('is_hidden','F');
    }
}
```

然后，在你的 controller 里面，你可以调用 repository 的 `pushCriteria` 方法:

```php
<?php
namespace App\Http\Controllers;

use App\Repositories\Eloquent\PostRepository;
use App\Repositories\Criteria\UnHiddenCriteria;

class PostController extends Controller
{
    protected $post;
    
    public function __construct(PostRepository $post)
    {
        $this->post = $post;
    }

    public function index()
    {
        $this->post->pushCriteria(new UnHiddenCriteria());
        $this->post->all();
    }
}
```

你也可以设置在 post 下全局使用的 criteria ，在 `PostRepository` 下 `boot` 方法中实现 `pushCriteria` 方法

```php
`PostRepository`

public function boot()
{
    $this->pushCriteria(new UnHiddenCriteria());
}
```

## 最后
以上的思路和代码在阅读了很多大神的代码后产生的，尤其是 [这个](https://github.com/prettus/l5-repository).










