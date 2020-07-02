<?php


namespace App\Model;

use Nette\Database\Context;
use Nette\Database\Table\ActiveRow;
use Nette\Database\Table\Selection;

class BaseModel
{
    /**
     * @var Context
     */
    public $db;

    /**
     * @var string Table name
     */
    public $table;

    public function __construct(Context $db)
    {
        $this->db = $db;
    }

    public function getItemById($id): ActiveRow
    {
        return $this->db->table($this->table)->where("id", $id)->fetch();
    }

    public function getItems(): Selection
    {
        return $this->db->table($this->table);
    }

    public function getItemsLike($column, $value): Selection
    {
        return $this->db->table($this->table)->where($column.' LIKE ?', '%'.$value.'%');
    }
}