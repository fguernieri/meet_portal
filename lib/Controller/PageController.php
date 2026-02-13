<?php

declare(strict_types=1);

namespace OCA\MeetPortal\Controller;

use OCA\MeetPortal\Service\PortalService;
use OCP\AppFramework\Controller;
use OCP\AppFramework\Http;
use OCP\AppFramework\Http\DataResponse;
use OCP\AppFramework\Http\TemplateResponse;
use OCP\IRequest;
use OCP\IUserSession;

class PageController extends Controller {
	public function __construct(
		string $appName,
		IRequest $request,
		private PortalService $portalService,
		private IUserSession $userSession
	) {
		parent::__construct($appName, $request);
	}

	/**
	 * @NoAdminRequired
	 * @NoCSRFRequired
	 */
	public function index(): TemplateResponse {
		return new TemplateResponse($this->appName, 'main');
	}

	/**
	 * @NoAdminRequired
	 */
	public function dashboard(): DataResponse {
		$user = $this->userSession->getUser();
		if ($user === null) {
			return new DataResponse(['message' => 'Unauthorized'], Http::STATUS_UNAUTHORIZED);
		}

		return new DataResponse($this->portalService->getDashboardData($user->getUID()));
	}

	/**
	 * @NoAdminRequired
	 */
	public function completeTask(int $id): DataResponse {
		$user = $this->userSession->getUser();
		if ($user === null) {
			return new DataResponse(['message' => 'Unauthorized'], Http::STATUS_UNAUTHORIZED);
		}

		$completed = $this->portalService->completeTask($user->getUID(), $id);

		if (!$completed) {
			return new DataResponse(['success' => false], Http::STATUS_NOT_FOUND);
		}

		return new DataResponse(['success' => true]);
	}
}
