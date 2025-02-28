<!--
  - @copyright Copyright (c) 2020, Joas Schilling <coding@schilljs.com>
  -
  - @author Joas Schilling <coding@schilljs.com>
  -
  - @license GNU AGPL version 3 or any later version
  -
  - This program is free software: you can redistribute it and/or modify
  - it under the terms of the GNU Affero General Public License as
  - published by the Free Software Foundation, either version 3 of the
  - License, or (at your option) any later version.
  -
  - This program is distributed in the hope that it will be useful,
  - but WITHOUT ANY WARRANTY; without even the implied warranty of
  - MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the
  - GNU Affero General Public License for more details.
  -
  - You should have received a copy of the GNU Affero General Public License
  - along with this program. If not, see <http://www.gnu.org/licenses/>.
-->

<template>
	<a class="deck-card"
		:class="{ 'wide': wide}"
		:href="link"
		:aria-label="deckCardAriaLabel"
		target="_blank">
		<div class="deck-card__lineone">
			<div class="icon-deck" />
			<div class="title">
				{{ name }}
			</div>
		</div>
		<div class="deck-card__linetwo">
			<div>
				{{ deckLocation }}
			</div>
		</div>
	</a>
</template>

<script>
import Tooltip from '@nextcloud/vue/dist/Directives/Tooltip'

export default {
	name: 'DeckCard',

	directives: {
		tooltip: Tooltip,
	},

	props: {
		type: {
			type: String,
			required: true,
		},
		id: {
			type: String,
			required: true,
		},
		name: {
			type: String,
			required: true,
		},
		boardname: {
			type: String,
			required: true,
		},
		stackname: {
			type: String,
			required: true,
		},
		link: {
			type: String,
			required: true,
		},

		wide: {
			type: Boolean,
			default: false,
		},
	},

	computed: {
		deckLocation() {
			return t('spreed', '{stack} in {board}', {
				stack: this.stackname,
				board: this.boardname,
			})
		},
		deckCardAriaLabel() {
			return t('spreed', 'Deck Card')
		},
	},
}
</script>

<style lang="scss" scoped>
.deck-card {
	display: flex;
	transition: box-shadow 0.1s ease-in-out;
	border: 1px solid var(--color-border);
	box-shadow: 0 0 2px 0 var(--color-box-shadow);
	border-radius: var(--border-radius-large);
	font-size: 100%;
	background-color: var(--color-main-background);
	margin: 4px 0;
	max-width: 300px;
	padding: 8px 16px;
	flex-direction: column;
	white-space: nowrap;
	&:hover,
	&:focus{
		box-shadow: 0 0 5px 0 var(--color-box-shadow);
	}
	&__lineone {
		height: 30px;
		display: flex;
		justify-content: flex-start;
		align-items: center;
		overflow: hidden;
		white-space: nowrap;
		text-overflow: ellipsis;

		.title {
			margin-left: 8px;
		}
	}
	&__linetwo {
		height: 30px;
		color: var(--color-text-lighter);
		overflow: hidden;
		white-space: nowrap;
		text-overflow: ellipsis;
	}
}

.icon-deck {
	opacity: .8;
}

.wide {
	max-width: 400px;
	margin: 4px auto;
}

</style>
