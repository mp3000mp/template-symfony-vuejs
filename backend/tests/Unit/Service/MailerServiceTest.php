<?php

namespace App\Tests\Unit\Service;

use App\Service\Mailer\MailerService;
use PHPUnit\Framework\TestCase;
use Prophecy\Prophet;
use Symfony\Component\Mailer\Exception\TransportExceptionInterface;
use Symfony\Component\Mailer\MailerInterface;
use Twig\Environment;
use Twig\Error\LoaderError;

class MailerServiceTest extends TestCase
{
    public function testSendEmail(): void
    {
        $mailerMock = $this->createMock(MailerInterface::class);
        $mailerMock->expects(self::once())
            ->method('send');

        $rendererMock = $this->createMock(Environment::class);
        $rendererMock->expects(self::exactly(2))
                ->method('render');

        $mailer = new MailerService($mailerMock, $rendererMock);
        $mailer->sendEmail('template_test', [], 'test subject', ['test@mp3000.fr']);
    }

    public function testSendEmailRendererException(): void
    {
        $mailerMock = $this->createMock(MailerInterface::class);

        $rendererMock = $this->createMock(Environment::class);
        $rendererMock->expects(self::once())
            ->method('render')
            ->will($this->throwException(new LoaderError('Test error')));

        $this->expectException(LoaderError::class);
        $mailer = new MailerService($mailerMock, $rendererMock);
        $mailer->sendEmail('template_test', [], 'test subject', ['test@mp3000.fr']);
    }

    public function testSendEmailMailerException(): void
    {
        $prophet = new Prophet();
        $mailerMock = $this->createMock(MailerInterface::class);
        $mailerMock->expects(self::once())
            ->method('send')
            ->will($this->throwException($prophet->prophesize(TransportExceptionInterface::class)->reveal()));

        $rendererMock = $this->createMock(Environment::class);

        $this->expectException(TransportExceptionInterface::class);
        $mailer = new MailerService($mailerMock, $rendererMock);
        $mailer->sendEmail('template_test', [], 'test subject', ['test@mp3000.fr']);
    }
}
