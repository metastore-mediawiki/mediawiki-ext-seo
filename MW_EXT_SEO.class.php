<?php

namespace MediaWiki\Extension\MW_EXT_SEO;

use ContentHandler;
use DateTime;
use Html;
use OutputPage;
use ParserOutput;
use RequestContext;
use Revision;
use User;

/**
 * Class MW_EXT_SEO
 * ------------------------------------------------------------------------------------------------------------------ */
class MW_EXT_SEO {

	/**
	 * Clear DATA (escape html).
	 *
	 * @param $string
	 *
	 * @return string
	 * -------------------------------------------------------------------------------------------------------------- */

	private static function clearData( $string ) {
		$outString = htmlspecialchars( trim( $string ), ENT_QUOTES );

		return $outString;
	}

	/**
	 * Get configuration parameters.
	 *
	 * @param $config
	 *
	 * @return mixed
	 * @throws \ConfigException
	 * -------------------------------------------------------------------------------------------------------------- */

	private static function getConfig( $config ) {
		$context   = RequestContext::getMain()->getConfig();
		$getConfig = $context->get( $config );

		return $getConfig;
	}

	/**
	 * Get "getTitle".
	 *
	 * @return null|\Title
	 * -------------------------------------------------------------------------------------------------------------- */

	private static function getTitle() {
		$context  = RequestContext::getMain();
		$getTitle = $context->getTitle();

		return $getTitle;
	}

	/**
	 * Get "getWikiPage".
	 *
	 * @return \WikiPage
	 * @throws \MWException
	 * -------------------------------------------------------------------------------------------------------------- */

	private static function getWikiPage() {
		$context     = RequestContext::getMain();
		$getWikiPage = $context->getWikiPage();

		return $getWikiPage;
	}

	/**
	 * Render function.
	 *
	 * @param OutputPage $out
	 * @param ParserOutput $parserOutput
	 *
	 * @return bool
	 * @throws \ConfigException
	 * @throws \MWException
	 * -------------------------------------------------------------------------------------------------------------- */

	public static function onRenderSEO( OutputPage $out, ParserOutput $parserOutput ) {
		// Get custom info.
		$getSiteURL   = self::getConfig( 'Server' );
		$getSiteName  = self::clearData( self::getConfig( 'Sitename' ) );
		$getSiteEmail = self::getConfig( 'EmergencyContact' );
		$getSiteLogo  = self::getConfig( 'Logo' );
		$getFavicon   = self::getConfig( 'Favicon' );

		// Get extension info.
		$getSitePhone        = self::getConfig( 'EXT_SEO_Phone' );
		$getPublisher        = self::getConfig( 'EXT_SEO_Publisher' );
		$getPublisherLogo    = self::getConfig( 'EXT_SEO_PublisherLogo' );
		$getManifest         = self::getConfig( 'EXT_SEO_Manifest' );
		$getURL_Vk           = self::getConfig( 'EXT_SEO_URL_Vk' );
		$getURL_Facebook     = self::getConfig( 'EXT_SEO_URL_Facebook' );
		$getURL_Twitter      = self::getConfig( 'EXT_SEO_URL_Twitter' );
		$getURL_Discord      = self::getConfig( 'EXT_SEO_URL_Discord' );
		$getThemeColor       = self::getConfig( 'EXT_SEO_ThemeColor' );
		$getMSTileColor      = self::getConfig( 'EXT_SEO_MSTileColor' );
		$getTwitter_Site     = self::getConfig( 'EXT_SEO_Twitter_Site' );
		$getTwitter_Creator  = self::getConfig( 'EXT_SEO_Twitter_Creator' );
		$getStreetAddress    = self::getConfig( 'EXT_SEO_StreetAddress' );
		$getAddressLocality  = self::getConfig( 'EXT_SEO_AddressLocality' );
		$getAddressRegion    = self::getConfig( 'EXT_SEO_AddressRegion' );
		$getPostalCode       = self::getConfig( 'EXT_SEO_PostalCode' );
		$getAddressCountry   = self::getConfig( 'EXT_SEO_AddressCountry' );
		$getContactType      = self::getConfig( 'EXT_SEO_ContactType' );
		$getArticlePublisher = self::getConfig( 'EXT_SEO_ArticlePublisher' );

		// Get system info.
		$getDateCreated    = DateTime::createFromFormat( 'YmdHis', self::getTitle()->getEarliestRevTime() );
		$getDateModified   = DateTime::createFromFormat( 'YmdHis', self::getTitle()->getTouched() );
		$getFirstRevision  = self::getTitle()->getFirstRevision();
		$getImage          = key( $out->getFileSearchOptions() );
		$getImageObject    = wfFindFile( $getImage );
		$getRevision       = self::getWikiPage()->getRevision();
		$getHeadline       = self::clearData( self::getTitle()->getText() );
		$getAltHeadline    = $getHeadline;
		$getKeywords       = self::clearData( str_replace( 'Категория:', '', implode( ', ', array_keys( self::getTitle()->getParentCategories() ) ) ) );
		$getWordCount      = self::getTitle()->getLength();
		$getArticleURL     = self::getTitle()->getFullURL();
		$getArticleID      = $getArticleURL;
		$getExtDescription = $parserOutput->getProperty( 'description' ); // Set by "Description2" extension.

		if ( $getExtDescription !== false ) {
			$getDescription = $getExtDescription;
		} else {
			$getDescription = '';
		};

		// Get article text.
		if ( $getRevision ) {
			$getContent     = $getRevision->getContent( Revision::FOR_PUBLIC );
			$getContentText = ContentHandler::getContentText( $getContent );
			$getArticleText = trim( preg_replace( '/\s+/', ' ', strip_tags( $getContentText ) ) );
		} else {
			$getArticleText = '';
		}

		$getArticleBody = self::clearData( $getArticleText );

		// Get article created date.
		$getDateCreated = $getDateCreated ? $getDateCreated->format( 'c' ) : '0';

		// Get article modified date.
		$getDateModified = $getDateModified ? $getDateModified->format( 'c' ) : '0';

		// Get article author.
		if ( $getFirstRevision ) {
			$getUser           = User::newFromId( $getFirstRevision->getUser() );
			$getUserName       = $getFirstRevision->getUserText();
			$getUserGroups     = $getUser->getGroups();
			$getAuthorName     = self::clearData( $getUserName );
			$getAuthorURL      = $getUser->getUserPage()->getFullURL();
			$getAuthorJobTitle = self::clearData( implode( ', ', array_values( $getUserGroups ) ) );
		} else {
			$getAuthorName     = self::getConfig( 'EXT_SEO_AuthorName' );
			$getAuthorJobTitle = '';
			$getAuthorURL      = '';
		}

		// Get article image.
		if ( $getImage && $getImageObject ) {
			$getImageURL    = $getImageObject->getFullURL();
			$getImageWidth  = $getImageObject->getWidth();
			$getImageHeight = $getImageObject->getHeight();
		} else {
			$getImageURL    = $getSiteURL . $getSiteLogo;
			$getImageWidth  = getimagesize( $getImageURL )[0];
			$getImageHeight = getimagesize( $getImageURL )[1];
		}

		// -------------------------------------------------------------------------------------------------------------
		// Init JSON-LD.
		// -------------------------------------------------------------------------------------------------------------

		$json = [];

		// Loading JSON-LD.
		$json['@context']            = 'http://schema.org';
		$json['@type']               = 'Article';
		$json['headline']            = $getHeadline;
		$json['alternativeHeadline'] = $getAltHeadline;
		$json['description']         = $getDescription;
		$json['keywords']            = $getKeywords;
		$json['dateCreated']         = $getDateCreated;
		$json['datePublished']       = $getDateCreated;
		$json['dateModified']        = $getDateModified;
		$json['wordCount']           = $getWordCount;
		$json['url']                 = $getArticleURL;
		// $json['articleBody']      = $getArticleBody;

		$json['mainEntityOfPage'] = [
			'@type' => 'WebPage',
			'@id'   => $getArticleID,
		];

		$json['author'] = [
			'@type' => 'Person',
			'name'  => $getSiteName,
			//'jobTitle' => $getAuthorJobTitle,
			'url'   => $getSiteURL,
		];

		$json['image'] = [
			'@type'  => 'ImageObject',
			'url'    => $getImageURL,
			'height' => $getImageWidth,
			'width'  => $getImageHeight,
		];

		$json['publisher'] = [
			'@type'        => 'Organization',
			'name'         => $getSiteName,
			'url'          => $getSiteURL,
			'logo'         => [
				'@type'  => 'ImageObject',
				'url'    => $getPublisherLogo,
				'height' => 60,
				'width'  => 600,
			],
			'address'      => [
				'@type'           => 'PostalAddress',
				'streetAddress'   => $getStreetAddress,
				'addressLocality' => $getAddressLocality,
				'addressRegion'   => $getAddressRegion,
				'postalCode'      => $getPostalCode,
				'addressCountry'  => $getAddressCountry,
			],
			'contactPoint' => [
				'@type'       => 'ContactPoint',
				'contactType' => $getContactType,
				'telephone'   => $getSitePhone,
				'email'       => $getSiteEmail,
				'url'         => $getSiteURL,
			],
			'sameAs'       => [
				$getURL_Vk,
				$getURL_Facebook,
				$getURL_Twitter,
				$getURL_Discord,
			]
		];

		// Render JSON-LD.
		$json_encode = json_encode( $json, JSON_UNESCAPED_UNICODE );
		$out->addHeadItem( 'mw-ext-seo-json', '<script type="application/ld+json">' . $json_encode . '</script>' );

		// -------------------------------------------------------------------------------------------------------------
		// HTTP-EQUIV.
		// -------------------------------------------------------------------------------------------------------------

		// Render HTTP-EQUIV.
		$out->addHeadItem( 'mw-ext-seo-http', '' . Html::element( 'meta', [
				'http-equiv' => 'X-UA-Compatible',
				'content'    => 'IE=edge',
			] ) );

		// -------------------------------------------------------------------------------------------------------------
		// DNS prefetch.
		// -------------------------------------------------------------------------------------------------------------

		$dns = [
			'//cdn.jsdelivr.net',
			'//cdnjs.cloudflare.com',
			'//fonts.googleapis.com',
			'//use.fontawesome.com',
			'//disqus.com',
			'//github.com',
		];

		// Render dns.
		foreach ( $dns as $key => $value ) {
			if ( $value ) {
				$out->addHeadItem( 'mw-ext-seo-dns-' . $key, '<link rel="dns-prefetch" href="' . $value . '"/>' );
			}
		}

		// -------------------------------------------------------------------------------------------------------------
		// Favicon.
		// -------------------------------------------------------------------------------------------------------------

		$out->addHeadItem( 'mw-ext-seo-favicon', '<link rel="icon" type="image/x-icon" href="' . $getFavicon . '"/>' );

		// -------------------------------------------------------------------------------------------------------------
		// Meta.
		// -------------------------------------------------------------------------------------------------------------

		$meta = [];

		// Loading Meta.
		$meta['viewport']                = 'width=device-width, initial-scale=1, maximum-scale=1';
		$meta['keywords']                = $getKeywords;
		$meta['author']                  = $getSiteName;
		$meta['designer']                = $getSiteName;
		$meta['publisher']               = $getSiteName;
		$meta['distribution']            = 'web';
		$meta['rating']                  = 'general';
		$meta['reply-to']                = $getSiteEmail;
		$meta['copyright']               = $getSiteName;
		$meta['referrer']                = 'strict-origin';
		$meta['theme-color']             = $getThemeColor;
		$meta['msapplication-TileColor'] = $getMSTileColor;

		// Render META.
		foreach ( $meta as $name => $value ) {
			if ( $value ) {
				$out->addHeadItem( 'mw-ext-seo-meta' . $name, '' . Html::element( 'meta', [
						'name'    => $name,
						'content' => $value,
					] ) );
			}
		}

		// -------------------------------------------------------------------------------------------------------------
		// Link.
		// -------------------------------------------------------------------------------------------------------------

		$link = [];

		$link['publisher'] = $getPublisher;
		$link['manifest']  = $getManifest;

		// Render link.
		foreach ( $link as $rel => $value ) {
			if ( $value ) {
				$out->addHeadItem( 'mw-ext-seo-rel' . $rel, '' . Html::element( 'link', [
						'rel'  => $rel,
						'href' => $value,
					] ) );
			}
		}

		// -------------------------------------------------------------------------------------------------------------
		// Open Graph.
		// -------------------------------------------------------------------------------------------------------------

		// OG type.
		$getType = self::getTitle()->isMainPage() ? 'website' : 'article';

		$og = [];

		// Loading Open Graph.
		$og['og:type']        = $getType;
		$og['og:site_name']   = $getSiteName;
		$og['og:title']       = $getHeadline;
		$og['og:description'] = $getDescription;
		$og['og:image']       = $getImageURL;
		//$og['og:image:width']         = $getImageWidth;
		//$og['og:image:height']        = $getImageHeight;
		$og['og:url']                 = $getArticleURL;
		$og['article:published_time'] = $getDateCreated;
		$og['article:modified_time']  = $getDateModified;
		$og['article:author']         = $getSiteName;
		$og['article:publisher']      = $getArticlePublisher;
		$og['article:tag']            = $getKeywords;
		$og['fb:app_id']              = '';

		// Render Open Graph.
		foreach ( $og as $property => $value ) {
			if ( $value ) {
				$out->addHeadItem( 'mw-ext-seo-og' . $property, '' . Html::element( 'meta', [
						'property' => $property,
						'content'  => $value,
					] ) );
			}
		}

		// -------------------------------------------------------------------------------------------------------------
		// Twitter.
		// -------------------------------------------------------------------------------------------------------------

		$twitter = [];

		// Loading Twitter Card.
		$twitter['twitter:card']        = 'summary';
		$twitter['twitter:title']       = $getHeadline;
		$twitter['twitter:description'] = $getDescription;
		$twitter['twitter:image']       = $getImageURL;
		$twitter['twitter:site']        = '@' . $getTwitter_Site;
		$twitter['twitter:creator']     = '@' . $getTwitter_Creator;

		// Render Twitter Card.
		foreach ( $twitter as $name => $value ) {
			if ( $value ) {
				$out->addHeadItem( 'mw-ext-seo-twitter' . $name, '' . Html::element( 'meta', [
						'name'    => $name,
						'content' => $value,
					] ) );
			}
		}

		// -------------------------------------------------------------------------------------------------------------
		// DC.
		// -------------------------------------------------------------------------------------------------------------

		$dc = [];

		$dc['DC.Title']        = $getHeadline;
		$dc['DC.Date.Issued']  = $getDateCreated;
		$dc['DC.Date.Created'] = $getDateCreated;

		// Render DC.
		foreach ( $dc as $name => $value ) {
			if ( $value ) {
				$out->addHeadItem( 'mw-ext-seo-dc' . $name, '' . Html::element( 'meta', [
						'name'    => $name,
						'content' => $value,
					] ) );
			}
		}

		return true;
	}

	/**
	 * Load function.
	 *
	 * @param OutputPage $out
	 * @param ParserOutput $parserOutput
	 *
	 * @return bool
	 * @throws \ConfigException
	 * @throws \MWException
	 * -------------------------------------------------------------------------------------------------------------- */

	public static function onOutputPageParserOutput( OutputPage $out, ParserOutput $parserOutput ) {

		if ( ! self::getTitle() || ! self::getTitle()->isContentPage() || ! self::getWikiPage() ) {
			return null;
		}

		self::onRenderSEO( $out, $parserOutput );

		return true;
	}
}
