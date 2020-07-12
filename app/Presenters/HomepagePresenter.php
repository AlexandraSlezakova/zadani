<?php
namespace App\Presenters;

use App\Filters\Filter;
use Nette;
use Nette\Application\UI\Form;

final class HomepagePresenter extends BasePresenter
{
    public $channelGroups = [];

    private $formInput = 0;

    public function beforeRender()
    {
        if (!$this->formInput) {
            /* get all ordered channels */
            $channels = $this->baseModel->db
                ->query("SELECT Channels.name, Channels.description, Channels.channelGroup, 
                              ChannelGroups.name as groupName FROM Channels 
                              JOIN ChannelGroups ON ChannelGroups.id = Channels.channelGroup 
                              ORDER BY ChannelGroups.order, Channels.order")
                ->fetchAll();

            foreach ($channels as $channel) {
                $this->channelGroups[$channel->channelGroup][] = $channel;
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

        $groups = [];
        foreach ($this->channelGroups as $key => $channelGroup) {
            $groups[$key] = $channelGroup[0]["groupName"];
        }

        $form->addMultiSelect('channelGroup', 'Skupina', $groups)
            ->setHtmlAttribute("autocomplete", "off");
        $form->addText('name', 'Názov');
        $form->addText('description', 'Popis');

        $form->addSubmit("submit", "Submit")
            ->setAttribute("class", "ajax");

        $form->onSuccess[] = [$this, "processForm"];
        $form->onError[] = [$this, "errorForm"];

        return $form;
    }

    public function errorForm()
    {
        bdump("error");
    }

    public function processForm(Form $form)
    {
        $values = $form->getHttpData();

        if (!empty($values)) {
            $this->channelGroups = [];
            $this->formInput = 1;

            $channels = !empty($values["channelGroup"])
                ? $this->baseModel->getItemsByChannelGroup($values["channelGroup"], $values["name"], $values["description"])
                : $this->baseModel->getItemsByName($values["name"], $values["description"]);

            foreach ($channels as $channel) {
                $name = $values["name"]
                    ? Filter::getEditedName($channel->name, $values["name"])
                    : $channel->name;

                $description = $values["description"]
                    ? Filter::getEditedName($channel->description, $values["description"])
                    : $channel->description;

                $this->channelGroups[$channel->channelGroup][] = ["name" => $name, "description" => $description,
                    "groupName" => $channel->groupName];
            }
        }
        else {
            $this->formInput = 0;
        }

        $this->redrawControl('tableSnippet');
    }

    public function handleReset()
    {
        $this->formInput = 0;
    }
}
