<?php
namespace App\Presenters;

use Nette\Application\UI\Form;

final class HomepagePresenter extends BasePresenter
{
    public $channelGroups = [];

    private $formSuccess = 0;

    public function beforeRender()
    {
        if (!$this->formSuccess) {
            /* get all ordered channels */
            $channels = $this->baseModel->db
                ->query("SELECT Channels.name, Channels.description, ChannelGroups.name as groupName FROM Channels 
                          JOIN ChannelGroups ON ChannelGroups.id = Channels.channelGroup 
                          ORDER BY ChannelGroups.order, Channels.order")
                ->fetchAll();

            foreach ($channels as $channel) {
                $this->channelGroups[$channel->groupName][] = $channel;
            }
        }
    }

    public function renderDefault()
    {
        $this->template->channelGroups = $this->channelGroups;
    }

    protected function createComponentFilterForm(): Form
    {
        $form = new Form;

        $form->addText('channelGroup', 'Skupina');
        $form->addText('name', 'Názov');
        $form->addText('description', 'Popis');

        $form->addSubmit("submit", "Submit")
            ->setAttribute("class", "ajax");

        $form->onSuccess[] = [$this, "processForm"];

        return $form;
    }

    public function processForm(Form $form)
    {
        $values = $form->getValues();

        if ($values->channelGroup || $values->name || $values->description) {
            $this->channelGroups = [];
            $this->formSuccess = 1;

            $channels = $this->baseModel->db->query("SELECT Channels.name, Channels.description, ChannelGroups.name as groupName FROM Channels 
                JOIN ChannelGroups ON ChannelGroups.id = Channels.channelGroup 
                WHERE ChannelGroups.name LIKE ? AND Channels.name LIKE ? AND Channels.description LIKE ? 
                ORDER BY ChannelGroups.order, Channels.order", '%'.$values->channelGroup.'%', '%'.$values->name.'%', '%'.$values->description.'%');

            foreach ($channels as $channel) {
                $name = $values->name
                    ? $this->baseModel->getEditedName($channel->name, $values->name)
                    : $channel->name;

                $description = $values->description
                    ? $this->baseModel->getEditedName($channel->description, $values->description)
                    : $channel->description;

                $this->channelGroups[$channel->groupName][] = ["name" => $name, "description" => $description];
            }
        }
        else {
            $this->formSuccess = 0;
        }

        $this->redrawControl('tableSnippet');
    }
}
