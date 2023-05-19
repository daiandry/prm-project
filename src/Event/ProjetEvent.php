<?php
/**
 * Created by PhpStorm.
 * User: nambinina2
 * Date: 08/01/2021
 * Time: 13:48
 */

namespace App\Event;


use App\Entity\PrmProjet;
use Symfony\Contracts\EventDispatcher\Event;

class ProjetEvent extends Event
{
    public const NAME = 'app.projet';
    private $projet;
    public function __construct(PrmProjet $projet)
    {
        $this->projet = $projet;
    }
    public function getProjet()
    {
        return $this->projet;
    }
}