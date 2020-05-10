<?php declare(strict_types=1);

namespace App\Twig;

use App\Service\SharedSession\SharedSession;
use Twig\Extension\AbstractExtension;
use Twig\TwigFunction;

/**
 * Class SharedSessionExtension
 *
 * @package App\Twig
 */
class SharedSessionExtension extends AbstractExtension
{
    /** @var SharedSession  */
    private $sharedSession;

    /**
     * SharedSessionExtension constructor.
     *
     * @param SharedSession $sharedSession
     */
    public function __construct(SharedSession $sharedSession)
    {
        $this->sharedSession = $sharedSession;
    }

    /**
     * @return TwigFunction[]
     */
    public function getFunctions(): array
    {
        return [
            new TwigFunction('getSharedSession', [$this, 'getSharedSession']),
        ];
    }

    /**
     * @return mixed
     */
    public function getSharedSession()
    {
        return $this->sharedSession->getToken();
    }
}
