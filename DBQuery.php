<?php

/** Класс запросов в БД на основе PDO */
class DBQuery
{
    private string $host;
    private string $nameDB;
    private string $userDB;
    private string $passwordDB;
    private string $db_type;
    private $dbConnection;
    // массив поддерживаемых СУБД
    private static $DB_TYPE = ['mysql', 'pgsql', 'mssql', 'sqlite', 'sybase'];

    /**
     * @param string $host        хост
     * @param string $nameDB      имя бд
     * @param string $userDB      пользователь
     * @param string $passwordDB  пароль
     * @param string $db_type     тип БД: mysql, pgsql, mssql, sqlite, sybase
    */
    public function __construct(string $host, string $nameDB, string $userDB, string $passwordDB, string $db_type='mysql')
    {
        if (!in_array($db_type, DBQuery::$DB_TYPE)) {
            throw new \Exception("Указанный тип БД ($db_type) не поддерживается");
        }

        $this->host = $host;
        $this->nameDB = $nameDB;
        $this->userDB = $userDB;
        $this->passwordDB = $passwordDB;
        $this->db_type = $db_type;
        $this->dbConnection = new \PDO("$this->db_type:dbname=$nameDB; host=$host", $userDB, $passwordDB);
    }
    
    /** Выполняет подготовленный запрос
     * 
     * @param array $arguments аргументы запроса вида ['arg'=>value], которые вставляются в SQL :arg
     * @param bool $is_one_value одно или множество запрашиваемых полей
     * @return mixed массив строк или одно значение
    */
    public function queryPrepared(string $sql, array $arguments=[], bool $is_one_value=true)
    {
        $stmt = $this->dbConnection->prepare($sql);
        if (count($arguments)>0) {
            $stmt->execute($arguments);
        } else {
            $stmt->execute();
        }

        $mode = \PDO::FETCH_OBJ;
        return $is_one_value ? $stmt->fetch($mode) : $stmt->fetchAll($mode);
    }

    /** Выполняет запрос
     * @param string $sql          запрос
     * @param bool   $is_one_value одно или множество запрашиваемых полей
     *
     * @return mixed массив строк или одно значение
    */
    public function query(string $sql, bool $is_one_value = true)
    {
        $data = $this->dbConnection->query($sql);
        $mode = \PDO::FETCH_OBJ;
        return $is_one_value ? $stmt->fetch($mode) : $stmt->fetchAll($mode);
    }
    
    /**
     * INSERT
     * @param $table_name имя таблицы
     * @param $fields_array массив полей строки
     * @return id
    */
    public function insert(string $table_name, array $fields_array): int
    {
        if (count($fields_array)===0) {
            return 0;
        }

        $names_str = implode(', ', array_keys($fields_array));
        $name_placeholders_str = ':'.implode(', :', array_keys($fields_array));
        $stmt = $this->dbConnection->prepare("INSERT INTO {$table_name}($names_str) VALUES($name_placeholders_str)");
        $stmt->execute($fields_array);

        return $this->dbConnection->lastInsertId();
    }
    
    /**
     * UPDATE
     * @param string $table_name имя таблицы
     * @param array $fields_array массив полей строки
     * @param integer $id id строки
     * @return integer
    */
    public function update(string $table_name, array $fields_array, int $id): int
    {
        if (count($fields_array)===0) {
            return false;
        }

        $values_string = '';
        foreach (array_keys($fields_array) as $key) {
            $values_string .= "$key = :$key, ";
        }
        $values_string = mb_substr($values_string, 0, mb_strlen($values_string)-2);
        $fields_array['id'] = $id;

        $stmt = $this->dbConnection->prepare("UPDATE $table_name SET $values_string WHERE id=:id");
        $stmt->execute($fields_array);
        
        return $stmt->rowCount();
    }
    
    /**
     * DELETE
     * @param $table_name имя таблицы
     * @param $where_condition условие
     * @param $args массив параметров WHERE условия
     * @return integer
    */
    public function delete(string $table_name, int $id): int
    {
        $stmt = $this->dbConnection->prepare("DELETE FROM $table_name WHERE id=:id");
        $stmt->execute(['id'=>$id]);
        return $stmt->rowCount();
    }
    
    /** Выполняет изменения данных.
     * @param string $sql запрос
     *
     * @return false|int число измененных строк
    */
    public function exec(string $sql)
    {
        return $this->dbConnection->exec($sql);
    }

    /** Выполняет процедуру с возвращаемым результатом
     * @param mixed $sql выражение
     * @param mixed $out выходная переменная, куда будет возвращен результат
    */
    public function executeProcedure($sql, $out)
    {
        $stmt = $this->dbConnection->prepare("call $sql");
        $stmt->execute();
        $stmt->closeCursor();
        $procRst = $this->dbConnection->query("select $out as info");

        return $procRst->fetch(\PDO::FETCH_ASSOC)['info'];
    }
}
