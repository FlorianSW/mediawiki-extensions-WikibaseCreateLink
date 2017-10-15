<?php

namespace WikibaseCreateLink;

use Wikibase\Client\WikibaseClient;
use BaseTemplate;
use Exception;

class Hooks {
	public static function setup() {
		if ( !class_exists( 'Wikibase\Client\WikibaseClient' ) ) {
			throw new Exception( 'The Wikibase Client extension needs to be ' .
				'installed to work with this extension.' );
		}
	}

	/**
	 * Add "Create Wikibase item" link in toolbox
	 *
	 * @param BaseTemplate $baseTemplate
	 * @param array $toolbox
	 */
	public static function onBaseTemplateToolbox( BaseTemplate $baseTemplate, array &$toolbox ) {
		$skin = $baseTemplate->getSkin();
		$title = $skin->getTitle();
		// if the page of the Title does not contain content, do not add a link to create a Wikibase item
		if ( !$title->isContentPage() ) {
			return;
		}
		$wikibaseClient = WikibaseClient::getDefaultInstance();
		$idString = $skin->getOutput()->getProperty( 'wikibase_item' );
		if ( !$idString ) {
			$repoLinker = $wikibaseClient->newRepoLinker();
			$createItemPage = $repoLinker->getPageUrl(
				\SpecialPage::getTitleFor( 'NewItem' )->getFullText()
			);
			$createItemPage = $repoLinker->addQueryParams(
				$createItemPage,
				[
					'lang' => $skin->getLanguage()->getCode(),
					'label' => $title->getPrefixedText(),
					'site' => $wikibaseClient->getSettings()->getSetting( 'siteGlobalID' ),
					'page' => $title->getPrefixedText(),
				]
			);
			$toolbox['wikibasecreatelink'] = array(
				'text' => $baseTemplate->getMsg( 'wikibasecreatelink-createlabel' )->text(),
				'href' => $createItemPage,
				'target' => 'blank',
				'id' => 't-wikibasecreatelink'
			);
		}
	}
}
