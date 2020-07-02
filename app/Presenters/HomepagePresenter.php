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
                if (!$channel || ($channel && !$channel->channelGroup)) {
                    continue;
                }

                if (!$name || $name != $channel->channelGroup) {
                    $name = $this->channelGroupModel->getItemById($channel->channelGroup);
                }

                $channelGroups[$name->name][] = $channel;
            }
        }

        $this->template->channelGroups = $channelGroups;
        $this->redrawControl('tableSnippet');
    }
}
