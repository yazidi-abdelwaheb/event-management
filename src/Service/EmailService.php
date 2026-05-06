<?php

namespace App\Service;

use App\Entity\Contact;
use App\Entity\Event;
use App\Entity\EventSubscribe;
use App\Entity\Newsletter;
use App\Entity\User;
use Symfony\Bridge\Twig\Mime\TemplatedEmail;
use Symfony\Component\DependencyInjection\ParameterBag\ParameterBagInterface;
use Symfony\Component\Mailer\MailerInterface;
use Symfony\Component\Mime\Address;

final class EmailService
{
    public function __construct(
        private MailerInterface $mailer,
        private ParameterBagInterface $params
    ) {
    }

    private function getFromAddress(): Address
    {
        return new Address(
            (string) $this->params->get('mailer_from_email'),
            (string) $this->params->get('mailer_from_name')
        );
    }

    private function send(TemplatedEmail $email): void
    {
        $this->mailer->send($email);
    }

    public function sendContactConfirmation(Contact $contact): void
    {
        $email = (new TemplatedEmail())
            ->from($this->getFromAddress())
            ->to($contact->getEmail())
            ->subject('We received your message')
            ->htmlTemplate('emails/contact_user.html.twig')
            ->context([
                'contact' => $contact,
            ]);

        $this->send($email);
    }

    public function sendNewsletterConfirmation(Newsletter $newsletter): void
    {
        $email = (new TemplatedEmail())
            ->from($this->getFromAddress())
            ->to($newsletter->getEmail())
            ->subject('Newsletter subscription confirmation')
            ->htmlTemplate('emails/newsletter_confirmation.html.twig')
            ->context([
                'newsletter' => $newsletter,
            ]);

        $this->send($email);
    }

    public function sendPasswordReset(User $user, mixed $resetToken): void
    {
        $email = (new TemplatedEmail())
            ->from($this->getFromAddress())
            ->to($user->getEmail())
            ->subject('Password reset request')
            ->htmlTemplate('emails/reset_password.html.twig')
            ->context([
                'resetToken' => $resetToken,
            ]);

        $this->send($email);
    }

    public function createRegistrationConfirmationEmail(User $user): TemplatedEmail
    {
        return (new TemplatedEmail())
            ->from($this->getFromAddress())
            ->to($user->getEmail())
            ->subject('Please confirm your email address')
            ->htmlTemplate('emails/registration_confirmation.html.twig');
    }

    public function sendEventSubscriptionConfirmation(EventSubscribe $subscription, Event $event, string $qrUrl): void
    {
        $email = (new TemplatedEmail())
            ->from($this->getFromAddress())
            ->to($subscription->getEmail())
            ->subject('Your event registration is confirmed — ' . $event->getTitle())
            ->htmlTemplate('emails/event_subscription_confirmation.html.twig')
            ->context([
                'subscription' => $subscription,
                'event' => $event,
                'qrUrl' => $qrUrl,
            ]);

        $this->send($email);
    }
}
