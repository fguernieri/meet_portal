<?php

declare(strict_types=1);

namespace OCA\MeetPortal\Service;

use OCP\DB\Exception;
use OCP\DB\QueryBuilder\IQueryBuilder;
use OCP\Files\File;
use OCP\Files\Folder;
use OCP\Files\IRootFolder;
use OCP\Files\NotFoundException;
use OCP\IDBConnection;
use OCP\IURLGenerator;
use Psr\Log\LoggerInterface;
use Throwable;

class PortalService {
	private const MOCK_CLIENT = [
		'id' => 0,
		'name' => 'Empresa Demo',
		'phase' => 'Mapear',
		'progress' => 20,
	];

	private const MOCK_TASKS = [
		[
			'id' => 1,
			'title' => 'Enviar documentos financeiros',
			'description' => 'Subir demonstrativos do ultimo trimestre no portal.',
			'due_date' => '2026-02-15',
			'status' => 'pending',
		],
		[
			'id' => 2,
			'title' => 'Agendar entrevista',
			'description' => 'Confirmar horario da entrevista de diagnostico.',
			'due_date' => '2026-02-18',
			'status' => 'pending',
		],
	];

	public function __construct(
		private IDBConnection $db,
		private IRootFolder $rootFolder,
		private IURLGenerator $urlGenerator,
		private LoggerInterface $logger
	) {
	}

	public function getDashboardData(string $userId): array {
		$client = $this->getClient($userId);
		$tasks = $this->getPendingTasks((int)$client['id']);
		$files = $this->getPortalFiles($userId, $client['name']);

		return [
			'client' => [
				'name' => $client['name'],
				'phase' => $client['phase'],
				'progress' => (int)$client['progress'],
			],
			'tasks' => array_map(static fn (array $task): array => [
				'id' => (int)$task['id'],
				'title' => $task['title'],
				'due_date' => $task['due_date'],
				'status' => $task['status'],
			], $tasks),
			'files' => $files,
		];
	}

	public function completeTask(string $userId, int $taskId): bool {
		$client = $this->getClient($userId);
		if ((int)$client['id'] === 0) {
			foreach (self::MOCK_TASKS as $mockTask) {
				if ((int)$mockTask['id'] === $taskId) {
					return true;
				}
			}

			return false;
		}

		try {
			$queryBuilder = $this->db->getQueryBuilder();
			$queryBuilder
				->update('meet_portal_tasks')
				->set('status', $queryBuilder->createNamedParameter('done'))
				->where($queryBuilder->expr()->eq('id', $queryBuilder->createNamedParameter($taskId, IQueryBuilder::PARAM_INT)))
				->andWhere($queryBuilder->expr()->eq('client_id', $queryBuilder->createNamedParameter((int)$client['id'], IQueryBuilder::PARAM_INT)));
			$rows = $queryBuilder->executeStatement();
			return $rows > 0;
		} catch (Throwable $e) {
			$this->logger->warning('MEET Portal task completion fallback used: ' . $e->getMessage(), ['app' => 'meet_portal']);
			return false;
		}
	}

	private function getClient(string $userId): array {
		try {
			$queryBuilder = $this->db->getQueryBuilder();
			$queryBuilder
				->select('id', 'client_name', 'current_phase', 'progress_percentage')
				->from('meet_portal_clients')
				->where($queryBuilder->expr()->eq('user_id', $queryBuilder->createNamedParameter($userId)))
				->setMaxResults(1);

			$result = $queryBuilder->executeQuery();
			$row = $result->fetchAssociative();
			$result->closeCursor();

			if ($row !== false) {
				return [
					'id' => (int)$row['id'],
					'name' => (string)$row['client_name'],
					'phase' => (string)$row['current_phase'],
					'progress' => (int)$row['progress_percentage'],
				];
			}
		} catch (Throwable $e) {
			$this->logger->warning('MEET Portal client fallback used: ' . $e->getMessage(), ['app' => 'meet_portal']);
		}

		return self::MOCK_CLIENT;
	}

	private function getPendingTasks(int $clientId): array {
		if ($clientId === 0) {
			return self::MOCK_TASKS;
		}

		try {
			$queryBuilder = $this->db->getQueryBuilder();
			$queryBuilder
				->select('id', 'title', 'description', 'due_date', 'status')
				->from('meet_portal_tasks')
				->where($queryBuilder->expr()->eq('client_id', $queryBuilder->createNamedParameter($clientId, IQueryBuilder::PARAM_INT)))
				->andWhere($queryBuilder->expr()->eq('status', $queryBuilder->createNamedParameter('pending')))
				->orderBy('due_date', 'ASC');

			$result = $queryBuilder->executeQuery();
			$rows = $result->fetchAllAssociative();
			$result->closeCursor();
			return $rows;
		} catch (Throwable $e) {
			$this->logger->warning('MEET Portal tasks fallback used: ' . $e->getMessage(), ['app' => 'meet_portal']);
			return self::MOCK_TASKS;
		}
	}

	private function getPortalFiles(string $userId, string $clientName): array {
		$portalPath = 'Clientes/' . $clientName . '/Portal';

		try {
			$userFolder = $this->rootFolder->getUserFolder($userId);
			$node = $userFolder->get($portalPath);
			if (!$node instanceof Folder) {
				return $this->getMockFiles();
			}

			$files = [];
			foreach ($node->getDirectoryListing() as $child) {
				if (!$child instanceof File) {
					continue;
				}

				$files[] = [
					'name' => $child->getName(),
					'url' => $this->urlGenerator->linkToRoute('files.view.index', [
						'dir' => '/' . $portalPath,
						'scrollto' => $child->getName(),
					]),
				];
			}

			if ($files === []) {
				return $this->getMockFiles();
			}

			return $files;
		} catch (NotFoundException) {
			return $this->getMockFiles();
		} catch (Throwable $e) {
			$this->logger->warning('MEET Portal files fallback used: ' . $e->getMessage(), ['app' => 'meet_portal']);
			return $this->getMockFiles();
		}
	}

	private function getMockFiles(): array {
		return [
			[
				'name' => 'Proposta.pdf',
				'url' => '#',
			],
		];
	}
}
