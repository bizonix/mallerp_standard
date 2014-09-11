<?php
class Cache_model extends Base_model
{
    public function clear_user_cache()
    {
        try
        {
            $this->cache->file->delete_by_tag('lower_priority_users_');
            $this->cache->file->delete_by_tag('user_priority_');
        }
        catch (Exception $e)
        {

        }
    }

    public function clear_product_cache_by_sku($sku)
    {
        try
        {
            $this->cache->file->delete('product_sku_' . $sku);  // product sku cache
            $this->cache->file->delete('product_by_sku_' . $sku);  // product object cache by sku
            $this->cache->file->delete('product_name_name_cn' . $sku);
            $this->cache->file->delete('product_name_name_en' . $sku);
            $this->cache->file->delete('product_sale_status_' . $sku);
            $this->cache->file->delete('product_packing_material_id_' . $sku);
            $this->cache->file->delete('product_price_by_sku_' . $sku);
            $this->cache->file->delete('product_market_model_' . $sku);
        }
        catch (Exception $e)
        {

        }
    }

    public function clear_product_sale_status_cache()
    {
        try
        {
            $this->cache->file->delete_by_tag('product_sale_status_');
        }
        catch (Exception $e)
        {

        }
    }

    public function clear_packing_material_id_by_sku($packing_material_id)
    {
        try
        {
            $this->cache->file->delete('packing_material_' . $packing_material_id);
        }
        catch (Exception $e)
        {

        }
    }

    public function clear_product_cache_by_id($id)
    {
        try
        {
            $this->cache->file->delete('product_purchaser_id_' . $id);
            $this->cache->file->delete('product_developer_id_' . $id);
            $this->cache->file->delete('product_tester_id_' . $id);
            $this->cache->file->delete('product_id_' . $id);
        }
        catch (Exception $e)
        {

        }
    }
}

?>
