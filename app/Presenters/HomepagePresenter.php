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

    private $channelGroups = [];

    public function renderDefault()
    {
        $groups = $this->channelGroupModel->getItems()->order("order");

        foreach ($groups as $group) {
            foreach ($group->related('Channels.channelGroup') as $channel) {
                $this->channelGroups[$group->name][] = $channel;
            }
        }

        $this->template->channelGroups = $this->channelGroups;
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
        
    }

}
