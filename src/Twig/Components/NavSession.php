<?php

namespace App\Twig\Components;

use Symfony\UX\TwigComponent\Attribute\AsTwigComponent;
use Symfony\UX\TwigComponent\Attribute\ExposeInTemplate;

#[AsTwigComponent]
final class NavSession
{
    private int $_intId;
    #[ExposeInTemplate(name: 'arrPage')]
    private ?array $_arrPage;

    
    public function mount(int $id, ?iterable $allPage = []): void
    {
        $this->_intId = $id;   

        if ($allPage instanceof \Doctrine\Common\Collections\Collection) {
            $this->_arrPage = $allPage->toArray();
        } else {
            $this->_arrPage = (array) $allPage;
        }            
    }

    public function getId(): string
    {
        return $this->_intId;
    }

    public function getArrPage(): ?array
    {
        return $this->_arrPage;
    }

}
