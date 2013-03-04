<?php 
/**
 * homepage latest news module Partial Class
 */	
SHARED_OBJECT::getObjectFromPackage(constant('__APPLICATION_ROOT__') . "::HYBERNATE::OBJECTS::NEWS::NEWS");
SHARED_OBJECT::getObjectFromPackage(constant('__APPLICATION_ROOT__') . "::HYBERNATE::OBJECTS::NEWS::NEWS_CATEGORIES");
SHARED_OBJECT::getObjectFromPackage(constant('__APPLICATION_ROOT__') . "::HYBERNATE::OBJECTS::NEWS::NEWS_TAG");
SHARED_OBJECT::getObjectFromPackage(constant('__APPLICATION_ROOT__') . "::HYBERNATE::OBJECTS::NEWS::NEWS_COMMENTS");
SHARED_OBJECT::getObjectFromPackage(constant('__APPLICATION_ROOT__') . "::HYBERNATE::OBJECTS::IMAGE::SITE_IMAGE");
SHARED_OBJECT::getObjectFromPackage(constant('__APPLICATION_ROOT__') . "::HYBERNATE::OBJECTS::NEWS::NEWS_CATEGORY");
SHARED_OBJECT::getObjectFromPackage(constant('__APPLICATION_ROOT__') . "::HYBERNATE::OBJECTS::NEWS::NEWS_CONTENT");
SHARED_OBJECT::getObjectFromPackage(constant('__APPLICATION_ROOT__') . "::HYBERNATE::OBJECTS::NEWS::NEWS_TAG");
SHARED_OBJECT::getObjectFromPackage(constant('__APPLICATION_ROOT__') . "::HYBERNATE::OBJECTS::ACTIVE_STATUS");
 
class PARTIAL_HP_LATEST_NEWS extends PARTIAL_BASE
{	
	public function __construct() {}
	
	public function execute(array $arrparameters)
	{
		$this->Application = APPLICATION::getInstance();
		$this->strApplicationStaticResPath = $this->Application->getBaseStaticResourcePath();
		$this->arrItemNews = array_shift(NEWS::getItemObjectClassView(array(
			'imagePositionId' 	=> array(10),
			'limit'				=> 1,
			'orderBy'			=> 'a.id',
			'direction'			=> 'DESC'			
		)));
	}
	
	public function render()
	{
		if (false === empty($this->arrItemNews)) {
			$strNewsPostLink = '/news/post:' . ((int) $this->arrItemNews['id']);
	?>
<article class="latest-news-hp small">
  <figure class="post_img">
    <a href="<?php echo($strNewsPostLink); ?>" title="Latest News" class="imgborder clearfix thumb listing">
      <img src="<?php echo($this->arrItemNews['imagePosition10']); ?>" alt="<?php echo($this->arrItemNews['title']); ?>" />
    </a>
  </figure>
  <div class="post_format">
    <a href="<?php echo($strNewsPostLink); ?>"></a>
  </div>
  <div class="post_area_title">
    <h2 class="entry-title">
      <a href="<?php echo($strNewsPostLink); ?>"><?php echo(utf8_encode($this->arrItemNews['title'])); ?></a>
    </h2>
    <div class="postcategories">
    	<?php
    		if (false === empty($this->arrItemNews['news_categories'])) {
    			$arrTagsLinks = array();
				$arrTags = explode(',', $this->arrItemNews['news_categories']);
				while (list($intCatIndexId, $strCategoryKey) = each($arrTags)) {
					$strCategoryData = explode(':', $strCategoryKey);
					$strCategoryName = array_shift($strCategoryData);
					$intCategoryId 	 = array_shift($strCategoryData);
					$arrTagsLinks[]  = '<a title="' . $strCategoryName . '" href="/news/category:' . $intCategoryId . '">' . $strCategoryName . '</a>';
				}
				
				echo (implode(' / ', $arrTagsLinks));
			}
    	?>
    </div>
  </div>
  <div class="entry-content">
    <p><?php echo(utf8_encode(substr(strip_tags($this->arrItemNews['content']), 0, 310))); ?>...</p>
    <div class="aligncenter">
      <a href="<?php echo($strNewsPostLink); ?>" class="button blue small float-right">
      	<?php echo($this->Application->translate('Read more', 'Lire La Suite')); ?></a>
      <div class="clear"></div>
    </div>
  </div>
  <div class="postmeta">
    <span class="postdata">
      <a href="<?php echo($strNewsPostLink); ?>" title="<?php echo($this->arrItemNews['title']); ?>">
        <?php echo($this->arrItemNews['post_date']); ?>
      </a>
    </span>
    <a title="<?php echo($this->arrItemNews['comment_count'] . ' ' . $this->Application->translate('Comment', 'Commentaire') . ($this->arrItemNews['comment_count'] > 1 ? 's' : '')); ?>" 
    	href="<?php echo($strNewsPostLink); ?>#comment" class="commentslink"><?php echo($this->arrItemNews['comment_count']); ?></a>
    <span class="share_box"  data-url="<?php echo($strNewsPostLink); ?>"></span>
  </div>
</article>
	<?php 
		}
	}
}