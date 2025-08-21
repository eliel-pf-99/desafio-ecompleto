<?php

function getIdByConditionAndTable(Database $db, string $condition, string $table){
        $safeTable = pg_escape_string($table);
        $safeCondition = pg_escape_string($condition);
        $result = $db->query("SELECT id FROM {$safeTable} WHERE descricao='{$safeCondition}'");
        
        if(empty($result)){
            return null;
        }
        
        return $result[0]['id'];
}