<?php

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
            foreach ($group->related('Channels.channelGroup')->order("order") as $channel) {
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

    /**
     * Handle input change
     * Filtering all wanted values from inputs
     * @param string $groupInput value from input channel group
     * @param string $nameInput value from input name
     * @param string $description value from input description
     * @throws Nette\Application\AbortException
     */
    public function handleInputChange($groupInput, $nameInput, $description)
    {
        if ($groupInput == "" && $nameInput == "" && $description == "") {
            $this->redirect("Homepage:default");
        }

        $channelGroups = [];

        /* channel groups */
        if ($groupInput != "") {
            $groupNames = $this->channelGroupModel->getItemsLike("id", $groupInput)
                ->order("order")->fetchAll();

            foreach ($groupNames as $group) {
                foreach ($group->related('Channels.channelGroup')->order("order") as $channel) {
                    $channelGroups[$group->name][] = $channel;
                }
            }
        }

        /* names */
        if ($nameInput != "") {
            if (empty($channelGroups)) {
                $channels = $this->channelsModel->getItemsLike("name", $nameInput)
                    ->order("order")->fetchAll();
                $name = NULL;

                foreach ($channels as $channel) {
                    $item = [];
                    if (!$channel || ($channel && !$channel->channelGroup))
                        continue;

                    if (!$name || $name != $channel->channelGroup)
                        $name = $this->channelGroupModel->getItemById($channel->channelGroup);

                    /* add <b> element */
                    $newName = $this->channelsModel->getEditedName($channel->name, $nameInput);
                    $item["name"] = Nette\Utils\Html::el()->setHtml($newName);
                    $item["description"] = $channel->description;
                    $channelGroups[$name->name][] = $item;
                }
            }
            else {
                $this->channelsModel->editChannelStorageByName($channelGroups, $nameInput);
            }
        }

        /* description */
        if ($description != "") {
            if (empty($channelGroups)) {
                $channels = $this->channelsModel->getItemsLike("description", $description)
                    ->order("order")->fetchAll();
                $name = NULL;

                foreach ($channels as $channel) {
                    $item = [];
                    if (!$channel || ($channel && !$channel->channelGroup))
                        continue;

                    if (!$name || $name != $channel->channelGroup)
                        $name = $this->channelGroupModel->getItemById($channel->channelGroup);

                    /* add <b> element */
                    $newName = $this->channelsModel->getEditedName($channel->description, $nameInput);
                    $item["name"] = $channel->name;
                    $item["description"] = Nette\Utils\Html::el()->setHtml($newName);
                    $channelGroups[$name->name][] = $item;
                }
            }
            else {
                $this->channelsModel->editChannelStorageByDescription($channelGroups, $description);
            }
        }

        $this->template->channelGroups = $channelGroups;
        $this->redrawControl('tableSnippet');
    }
}
