<?php

namespace App\Model;

use Nette\Database\Context;
use App\Filters\Filter;

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

    /**
     * Add <b> element to searched part of word
     * @param string $name name of channel
     * @param string $inputValue value from input
     * @return string string with <b> element
     */
    public function getEditedName($name, $inputValue)
    {
        $array = str_split($name);
        $noAccentsName = Filter::removeAccents($name);
        $noAccentsInput = Filter::removeAccents($inputValue);
        $valueLength = strlen($noAccentsInput);
        $pos = stripos($noAccentsName, $noAccentsInput);

        $newName = Filter::highlightString($pos, sizeof($array), $name, $valueLength);

        return \Nette\Utils\Html::el()->setHtml($newName);
    }
}