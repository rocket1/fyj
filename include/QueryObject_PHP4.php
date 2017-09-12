<?php

function query_was_good( $qryObj )
{
    return ($qryObj && $qryObj->errno == 0);
}

function query_has_results( $qryObj )
{
    return (query_was_good($qryObj) && $qryObj->result && mysql_num_rows($qryObj->result) > 0);
}


class QueryObject
{
    var $sql;
    var $db_link;
    var $result;
    var $errno;
    var $numRows;

    function perform_db_query()
    {
        $this->result = mysql_query($this->sql, $this->db_link);
        $this->errno  = mysql_errno($this->db_link);
        if( $this->errno != 0 )
        {
           echo "<br><br>".mysql_error()."<br><br>";
        }
    }



    function QueryObject( $sql, $db_link )
    {
        $this->sql = $sql;
      // echo "<br><br>$sql</br></br>";
        $this->db_link = $db_link;
        $this->result = 0;
        $this->errno = 0;
        $this->numRows = 0;
        $this->perform_db_query();
    }
}
