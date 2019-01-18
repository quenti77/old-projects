<?php

namespace EasyHQ\Base;

use EasyHQ\Config;
use EasyHQ\Database;
use EasyHQ\Query\Statement;
use EasyHQ\Exception\StatementException;

/**
 * Class BaseModel
 * @package App\Models
 */
class BaseModel {

	protected $db = null;
	protected $table = '';
	protected $mode;
	protected $class_name = '';

	/**
	 * @var array
	 */
	public $desc = [];
	public $whereArray = [];

	private $short_class_name = '';

	const MODE_NEW = 0;
	const MODE_UPDATE = 1;


	public function __construct($mode = BaseModel::MODE_UPDATE) {
		$this->db = Database::get();
		$this->short_class_name = Config::getClassName($this->class_name);
		$this->mode = $mode;
	}

    public static function now() {
        return date('Y-m-d H:i:s');
    }

	/**
	 * Permet de sauvegarder les données de l'objet actuel dans la table
	 * @return bool
	 */
	public function save() {
		if (empty($this->desc)) {
			return false;
		}

		$query = new Statement($this->table, $this->class_name, $this->short_class_name);

		switch($this->mode) {
			case BaseModel::MODE_NEW:
				return $this->saveInsert($query->insert());
			case BaseModel::MODE_UPDATE:
				return $this->saveUpdate($query->update());
			default:
				return false;
		}
	}

    private function saveInsert($query) {
        $keys = [];

        foreach($this->desc as $k => $v) {
            $val = $this->$k;
            switch($v) {
                case \PDO::PARAM_INT:
                case \PDO::PARAM_BOOL:
                    $keys[$k] = $val;
                    break;
                case \PDO::PARAM_STR:
                    $keys[$k] = Database::get()->quote($val);
                    break;
                default:
                    break;
            }
        }

        $query = $query->columns(array_keys($keys));
        return $query->values($keys)->save();
    }

    private function saveUpdate($query) {
        foreach($this->desc as $k => $v) {
            if (isset($this->$k)) {
                switch($v) {
                    case \PDO::PARAM_INT:
                    case \PDO::PARAM_BOOL:
                        $query = $this->saveSet($query, $k, $this->$k, false);
                        break;
                    case \PDO::PARAM_STR:
                        $query = $this->saveSet($query, $k, $this->$k, true);
                        break;
                    default:
                        break;
                }
            }
        }

        $set = false;
        foreach($this->whereArray as $k => $v) {
            if ($set) {
                $query = $query->andWhere($k, $v);
            } else {
                $query = $query->where($k, $v);
                $set = true;
            }
        }

        return $query->save();
    }

    /**
     * @param $query
     * @param $k
     * @param $v
     * @param $escape
     * @return mixed
     */
    private function saveSet($query, $k, $v, $escape) {
        return $query->set($k, $v, $escape);
    }

	/**
	 * Permet de supprimer l'entrée dans la table de l'objet actuel
	 * @return bool
	 */
	public function delete() {
		if (empty($this->desc) || $this->mode == BaseModel::MODE_NEW) {
			return false;
		}

		$query = new Statement($this->table, $this->class_name, $this->short_class_name);
		$query->delete();

		$set = false;
		foreach($this->whereArray as $k => $v) {
			if ($set) {
				$query = $query->andWhere($k, $v);
			} else {
				$query = $query->where($k, $v);
				$set = true;
			}
		}

		return $query->save();
	}

    public static function insert() {
        $name = get_called_class();
        $obj = new $name();

        $table = $obj->table;

        $query = new Statement($table, $obj->class_name, $obj->short_class_name);
        $query = $query->insert();
        return $query;
    }

    /**
     * Permet de commencer une requête select.
     * @param $alias string fix alias if not empty
     * @return Statement|null
     */
	public static function select($alias = '') {
		$name = get_called_class();
		$obj = new $name();

        $table = $obj->table;
        if (empty($alias) || !preg_match('#^[a-zA-Z\_]+$#', $alias)) {
            $table = "$table@$table";
        } else {
            $table = "$table@$alias";
        }

		$query = new Statement($table, $obj->class_name, $obj->short_class_name);
		$query = $query->select();
		return $query;
	}

	/**
	 * Permet de prendre une instance ou un tableau d'instance via une condition.
	 * @param $col
	 * @param $operator
	 * @param null $val
	 * @param bool $escape
	 * @return array
	 */
	public static function find($col, $operator, $val = null, $escape = true) {
		$result = self::select()->where($col, $operator, $val, $escape)->get();

		if (count($result) == 1) {
			$result = $result[0];
		}

		return $result;
	}

	/**
	 * Permet de prendre une instance ou un tableau d'instance via une condition.
	 * Si aucun enregistrement trouvé, alors on renvoie une esception.
	 * @param $col
	 * @param $operator
	 * @param null $val
	 * @param bool $escape
	 * @return array
	 * @throws StatementException
	 */
	public static function findOrFail($col, $operator, $val = null, $escape = true) {
		$result = self::find($col, $operator, $val, $escape);

		if (empty($result)) {
			throw new StatementException('No results');
		}

		return $result;
	}

	/**
	 * Permet de prendre une instance ou un tableau d'instance via une condition.
	 * Si aucun enregistrement trouvé, alors on créé une nouvelle instance.
	 * @param $col
	 * @param $operator
	 * @param null $val
	 * @param bool $escape
	 * @return array|null
	 */
	public static function findOrCreate($col, $operator, $val = null, $escape = true) {
		$result = null;

		try {
			$result = self::findOrFail($col, $operator, $val, $escape);
		} catch (StatementException $e) {
			$result = self::create();
		}

		return $result;
	}

	/**
	 * Retourne toutes les instances possibles de la table.
	 * @return array
	 */
	public static function findAll() {
		$name = get_called_class();
		$obj = new $name();

		$query = new Statement($obj->table, $obj->class_name, $obj->short_class_name);
		return $query->select()->get();
	}

	/**
	 * Créé une nouvelle instance. Pas de sauvegarde dans la table.
	 * Il faut faire un save pour insérer une nouvelle ligne.
	 * @return mixed
	 */
	public static function create() {
		$result = null;
		$name = get_called_class();
		$obj = new $name();
		$obj->mode = BaseModel::MODE_NEW;

		foreach($obj->desc as $k => $v) {
			switch($v) {
				case 0:
				case \PDO::PARAM_INT:
					$obj->$k = 0;
					break;
				case \PDO::PARAM_BOOL:
					$obj->$k = false;
					break;
				case \PDO::PARAM_STR:
					$obj->$k = '';
					break;
				default:
					break;
			}
		}
		return $obj;

	}

}
