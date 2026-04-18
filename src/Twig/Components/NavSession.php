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

    /**
     * NavBar des sessions.
     * 
     * @param int $id identifiant de la session.
     * @param ?iterable $allPage tableaux contenant la liste des pages propres à la session.
     */
    public function mount(int $id, ?iterable $allPage = []): void
    {
        $this->_intId = $id;   

        if ($allPage instanceof \Doctrine\Common\Collections\Collection) {
            $this->_arrPage = $allPage->toArray();
        } else {
            $this->_arrPage = (array) $allPage;
        }            
    }

    /**
     * Retourne l'identifiant de la session.
     * 
     * @return int
     */
    public function getId(): int
    {
        return $this->_intId;
    }

    /**
     * Retourne le tableau des page de la session.
     * 
     * @return array | null
     */
    public function getArrPage(): ?array
    {
        return $this->_arrPage;
    }

}
