<?php
declare(strict_types = 1);
namespace TYPO3\CMS\Install\Controller;

/*
 * This file is part of the TYPO3 CMS project.
 *
 * It is free software; you can redistribute it and/or modify it under
 * the terms of the GNU General Public License, either version 2
 * of the License, or any later version.
 *
 * For the full copyright and license information, please read the
 * LICENSE.txt file that was distributed with this source code.
 *
 * The TYPO3 project - inspiring people to share!
 */

use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use TYPO3\CMS\Core\Http\RedirectResponse;
use TYPO3\CMS\Install\Service\SessionService;

/**
 * Backend module controller to the install tool. Sets an install tool session
 * marked as "initialized by a valid system administrator backend user" and
 * redirects to the install tool entry point.
 *
 * This is a classic backend module that does not interfere with other code
 * within the install tool, it can be seen as a facade around install tool just
 * to embed the install tool in backend.
 */
class BackendModuleController
{
    /**
     * Initialize session and redirect to "maintenance"
     *
     * @param ServerRequestInterface $request
     * @param ResponseInterface $response
     * @return ResponseInterface
     */
    public function maintenanceAction(ServerRequestInterface $request, ResponseInterface $response): ResponseInterface
    {
        return $this->setAuthorizedAndRedirect('maintenance');
    }

    /**
     * Initialize session and redirect to "settings"
     *
     * @param ServerRequestInterface $request
     * @param ResponseInterface $response
     * @return ResponseInterface
     */
    public function settingsAction(ServerRequestInterface $request, ResponseInterface $response): ResponseInterface
    {
        return $this->setAuthorizedAndRedirect('settings');
    }

    /**
     * Initialize session and redirect to "upgrade"
     *
     * @param ServerRequestInterface $request
     * @param ResponseInterface $response
     * @return ResponseInterface
     */
    public function upgradeAction(ServerRequestInterface $request, ResponseInterface $response): ResponseInterface
    {
        return $this->setAuthorizedAndRedirect('upgrade');
    }

    /**
     * Initialize session and redirect to "environment"
     *
     * @param ServerRequestInterface $request
     * @param ResponseInterface $response
     * @return ResponseInterface
     */
    public function environmentAction(ServerRequestInterface $request, ResponseInterface $response): ResponseInterface
    {
        return $this->setAuthorizedAndRedirect('environment');
    }

    /**
     * Starts / updates the session and redirects to the install tool
     * with given action.
     *
     * @param $controller
     * @return ResponseInterface
     */
    protected function setAuthorizedAndRedirect(string $controller): ResponseInterface
    {
        $sessionService = new SessionService();
        $sessionService->setAuthorizedBackendSession();
        $redirectLocation = 'install.php?install[controller]=' . $controller . '&install[context]=backend';
        return new RedirectResponse($redirectLocation, 303);
    }
}
