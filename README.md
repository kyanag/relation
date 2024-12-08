# Relation

没有 Laravel 也能使用的关联模型库

## 安装
```
composer require kyanag/relation
```

使用
===========
```php
$pdo = new \PDO("sqlite:{$sqlite}");
$pdo->setAttribute(PDO::ATTR_DEFAULT_FETCH_MODE, PDO::FETCH_NAMED);

//创建Loader
$loader = \Kyanag\Relation\Relation::createLoader($pdo);
$authors = $pdo->query("select * from authors")->fetchAll();

## 一对一(HasOne)
$authors = $loader->load($authors, [
    'author_detail' => \Kyanag\Relation\Relation::hasOne("authors.id", "author_details.author_id"),
]);

## 一对一(belongsTo)
$book = $pdo->query("select * from books limit 1")->fetch();
//单条记录使用 loadOne 进行加载
$book = $loader->loadOne($book, [
    'author' => \Kyanag\Relation\Relation::belongsTo("books.author_id", "author.id"),
]);

## 一对多
$authors = $loader->load($authors, [
    'books' => \Kyanag\Relation\Relation::hasMany("authors.id", "books.author_id")
]);

## 多对多(未完成)
## 远层一对多(未完成)
## 多态关联(未完成)
```

### 嵌套关联

```php
//关联类型的最后一个参数表示嵌套关联
$authors = $loader->load($authors, [
    'books' => \Kyanag\Relation\Relation::hasMany("authors.id", "books.author_id", [
        'comments' => \Kyanag\Relation\Relation::hasMany("books.id", "comments.book_id")
    ])
]);
```

### 关联约束
```php
$authors = $loader->load($authors, [
    'books' => \Kyanag\Relation\Relation::hasMany("authors.id", "books.author_id")
        //支持关联约束
        ->where("author_id", ">", 2)
]);
```

### 代码示例表结构 (SQLITE)
```sql
-- 作家表
drop table if exists `authors`;
CREATE TABLE `authors` (
       `id` int primary key not null ,
       `name` CHARACTER(30) not null ,
       `status` int not null default 1
);
INSERT INTO authors (id, name, status)
VALUES (1, "余华", 1),
       (2, "刘慈欣", 1);

-- 作家详情表
drop table if exists `author_details`;
CREATE TABLE `author_details` (
      `author_id` int primary key not null ,
      `nickname` CHARACTER(30) not null default '',
      `address` CHARACTER(100) not null default ''
);
INSERT INTO author_details (author_id, nickname, `address`)
VALUES (1, "余华", "浙江海盐"),
       (2, "刘慈欣", "山西阳泉");

-- 作品表
drop table if exists `books`;
CREATE TABLE `books` (
     `id` int primary key not null ,
     `author_id` int not null ,
     `book_name` CHARACTER(30) not null
);
INSERT INTO books (id, author_id, book_name)
VALUES (1, 1, "活着"),
       (2, 2, "三体"),
       (3, 2, "球状闪电");

drop table if exists `comments`;
CREATE TABLE `comments` (
    `id` int primary key not null ,
    `book_id` int not null ,
    `content` TEXT
);
INSERT INTO comments (id, book_id, content)
VALUES (1, 1, "活着. 评论1"),
       (2, 1, "活着. 评论2"),
       (3, 2, "三体. 评论1"),
       (4, 3, "球状闪电. 评论1");
```