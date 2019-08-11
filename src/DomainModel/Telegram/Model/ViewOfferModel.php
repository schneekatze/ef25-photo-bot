<?php

namespace App\DomainModel\Telegram\Model;

use App\Entity\PhotoOffer;

class ViewOfferModel implements ModelInterface
{
    /**
     * @var PhotoOffer
     */
    private $entity;

    /**
     * @var bool
     */
    private $canEdit;

    public function __construct(PhotoOffer $entity, bool $canEdit = false)
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
Click /delete{$this->entity->getCode()} to remove the offer.
EDIT;

            $you = ' (You)';
        }

        $timeFormatted = date('d.m.Y H:i', $this->entity->getTime());

        return <<<MODEL
*Photographer$you*
@{$this->entity->getUsername()}
*Description*
{$this->entity->getDescription()}
*Location*
{$this->entity->getLocation()}
*Time*
{$timeFormatted}
*Pricing*
{$this->entity->getPrice()}
{$editBlock}
MODEL;
    }
}
