<div id="nav">
    <ul id="MenuBar1" class="MenuBarHorizontal">
        <?php if ($user): ?>
        <li class="border" style="width:105px;padding-right:0">
            <span>
            <a href="<?=site_url($home)?>" title="<?=lang('home')?>" style="padding: 0px;"><img src="<?=base_url()?>static/images/mallerp-system-logo.png" height="25" style="vertical-align:middle; padding: 3px 0px;"></a>
            </span>
        </li>
        <?php endif;?>
        <?php foreach ($nav as $head => $items): ?>
        <li class="border down">
            <span>
                <a href="#"class="MenuBarItemSubmenu">
                    <?=lang($head)?>
                </a>
            </span>
            <ul>
                <?php foreach ($items as $key => $value): ?>
                <li>
                    <?php if (is_array($value)): ?>
                        <?php if (count($value) > 1): ?>
                            <a href="#"  class="MenuBarItemSubmenu"><?=lang($key)?></a>
                            <ul>
                            <?php foreach ($value as $inner_key => $inner_value): ?>
                                <li>
                                    <a href="<?= site_url($inner_key); ?>"><?=lang($inner_value)?></a>
                                </li>
                            <?php endforeach; ?>
                            </ul>
                        <?php else: ?>
                            <?php list($uri, $val) = each($value); ?>
                            <a href="<?=site_url($uri);?>"><?=lang($val)?></a>
                        <?php endif; ?>
                    <?php else: ?>
                    <a href="<?= site_url($key); ?>"><?=lang($value)?></a>
                    <?php endif; ?>
                </li>
                <?php endforeach; ?>
            </ul>
        </li>
        <?php endforeach; ?>
    <li style="float:right; _width:120px;" class="border">
        <span>
            <?php if ($user): ?>
            <a href="<?= site_url('authenticate/logout'); ?>">
            <?=lang('logout')?>(<?= get_current_user_name()?>)</a>
            <?php else: ?>
            <a href="<?= site_url(''); ?>" >
                <?=lang('login')?>

            </a>
            <?php endif; ?>
        </span>
    </li>

    <div style="clear:both;"></div>
    </ul>
</div>

<script type="text/javascript">
    <!--
    var MenuBar1 = new Spry.Widget.MenuBar("MenuBar1");
    //-->
</script>
