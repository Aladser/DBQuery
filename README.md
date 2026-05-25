# Класс запросов в БД на основе PDO

```
DBQuery(
* @param string $host       хост
* @param string $nameDB     имя бд
* @param string $userDB     пользователь
* @param string $passwordDB пароль
* @param string $db_type    тип БД: mysql, pgsql, mssql, sqlite, sybase
)
```

Типы операций:
+ ``insert``
+ ``update``
+ ``delete``
+ ``queryPrepared(string $sqlExpession, ?array $arguments = null, bool $isOneValue = true)``
+ ``query(string $sql, bool $isOneValue = true)``
+ ``exec(string $sql)``
+ ``executeProcedure($sql, $out)``

```
$dbQuery = new DBQuery('адрес', 'имя базы', 'пользователь', 'пароль');
$data = $dbQuery->query('SELECT * FROM catalog_engine_types WHERE id = 1');
$data = $dbQuery->queryPrepared('SELECT * FROM catalog_engine_types WHERE id = :id', ['id'=>1]);
```
