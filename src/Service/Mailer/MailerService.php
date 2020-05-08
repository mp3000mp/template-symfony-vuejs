<?php

namespace App\Service\Mailer;

use Swift_Mailer;
use Symfony\Contracts\Translation\TranslatorInterface;
use Twig\Environment;

class MailerService
{

    /** @var Swift_Mailer  */
    private $mailer;
    /** @var Environment  */
    private $renderer;
    /** @var TranslatorInterface  */
    private $translator;
    /** @var string */
    private $env;
    public static $EMAIL_DEV;


    /**
     * Swift constructor.
     *
     * @param Swift_Mailer $mailer
     * @param Environment $renderer
     * @param TranslatorInterface $translator
     * @param string $EMAIL_DEV
     */
    public function __construct(string $APP_ENV, string $EMAIL_DEV, Swift_Mailer $mailer, Environment $renderer, TranslatorInterface $translator)
    {
        $this->mailer = $mailer;
        $this->renderer = $renderer;
        $this->translator = $translator;
        $this->env = $APP_ENV;
        self::$EMAIL_DEV = $EMAIL_DEV;
    }

    /**
     * @param string $template        twig template (without extension, will search for .txt.twig and .html.twig)
     * @param array  $template_params associative array to be passed to twig
     * @param string $subject         will be translated
     * @param array  $to
     * @param array  $cc
     * @param array  $bcc
     *
     * @throws \Twig_Error_Loader
     * @throws \Twig_Error_Runtime
     * @throws \Twig_Error_Syntax
     */
    public function sendEmail(string $template, array $template_params, string $subject, array $subject_params, array $to, array $cc = [], array $bcc = []): void
    {
        // envoi mail
        $from = 'no-reply@mp3000.fr';

        // remove recipients if not prod
        if($this->env !== 'prod') {
            $to  = [];
            $cc  = [];
            $bcc = [];
        }

        // add dev bcc
        $bcc[] = self::$EMAIL_DEV;

        // configure mail
        $mail = new \Swift_Message();
        $mail->setFrom($from, 'mp3000 Bot')
             ->setReplyTo($from)
             ->setReturnPath($from)
             ->setSender($from, 'mp3000 Bot')
             ->setSubject($this->translator->trans($subject, $subject_params))
             ->setBody(
                 $this->renderer->render('email/'.$template.'.html.twig', $template_params),
                 'text/html'
             )
             ->addPart(
                 $this->renderer->render(
                     'email/'.$template.'.txt.twig', $template_params),
                 'text/plain'
             )
        ;
        $mail->setTo($to);
        $mail->setCc($cc);
        $mail->setBcc($bcc);
        $mail->getHeaders()->addMailboxHeader('From', [$from]);

        // send
        $this->mailer->send($mail);
    }

    /**
     * @param string $locale
     */
    public function setLocale(string $locale): void
    {
        $this->translator->setLocale($locale);
    }


}
