<?php

class tag_cloud
{
	function tag_cloud($prefix, $lost_found_flag)
	{
        // Very important.
		global $site_globals;

		// To eliminate typing...
		$link = $site_globals['link'];
		
		$zip = $_REQUEST['zip_code'];
		
		$sql = "SELECT item_id FROM {$site_globals['ITEMS_TABLE']} WHERE lost_found_flag='$lost_found_flag' AND zip='{$_REQUEST['zip_code']}'";
		$qryObj = new QueryObject($sql, $link);
		
		if (query_has_results($qryObj))
		{
			$item_ids = array();
			
			while ($row = mysql_fetch_row($qryObj->result))
				array_push( $item_ids, $row[0]);
			
			$item_id_list = implode(',', $item_ids);
			
			$sql = "SELECT DISTINCT keyword FROM {$site_globals['KEYWORDS_TABLE']} WHERE item_id IN($item_id_list)";
			$qryObj = new QueryObject($sql, $link);
			
			if (query_has_results($qryObj))
			{
				$items = array();
			
				while ($row = mysql_fetch_row($qryObj->result))
				{
					$sql = "SELECT COUNT(*) FROM {$site_globals['KEYWORDS_TABLE']} WHERE keyword='{$row[0]}' AND item_id IN($item_id_list)";
					$count_qryObj = new QueryObject($sql, $link);
					$count_row = mysql_fetch_row($count_qryObj->result);
					$items[$row[0]] = $count_row[0];
				}

				ksort($items) ; // put them alphabetically.
				$max_weight = 0;

				foreach ($items as $item => $weight)
					$weight > $max_weight && $max_weight = $weight;

				foreach ($items as $item => $weight)
				{
					$class_num = ceil(($weight/$max_weight)*7);
					echo "<a class=\"{$prefix}_{$class_num}\" style=\"text-decoration:none; margin:0px 10px 10px 0px\" href=\"javascript:location.href='?c=map_search_results&item=$item&lost_found_flag=$lost_found_flag'\">$item</a>\n";
				}
			}
		}
		else
		{
		    echo "Sorry, no items for zip code: $zip";
		}
	}
}

?>
