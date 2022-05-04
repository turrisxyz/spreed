<?php

declare(strict_types=1);
/**
 * @copyright Copyright (c) 2016 Lukas Reschke <lukas@statuscode.ch>
 *
 * @author Lukas Reschke <lukas@statuscode.ch>
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

$requirements = [
	'apiVersion' => 'v1',
	'token' => '^[a-z0-9]{4,30}$',
];

$requirementsWithPollId = [
	'apiVersion' => 'v1',
	'token' => '^[a-z0-9]{4,30}$',
	'pollId' => '^[0-9]+$',
];

return [
	'ocs' => [
		/** @see \OCA\Talk\Controller\PollController::createPoll() */
		['name' => 'Poll#createPoll', 'url' => '/api/{apiVersion}/poll/{token}', 'verb' => 'POST', 'requirements' => $requirements],
	],
];
