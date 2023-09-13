<?
require_once($_SERVER["DOCUMENT_ROOT"]."/bitrix/modules/search/classes/mysql/search.php");

class CSearchExt extends CSearch
{
	function GetFilterMD5(){
		return '';
	}
	
	function NavStart($nPageSize = 0, $bShowAll = true, $iNumPage = false){
		CDBResult::NavStart($nPageSize, $bShowAll, $iNumPage);
	}
	
	function MakeSQL($query, $strSqlWhere, $strSort, $bIncSites, $bStem){
		$DB = CDatabase::GetModuleConnection('search');

		$bDistinct = false;
		$arSelect = array(
			"ID" => "sc.ID",
			"MODULE_ID" => "sc.MODULE_ID",
			"ITEM_ID" => "sc.ITEM_ID",
			"TITLE" => "sc.TITLE",
			"TAGS" => "sc.TAGS",
			"PARAM1" => "sc.PARAM1",
			"PARAM2" => "sc.PARAM2",
			"UPD" => "sc.UPD",
			"DATE_FROM" => "sc.DATE_FROM",
			"DATE_TO" => "sc.DATE_TO",
			"URL" => "sc.URL",
			"CUSTOM_RANK" => "sc.CUSTOM_RANK",
			"FULL_DATE_CHANGE" => $DB->DateToCharFunction("sc.DATE_CHANGE")." as FULL_DATE_CHANGE",
			"DATE_CHANGE" => $DB->DateToCharFunction("sc.DATE_CHANGE", "SHORT")." as DATE_CHANGE",
		);
		if (BX_SEARCH_VERSION > 1)
		{
			if ($this->Query->bText)
				$arSelect["SEARCHABLE_CONTENT"] = "sct.SEARCHABLE_CONTENT";
			$arSelect["USER_ID"] = "sc.USER_ID";
		}
		else
		{
			$arSelect["LID"] = "sc.LID";
			$arSelect["SEARCHABLE_CONTENT"] = "sc.SEARCHABLE_CONTENT";
		}

		if (strpos($strSort, "TITLE_RANK") !== false)
		{
			/*
			if ($bStem){
				foreach ($this->Query->m_stemmed_words as $stem){
					if (strlen($strSelect) > 0)
						$strSelect .= " + ";
					$strSelect .= "if(locate('".$stem."', upper(sc.TITLE)) > 0, locate('".$stem."', upper(sc.TITLE)), 1000)";
				}
			}else{
				foreach ($this->Query->m_words as $word){
					if (strlen($strSelect) > 0)
						$strSelect .= " + ";
					$strSelect .= "if(locate('".$DB->ForSql(ToUpper($word))."', upper(sc.TITLE)) > 0, locate('".$DB->ForSql(ToUpper($word))."', upper(sc.TITLE)), 1000)";
				}
			}
			*/
			
			
			$strSelect = "";
			$rankWord = [];
			
			if ($bStem){
				$wordE = explode(' ', ToUpper($this->Query->m_query));
				
				foreach ($this->Query->m_stemmed_words as $stem){
					foreach($wordE as $k=>$baseWord){
						if(stripos($baseWord, $stem) !== false){
							$rankWord[] = $stem;
							unset($wordE[$k]);
						}
					}
				}
				
				if(count($wordE)){
					foreach($wordE as $baseWord){
						$rankWord[] = $baseWord;
					}
				}
			}else{
				foreach ($this->Query->m_words as $word){
					$rankWord[] = ToUpper($word);
				}
			}
			
			foreach ($rankWord as $stem){
				if (strlen($strSelect) > 0)
					$strSelect .= " + ";
				// $strSelect .= "if(locate('".$stem."', upper(sc.TITLE)) > 0, locate('".$stem."', upper(sc.TITLE)), 1000)";
				$strSelect .= "if((locate(' ".$stem."', upper(sc.TITLE)) > 0 OR locate('".$stem."', upper(sc.TITLE)) = 1), locate(' ".$stem."', upper(sc.TITLE)), (if(locate('".$stem."', upper(sc.TITLE)) > 0, (locate('".$stem."', upper(sc.TITLE)) + 1000), 10000)))";
			}
			
			$arSelect["TITLE_RANK"] = $strSelect." as TITLE_RANK";
			$strSort .= ', TITLE';
		}

		$strStemList = '';
		if ($bStem)
		{
			if (BX_SEARCH_VERSION > 1)
				$strStemList = implode(", ", $this->Query->m_stemmed_words_id);
			else
				$strStemList = "'".implode("' ,'", $this->Query->m_stemmed_words)."'";
		}

		// $bWordPos = BX_SEARCH_VERSION > 1 && COption::GetOptionString("search", "use_word_distance") == "Y";
		$bWordPos = 1;

		if ($bIncSites && $bStem)
		{
			$arSelect["SITE_URL"] = "scsite.URL as SITE_URL";
			$arSelect["SITE_ID"] = "scsite.SITE_ID";

			if (!preg_match("/(sc|sct)./", $query))
			{
				$strSqlWhere = preg_replace('#AND\\(st.TF >= [0-9\.,]+\\)#i', "", $strSqlWhere);

				if (count($this->Query->m_stemmed_words) > 1)
					$arSelect["RANK"] = "stt.RANK as `RANK`";
				else
					$arSelect["RANK"] = "stt.TF as `RANK`";

				$strSql = "
				FROM b_search_content sc
					".($this->Query->bText? "INNER JOIN b_search_content_text sct ON sct.SEARCH_CONTENT_ID = sc.ID": "")."
					INNER JOIN b_search_content_site scsite ON sc.ID=scsite.SEARCH_CONTENT_ID
					".(count($this->Query->m_stemmed_words) > 1?
						"INNER JOIN  (
							select search_content_id, max(st.TF) TF, ".($bWordPos? "if(STDDEV(st.PS)-".$this->normdev(count($this->Query->m_stemmed_words))." between -0.000001 and 1, 1/STDDEV(st.PS), 0) + ": "")."sum(st.TF/sf.FREQ) as `RANK`
							from b_search_content_stem st, b_search_content_freq sf
							where st.language_id = '".$this->Query->m_lang."'
							and st.stem = sf.stem
							and sf.language_id = st.language_id
							and st.stem in (".$strStemList.")
							".($this->tf_hwm > 0? "and st.TF >= ".number_format($this->tf_hwm, 2, ".", ""): "")."
							".(strlen($this->tf_hwm_site_id) > 0? "and sf.SITE_ID = '".$DB->ForSQL($this->tf_hwm_site_id, 2)."'": "and sf.SITE_ID IS NULL")."
							group by st.search_content_id
							having (".$query.")
						) stt ON sc.id = stt.search_content_id"
						: "INNER JOIN b_search_content_stem stt ON sc.id = stt.search_content_id"
					)."
				WHERE
				".CSearch::CheckPermissions("sc.ID")."
				".(count($this->Query->m_stemmed_words) > 1? "": "
					and stt.language_id = '".$this->Query->m_lang."'
					and stt.stem in (".$strStemList.")
					".($this->tf_hwm > 0? "and stt.TF >= ".number_format($this->tf_hwm, 2, ".", ""): "")."")."
				".$strSqlWhere."
				";
			}
			else
			{
				if (count($this->Query->m_stemmed_words) > 1)
				{
					if ($bWordPos)
						$arSelect["RANK"] = "if(STDDEV(st.PS)-".$this->normdev(count($this->Query->m_stemmed_words))." between -0.000001 and 1, 1/STDDEV(st.PS), 0) + sum(st.TF/sf.FREQ) as `RANK`";
					else
						$arSelect["RANK"] = "sum(st.TF/sf.FREQ) as `RANK`";
				}
				else
				{
					$arSelect["RANK"] = "st.TF as `RANK`";
				}

				$strSql = "
				FROM b_search_content sc
					".($this->Query->bText? "INNER JOIN b_search_content_text sct ON sct.SEARCH_CONTENT_ID = sc.ID": "")."
					INNER JOIN b_search_content_site scsite ON sc.ID=scsite.SEARCH_CONTENT_ID
					INNER JOIN b_search_content_stem st ON sc.id = st.search_content_id+0
					".(count($this->Query->m_stemmed_words) > 1?
						"INNER JOIN b_search_content_freq sf ON
							st.language_id = sf.language_id
							and st.stem=sf.stem
							".(strlen($this->tf_hwm_site_id) > 0?
							"and sf.SITE_ID = '".$DB->ForSQL($this->tf_hwm_site_id, 2)."'":
							"and sf.SITE_ID IS NULL"
						):
						""
					)."
				WHERE
					".CSearch::CheckPermissions("sc.ID")."
					AND st.STEM in (".$strStemList.")
					".(count($this->Query->m_stemmed_words) > 1? "AND sf.STEM in (".$strStemList.")": "")."
					AND st.language_id='".$this->Query->m_lang."'
					".$strSqlWhere."
				GROUP BY
					sc.ID
					,scsite.URL
					,scsite.SITE_ID
				HAVING
					(".$query.")
				";
			}
		}
		elseif ($bIncSites && !$bStem)
		{
			$bDistinct = true;

			$arSelect["SITE_URL"] = "scsite.URL as SITE_URL";
			$arSelect["SITE_ID"] = "scsite.SITE_ID";
			$arSelect["RANK"] = "1 as `RANK`";

			if ($this->Query->bTagsSearch)
			{
				$strSql = "
				FROM b_search_content sc
					".($this->Query->bText? "INNER JOIN b_search_content_text sct ON sct.SEARCH_CONTENT_ID = sc.ID": "")."
					INNER JOIN b_search_content_site scsite ON sc.ID=scsite.SEARCH_CONTENT_ID
					INNER JOIN b_search_tags stags ON (sc.ID = stags.SEARCH_CONTENT_ID)
				WHERE
					".CSearch::CheckPermissions("sc.ID")."
					".$strSqlWhere."
					".(is_array($this->Query->m_tags_words) && count($this->Query->m_tags_words) > 0? "AND stags.NAME in ('".implode("','", $this->Query->m_tags_words)."')": "")."
				GROUP BY
					sc.ID
					,scsite.URL
					,scsite.SITE_ID
				HAVING
					".$query."
				";
			}
			else
			{
				$strSql = "
				FROM
					".($this->Query->bText? "
						b_search_content_text sct
						INNER JOIN b_search_content sc ON sc.ID = sct.SEARCH_CONTENT_ID
						INNER JOIN b_search_content_site scsite ON sc.ID = scsite.SEARCH_CONTENT_ID
					": "
						b_search_content sc
						INNER JOIN b_search_content_site scsite ON sc.ID = scsite.SEARCH_CONTENT_ID
					")."
				WHERE
					".CSearch::CheckPermissions("sc.ID")."
					AND (".$query.")
					".$strSqlWhere."
				";
			}
		}
		elseif (!$bIncSites && $bStem)
		{
			if (BX_SEARCH_VERSION <= 1)
				$arSelect["SITE_ID"] = "sc.LID as SITE_ID";

			if (count($this->Query->m_stemmed_words) > 1)
			{
				if ($bWordPos)
					$arSelect["RANK"] = "if(STDDEV(st.PS)-".$this->normdev(count($this->Query->m_stemmed_words))." between -0.000001 and 1, 1/STDDEV(st.PS), 0) + sum(st.TF/sf.FREQ) as `RANK`";
				else
					$arSelect["RANK"] = "sum(st.TF/sf.FREQ) as `RANK`";
			}
			else
			{
				$arSelect["RANK"] = "st.TF as `RANK`";
			}

			$strSql = "
			FROM b_search_content sc
				".($this->Query->bText? "INNER JOIN b_search_content_text sct ON sct.SEARCH_CONTENT_ID = sc.ID": "")."
				INNER JOIN b_search_content_stem st ON sc.id = st.search_content_id
				".(count($this->Query->m_stemmed_words) > 1?
					"INNER JOIN b_search_content_freq sf ON
						st.language_id = sf.language_id
						and st.stem=sf.stem
						".(strlen($this->tf_hwm_site_id) > 0?
						"and sf.SITE_ID = '".$DB->ForSQL($this->tf_hwm_site_id, 2)."'":
						"and sf.SITE_ID IS NULL"
					):
					""
				)."
			WHERE
				".CSearch::CheckPermissions("sc.ID")."
				AND st.STEM in (".$strStemList.")
				".(count($this->Query->m_stemmed_words) > 1? "AND sf.STEM in (".$strStemList.")": "")."
				AND st.language_id='".$this->Query->m_lang."'
				".$strSqlWhere."
			".(count($this->Query->m_stemmed_words) > 1? "
			GROUP BY
				sc.ID
			HAVING
				(".$query.") ": "")."
			";
		}
		else //if(!$bIncSites && !$bStem)
		{
			$bDistinct = true;

			if (BX_SEARCH_VERSION <= 1)
				$arSelect["SITE_ID"] = "sc.LID as SITE_ID";
			$arSelect["RANK"] = "1 as `RANK`";

			$strSql = "
			FROM b_search_content sc
				".($this->Query->bText? "INNER JOIN b_search_content_text sct ON sct.SEARCH_CONTENT_ID = sc.ID": "")."
				".($this->Query->bTagsSearch? "INNER JOIN b_search_tags stags ON (sc.ID = stags.SEARCH_CONTENT_ID)
			WHERE
				".CSearch::CheckPermissions("sc.ID")."
				".$strSqlWhere."
				".(is_array($this->Query->m_tags_words) && count($this->Query->m_tags_words) > 0? "AND stags.NAME in ('".implode("','", $this->Query->m_tags_words)."')": "")."
			GROUP BY
				sc.ID
			HAVING
				(".$query.")":
					" WHERE
				(".$query.")
				".$strSqlWhere."
			")."
			";
		}

		if ($this->offset === false){
			$limit = $this->limit;
		}else{
			$limit = $this->offset.", ".$this->limit;
		}
		
		if($limit < 1)
		{
			$baseLimit = COption::GetOptionInt("search", "max_result_size");
			$limit = ($baseLimit) ? $baseLimit : 500;
		}

		$strSelect = "SELECT ".($bDistinct? "DISTINCT": "")."\n".implode("\n,", $arSelect);

		// echo '<pre>bIncSites = '; print_r($bIncSites); echo '</pre>';
		// echo '<pre>bStem = '; print_r($bStem); echo '</pre>';
		// echo '<pre>'; print_r($strSelect."\n".$strSql.$strSort."\nLIMIT ".$limit); echo '</pre>';
		// AddMessage2Log($strSelect."\n".$strSql.$strSort."\nLIMIT ".$limit, 'search.page strSql', 0);

		return $strSelect."\n".$strSql.$strSort."\nLIMIT ".$limit;
	}
}
?>