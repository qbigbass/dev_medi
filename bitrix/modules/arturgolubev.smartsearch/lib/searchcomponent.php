<?
namespace Arturgolubev\Smartsearch;

use \Arturgolubev\Smartsearch\Unitools as Tools;

class SearchComponent {
	public $options = array(); 
	public $baseQuery = false; 
	public $query = false; 
	public $system_mode = false; 
	
	public function baseInit($q, $type = ''){
		$this->options['disable_item_id_filter'] = 0;
		
		$this->options['debug'] = Tools::getSetting('debug');
		
		$this->options['theme_class'] = Tools::getSetting('color_theme', 'blue');
		$this->options['theme_color'] = Tools::getSetting('my_color_theme');
		
		$this->options['use_fixes'] = (Tools::getSetting('mode_metaphone') == 'Y');
		$this->options['use_clarify'] = (Tools::getSetting('clarify_section') == "Y");
		$this->options['use_guessplus'] = (Tools::getSetting("mode_guessplus") == "Y");
		
		$this->options['engine'] = \COption::GetOptionString("search", 'full_text_engine');
		$this->options['use_stemming'] = (\COption::GetOptionString("search", 'use_stemming') == 'Y');
				
		if($this->options['engine'] == 'sphinx'){
			$this->options['mode'] = 'standart';
		}else{	
			if($type == 'page'){
				$this->options['mode'] = Tools::getSetting("mode_spage");
			}elseif($type == 'title'){
				$this->options['theme_placeholder'] = Tools::getSetting('input_search_placeholder');
				
				$this->options['mode'] = Tools::getSetting("mode_stitle");
			}
		}
		
		if($q){
			$this->baseQuery = $q;
			
			foreach(GetModuleEvents(\CArturgolubevSmartsearch::MODULE_ID, "onBeforePrepareQuery", true) as $arEvent)
				ExecuteModuleEventEx($arEvent, array(&$q));
			
			$q = str_replace('&nbsp;', ' ', $q);
			
			$q = \CArturgolubevSmartsearch::checkReplaceRules($q);
			$q = \CArturgolubevSmartsearch::prepareQuery($q);
			$q = \CArturgolubevSmartsearch::clearExceptionsWords($q);
			
			$this->query = $q;
		}
	}
	
	public function setTitle(){
		$this->options['set_page_title'] = (Tools::getSetting("set_title") == 'Y');
		
		if($this->options['set_page_title']){
			$this->options['set_page_title_template'] = Tools::getSetting("set_title_template");
			
			if($this->options['set_page_title_template']){
				global $APPLICATION;
				$APPLICATION->SetPageProperty("title", str_replace('#QUERY#', $this->baseQuery, $this->options['set_page_title_template']));
			}
		}
	}
	
	public function setItemIdFilterMode($disableParam){
		if($disableParam == 'Y' || $this->options['engine'] == 'sphinx'){
			$this->options['disable_item_id_filter'] = 1;
		}
	}
	
	public $folderPath = '';
	public function searchRowPrepare($ar){
		if(!$this->system_mode){
			global $APPLICATION;
			
			$ar["CHAIN_PATH"] = $APPLICATION->GetNavChain($ar["URL"], 0, $this->folderPath."/chain_template.php", true, false);
			$ar["URL"] = htmlspecialcharsbx($ar["URL"]);
			$ar["TAGS"] = array();
			if (!empty($ar["~TAGS_FORMATED"]))
			{
				foreach ($ar["~TAGS_FORMATED"] as $name => $tag)
				{
					if($arParams["TAGS_INHERIT"] == "Y")
					{
						$arTags = $arResult["REQUEST"]["~TAGS_ARRAY"];
						$arTags[$tag] = $tag;
						$tags = implode("," , $arTags);
					}
					else
					{
						$tags = $tag;
					}
					$ar["TAGS"][] = array(
						"URL" => $APPLICATION->GetCurPageParam("tags=".urlencode($tags), array("tags")),
						"TAG_NAME" => htmlspecialcharsex($name),
					);
				}
			}
		}
		
		return $ar;
	}
	
	public static function reformatDescription($old, $newPreview, $textLengh){
		$newPreview = strip_tags(htmlspecialchars_decode($newPreview));
		$newPreview = \CArturgolubevSmartsearch::formatElementName($old, $newPreview);
		
		if(Encoding::exStrlen($newPreview) > $textLengh){
			if(Encoding::exStrpos($newPreview, '<') !== false){
				$startPos = Encoding::exStrpos($newPreview, '<') - ($textLengh / 2);
				if($startPos > 0){
					$newPreview = Encoding::exSubstr($newPreview, $startPos);
				}
				
				if(Encoding::exStrlen($newPreview) > $textLengh){
					$obParser = new \CTextParser;
					$newPreview = $obParser->html_cut($newPreview, $textLengh);
				}
				
				if($startPos > 0){
					$newPreview = '...'.$newPreview;
				}
			}else{
				$obParser = new \CTextParser;
				$newPreview = $obParser->html_cut($newPreview, $textLengh);
			}
		}
		
		return $newPreview;
	}
}