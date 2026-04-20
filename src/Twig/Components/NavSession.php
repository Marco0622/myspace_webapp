<?php

namespace App\Twig\Components;

use Symfony\UX\TwigComponent\Attribute\AsTwigComponent;
use Symfony\UX\TwigComponent\Attribute\ExposeInTemplate;


#[AsTwigComponent]
final class NavSession
{
    private object $_session;
    #[ExposeInTemplate(name: 'arrPage')]
    private ?array $_arrPage;

    /**
     * NavBar des sessions.
     * 
     * @param int $id identifiant de la session.
     * @param ?iterable $allPage tableaux contenant la liste des pages propres à la session.
     */
    public function mount(object $session, ?iterable $allPage = []): void
    {
        $this->_session = $session;   

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
    public function getSession(): object
    {
        return $this->_session;
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
