<div class="viewport">
    <div style="margin-top: 16px;"></div>
    <form action="<?php echo site_url('authenticate/login'); ?>" method="post">

        <table cellspacing="0" cellpadding="0" border="0" align="center" style="width: 560px;" class="tableBasic">
            <tbody>
                <tr>
                    <td align="right" style="width: 35%;" class="tableCellTwo">
                        <b></b>
                    </td>
                    <td style="width: 75%; padding-left: 10px;" class="tableCellTwo">
                        <?php $url = site_url('mallerp/change_language'); ?>
                    <select name="language" onchange="helper.change_lang('<?=$url?>', this.value);">
                        <option value="english">English</option>
                        <option <?php if (isset($language) && $language == 'chinese') echo 'selected="selected"'; ?> value="chinese">简体中文</option>
                    </select>
                        <img src="<?=base_url()?>static/images/mallerp-system-logo.png">
                    </td>

                </tr>
                <tr>
                    <td align="right" style="width: 35%;" class="tableCellTwo">
                        <b><?=lang('username')?></b>
                    </td>
                    <td style="width: 75%; padding-left: 10px;" class="tableCellTwo">
                        <input type="text" maxlength="50" value="" size="20" name="username" class="input" style="width: 80%;">
                    </td>

                </tr>
                <tr>

                    <td align="right" class="tableCellTwo"><b><?=lang('password')?></b></td>
                    <td style="padding-left: 10px;" class="tableCellTwo">
                        <input type="password" maxlength="32" value="" size="20" name="password" class="input" style="width: 80%;">
                    </td>
                </tr><tr>

                    <td class="tableCellOne buttonRow">&nbsp;
                        
                    </td>
                    <td style="padding-right:65px;" align="right" class="tableCellOne buttonRow">
                    <?php echo block_button(
                        array(
                            'type'      => 'submit',
                            'value'     => lang('login'),
                            'name'      => 'submit',
                            'style'     => "padding: 3px;",
                        )
                    );
                    ?>

                </tr>
            </tbody></table>

    </form>
</div>
