<?php

namespace App\Helpers;

class RequirementsChecker {

	/**
	 * Check for the server requirements.
	 *
	 * @param array $requirements
	 * @return array
	 */
	public function check(array $requirements) {
		$results = [];

		foreach ($requirements as $requirement) {
			$results['requirements'][$requirement] = true;
			$results['errors'] = null;
			if (!extension_loaded($requirement)) {
				$results['requirements'][$requirement] = false;

				$results['errors'] = true;
			}
		}

		return $results;
	}
}