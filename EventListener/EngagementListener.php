<?php
/**
 * Created by PhpStorm.
 * User: nambinina2
 * Date: 11/11/2020
 * Time: 14:07
 */

namespace App\EventListener;


use App\Entity\PrmEngagement;
use Doctrine\Persistence\Event\LifecycleEventArgs;

/**
 * Class EngagementListener
 * @package App\EventListener
 */
class EngagementListener
{
    private $cacheDriver;

    public function __construct($cacheDriver)
    {
        $this->cacheDriver = $cacheDriver;
    }

    public function postPersist(PrmEngagement $engagement, LifecycleEventArgs $args)
    {
        $this->cacheDriver->expire('[list_engagement][1]', 0);
    }

    public function postUpdate(PrmEngagement $engagement, LifecycleEventArgs $args)
    {
        $this->cacheDriver->expire('[list_engagement][1]', 0);
    }
}