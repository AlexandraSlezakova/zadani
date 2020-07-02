<?php

declare(strict_types=1);

namespace App\Presenters;

use App\Model\ChannelGroupsModel;
use App\Model\ChannelsModel;
use Nette;
use Nette\Application\UI\Form;


final class HomepagePresenter extends Nette\Application\UI\Presenter
{
    /**
     * @var ChannelsModel @inject
     */
    public $channelsModel;

    /**
     * @var ChannelGroupsModel @inject
     */
    public $channelGroupModel;

    public function actionDefault()
    {
        $groups = $this->channelGroupModel->getItems()->order("order");
        $channelGroups = [];
        foreach ($groups as $group) {
            foreach ($group->related('Channels.channelGroup') as $channel) {
                $channelGroups[$group->name][] = $channel;
            }
        }
        $this->template->channelGroups = $channelGroups;
    }

    protected function createComponentFilterForm(): Form
    {
        $form = new Form;

        $form->addText('channelGroup', 'Skupina');
        $form->addText('name', 'Názov');
        $form->addText('description', 'Popis');

        return $form;
    }

    public function handleInputChange($inputId, $value)
    {
        $channelGroups = [];
        if ($value == "") {
            $this->redirect("Homepage:default");
        }

        if ($inputId == "channelGroupId") {
            $groupNames = $this->channelGroupModel->getItemsLike("id", $value)
                ->order("order")->fetchAll();

            foreach ($groupNames as $group) {
                foreach ($group->related('Channels.channelGroup') as $channel) {
                    $channelGroups[$group->name][] = $channel;
                }
            }
        }
        else {
            $column = $inputId == "nameId" ? "name" : "description";
            $channels = $this->channelsModel->getItemsLike($column, $value)->fetchAll();
            $name = NULL;

            foreach ($channels as $channel) {
                $item = [];

                if (!$channel || ($channel && !$channel->channelGroup))
                    continue;

                if (!$name || $name != $channel->channelGroup)
                    $name = $this->channelGroupModel->getItemById($channel->channelGroup);

                /* add <br> element */
                $currentName = $inputId == "nameId" ? $channel->name : $channel->description;
                /* find the position of the first occurrence of a case-insensitive */
                $pos = stripos($currentName, $value);
                $length = strlen($currentName);
                $valueLength = strlen($value);
                $newName = "";

                for ($i = 0; $i < $length; $i++) {
                    $letter = $currentName[$i];
                    /* compare each letter from value and db */
                    if ($i == $pos) {
                        $newName .= "<b>";
                        for ($j = $i; $j < $valueLength + $i; $j++) {
                            $newName .= $currentName[$j];
                        }
                        $i = $j - 1;
                        $newName .= "</b>";
                    }
                    else {
                        $newName .= $letter;
                    }
                }

                if ($inputId == "nameId") {
                    $item["name"] = Nette\Utils\Html::el()->setHtml($newName);
                    $item["description"] = $channel->description;
                }
                else {
                    $item["name"] = $channel->name;
                    $item["description"] = Nette\Utils\Html::el()->setHtml($newName);
                }

                $channelGroups[$name->name][] = $item;
            }
        }

        $this->template->channelGroups = $channelGroups;
        $this->redrawControl('tableSnippet');
    }
}
