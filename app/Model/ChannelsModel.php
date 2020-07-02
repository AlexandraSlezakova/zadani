<?php


namespace App\Model;


class ChannelsModel extends BaseModel
{
    public $table = "Channels";

    /**
     * Add <b> element to searched part of word
     * @param string $name name of channel
     * @param string $inputValue value from input
     * @return string string with <b> element
     */
    public function getEditedName($name, $inputValue)
    {
        /* find the position of the first occurrence of a case-insensitive */
        $pos = stripos($name, $inputValue);
        $length = strlen($name);
        $valueLength = strlen($inputValue);
        $newName = "";

        for ($i = 0; $i < $length; $i++) {
            $letter = $name[$i];
            /* compare each letter from value and db */
            if ($i == $pos) {
                $newName .= "<b>";
                for ($j = $i; $j < $valueLength + $i; $j++) {
                    $newName .= $name[$j];
                }
                $i = $j - 1;
                $newName .= "</b>";
            } else {
                $newName .= $letter;
            }
        }

        return $newName;
    }

    /**
     * Edit storage of channels grouped by group channel
     * filtering all values from input
     * @param array $channelStorage channels
     * @param string $nameInput value from input name
     */
    public function editChannelStorageByName(&$channelStorage, $nameInput)
    {
        foreach ($channelStorage as $groupKey => $group) {
            foreach ($group as $key => $channel) {
                $name = $channel["name"];
                if (!(stristr($name, $nameInput))) {
                    unset($group[$key]);
                } else {
                    $newName = $this->getEditedName($name, $nameInput);
                    $item["name"] = \Nette\Utils\Html::el()->setHtml($newName);
                    $item["description"] = $channel["description"];
                    $group[$key] = $item;
                }
            }
            $channelStorage[$groupKey] = $group;
        }
    }

    /**
     * Edit storage of channels grouped by group channel
     * filtering all values from input
     * @param array $channelStorage channels
     * @param string $description value from input description
     */
    public function editChannelStorageByDescription(&$channelStorage, $description)
    {
        foreach ($channelStorage as $groupKey => $group) {
            foreach ($group as $key => $channel) {
                $name = $channel["description"];
                if (!(stristr($name, $description))) {
                    unset($group[$key]);
                } else {
                    $newName = $this->getEditedName($name, $description);
                    $item["name"] = $channel["name"];
                    $item["description"] = \Nette\Utils\Html::el()->setHtml($newName);
                    $group[$key] = $item;
                }
            }
            $channelStorage[$groupKey] = $group;
        }
    }
}