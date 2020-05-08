<?php declare(strict_types=1);

namespace App\Controller;

use App\Entity\TermsOfServiceSignature;
use App\Entity\User;
use App\Service\TOS\TOSService;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

/**
 * Class TermsOfServiceController.
 */
class TermsOfServiceController extends AbstractController
{

    /**
     * @route("/tos", name="tos")
     *
     * @param Request $request
     * @param TOSService $TOSService
     *
     * @return Response
     * @throws \Exception
     */
    public function tos(Request $request, TOSService $TOSService): Response
    {
        /** @var User $user */
        $user = $this->getUser();
        $locale = $request->getLocale();

        if($request->getMethod() === 'POST'){
            if($TOSService->hasSignedLastTOS($user)){
                return $this->redirectToRoute('home');
            }else{
                if($request->get('accept') !== 'on'){
                    $request->getSession()->getFlashBag()->add('info', 'tos.please_check');
                    return $this->redirectToRoute('tos');
                }else{
                    $signature = new TermsOfServiceSignature();
                    $signature->setUser($user);
                    $signature->setTermsOfService($TOSService->getLastTOS());
                    $signature->setSignedAt(new \DateTime());

                    $em = $this->getDoctrine()->getManager();
                    $em->persist($signature);
                    $em->flush();

                    return $this->redirectToRoute('home'); // todo target
                }
            }
        }

        return $this->render('app/tos/tos_'.$locale.'.html.twig', [
            'signed' => $TOSService->hasSignedLastTOS($user),
        ]);
    }

}
