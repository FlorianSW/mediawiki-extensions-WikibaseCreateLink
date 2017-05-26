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
		$wikibaseClient = WikibaseClient::getDefaultInstance();
		$skin = $baseTemplate->getSkin();
		$title = $skin->getTitle();
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
				]
			);
			$toolbox['wikibasecreatelink'] = array(
				'text' => $baseTemplate->getMsg( 'wikibasecreatelink-createlabel' )->text(),
				'href' => $createItemPage,
				'id' => 't-wikibasecreatelink'
			);
		}
	}
}
