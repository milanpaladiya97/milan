<?php
namespace WpAssetCleanUp\OptimiseAssets;

/**
 * Class FontsGoogle
 * @package WpAssetCleanUp\OptimiseAssets
 */
class FontsGoogleRemove
{
	/**
	 * @var array
	 */
	public static $stringsToCheck = array(
		'//fonts.googleapis.com',
		'//fonts.gstatic.com'
	);

	/**
	 * Called late from OptimizeCss after all other optimizations are done (e.g. minify, combine)
	 *
	 * @param $htmlSource
	 *
	 * @return mixed
	 */
	public static function cleanHtmlSource($htmlSource)
	{
		$htmlSource = self::cleanLinkTags($htmlSource);
		$htmlSource = self::cleanFromInlineStyleTags($htmlSource);

		return $htmlSource;
	}

	/**
	 * @param $htmlSource
	 *
	 * @return mixed
	 */
	public static function cleanLinkTags($htmlSource)
	{
		// Cleaner HTML Source
		$altHtmlSource = preg_replace('@<(script|style)[^>]*?>.*?</\\1>@si', '', $htmlSource);
		$altHtmlSource = preg_replace('/<!--(.|\s)*?-->/', '', $altHtmlSource);

		// Do not continue if there is no single reference to the string we look for in the clean HTML source
		if (stripos($altHtmlSource, FontsGoogle::$containsStr) === false) {
			return $htmlSource;
		}

		// Get all valid LINKs that have the self::$stringsToCheck within them
		$strContainsArray = array_map(static function($containsStr) {
			return preg_quote($containsStr, '/');
		}, self::$stringsToCheck);

		$strContainsFormat = implode('|', $strContainsArray);

		preg_match_all('#<link[^>]*(' . $strContainsFormat . ').*(>)#Usmi', $altHtmlSource, $matchesFromLinkTags, PREG_SET_ORDER);

		// Needs to match at least one to carry on with the replacements
		if (isset($matchesFromLinkTags[0]) && ! empty($matchesFromLinkTags[0])) {
			foreach ($matchesFromLinkTags as $linkIndex => $linkTagArray) {
				$linkTag = trim(trim($linkTagArray[0], '"\''));
				$htmlSource = str_ireplace(array($linkTag."\n", $linkTag), '', $htmlSource);
			}
		}

		return $htmlSource;
	}

	/**
	 * @param $htmlSource
	 *
	 * @return mixed
	 */
	public static function cleanFromInlineStyleTags($htmlSource)
	{
		if (! preg_match('/(;?)(@import (?<url>url\(|\()?(?P<quotes>["\'()]?).+?(?P=quotes)(?(url)\)));?/', $htmlSource)) {
			return $htmlSource;
		}

		preg_match_all('#<\s*?style\b[^>]*>(.*?)</style\b[^>]*>#s', $htmlSource, $styleMatches, PREG_SET_ORDER);

		if (empty($styleMatches)) {
			return $htmlSource;
		}

		// Go through each STYLE tag
		foreach ($styleMatches as $styleInlineArray) {
			list($styleInlineTag, $styleInlineContent) = $styleInlineArray;

			$newStyleInlineTag = $styleInlineTag;
			$newStyleInlineContent = $styleInlineContent;

			// Is the content relevant?
			preg_match_all('/(;?)(@import (?<url>url\(|\()?(?P<quotes>["\'()]?).+?(?P=quotes)(?(url)\)));?/', $styleInlineContent, $matches);

			if (isset($matches[0]) && ! empty($matches[0])) {
				foreach ($matches[0] as $matchedImport) {
					$newStyleInlineContent = str_replace($matchedImport, '', $newStyleInlineContent);
				}

				$newStyleInlineContent = trim($newStyleInlineContent);

				// Is the STYLE tag empty after the @imports are removed? It happens on some websites; strip the tag, no point of having it empty
				if ($newStyleInlineContent === '') {
					$htmlSource = str_replace($styleInlineTag, '', $htmlSource);
				} else {
					$newStyleInlineTag = str_replace($styleInlineContent, $newStyleInlineContent, $styleInlineTag);
					$htmlSource = str_replace($styleInlineTag, $newStyleInlineTag, $htmlSource);
				}
			}

			$styleTagAfterImportsCleaned = $newStyleInlineTag;
			$styleTagAfterFontFaceCleaned = trim(self::cleanFontFaceReferences($newStyleInlineContent));
			$newStyleInlineTag = str_replace($newStyleInlineContent, $styleTagAfterFontFaceCleaned, $newStyleInlineTag);

			$htmlSource = str_replace($styleTagAfterImportsCleaned, $newStyleInlineTag, $htmlSource);
		}

		return $htmlSource;
	}

	/**
	 * @param $importsAddToTop
	 *
	 * @return mixed
	 */
	public static function stripGoogleApisImport($importsAddToTop)
	{
		// Remove any Google Fonts imports
		foreach ($importsAddToTop as $importKey => $importToPrepend) {
			if (stripos($importToPrepend, FontsGoogle::$containsStr) !== false) {
				unset($importsAddToTop[$importKey]);
			}
		}

		return $importsAddToTop;
	}

	/**
	 * @param $cssContent
	 *
	 * @return mixed
	 */
	public static function cleanFontFaceReferences($cssContent)
	{
		preg_match_all('#@font-face(|\s+){(.*?)}#si', $cssContent, $matchesFromCssCode, PREG_SET_ORDER);

		if (! empty($matchesFromCssCode)) {
			foreach ($matchesFromCssCode as $matches) {
				$fontFaceSyntax = $matches[0];
				preg_match_all('/url(\s+|)\((?![\'"]?(?:data):)[\'"]?([^\'")]*)[\'"]?\)/i', $matches[0], $matchesFromUrlSyntax);

				if (! empty($matchesFromUrlSyntax) && stripos(implode('', $matchesFromUrlSyntax[0]), '//fonts.gstatic.com/') !== false) {
					$cssContent = str_replace($fontFaceSyntax, '', $cssContent);
				}
			}
		}

		return $cssContent;
	}
}
