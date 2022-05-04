<?php

declare(strict_types=1);
/**
 * @copyright Copyright (c) 2016 Lukas Reschke <lukas@statuscode.ch>
 * @copyright Copyright (c) 2016 Joas Schilling <coding@schilljs.com>
 *
 * @author Lukas Reschke <lukas@statuscode.ch>
 * @author Joas Schilling <coding@schilljs.com>
 *
 * @license GNU AGPL version 3 or any later version
 *
 * This program is free software: you can redistribute it and/or modify
 * it under the terms of the GNU Affero General Public License as
 * published by the Free Software Foundation, either version 3 of the
 * License, or (at your option) any later version.
 *
 * This program is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU Affero General Public License for more details.
 *
 * You should have received a copy of the GNU Affero General Public License
 * along with this program.  If not, see <http://www.gnu.org/licenses/>.
 *
 */

namespace OCA\Talk\Controller;

use OCA\Talk\Chat\ChatManager;
use OCA\Talk\Service\PollService;
use OCP\AppFramework\Http;
use OCP\AppFramework\Http\DataResponse;
use OCP\AppFramework\Utility\ITimeFactory;
use OCP\Comments\MessageTooLongException;
use OCP\IRequest;
use Psr\Log\LoggerInterface;

class PollController extends AEnvironmentAwareController {
	protected ChatManager $chatManager;
	protected PollService $pollService;
	protected ITimeFactory $timeFactory;
	protected LoggerInterface $logger;

	public function __construct(string $appName,
								IRequest $request,
								ChatManager $chatManager,
								PollService $pollService,
								ITimeFactory $timeFactory,
								LoggerInterface $logger) {
		parent::__construct($appName, $request);
		$this->pollService = $pollService;
		$this->chatManager = $chatManager;
		$this->timeFactory = $timeFactory;
		$this->logger = $logger;
	}

	/**
	 * @PublicPage
	 * @RequireParticipant
	 * @RequireReadWriteConversation
	 * @RequirePermissions(permissions=chat)
	 * @RequireModeratorOrNoLobby
	 *
	 * @param string $question
	 * @param array $options
	 * @param int $resultMode
	 * @param int $maxVotes
	 * @return DataResponse
	 */
	public function createPoll(string $question, array $options, int $resultMode, int $maxVotes): DataResponse {
		$attendee = $this->participant->getAttendee();
		try {
			$poll = $this->pollService->createPoll(
				$this->room->getId(),
				$attendee->getActorType(),
				$attendee->getActorId(),
				$question,
				$options,
				$resultMode,
				$maxVotes
			);
		} catch (\Exception $e) {
			return new DataResponse([], Http::STATUS_BAD_REQUEST);
		}

		$message = json_encode([
			'message' => 'object_shared',
			'parameters' => [
				'objectType' => 'highlight', // FIXME 'talk-poll',
				'objectId' => $poll->getId(),
				'metaData' => [
					'type' => 'highlight', // FIXME 'talk-poll',
					'id' => $poll->getId(),
					'name' => $question,
				]
			],
		]);

		try {
			$this->chatManager->addSystemMessage($this->room, $attendee->getActorType(), $attendee->getActorId(), $message, $this->timeFactory->getDateTime(), true);
		} catch (\Exception $e) {
			$this->logger->error($e->getMessage(), ['exception' => $e]);
		}

		return new DataResponse($poll->asArray());
	}
}
