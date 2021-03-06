plugin.tx_vantomas {
	persistence {
		classes {
			DreadLabs\Vantomas\Domain\Model\Page {
				mapping {
					tableName = pages
					// sets page.doktype = 'Tx_Vantomas_Domain_Model_Page' in query string
					//recordType = Tx_Vantomas_Domain_Model_Page
					columns {
						uid.mapOnProperty = uid
						pid.mapOnProperty = pid
						nav_hide.mapOnProperty = hideInNavigation
						title.mapOnProperty = title
						subtitle.mapOnProperty = subtitle
						lastUpdated.mapOnProperty = lastUpdated
						crdate.mapOnProperty = creationDate
						abstract.mapOnProperty = abstract
						media.mapOnProperty = media
						keywords.mapOnProperty = keywords
					}
				}
			}
		}
	}
	
	settings {
		twitter {
			consumerKey = {$plugin.tx_vantomas.settings.twitter.consumerKey}
			consumerSecret = {$plugin.tx_vantomas.settings.twitter.consumerSecret}

			bearerTokenUrl = {$plugin.tx_vantomas.settings.twitter.bearerTokenUrl}

			userAgent = {$plugin.tx_vantomas.settings.twitter.userAgent}
			bearerCacheLifetime = {$plugin.tx_vantomas.settings.twitter.bearerCacheLifetime}
		}
	}

	view {
		partialRootPath = EXT:vantomas/Resources/Private/Partials/
		templateRootPath = EXT:vantomas/Resources/Private/Templates/
		layoutRootPath = EXT:vantomas/Resources/Private/Layouts/
	}
}