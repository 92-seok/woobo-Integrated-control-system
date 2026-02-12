<?php
// ini_set('display_errors', 1);
// error_reporting(E_ALL);

require_once $_SERVER['DOCUMENT_ROOT'] . '/include/db/vo/vo.php';
require_once $_SERVER['DOCUMENT_ROOT'] . '/include/db/DBConnector.php';

class DAO_T
{
    const STDLIMIT = 1000;

    public $conn;
    public $sql = '';
    public $table = '';
    public $selectKey = '';
    public $voName = '';

    //////////////////////////////////////////////////////////////////////////
    //////////                DB 기본 구동 로직                      //////////
    //////////////////////////////////////////////////////////////////////////
    function __construct(string $table, string $selectKey, string $voName = null)
    {
        $dbconn = new DBCONNECT();
        $this->conn = $dbconn->connect();

        $this->table = $table;
        $this->selectKey = $selectKey;

        if ($voName === null) {
            $this->voName = "{$this->table}_vo";
        } else {
            $this->voName = $voName;
        }
    }

    function EXECUTE(): void
    {
        try {
            $statement = $this->conn->query($this->sql);
            if ($statement === false) {
                throw new PDOException("[WARN] EXECUTE SQL 문구에 오류가 있습니다. ({$this->sql})");
            }
        } catch (PDOException | Exception $e) {
            warningLog("{$e->getMessage()}");
            return;
        }
    }

    function QUERY(): array
    {
        $rtv = [];
        try {
            $statement = $this->conn->query($this->sql);
            $statement->setFetchMode(PDO::FETCH_CLASS, "{$this->voName}");
            $rtv = $statement->fetchAll();
            if ($rtv === false) {
                throw new PDOException("[WARN] QUERY SQL 문구에 오류가 있습니다. ({$this->sql})");
            }
        } catch (PDOException | Exception $e) {
            warningLog("{$e->getMessage()}");
            return [];
        }

        return $rtv;
    }

    function PREPARE(string $sql, object $obj): array
    {
        try {
            $stmt = $this->conn->prepare($sql);
            foreach ($obj as $k => &$v) {
                switch (gettype($v)) {
                    case 'double':
                    case 'string':
                        $stmt->bindParam($k, $v, PDO::PARAM_STR);
                        break;

                    case 'integer':
                        $stmt->bindParam($k, $v, PDO::PARAM_INT);
                        break;

                    case 'array':
                        for ($i = 0; $i < count($v); $i++) {
                            $stmt->bindValue($i + 1, $v[$i]);
                        }
                        break;

                    default:
                        throw new Exception('값이 잘못되었습니다.(' . print_r($v) . ')');
                }
            }

            $stmt->execute();
            $stmt->setFetchMode(PDO::FETCH_CLASS, "{$this->voName}");
            $vo = $stmt->fetchAll();
            if ($vo === false) {
                throw new PDOException("[WARN] PREPARE SQL 문구에 오류가 있습니다. ({$sql})");
            }

            return $vo;
        } catch (PDOException | Exception $e) {
            warningLog("{$e->getMessage()}({$sql} : " . gettype($v) . ')');
            return [];
        }
    }

    function INSERTID()
    {
        return $this->conn->lastInsertId();
    }

    //////////////////////////////////////////////////////////////////////////
    //////////                  기본 DML  로직                       //////////
    //////////////////////////////////////////////////////////////////////////
    public function SQL(string $sql): array
    {
        $this->sql = $sql;

        if (strpos($sql, 'SELECT') === 0) {
            return $this->QUERY();
        } else {
            $this->EXECUTE();
            return [];
        }
    }

    public function SQLTOARRAY(string $sql): array
    {
        // VO Class를 갖지 않는 SQL문
        $rtv = [];
        $this->sql = $sql;

        try {
            $statement = $this->conn->query($this->sql);
            $statement->setFetchMode(PDO::FETCH_ASSOC);
            $rtv = $statement->fetchAll();
            if ($rtv === false) {
                throw new PDOException("[WARN] SQLTOARRAY SQL 문구에 오류가 있습니다. ({$this->sql})");
            }
        } catch (PDOException | Exception $e) {
            warningLog("{$e->getMessage()}({$this->sql})");
            return [];
        }

        return $rtv;
    }

    public function SELECT(string $where = '1=1', string $order = '', int $limit = self::STDLIMIT, int $count = 0): array
    {
        $vo = false;

        try {
            $this->sql = "SELECT * FROM {$this->table} WHERE {$where} ORDER BY ";
            $this->sql .= $order == '' ? "{$this->selectKey}" : "{$order}";

            if ($limit >= 0) {
                $this->sql .= " LIMIT {$limit}";
            }
            if ($count > 0) {
                $this->sql .= ",{$count}";
            }

            $vo = $this->QUERY();
        } catch (PDOException | Exception $ex) {
            warningLog("{$ex->getMessage()}({$this->sql})");
        }

        return $vo;
    }

    public function SEL_PRE(object $obj, string $where = '1=1', string $order = '', int $limit = self::STDLIMIT, int $count = 0): array
    {
        $vo = false;

        try {
            $this->sql = "SELECT * FROM {$this->table} WHERE {$where} ORDER BY ";
            $this->sql .= $order == '' ? "{$this->selectKey}" : "{$order}";

            if ($limit >= 0) {
                $this->sql .= " LIMIT {$limit}";
            }
            if ($count > 0) {
                $this->sql .= ",{$count}";
            }

            $vo = $this->PREPARE($this->sql, $obj);
        } catch (PDOException | Exception $ex) {
            warningLog("{$ex->getMessage()}({$this->sql})");
        }

        return $vo;
    }

    public function SINGLE(string $where = '1=1', string $order = '', int $limit = self::STDLIMIT, int $count = 0)
    {
        $rtv = null;

        try {
            $voArray = $this->SELECT($where, $order, $limit, $count);

            foreach ($voArray as $l) {
                if ($rtv === null) {
                    $rtv = $l;
                }
                if ($rtv !== null) {
                    break;
                }
            }

            if ($rtv === null) {
                $rtv = false;
            }
        } catch (PDOException | Exception $ex) {
            warningLog("{$ex->getMessage()}({$this->sql})");
            return false;
        }

        return $rtv;
    }

    public function ARRAYTOSINGLE(string $sql)
    {
        $rtv = null;

        try {
            $voArray = $this->SQLTOARRAY($sql);

            foreach ($voArray as $l) {
                if ($rtv === null) {
                    $rtv = $l;
                }
                if ($rtv !== null) {
                    break;
                }
            }

            if ($rtv === null) {
                $rtv = false;
            }
        } catch (PDOException | Exception $ex) {
            warningLog("{$ex->getMessage()}({$this->sql})");
            throw $ex;
        }

        return $rtv;
    }

    public function INSERT($vo): void
    {
        try {
            $select = '';
            $value = '';

            foreach ($vo as $key => $val) {
                if ($select !== '') {
                    $select .= ', ';
                }
                if ($value !== '') {
                    $value .= ', ';
                }

                if (key($vo) === $key && $val === null) {
                }
                // Key가 Auto_Increment로 입력하지 않아도 될 경우
                else {
                    $select .= "{$key}";

                    if ($val === null) {
                        $value .= 'NULL';
                    } elseif (gettype($val) == 'integer' || gettype($val) == 'double') {
                        $value .= "{$val}";
                    } elseif (gettype($val) == 'string') {
                        $value .= "'{$val}'";
                    } else {
                        throw new PDOException('[WARN] INSERT VALUE 값이 잘못되었습니다. (' . print_r($vo) . ')');
                    }
                }
            }

            $this->sql = "INSERT INTO {$this->table}( {$select} ) VALUES ( {$value} )";
            $this->EXECUTE();
        } catch (PDOException | Exception $e) {
            warningLog($e->getMessage());
        }
    }

    public function Update($value, $where): void
    {
        try {
            if ($value == '' || $where == '') {
                throw new Exception('Value 및 Where 조건이 비어있습니다.');
            }

            $this->sql = "UPDATE {$this->table} SET {$value} WHERE {$where}";
            $this->EXECUTE();
        } catch (PDOException | Exception $e) {
            warningLog($e->getMessage());
        }
    }

    public function DELETE($where): void
    {
        try {
            if ($where == '') {
                throw new PDOException('[WARN] DELETE Where절 조건이 설정되지 않았습니다 (' . print_r($where) . ')');
            }

            $this->sql = "DELETE FROM {$this->table} WHERE {$where}";
            $this->EXECUTE();
        } catch (PDOException | Exception $e) {
            warningLog($e->getMessage());
        }
    }

    public function rowCount(string $where = '1=1'): int
    {
        $rtv = 0;

        try {
            $this->sql = "SELECT count(*) AS cnt FROM {$this->table} WHERE {$where}";

            $statement = $this->conn->query($this->sql);
            $statement->setFetchMode(PDO::FETCH_ASSOC);
            $vo = $statement->fetchAll();
            if ($vo === false) {
                throw new PDOException("[WARN] rowCount SQL 문구에 오류가 있습니다. ({$this->sql})");
            }

            $rtv = (int) $vo[0]['cnt'];
        } catch (PDOException | Exception $e) {
            warningLog($e->getMessage());
            return 0;
        }

        return $rtv;
    }

    public function existTable($table = null): bool
    {
        $rtv = false;

        try {
            $table = $table !== null ? $table : $this->table;
            $statement = $this->conn->query("SHOW TABLES LIKE '{$table}'");
            $statement->setFetchMode(PDO::FETCH_ASSOC);
            $vo = $statement->fetchAll();
            if ($vo === false) {
                throw new PDOException("[WARN] existTable SQL 문구에 오류가 있습니다. ({$table})");
            }

            if (count($vo) > 0) {
                $rtv = true;
            } else {
                $rtv = false;
            }
        } catch (PDOException | Exception $e) {
            warningLog("{$e->getMessage()}({$this->sql})");
            return false;
        }

        return $rtv;
    }
}