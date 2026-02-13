<?php

declare(strict_types=1);

namespace OCA\MeetPortal\AppInfo;

use OCP\AppFramework\App;
use OCP\INavigationManager;
use OCP\IURLGenerator;
use Psr\Container\ContainerInterface;

class Application extends App {
	public const APP_ID = 'meet_portal';

	public function __construct(array $urlParams = []) {
		parent::__construct(self::APP_ID, $urlParams);
	}

	public function registerNavigationEntry(): void {
		$container = $this->getContainer();
		/** @var INavigationManager $navigationManager */
		$navigationManager = $container->get(INavigationManager::class);
		/** @var IURLGenerator $urlGenerator */
		$urlGenerator = $container->get(IURLGenerator::class);

		$navigationManager->add(function () use ($urlGenerator): array {
			return [
				'id' => self::APP_ID,
				'order' => 50,
				'href' => $urlGenerator->linkToRoute('meet_portal.page.index'),
				'icon' => $urlGenerator->imagePath('core', 'actions/user.svg'),
				'name' => 'MEET Portal',
			];
		});
	}
}
