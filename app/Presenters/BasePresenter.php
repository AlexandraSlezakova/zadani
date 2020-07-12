<?php


namespace App\Presenters;

use App\Model\BaseModel;
use Nette;

abstract class BasePresenter extends Nette\Application\UI\Presenter
{
    /**
     * @var BaseModel @inject
     */
    public $baseModel;
}