<div id="page:left" class="side-col">
    <div class="categories-side-col">
        <div class="tree-actions">
            <a onclick="collapse_tree(); return false;" href="#"><?=lang('collapse_all')?></a>
            <span class="separator">|</span>
            <a onclick="expand_tree(); return false;" href="#"><?=lang('expand_all')?></a>
        </div>
        <div class="tree-holder">
            <div style="width: 100%; overflow: auto;" id="tree-div" class=" x-tree">
                <ul class="x-tree-root-ct x-tree-lines" id="ext-gen5">
                    <div class="x-tree-root-node">
                        <li class="x-tree-node">
                            <div class="x-tree-node-el folder active-category x-tree-node-expanded" id="extdd-1">
                                <span class="x-tree-node-indent"></span>
                                <img class="x-tree-ec-icon x-tree-elbow-minus" onclick="toggle_tree(this, '', -1, 1, this); return false;"src="<?=base_url()?>static/images/tree/spacer.gif" id="ext-gen20">
                                <img unselectable="on" class="x-tree-node-icon" src="<?=base_url()?>static/images/tree/spacer.gif" id="ext-gen17">
                                <a tabindex="1" href="#" hidefocus="on" id="ext-gen14" onClick="update_content(this, '<?=$content_url?>', -1);return false;">
                                    <span unselectable="on" id="extdd-2">Root</span>
                                </a>
                            </div>
                            <ul style="" class="x-tree-node-ct">
                                <?=$tree?>
                            </ul>
                        </li>
                    </div>
                </ul>
            </div>
        </div>
    </div>
</div>