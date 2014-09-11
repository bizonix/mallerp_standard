<table cellpadding="0" cellspaceing="0" border="0" style="width:98%">
    <tr>
        <td class="td" valign="top">
            <ul>
                <li>
                    <?php $url = site_url('mallerp/change_language'); ?>
                    <select name="language" onchange="helper.change_lang('<?=$url?>', this.value);">
                        <option selected="selected" value="0"><?=lang('choose_language')?></option>
                        <option value="english">english</option>
                        <option value="chinese">简体中文</option>
                    </select>
                </li>
                <?php if (count($groups) > 1): ?>
                <li>
                    <a onclick="return helper.modal(this, '<?=lang('choose_system')?>');"
                        href="<?=site_url('mallerp/system_choosing', array(1))?>">
                        <?=lang('choose_system')?>
                    </a>
                </li>
                <?php endif; ?>
                <li>
                    <?php if ($user): ?>
                    <a href="<?= site_url('authenticate/logout'); ?>"><?=lang('logout')?></a>
                    <?php else: ?>
                    <a href="<?= site_url(''); ?>"><?=lang('login')?></a>
                    <?php endif; ?>
                </li>
            </ul>
        </td>
        <?php foreach ($nav as $head => $items): ?>
        <td class="td_sep" valign="top">
            <h3><?=lang($head)?></h3>
            <ul>
                <?php foreach ($items as $key => $value): ?>
                <li><a href="<?= site_url($key); ?>"><?=lang($value)?></a></li>
                <?php endforeach; ?>
            </ul>
        </td>
        <?php endforeach; ?>
    </tr>
</table>