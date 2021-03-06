<?php

	App::uses('AppHelper', 'View/Helper');

	class GlasenuhrHelper extends AppHelper {

		/**
		 * name strings of the watches
		 *
		 * @var array
		 */
		protected static $_watchNames;

		/**
		 * Setup with settings
		 *
		 * @param array $settings
		 */
		public function setup(array $settings = []) {
			$settings += [
					'watches' => [
							0 => __('Nachts'),
							1 => __('Morgens'),
							2 => __('Vormittags'),
							3 => __('Nachmittags'),
							4 => __('Freiwache'),
							5 => __('Abends'),
					]
			];

			static::$_watchNames = $settings['watches'];
		}

		/**
		 * Returns formatted time
		 *
		 * @param int $timestamp
		 * @return string
		 */
		public function ftime($timestamp) {
			$glasen = $this->glasen($timestamp);
			$watch = $this->watchName($timestamp);

			return sprintf("%s Gl. %s", $glasen, $watch);
		}

		/**
		 * Calculates bells
		 *
		 * @param int $timestamp
		 * @return int
		 */
		public function glasen($timestamp) {
			$hour = $this->_decimalHourFromTimestamp($timestamp);

			// bases to half hours: 60 min/30 min = 2
			$glasen = ($hour * 2) % 8;
			if ($glasen == 0) {
				$glasen = $glasen + 8;
			}
			return $glasen;
		}

		/**
		 * Calculates watch
		 *
		 * @param int timestamp
		 * @return string
		 */
		public function watchName($timestamp) {
			if (empty(static::$_watchNames)) {
				$this->setup();
			}
			// 30 * 60 s = 1800 s for half an hour shift:
			// first half hour of new watch is 8 bells on the prior watch
			$hour = $this->_decimalHourFromTimestamp($timestamp - 1800);
			return (static::$_watchNames[(int)$hour / 4]);
		}

		/**
		 * Returns the hour with the minutes as decimal value
		 *
		 * Example: `01:20` returns `1.333…`; `04:45` returns `4.75`
		 *
		 * @param int $timestamp
		 * @return float
		 */
		protected function _decimalHourFromTimestamp($timestamp) {
			$getdate = getdate($timestamp);
			return (int)$getdate['hours'] + ((int)$getdate['minutes'] / 60);
		}

	}
