<?php
namespace DreadLabs\Vantomas\Domain\Model;

/***************************************************************
 *  Copyright notice
 *
 *  (c) 2013 Thomas Juhnke (tommy@van-tomas.de)
 *  All rights reserved
 *
 *  This script is part of the TYPO3 project. The TYPO3 project is
 *  free software; you can redistribute it and/or modify
 *  it under the terms of the GNU General Public License as published by
 *  the Free Software Foundation; either version 2 of the License, or
 *  (at your option) any later version.
 *
 *  The GNU General Public License can be found at
 *  http://www.gnu.org/copyleft/gpl.html.
 *  A copy is found in the textfile GPL.txt and important notices to the license
 *  from the author is found in LICENSE.txt distributed with these scripts.
 *
 *
 *  This script is distributed in the hope that it will be useful,
 *  but WITHOUT ANY WARRANTY; without even the implied warranty of
 *  MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 *  GNU General Public License for more details.
 *
 *  This copyright notice MUST APPEAR in all copies of the script!
 ***************************************************************/

use TYPO3\CMS\Extbase\DomainObject\AbstractValueObject;

/**
 * GenericCounter gives access to cs_counterplus_info records
 *
 * Implemented as a ValueObject because this table is missing uid/primary key
 * column - therefore it's a ValueObject.
 *
 * @author Thomas Juhnke <tommy@van-tomas.de>
 */
class GenericCounter extends AbstractValueObject {

	/**
	 * @var int
	 * @validate NotEmpty
	 */
	protected $counterId;

	/**
	 * @var int
	 * @validate NotEmpty
	 */
	protected $numberOfVisits;

	/**
	 * @var int
	 * @validate NotEmpty
	 */
	protected $timestamp;

	/**
	 *
	 * @param integer $counterId
	 * @return void
	 * @api
	 */
	public function setCounterId($counterId) {
		$this->counterId = $counterId;
	}

	/**
	 *
	 * @return integer
	 * @api
	 */
	public function getCounterId() {
		return $this->counterId;
	}

	/**
	 *
	 * @param integer $numberOfVisits
	 * @return void
	 * @api
	 */
	public function setNumberOfVisits($numberOfVisits) {
		$this->numberOfVisits = $numberOfVisits;
	}

	/**
	 *
	 * @return integer
	 * @api
	 */
	public function getNumberOfVisits() {
		return $this->numberOfVisits;
	}

	/**
	 *
	 * @param integer $timestamp
	 * @return void
	 * @api
	 */
	public function setTimestamp($timestamp) {
		$this->timestamp = $timestamp;
	}

	/**
	 *
	 * @return integer
	 * @api
	 */
	public function getTimestamp() {
		return $this->timestamp;
	}
}
?>