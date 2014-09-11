<?php foreach ($cats as $cat): ?>
<li class="x-tree-node">
<?php if (empty ($cat['has_children'])): ?>
<li class="x-tree-node">
    <div class="x-tree-node-el folder active-category x-tree-node-leaf-mark x-tree-node-collapsed" id="">
        <span class="x-tree-node-indent">
            <?php for ($i = 0; $i < $cat['level']; $i++): ?>
            <img class="x-tree-elbow-line" src="<?=base_url()?>static/images/tree/spacer.gif">
            <?php endfor; ?>
        </span>
        <img class="x-tree-ec-icon x-tree-elbow" src="<?=base_url()?>static/images/tree/spacer.gif" id="">
        <img unselectable="on" class="x-tree-node-icon" src="<?=base_url()?>static/images/tree/spacer.gif" id="">
        <a tabindex="1" href="#" hidefocus="on" id="" onClick="update_content(this, '<?=$content_url?>', <?=$cat['id']?>);return false;">
            <span unselectable="on" id=""><?=$cat['name']?></span>
        </a>
    </div>
    <ul style="display: none;" class="x-tree-node-ct"></ul>
</li>
<?php else: ?>
    <li class="x-tree-node">
        <div class="x-tree-node-el folder active-category x-tree-node-collapsed" id="">
            <?php for ($i = 0; $i < $cat['level']; $i++): ?>
            <img class="x-tree-elbow-line" src="<?=base_url()?>static/images/tree/spacer.gif">
            <?php endfor; ?>
            <img class="x-tree-ec-icon x-tree-elbow-plus" src="<?=base_url()?>static/images/tree/spacer.gif" id="" onClick="toggle_tree(this, '<?=$child_tree_url?>', <?=$cat['id']?>, <?=$cat['level']?>, this); return false;">
            <img unselectable="on" class="x-tree-node-icon" src="<?=base_url()?>static/images/tree/spacer.gif" id="">
            <a tabindex="1" href="#" hidefocus="on" id="" onClick="toggle_tree_update_content(this, '<?=$content_url?>', '<?=$child_tree_url?>', <?=$cat['id']?>, <?=$cat['level']?>);return false;">
                <span unselectable="on" id=""><?=$cat['name']?></span>
            </a>
        </div>
        <ul style="display: none;" class="x-tree-node-ct"></ul>
    </li>
<?php endif; ?>
</li>
<?php endforeach; ?>
