<?php
namespace DreadLabs\Vantomas\Domain\Repository;

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

use TYPO3\CMS\Extbase\Persistence\Repository;
use TYPO3\CMS\Extbase\Persistence\QueryInterface;
use TYPO3\CMS\Extbase\DomainObject\AbstractDomainObject;

/**
 * PageRepository gives low level access to pages records
 *
 * @author Thomas Juhnke <tommy@van-tomas.de>
 */
class PageRepository extends Repository {

	/**
	 *
	 * @var \DreadLabs\Vantomas\Domain\Repository\GenericCounterRepository
	 */
	protected $genericCounterRepository = NULL;

	/**
	 *
	 * @param \DreadLabs\Vantomas\Domain\Repository\GenericCounterRepository $genericCounterRepository
	 * @return void
	 */
	public function injectGenericCounterRepository(\DreadLabs\Vantomas\Domain\Repository\GenericCounterRepository $genericCounterRepository) {
		$this->genericCounterRepository = $genericCounterRepository;
	}

	/**
	 *
	 * @param integer $storagePid
	 * @return \TYPO3\CMS\Extbase\Persistence\ObjectStorage<\DreadLabs\Vantomas\Domain\Model\Page>
	 */
	public function findForArchiveList($storagePid) {
		$query = $this->createQuery();

		// circumvents 'AND pid IN ()' in query string
		$query->getQuerySettings()->setRespectStoragePage(FALSE);

		$query->matching(
			$query->logicalAnd(
				$query->equals('pid', $storagePid),
				$query->equals('hideInNavigation', 0)
			)
		);

		$query->setOrderings(array(
			'lastUpdated' => QueryInterface::ORDER_DESCENDING
		));

		$pages = $query->execute();

		return $pages;
	}

	/**
	 *
	 * @param integer $storagePid
	 * @param integer $month
	 * @param integer $year
	 * @return \TYPO3\CMS\Extbase\Persistence\ObjectStorage<\DreadLabs\Vantomas\Domain\Model\Page>
	 */
	public function findforArchiveSearchByMonthAndYear($storagePid, $month, $year) {
		$firstDayOfIncomingMonthTimestamp = mktime(0, 0, 1, $month, 1, $year);
		$lastDayOfIncomingMonthTimestamp = mktime(23, 59, 59, $month + 1, 0, $year);

		$query = $this->createQuery();

		$query->getQuerySettings()->setRespectStoragePage(FALSE);

		$query->matching(
			$query->logicalAnd(
				$query->equals('pid', $storagePid),
				// TS: select.andWhere was that complex:
				// lastUpdated BETWEEN UNIX_TIMESTAMP('%s-%s-01 00:00:01') AND UNIX_TIMESTAMP(CONCAT(LAST_DAY('%s-%s-01'), ' 23:59:59'))
				$query->logicalAnd(
					$query->greaterThanOrEqual('lastUpdated', $firstDayOfIncomingMonthTimestamp),
					$query->lessThanOrEqual('lastUpdated', $lastDayOfIncomingMonthTimestamp)
				)
			)
		);

		$query->setOrderings(array(
			'lastUpdated' => QueryInterface::ORDER_DESCENDING
		));

		$pages = $query->execute();

		return $pages;
	}

	/**
	 *
	 * @param integer $storagePid
	 * @param integer $limit
	 * @return \TYPO3\CMS\Extbase\Persistence\ObjectStorage<\DreadLabs\Vantomas\Domain\Model\Page>
	 */
	public function findMostPopular($storagePid, $limit = 5) {
		$genericCounters = $this->genericCounterRepository->findHighestVisits($limit);

		$query = $this->createQuery();

		$query->getQuerySettings()->setRespectStoragePage(FALSE);

		// build an OR ($query->logicalOr()) with a contains for every record
		$pidConstraints = array();

		foreach ($genericCounters as $genericCounter) {
			$pidConstraints[] = $query->equals('uid', $genericCounter['cid']);
		}

		$query->matching(
			$query->logicalAnd(
				$query->equals('pid', $storagePid),
				$query->logicalOr($pidConstraints)
			)
		);

		$pages = $query->execute();

		$sortedPages = array();

		foreach ($genericCounters as $genericCounter) {
			$sortedPages[] = $this->sortMostPopular($pages, $genericCounter['cid']);
		}

		return $sortedPages;

		// @see http://forge.typo3.org/issues/10212
// 		$query = $this->createQuery();
// 		$qomFactory = Tx_Extbase_Dispatcher::getPersistenceManager()->getBackend()->getQomFactory();

// 		$selectorA = $qomFactory->selector(null, 'tx_extension_domain_model_a');
// 		$mmSelector = $qomFactory->selector(null, 'tx_extension_b_a_mm');
// 		$selectorB = $query->getSource();

// 		$AMMJoinCondition = $qomFactory->equiJoinCondition(
// 			$mmSelector->getSelectorName(), 'uid_foreign',
// 			$selectorA->getSelectorName(), 'uid'
// 		);

// 		$MMBJoinCondition = $qomFactory->equiJoinCondition(
// 			$selectorB->getSelectorName(), 'uid',
// 			$mmSelector->getSelectorName(), 'uid_local'
// 		);

// 		$query->setSource(
// 			$qomFactory->join(
// 				$selectorA,
// 				$qomFactory->join(
// 					$mmSelector,
// 					$selectorB,
// 					Tx_Extbase_Persistence_QueryInterface::JCR_JOIN_TYPE_INNER,
// 					$MMBJoinCondition
// 				),
// 				Tx_Extbase_Persistence_QueryInterface::JCR_JOIN_TYPE_INNER,
// 				$AMMJoinCondition
// 			)
// 		);

// 		return $query->execute();

		/*
		SELECT
			tx_extension_domain_model_b.*
		FROM
			tx_extension_domain_model_a tx_extension_b_a_mm
			LEFT JOIN
				tx_extension_b_a_mm ON
					tx_extension_b_a_mm.uid_foreign = tx_extension_domain_model_a.uid
						LEFT JOIN
							tx_extension_domain_model_b ON
								tx_extension_domain_model_b.uid = tx_extension_b_a_mm.uid_local
		WHERE
			tx_extension_domain_model_b.deleted=0
			AND tx_extension_domain_model_b.hidden=0
			AND tx_extension_domain_model_b.pid IN (11, 0)
		 */
	}

	// @see http://blog.schreibersebastian.de/2011/07/sortierung-anhand-einer-csv-list/
	private function sortMostPopular($pages, $counterId) {
		foreach ($pages as $page) {
			if ($page instanceof AbstractDomainObject) {
				$recordUid = $page->getUid();
			}
			if ((integer) $recordUid === (integer) $counterId) {
				return $page;
			}
		}
	}

	/**
	 *
	 * @param integer $storagePid
	 * @param integer $offset
	 * @param integer $limit
	 * @return \TYPO3\CMS\Extbase\Persistence\ObjectStorage<\DreadLabs\Vantomas\Domain\Model\Page>
	 */
	public function findLastUpdated($storagePid, $offset = 0, $limit = 1) {
		$query = $this->createQuery();

		$query->getQuerySettings()->setRespectStoragePage(FALSE);

		$query->matching(
			$query->equals('pid', $storagePid)
		);

		$query->setOffset($offset - 1);

		$query->setLimit($limit);

		$query->setOrderings(array(
			'lastUpdated' => QueryInterface::ORDER_DESCENDING
		));

		$pages = $query->execute();

		return $pages;
	}

	/**
	 *
	 * @param integer $uid
	 * @return \DreadLabs\Vantomas\Domain\Model\Page
	 */
	public function findOneByUid($uid) {
		$query = $this->createQuery();

		$query->getQuerySettings()->setRespectStoragePage(FALSE);

		$query->matching(
			$query->equals('uid', $uid)
		);

		return $query->setLimit(1)->execute()->getFirst();
	}
}
?>