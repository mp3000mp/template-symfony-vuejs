<?php

declare(strict_types=1);

namespace App\Service\Mailer;

use Symfony\Component\Mailer\MailerInterface;
use Symfony\Component\Mime\Email;
use Twig\Environment;

/**
 * Class MailerService.
 */
class MailerService
{
    /** @var MailerInterface */
    private $mailer;
    /** @var Environment */
    private $renderer;

    public function __construct(MailerInterface $mailer, Environment $renderer)
    {
        $this->mailer = $mailer;
        $this->renderer = $renderer;
    }

    /**
     * @param string $template        twig template (without extension, will search for .txt.twig and .html.twig)
     * @param array  $template_params associative array to be passed to twig
     */
    public function sendEmail(string $template, array $template_params, string $subject, array $to, array $cc = [], array $bcc = []): void
    {
        $email = new Email();
        $email->to(...$to)
        ->cc(...$cc)
        ->bcc(...$bcc)
        ->subject($subject)
        ->html($this->renderer->render('email/'.$template.'.html.twig', $template_params))
        ->text($this->renderer->render('email/'.$template.'.txt.twig', $template_params));

        $this->mailer->send($email);
    }
}
