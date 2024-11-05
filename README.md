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

Операции. 
Аргументы: 
+ ``$sql`` - запрос вида ``select * from table_name where field=:arg``
+ ``$args`` - массив аргументов [':arg' => $arg]

Типы операций:
+ ``insert(string $sql, array $args)``
+ ``update(string $sql, array $args): bool``
+ ``delete(string $sql, array $args): bool``
+ ``queryPrepared(string $sqlExpession, ?array $arguments = null, bool $isOneValue = true)``
+ ``query(string $sql, bool $isOneValue = true)``
+ ``exec(string $sql)``
+ ``executeProcedure($sql, $out)``
