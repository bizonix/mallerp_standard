<ul id="nav">
	<li class="top">
        <a href="<?=site_url($home)?>" class="top_link">
            <span><?=lang('home')?></span>
        </a>
    </li>
    <?php foreach ($nav as $head => $items): ?>
	<li class="top">
        <a href="#" id="shop" class="top_link">
            <span class="down"><?=lang($head)?></span>
        </a>
		<ul class="sub">
            <?php foreach ($items as $key => $value): ?>
			<li>
                <?php if (is_array($value)): ?>
                    <?php if (count($value) > 1): ?>
                        <a href="#" class="fly"><?=lang($key)?></a>
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
    <li class="top">
        <?php if ($user): ?>
        <a href="<?= site_url('authenticate/logout'); ?>" class="top_link">
        <span><?=lang('logout')?></span></a>
        <?php else: ?>
        <a href="<?= site_url(''); ?>"  class="top_link">
            <span><?=lang('login')?></span>
        </a>
        <?php endif; ?>
    </li>
</ul>