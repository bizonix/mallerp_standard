<?php $current_uri = fetch_request_uri(); ?>
<div id="page:left" class="side-col">
    <div class="switcher">
    <span class="title"><h3><?=lang('options')?></h3></span>
    </div>
    <ul class="tabs config-tabs" id="system_config_tabs">
        <?php foreach ($links as $title => $link): ?>
        <li>
            <dl>
                <dt style="" class="label"><?=lang($title)?></dt>
                <?php $link_count = count($link); $i = 0; ?>
                <?php foreach ($link as $uri => $span): ?>
                    <?php $class = $uri == $current_uri ? ' active ' : ''; ?>
                    <?php $class .= ($link_count == $i+1) ? ' last ' : ''; ?>
                <dd>
                    <a class="<?=$class?>" href="<?=site_url($uri)?>">
                        <span>
                            <?=lang($span)?>
                        </span>
                    </a>
                </dd>
                <?php $i++; ?>
                <?php endforeach; ?>
            </dl>
        </li>
        <?php endforeach; ?>
    </ul>
</div>