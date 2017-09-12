<?php

// htmlTable.php
//
// AUTHOR: Jason Frinchaboy
// DESCRIPTION:
//   htmlTable class will generate a formatted html table using
// a database result set as the data source.
//
// CLASS: tableData_MYSQL
// METHODS:
//


include_once( "include/common_functions.php" );
//include_once( "../../include/functions.php" );


/*
interface tableData
{
    function getColumnNames();
    function setColumnNames( $cNames );
	// Returns an array of arrays (rows of records that are mapped to their columnNames);
    function getData();
    function numRows();
}
*/

class tableData_MYSQL
{
	var $data = array();
	var $colNames = array();
	var $sortColumnName;
	var $orderDir = "asc";
	var $result;
	var $rowCount = 0;
	var $maxPage;
	var $rowsPerPage = 25;
	var $pageNum = 1;
	
	function getMaxPage()
    {
		return $this->maxPage;
	}
    
	function getPageNum()
    {
		return $this->pageNum;
	}
    
	function numRows()
    {
		return $this->rowCount;
	}
	
	function setMaxRowsPerPage( $maxRows )
    {
		$this->rowsPerPage = $maxRows;
	}
	
	function getSortColumnName()
    {
		return $this->sortColumnName;
	}
	
	function setSortColumn( $colName )
    {
		$this->sortColumnName = $colName;
	}
	
	function verifyResult( $result )
    {
		if ( !$result || mysql_num_rows($result) < 1 )
        {
			return false;
		}
		return true;
	}
	
	function getNextDirection()
    {
		if ( $this->orderDir == "asc" )
        {
			return "desc";
		}
		else if ( $this->orderDir == "desc" )
        {
			return "asc";
		}
	}
	
	function getOrderDirection()
    {
		return $this->orderDir;
	}
	
	function setColumnNames( $cNames )
    {
		if ( !is_array($cNames) )
        {
			return false;
		}
		$this->colNames = $cNames;
	}
	
	function getColumnNames()
    {
		return $this->colNames;
	}
	
	function processMySQLQuery( $sql, $requestVars )
    {
		if ( empty($sql) )
        {
			return false;
		}

		if ( !empty( $requestVars['ORDER_BY'] ) )
        {
			$orderBy = 	$requestVars['ORDER_BY'];
			$sql .= " ORDER BY $orderBy";
			$this->sortColumnName = $orderBy;
		}
		else if ( !empty( $this->sortColumnName ) )
        {
			$orderBy = 	$requestVars['ORDER_BY'];
			$sql .= " ORDER BY {$this->sortColumnName}";
		}
		if ( !empty( $requestVars['ORDER_DIR'] ) )
        {
			$direction = $requestVars['ORDER_DIR'];
			$this->orderDir = $direction;
			$sql .= " $direction";
		}
		if ( is_extant( $requestVars['PAGE_NUM'] ) )
        {
			$this->pageNum = $requestVars['PAGE_NUM'];
			$offset = ($this->pageNum - 1) * $this->rowsPerPage;
		}
		else
        {
			$offset = 0;
		}
        
		$getALLSQL = $sql;
		$sql .= " LIMIT $offset, {$this->rowsPerPage}";
		$countResult = mysql_query($getALLSQL) or die( mysql_error() );
		$totalNumRows = mysql_num_rows($countResult);
		$this->result = mysql_query($sql) or die( mysql_error() );
		$this->rowCount = mysql_num_rows($this->result);
		$this->maxPage = ceil( $totalNumRows / $this->rowsPerPage );
		$this->processMySQLResultSet($this->result);
	}
	
	function processMySQLResultSet( $resultSet )
    {
		if ( !$this->verifyResult($resultSet) )
        {
			return false;	
		}
        
		$tmpCols = $this->colNames;
        
		while ( $row = mysql_fetch_array($resultSet, MYSQL_ASSOC) )
        {
			//$colName = array_search( array_shift($tmpCols), $this->colNames );
			//$dataRow = array_combine( array_keys($this->colNames), $row );
			array_push( $this->data, $row);
		}
       // echo "<pre>";
      //  print_r($this->data);
      //  echo "</pre>";
		return true;
	}
	
	function getData()
    {
		return $this->data;	
	}
}

class htmlTable
{
    var $dataSource;
	var $headerLabel;
	var $columnNames = array();
	var $columnCount = 0;
	var $hiddenColumnNames = array();
	var $linkColumns = array();
	var $cellspacing = 0;
	var $cellpadding = 0;
	var $borderWidth = 0;
	var $columnHeaderStyle;
	var $selectedColumnHeaderStyle;
	var $headerStyle;
	var $cellStyle;
	var $headerBgColor;
	var $tableBgcolor;
	var $data = array();
	var $rowClr1;
	var $rowClr2;
	var $textClr1;
	var $textClr2;
	
	var $cellFontFamily;
	var $cellFontSize;                                                                                                                                             
	var $cellFontWeight;
	var $cellFontColor;
	var $cellBackgroundColor;
	var $cellTextAlign;
	var $tableWidth;
    
	var $hideHeader = false;
	
	var $colFuncMap = array();
    
	var $checkBoxColumn = array( 'colNum' => '', 'valueSource' => '' );
	var $sortingEnabled = false;
	
	var $emptyFills = array();
	var $controls = array();
	
	var $tableAlign = "center";
	var $highlightRows = array();
	var $columnProperties = array();
	var $sortDisabledColumns = array();
  
	function htmlTable( $source )
    {
		$this->dataSource = $source;
		$this->data = $source->getData();
		$this->columnNames = $source->getColumnNames();
		$this->columnCount = count($this->columnNames);
	}
	
	function display()
    {
        $columnCount = $this->columnCount - count($this->hiddenColumnNames);
			
        // Create Header
        if ( !$this->hideHeader )
        {
            $tableHTML .= "<tr><td style=\"{$this->headerStyle}\" colspan=\"$columnCount\"><table width=\"100%\" cellspacing=0 cellpadding=0 border=0>\n<tr><td style=\"{$this->headerStyle}\">{$this->headerLabel}</td>";
				
            // Display Controls
            
            if ( !empty($this->controls) )
            {
                $tableHTML .= "<td align=\"right\"><table cellspacing=0 cellpadding=1 border=0><tr>";
                
                foreach ( $this->controls as $ctrl )
                {
                    $tableHTML .= "<td>$ctrl</td>";					
                }
                
                $tableHTML .= "</tr></table></td>\n";
            }
            
            $tableHTML .= "</tr></table>\n</td></tr>\n";
        }
			
        // Create Column Headers
        
        $colNum = 0;
        $tableHTML .= "\n\n<!-- BEGIN COLUMN NAMES -->\n\n<tr>";
        
        foreach ( $this->columnNames as $colName => $colDisplay )
        {
            if ( is_extant($this->checkBoxColumn['colNum']) && $this->checkBoxColumn['colNum'] == $colNum )
            {
                $tableHTML .= "<td style=\"{$this->columnHeaderStyle};width:15px\"><input type=\"checkbox\" name=\"checkAllBox\" onclick=\"javascript: doCBClick(this)\"></td>\n";
            }
            
            if ( !in_array( $colName, $this->hiddenColumnNames ) )
            {
                $colDirIcon = "";
                
                if ( $colName == $this->dataSource->getSortColumnName() )
                {
                    $colStyle = $this->selectedColumnHeaderStyle;
                    
                    if ( $this->dataSource->getOrderDirection() == "asc" )
                    {
                        $colDirIcon .= "&nbsp;<img class=\"sort_arrow\" src=\"./images/asc_sort_arrow.gif\">";
                    }
                    else
                    {
                        $colDirIcon .= "&nbsp;<img class=\"sort_arrow\" src=\"./images/desc_sort_arrow.gif\">";
                    }
                }
                else
                {
                    $colStyle = $this->columnHeaderStyle;
                }

                if ( !empty($this->columnProperties[$colName]['width']) )
                {
                    $colStyle .= "; width:{$this->columnProperties[$colName]['width']}px";
                }
					
                if ( $this->sortingEnabled && !in_array($colName,$this->sortDisabledColumns) )
                {
                    $getVarArray = $this->getURLGetVars( array("ORDER_BY","ORDER_DIR") );
                    array_push( $getVarArray, "ORDER_BY=$colName" );
                    array_push( $getVarArray, "ORDER_DIR=".($this->dataSource->getNextDirection()) );
                    $queryStr = "";	
                    
                    if ( is_array( $getVarArray ) && !empty( $getVarArray ) )
                    {
                        $queryStr = "?".(implode( "&", $getVarArray ));	
                    }

                    $headerDisplay = "<a style=\"$colStyle\" href=\"$queryStr\"><span style=\"text-decoration: underline\">{$colDisplay}</span></a>{$colDirIcon}";
                }
                else
                {
                    $headerDisplay = $colDisplay;
						
                }
                
                $tableHTML .= "<td width=\"{$this->columnProperties[$colName]['width']}\"  style=\"$colStyle\" nowrap>$headerDisplay</td>\n";   
            }
            $colNum++;
        }
        $tableHTML .= "</tr>\n<!-- END COLUMN NAMES -->\n";
			
        if ( empty($this->data) )
        {
            $tableHTML .= "<tr><td bgcolor=\"white\" colspan=\"$columnCount\">No Records Found.</td></tr></table>";
        }
        else
        {	
            // Set Alternating Row Colors
            
            if ( !empty($this->rowClr1) && !empty($this->rowClr2) && !empty($this->textClr1) && !empty($this->textClr2) )
            {
				$doAltRowColors = true;	
			}
            $rowNum = 0;
			
            // Generate Rows
            
            foreach( $this->data as $row )
            {
                $rowHTML = "";
                $highlightRowColor = false;
                
				// Do Alternating Row Colors
                
                if( $doAltRowColors )
                {
                    if( ($rowNum++ % 2) == 0 )
                    {
                        $thisRowColor = $this->rowClr1;
                        $thisTextColor = $this->textClr1;
                    }
                    else
                    {
                        $thisRowColor = $this->rowClr2;
						$thisTextColor = $this->textClr2;
					}
                    
                    $thisRowStyle  = "background-color:$thisRowColor;";
                    $thisCellStyle = "text-align:{$this->cellTextAlign};font-family:{$this->cellFontFamily};font-size:{$this->cellFontSize};font-weight:{$this->cellFontWeight};color:$thisTextColor";
                }
                else
                {
                    $thisRowStyle  = "background-color:{$this->cellBackgroundColor}";
                    $thisCellStyle = "text-align:{$this->cellTextAlign};font-family:{$this->cellFontFamily};font-size:{$this->cellFontSize};font-weight:{$this->cellFontWeight};color:{$this->cellFontColor}";
				}
				
				$columnNum = 0;
				$tmpCols = $this->columnNames;
				
				// Generate values for each Row
                
				foreach ( $row as $val )
                {
                    $colName = array_search( array_shift($tmpCols), $this->columnNames );
					
					// Apply any functions
                    $val = $row[$colName];
					$valOrig = $val;
                    
					if ( array_key_exists( $colName, $this->colFuncMap ) )
                    {
                        // If this column function has been passed parameters...
                        
                        if ( is_array($this->colFuncMap[$colName]) )
                        {
                            $evalStr = $this->colFuncMap[$colName][0]."(".(implode(",", $this->colFuncMap[$colName][1])).")";
                        }
                        else
                        {
                            $evalStr = $this->colFuncMap[$colName]."('".addslashes($val)."');";
                        }
                        
						eval( "\$val = $evalStr" );
					}
                    
					if ( is_extant($this->checkBoxColumn['colNum']) && $this->checkBoxColumn['colNum'] == $columnNum )
                    {
                        $varPos = array_search( $varName, $this->columnNames );
                        $rowHTML .= "<td style=\"$thisCellStyle\"><center><input type=\"checkbox\" onclick=\"javascript: doCBClick(this)\" value=\"{$row[$this->checkBoxColumn['valueSource']]}\"></center></td>\n";
					}
                    
					if ( !in_array( $colName, $this->hiddenColumnNames ) )
                    {
                        $rowHTML .= "<td style=\"$thisCellStyle\">";
                        
						if ( array_key_exists( $colName, $this->linkColumns ) && is_extant($val) )
                        {
							$matches = array();
							$link = $this->linkColumns[$colName];
							preg_match_all( "/\[\[[a-zA-Z0-9_^\[^\]]*\]\]/", $link, $matches );
                            
							if ( !empty($matches) )
                            {
								foreach ( $matches as $m )
                                {
									if ( !empty($m) )
                                    {
										foreach ( $m as $match )
                                        {
											$firstCloseBracketPos = strpos($match,"]");
											$varName = substr( $match, 2, $firstCloseBracketPos - 2 );
                                            
											if ( array_key_exists( $varName, $this->columnNames ) )
                                            {
												$varPos = array_search( $this->columnNames[$varName], $this->columnNames );

												if ( $varPos === false )
                                                {
													continue;
                                                }
                                                
												$link = str_replace( $match, $row[$varPos], $link );
											}
										}
									}
								}
							}
                            
							$rowHTML .= "<a style=\"$thisCellStyle; text-decoration:underline\" href=\"$link\">$val</a></td>\n";
						}
						else
                        {
							if ( !is_extant($val) && key_exists( $colName, $this->emptyFills ) )
                            {
								$rowHTML .= $this->emptyFills[$colName];
							}
							else
                            {
								$rowHTML .= ( is_extant($val) ? $val : "&nbsp" );
							}
							$rowHTML .= "</td>\n";;
						}
					}
                    
					// If this is a highlight row...
                    
					if ( key_exists( $colName, $this->highlightRows ) )
                    {
						if ( key_exists( $valOrig, $this->highlightRows[$colName] ) )
                        {
							$highlightRowColor = $this->highlightRows[$colName][$valOrig];
						}
					}
                    
					$columnNum++;
				}
                
				if ( !($highlightRowColor === false) )
                {
					$thisRowStyle = "background-color:$highlightRowColor";
				}
				
                $rowHTML = "<tr style=\"$thisRowStyle\">$rowHTML</tr>\n\n";
                $tableHTML .= $rowHTML;
            }
        }
        $tableHTML = "<div id=\"\" name=\"\" align=\"".($this->tableAlign)."\"><center>".($this->getNavLinks())."</center>\n<form id=\"tableForm\" name=\"tableForm\">\n<table style=\"width:".($this->tableWidth)."; background-color: ".($this->tableBgcolor)."\" cellspacing=\"".($this->cellspacing)."\" cellpadding=\"".($this->cellpadding)."\" border=\"".($this->borderWidth)."\">$tableHTML\n</table><center>".($this->getNavLinks())."</center></form></div>";
		
		echo $tableHTML;
	}

	function setMaxRowsPerPage( $maxRows )
    {
		$this->dataSource->setMaxRowsPerPage($maxRows);
	}
	
	function applyFunction( $colName, $funcName )
    {
		$this->colFuncMap[$colName] = $funcName;
	}
    
    /*function applyFunctionWithParams( $colName, $funcName, &$paramArray )
    {
        $params = array();
        
        foreach ( $paramArray as $param )
        {
            if( is_array($param)
            {
                array_push( $params, $param );
            }
            else
            {
                array_push( $params, "'$param'" );
            }
        }
        
        $this->colFuncMap[$colName] = array($funcName,$params);
    }*/
	
	function getURLGetVars( $excludes )
    {
		if( is_array($_GET) && !empty($_GET) ) {
			$qStr = array();
			foreach( $_GET as $key => $value ) {
				if( !empty($excludes) && is_array($excludes) ) {	
					if( in_array( $key, $excludes ) ) {
						continue;
					}
				}
				array_push( $qStr, "$key=$value" );
			}
			return $qStr;
		}
		return array();
	}

	function hideColumn( $columnName )
    {
		array_push( $this->hiddenColumnNames, $columnName );
	}
	
	function setHeaderlabel( $hLabel )
    {
		$this->headerLabel = $hLabel;	
	}
	
	function setHeaderStyle( $family, $size, $weight, $color, $cellcolor, $align )
    {
		$hdrStyle = array();
        
		if( $family )
			array_push( $hdrStyle, "font-family: $family" );
		if( $size )
			array_push( $hdrStyle, "font-size: $size" );
		if( $weight )
			array_push( $hdrStyle, "font-weight: $weight" );
		if( $color )
			array_push( $hdrStyle, "color: $color" );
		if( $cellcolor )
			array_push( $hdrStyle, "background-color: $cellcolor" );
		if( $align )
			array_push( $hdrStyle, "text-align: $align" );

		$this->headerStyle = implode( $hdrStyle, ";" );
	}
	
	function setColumnHeaderStyle( $family, $size, $weight, $color, $cellcolor, $align )
    {
		$hdrStyle = array();
        
		if( $family )
			array_push( $hdrStyle, "font-family: $family" );
		if( $size )
			array_push( $hdrStyle, "font-size: $size" );
		if( $weight )
			array_push( $hdrStyle, "font-weight: $weight" );
		if( $color )
			array_push( $hdrStyle, "color: $color" );
		if( $cellcolor )
			array_push( $hdrStyle, "background-color: $cellcolor" );
		if( $align )
			array_push( $hdrStyle, "text-align: $align" );

		$this->columnHeaderStyle = implode( $hdrStyle, ";" );
	}

	function setCellStyle( $family, $size, $weight, $color, $cellcolor, $align )
    {
		$hdrStyle = array();
        
		if( $family )
			$this->cellFontFamily = $family;
		if( $size )
			$this->cellFontSize = $size;
		if( $weight )
			$this->cellFontWeight = $weight;
		if( $color )
			$this->cellFontColor = $color;
		if( $cellcolor )
			$this->cellBackgroundColor = $cellcolor;
		if( $align )
			$this->cellTextAlign = $align;

		$this->cellStyle = implode( $hdrStyle, ";" );
	}

	function setTableStyle( $cellpadding, $cellspacing, $width )
    {
		$this->cellpadding = $cellpadding;
		$this->cellspacing = $cellspacing;
		$this->borderWidth = $width;
	}

	function setTableBgColor( $bgcolor )
    {
		$this->tableBgcolor = $bgcolor;
	} 
	
	function setTableWidth( $w )
    {
        $this->tableWidth = $w;
    }
    
	function enableColumnSorting( $doEnable )
    {
		$this->sortingEnabled = $doEnable;
	}
    
    function disableColumnSorting( $colName )
    {
        if ( is_extant($colName) )
        {
            array_push( $this->sortDisabledColumns, $colName );
        }
    }
	
	function setBorderColor( $clr )
    {
		$this->setTableBgColor( $clr );
		$this->cellspacing = 1;
	}
	
	function setLinkColumn( $columnName, $href )
    {
		$this->linkColumns[$columnName] = $href;
	}
	
	function setColumnWidth( $columnName, $newWidth )
    {
		$this->columnProperties[$columnName]['width'] = $newWidth;
	}
	
	function hideHeader()
    {
		$this->hideHeader = true;	
	}
	
	function setAlternatingTextColors( $t1, $t2 )
    {
		$this->textClr1 = $t1;
		$this->textClr2 = $t2;	
	}
	
	function setAlternatingRowColors( $c1, $c2 )
    {
		$this->rowClr1 = $c1;
		$this->rowClr2 = $c2;
	}
	
	function insertCheckBoxColumn( $colNum, $valueSource )
    {
		$this->checkBoxColumn['colNum'] = $colNum;
		$this->checkBoxColumn['valueSource'] = $valueSource;
		$this->columnCount++;
	}

	function fillEmptyValue( $colName, $value )
    {
		if ( is_extant($colName) && is_extant($value) )
        {
			$this->emptyFills[$colName] = $value;
		}
	}
	
	function setSelectedColumnStyle( $family, $size, $weight, $color, $cellcolor, $align )
    {
		$hdrStyle = array();
        
		if( $family )
			array_push( $hdrStyle, "font-family: $family" );
		if( $size )
			array_push( $hdrStyle, "font-size: $size" );
		if( $weight )
			array_push( $hdrStyle, "font-weight: $weight" );
		if( $color )
			array_push( $hdrStyle, "color: $color" );
		if( $cellcolor )
			array_push( $hdrStyle, "background-color: $cellcolor" );
		if( $align )
			array_push( $hdrStyle, "text-align: $align" );

		$this->selectedColumnHeaderStyle = implode( $hdrStyle, ";" );
	}

	function addDeleteControl( $tableName, $colName, $label )
    {
		$controlName = "delete";
		$controlHTML = "<input value=\"$label\" type=\"button\" id=\"$controlName\" name=\"$controlName\" onclick=\"doDelete( '$tableName', '$colName' )\">";
		$this->controls[$controlName] = $controlHTML;
	}
	
	function addControl( $controlName, $controlHTML )
    {
		$this->controls[$controlName] = $controlHTML;
	}
	
	function highlightRowbyColumnValue( $colName, $colValue, $rowColor )
    {
		if ( !key_exists( $colName, $this->highlightRows ) )
        {
			$this->highlightRows[$colName] = array( $colValue => $rowColor );
		}
		else
        {
			$this->highlightRows[$colName][$colValue] = $rowColor;
		}
	}
	
	function setAlign( $align )
    {
		$this->tableAlign = $align;	
	}
	
	function getNavLinks()
    {
		$maxPage = $this->dataSource->getMaxPage();
		
		if( $maxPage < 2 )
        {
			return;
        }
		
		$pageNum = $this->dataSource->getPageNum();
		$getVarArray = $this->getURLGetVars( array( "PAGE_NUM" ) );
		$queryStr = "";	
        
		if ( is_array( $getVarArray ) && !empty( $getVarArray ) )
        {
			$queryStr = "&".(implode( "&", $getVarArray ));	
		}
        
		if ( $pageNum > 1 )
        {
			$prevPage = $this->dataSource->getPageNum() - 1;
			$prev = " <a class=\"navActive\" href=\"{$_SERVER['PHP_SELF']}?PAGE_NUM=$prevPage{$queryStr}\">[Prev]</a> ";
			$first = " <a class=\"navActive\" href=\"{$_SERVER['PHP_SELF']}?PAGE_NUM=1{$queryStr}\">[First Page]</a> ";
		} 
		else
        {
			$prev  = "<span class=\"navGrayedOut\">[Prev]</span>"; // we're on page one, don't enable 'previous' link
			$first = "<span class=\"navGrayedOut\">[First Page]</span>"; // nor 'first page' link
		}
		if ( $pageNum < $maxPage )
        {
			$nextPage = $this->dataSource->getPageNum() + 1;
			$next = "<a class=\"navActive\" href=\"{$_SERVER['PHP_SELF']}?PAGE_NUM=$nextPage{$queryStr}\">[Next]</a>";  
			$last = "<a class=\"navActive\" href=\"{$_SERVER['PHP_SELF']}?PAGE_NUM=$maxPage{$queryStr}\">[Last Page]</a>";
		} 
		else
        {
			$next = "<span class=\"navGrayedOut\">[Next]</span>"; // we're on the last page, don't enable 'next' link
			$last = "<span class=\"navGrayedOut\">[Last Page]</span>"; // nor 'last page' link
		}
        
		$pageLinks = "";
        
		for ( $i = 1; $i <= $maxPage; $i++ )
        {
			if ( $i == $this->dataSource->getPageNum() )
            {
				$pageLinks .= "<span class=\"navActive\" style=\"text-decoration: none\">&nbsp;$i&nbsp;</span>";
			}
			else
            {
				$pageLinks .= "&nbsp;<a class=\"navActive\" href=\"{$_SERVER['PHP_SELF']}?PAGE_NUM=$i{$queryStr}\">$i</a>&nbsp;";
			}
		}
		
		return "$first $prev $pageLinks $next $last";
	}
}
?>
