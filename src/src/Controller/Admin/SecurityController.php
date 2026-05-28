<?php

declare(strict_types=1);

namespace App\Controller\Admin;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Security\Http\Authentication\AuthenticationUtils;

/**
 * Контроллер безопасности для админки
 */
final class SecurityController extends AbstractController
{
    /**
     * Страница входа
     */
    public function login(AuthenticationUtils $authenticationUtils): Response
    {
        // Если пользователь уже авторизован, редирект на админку
        if ($this->isGranted('ROLE_ADMIN')) {
            return $this->redirectToRoute('admin');
        }

        // Получаем ошибку последней попытки входа
        $error = $authenticationUtils->getLastAuthenticationError();

        // Получаем последний введенный логин
        $lastUsername = $authenticationUtils->getLastUsername();

        return $this->render('admin/security/login.html.twig', [
            'last_username' => $lastUsername,
            'error' => $error,
        ]);
    }
}
