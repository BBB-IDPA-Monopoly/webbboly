<?php

namespace App\Controller;

use App\Form\GameCodeType;
use App\Twig\AlertComponent;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Attribute\MapQueryParameter;
use Symfony\Component\Routing\Annotation\Route;

final class IndexController extends AbstractController
{
    #[Route('/', name: 'app_index')]
    public function index(
        Request $request,
        #[MapQueryParameter] string|null $message,
        #[MapQueryParameter] string|null $type,
    ): Response
    {
        $form = $this->createForm(GameCodeType::class);

        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $code = $form->get('code')->getData();

            return $this->redirectToRoute('app_lobby_nickname', compact('code'));
        }

        if ($message) {
            $this->addFlash($type ?? AlertComponent::ALERT_TYPE_INFO, $message);
        }

        return $this->render('index/index.html.twig', compact('form'));
    }
}
