<?php
class Permission_block_model extends Base_model
{
    public function get_permission_block($prefix)
    {
        return $this->get_result('permission_block', '*', array('prefix' => $prefix));
    }
}

?>
