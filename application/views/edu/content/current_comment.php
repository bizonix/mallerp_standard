<?php

foreach ($comments as $comment)
{
    $delete_url = site_url('edu/content/drop_comment', array($comment->id,$content_id));

    echo "<div style='width:600; background-color:#CCCCCC;'>$comment->u_name  发表于 $comment->created_date</div>";
    echo $comment->comment;
    echo '<br><br>';
    if($tag)
    {
        echo '<div align="right"><a href="#" onclick="delete_comment('."'$delete_url'".'); return false;" >'.lang('delete').'</a></div>';
        echo '<br><br>';
    }
}

?>
