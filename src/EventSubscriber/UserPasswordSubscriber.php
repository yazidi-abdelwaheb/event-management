<?php
namespace App\EventSubscriber;

use App\Entity\User;
use EasyCorp\Bundle\EasyAdminBundle\Event\BeforeEntityPersistedEvent;
use EasyCorp\Bundle\EasyAdminBundle\Event\BeforeEntityUpdatedEvent;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;

class UserPasswordSubscriber implements EventSubscriberInterface
{
    public function __construct(
        private UserPasswordHasherInterface $passwordHasher
    ) {}

    public static function getSubscribedEvents(): array
    {
        return [
            BeforeEntityPersistedEvent::class => ['hashOnCreate'],
            BeforeEntityUpdatedEvent::class   => ['hashOnUpdate'],
        ];
    }

    public function hashOnCreate(BeforeEntityPersistedEvent $event): void
    {
        $this->hashIfUser($event->getEntityInstance());
    }

    public function hashOnUpdate(BeforeEntityUpdatedEvent $event): void
    {
        $this->hashIfUser($event->getEntityInstance());
    }

    private function hashIfUser(mixed $entity): void
    {
        if (!$entity instanceof User) {
            return;
        }

        $plain = $entity->getPassword();

        
        if (empty($plain) || str_starts_with($plain, '$2y$') || str_starts_with($plain, '$argon')) {
            return;
        }

        $entity->setPassword(
            $this->passwordHasher->hashPassword($entity, $plain)
        );
    }
}