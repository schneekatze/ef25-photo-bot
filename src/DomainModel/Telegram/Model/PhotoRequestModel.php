<?php

namespace App\DomainModel\Telegram\Model;

use App\Entity\PhotoRequest;

class PhotoRequestModel implements ModelInterface
{
    /**
     * @var PhotoRequest
     */
    private $entity;

    /**
     * @var bool
     */
    private $canEdit;

    public function __construct(PhotoRequest $entity, bool $canEdit = false)
    {
        $this->entity = $entity;
        $this->canEdit = $canEdit;
    }

    public function render(): string
    {
        $editBlock = '';
        $you = '';

        if ($this->canEdit) {
            $editBlock = <<<EDIT
Click /delete{$this->entity->getCode()} to remove the request.
EDIT;

            $you = ' (You)';
        }

        return <<<MODEL
*@{$this->entity->getUsername()}* asks for a 📷photo
*Description*
{$this->entity->getDescription()}
{$editBlock}
MODEL;
    }
}
