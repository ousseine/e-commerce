<?php

namespace App\EventListener;

use App\Entity\Product;
use Doctrine\Persistence\Event\LifecycleEventArgs;
use Doctrine\Bundle\DoctrineBundle\EventSubscriber\EventSubscriberInterface;
use Doctrine\ORM\Events;
use Symfony\Component\String\Slugger\SluggerInterface;

class DatabaseActivitySubscriber implements EventSubscriberInterface
{
    private SluggerInterface $slugger;

    public function __construct(SluggerInterface $slugger)
    {
        $this->slugger = $slugger;
    }

    public function getSubscribedEvents(): array
    {
        return [
            Events::prePersist,
            Events::preUpdate,
            Events::preRemove
        ];
    }

    public function prePersist(LifecycleEventArgs $args): void
    {
        $entity = $args->getObject();

        if ($entity instanceof Product) {
            $entity->setUpdatedAt(new \DateTimeImmutable());
            $entity->setCreatedAt(new \DateTimeImmutable());
            $entity->setSlug($this->slugger->slug($entity->getTitle()));

            if ($entity->isIsPublished()) $entity->setPublishedAt(new \DateTimeImmutable());
        }
    }

    public function preUpdate(LifecycleEventArgs $args): void
    {
        $entity = $args->getObject();

        if ($entity instanceof Product) {
            $entity->setUpdatedAt(new \DateTimeImmutable());
            if ($entity->isIsPublished()) $entity->setPublishedAt(new \DateTimeImmutable());
            $entity->setSlug($this->slugger->slug($entity->getTitle()));
        }
    }

    public function preRemove()
    {
        return;
    }
}