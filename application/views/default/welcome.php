<?php
    $CI = &get_instance();

    $group_ids = $CI->user_model->fetch_groups_by_user_id();
    
    if (! isset($CI->home_setting_model))
    {
        $CI->load->model('home_setting_model');
    }

    $show_home_info = '';
    
    $priority = $CI->user_model->fetch_user_priority_by_system_code($CI->default_system());
    
    foreach ($group_ids as $group_id)
    {
        $show_home_info .= $CI->home_setting_model->get_block_html_by_group_id($group_id);
    }
    
    if($show_home_info)
    {
        if($priority >= 2)
        {
            $show_home_info = $CI->home_setting_model->get_important_message_by_group_code($CI->default_system()) . "<br><br>" . $show_home_info;
        }
        
        echo block_notice_div($show_home_info);
    }
    else
    {
        if($priority >= 2)
        {
            echo block_notice_div($CI->home_setting_model->get_important_message_by_group_code($CI->default_system()));
        }
    }
    
?>


<div id="message_popup" style="right: 0px; top: 0px; display: none">
</div>

<?php

/*

$CI = & get_instance();
$CI->is_super_user();
$systems = $CI->user_model->fetch_current_system_codes();

for($i = 0; $i < count($systems); $i++)
{
    switch ($systems[$i])
    {
        case 'purchase':
        {          
            $priority = $CI->user_model->fetch_user_priority_by_system_code('purchase');
            if('1' == $priority )
            {               
                list($head, $data) = fetch_list_to_be_purchase();                
                echo block_entry(lang('list_to_be_purchase'), block_table($head, $data), NULL, TRUE);                                          
                list($head, $data) = fetch_product_to_be_edit();
                echo block_entry(lang('product_to_be_edit'), block_table($head, $data), NULL, FALSE);              
            }

            if('2'== $priority)
            {               
                list($head, $data) = list_to_be_review();
                echo block_entry(lang('list_to_be_review'), block_table($head, $data), NULL, TRUE);
                list($head, $data) = fetch_list_to_be_purchase();
                echo block_entry(lang('list_to_be_purchase'), block_table($head, $data), NULL, FALSE);
                list($head, $data) = fetch_product_to_be_edit();
                echo block_entry(lang('product_to_be_edit'), block_table($head, $data), NULL, FALSE); 

            }

        }
        break;
        case 'shipping':
        {   
            list($head, $data) = to_be_order_check();
            echo block_entry(lang('list_to_be_order_check'), block_table($head, $data), NULL, TRUE);
            $priority = $CI->user_model->fetch_user_priority_by_system_code('shipping');        
            if('0'== $priority)
            {
                list($head, $data) = fetch_product_to_be_edit();
                echo block_entry(lang('product_to_be_edit'), block_table($head, $data), NULL, FALSE);
            }         
            
        }
        break;
        case 'order':
        {
            $priority = $CI->user_model->fetch_user_priority_by_system_code('order');
            if('1' == $priority)
            {
                list($head, $data) = fetch_wait_for_confirmation_orders();
                echo block_entry(lang('order_list_to_be_confirm'), block_table($head, $data), NULL, FALSE);
                list($head, $data) = fetch_wait_for_holded_orders();
                echo block_entry(lang('order_list_to_be_holded'), block_table($head, $data), NULL, FALSE);
                list($head, $data) = approved_resending_orders();
                echo block_entry(lang('approved_resending_orders'), block_table($head, $data), NULL, FALSE);
            }
                                             
        }

    }
}
    */

?>
