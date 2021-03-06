<?php
/** 
 * @package JMAP::SITEMAP::components::com_jmap
 * @subpackage views
 * @subpackage sitemap
 * @subpackage tmpl
 * @author Joomla! Extensions Store
 * @copyright (C) 2013 - Joomla! Extensions Store
 * @license GNU/GPLv2 http://www.gnu.org/licenses/gpl-2.0.html
 */
defined ( '_JEXEC' ) or die ( 'Restricted access' );

$priority =  $this->sourceparams->get ( 'priority', '0.5' );
$changefreq = $this->sourceparams->get ( 'changefreq', 'daily' );

// Additional query string params
$additionalQueryStringParams =  $this->sourceparams->get ( 'additionalquerystring', null);
if($additionalQueryStringParams) {
	$additionalQueryStringParams = '&' . preg_replace('/,\s*/i', '&', $additionalQueryStringParams);
	$additionalQueryStringParams =  preg_replace('/\s+/i', '', $additionalQueryStringParams);
}

$targetOption = $this->source->chunks->option;
$targetView = !empty($this->source->chunks->view) ? '&view=' . $this->source->chunks->view : null;

// Fallback identifiers
$idIdentifier =  $this->source->chunks->idfield_as ?  $this->source->chunks->idfield_as :  $this->source->chunks->id;
$titleIdentifier =  $this->source->chunks->titlefield_as ?  $this->source->chunks->titlefield_as :  $this->source->chunks->titlefield;
$catidIdentifier =  $this->source->chunks->catidfield_as ?  $this->source->chunks->catidfield_as :  $this->source->chunks->catid;
  
if (count ( $this->source->data ) != 0) {  
	foreach ( $this->source->data as $elm ) {
		$id = isset($idIdentifier) &&  $idIdentifier != ''  ? '&' .$idIdentifier . '=' . $elm->{$idIdentifier} : null;
		$title = isset($titleIdentifier) &&  $titleIdentifier != ''  ? $elm->{$titleIdentifier} : null;
		$catid = isset($catidIdentifier) &&  $catidIdentifier != '' ? '&' .$catidIdentifier . '=' . $elm->{$catidIdentifier} : null;
		
		// Additional fields
		$additionalParamsQueryString = null;
		$objectVars = array_diff_key(get_object_vars($elm), array ($idIdentifier=>null, $titleIdentifier=>null, $catidIdentifier=>null));
		if(is_array($objectVars) && count($objectVars)) {
			$additionalParamsQueryString = '&' . http_build_query($objectVars);
		}
		
		$seflink = JRoute::_ ( 'index.php?option=' . $targetOption . $id . $targetView . $catid . $additionalParamsQueryString . $additionalQueryStringParams);
		
		// Skip outputting
		if(in_array($seflink, $this->outputtedLinksBuffer)) {
			continue;
		}
		// Else store to prevent duplication
		$this->outputtedLinksBuffer[] = $seflink;
		?>
<url>
<loc><?php echo $this->liveSite . $seflink; ?></loc>
<changefreq><?php echo $changefreq;?></changefreq>
<priority><?php echo $priority;?></priority>
</url>
<?php 
	}
}
