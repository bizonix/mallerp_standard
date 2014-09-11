<div class="viewport">
    <div style="margin-top: 36px;"></div>
        <table cellspacing="0" cellpadding="0" border="0" align="center" class="tableBasic" style="width: 560px;">
            <tbody>
                <tr>
                <?php $count = 0; foreach ($groups as $group): ?>
                    <?php $count++; $bind = $group['bind']; ?>
                    <?php $url = site_url($bind . '/index/index'); ?>
                    <td align="center" class="tableCellTwo" style="width: 35%;">
                        <a href="<?=$url?>">
                            <img src="<?=base_url()?>static/images/symbol/<?=$bind?>.png"/><br/>
                        </a>
                        <b><?=$group['system']?></b>
                    </td>
                    <?php if ($count % 4 == 0): ?>
                </tr>
                <tr>
                    <?php endif; ?>
                <?php endforeach; ?>
                </tr>
            </tbody></table>
</div>