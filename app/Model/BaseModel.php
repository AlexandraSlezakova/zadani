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

    public function getItemsByChannelGroup(array $channelGroup, string $name, string $description)
    {
        return $this->db->query("SELECT Channels.name, Channels.description,
                Channels.channelGroup, ChannelGroups.name as groupName FROM Channels
                JOIN ChannelGroups ON ChannelGroups.id = Channels.channelGroup
                WHERE ChannelGroups.id IN('".implode("','",$channelGroup)."') 
                AND Channels.name LIKE ? AND Channels.description LIKE ?
                ORDER BY ChannelGroups.order, Channels.order",
            '%'.$name.'%', '%'.$description.'%');
    }

    public function getItemsByName(string $name, string $description)
    {
        return $this->db->query("SELECT Channels.name, Channels.description,
                Channels.channelGroup, ChannelGroups.name as groupName FROM Channels
                JOIN ChannelGroups ON ChannelGroups.id = Channels.channelGroup
                WHERE Channels.name LIKE ? AND Channels.description LIKE ?
                ORDER BY ChannelGroups.order, Channels.order",'%'.$name.'%', '%'.$description.'%');
    }
}