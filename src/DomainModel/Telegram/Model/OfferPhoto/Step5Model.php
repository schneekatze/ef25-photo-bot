<?php

namespace App\DomainModel\Telegram\Model\OfferPhoto;

use App\DomainModel\Telegram\Model\ModelInterface;
use App\DomainModel\Telegram\Model\ViewOfferModel;
use App\Entity\PhotoOffer;

class Step5Model implements ModelInterface
{
    /**
     * @var PhotoOffer
     */
    private $offer;

    public function __construct(PhotoOffer $offer)
    {
        $this->offer = $offer;
    }

    public function render(): string
    {
        $text = "Offer a photo. Step *5* of *5*\nAlmost there! Please confirm that everything's fine, click the \"Publish!\" and I'll do so right after.";
        $text .= "\n\n";

        $text .= (new ViewOfferModel($this->offer))->render();

        return $text;
    }
}
