-- phpMyAdmin SQL Dump
-- version 3.3.9.2
-- http://www.phpmyadmin.net
--
-- 主机: localhost
-- 生成日期: 2014 年 09 月 11 日 14:12
-- 服务器版本: 5.5.38
-- PHP 版本: 5.5.14

SET SQL_MODE="NO_AUTO_VALUE_ON_ZERO";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8 */;

--
-- 数据库: `mallerp`
--

-- --------------------------------------------------------

--
-- 表的结构 `aliexpress_token`
--

CREATE TABLE IF NOT EXISTS `aliexpress_token` (
  `aliid` varchar(25) COLLATE utf8_unicode_ci DEFAULT NULL,
  `resource_owner` varchar(25) COLLATE utf8_unicode_ci DEFAULT NULL,
  `refresh_token` varchar(100) COLLATE utf8_unicode_ci DEFAULT NULL,
  `access_token` varchar(100) COLLATE utf8_unicode_ci DEFAULT NULL,
  `appkey` varchar(25) COLLATE utf8_unicode_ci DEFAULT NULL,
  `appsecret` varchar(100) COLLATE utf8_unicode_ci DEFAULT NULL,
  `code_sign` varchar(100) COLLATE utf8_unicode_ci DEFAULT NULL,
  `access_token_update` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  UNIQUE KEY `aliid` (`aliid`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

--
-- 转存表中的数据 `aliexpress_token`
--


-- --------------------------------------------------------

--
-- 表的结构 `amazon_ack_failed`
--

CREATE TABLE IF NOT EXISTS `amazon_ack_failed` (
  `amazonorderid` varchar(30) NOT NULL,
  `sellerid` varchar(100) NOT NULL,
  KEY `amazonorderid` (`amazonorderid`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

--
-- 转存表中的数据 `amazon_ack_failed`
--


-- --------------------------------------------------------

--
-- 表的结构 `auto_country_amount`
--

CREATE TABLE IF NOT EXISTS `auto_country_amount` (
  `id` int(10) NOT NULL AUTO_INCREMENT,
  `country` varchar(50) NOT NULL,
  `amount` float NOT NULL,
  `created_date` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COMMENT='国家金额自动确认表' AUTO_INCREMENT=1 ;

--
-- 转存表中的数据 `auto_country_amount`
--


-- --------------------------------------------------------

--
-- 表的结构 `ci_sessions`
--

CREATE TABLE IF NOT EXISTS `ci_sessions` (
  `session_id` varchar(40) NOT NULL DEFAULT '0',
  `ip_address` varchar(16) NOT NULL DEFAULT '0',
  `user_agent` varchar(50) NOT NULL,
  `last_activity` int(10) unsigned NOT NULL DEFAULT '0',
  `user_data` text,
  PRIMARY KEY (`session_id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

--
-- 转存表中的数据 `ci_sessions`
--

INSERT INTO `ci_sessions` (`session_id`, `ip_address`, `user_agent`, `last_activity`, `user_data`) VALUES
('b705aab562c0c57a5a41367a354e7f62', '192.168.1.100', 'Mozilla/5.0 (Windows NT 6.1; WOW64) AppleWebKit/53', 1410444721, 'a:240:{s:13:"mallerp/login";s:40:"116831832c621956f1717a3eb646bb57c0ae9429";s:18:"authenticate/login";s:40:"88b5db44baceff912efa97174567af37301ebba5";s:23:"mallerp/change_language";s:40:"2d30af22f5e33cddd12ed3dba8186feeee6451e4";s:22:"message/fetch_messages";s:40:"61e97744f72e7c21175cc198785c41682b6c3aac";s:7:"account";a:4:{s:4:"name";s:9:"赵森林";s:10:"login_name";s:5:"admin";s:2:"id";s:1:"1";s:11:"system_code";a:0:{}}s:14:"default_system";s:5:"admin";s:12:"mallerp/home";s:40:"d066133a09bf5809cbd2516616c0f3ec6917887e";s:29:"seo/resource/resource_catalog";s:40:"39fa070d189e645268c3ff02280684d7a3ef9b2d";s:34:"seo/resource/view_resource_catalog";s:40:"11c8b25f1b6c0de4e51bea7231266d2dba4e7b2f";s:21:"seo/resource/add_edit";s:40:"c65b34d1ce6ddae9eb0ec8f68ef0eab2fa664ab8";s:19:"seo/resource/manage";s:40:"f52b3546749d1d7243fbad130200554c9d4034df";s:22:"seo/resource/view_list";s:40:"9673417702f143778846f617053aa5a4b8c745ac";s:23:"seo/resource/csv_upload";s:40:"c235d288f67d2b72413be9b3c22035cb166474c6";s:29:"seo/content_edit/content_type";s:40:"b5c5fb1b0c163454d1978ffd562dd875f6d48603";s:34:"seo/content_edit/view_content_type";s:40:"f2fa23812c5bc9a06f73dab8252ce50ac5d1868b";s:25:"seo/content_edit/add_edit";s:40:"40989d9cdfb310385970e9251297edcefe1dbdfa";s:23:"seo/content_edit/manage";s:40:"8ed22c6d7fbe1d7a84341978cecd5b1d5c1c156a";s:26:"seo/content_edit/view_list";s:40:"fc55845d9273fb78ea94b17451364f03e16602ce";s:24:"seo/seo_keyword/add_edit";s:40:"740a2d78ac7529f306861ef29da0c30b36f9b353";s:22:"seo/seo_keyword/manage";s:40:"4a8458d61df114e071a798637d2258637fd306c9";s:25:"seo/seo_keyword/view_list";s:40:"31ca111eebbb9d2159e74e5424cd01232ba4b2a8";s:18:"seo/release/manage";s:40:"643b05cf170b3b304ceff7db3c4a66f97f419890";s:40:"seo/release/personal_released_management";s:40:"261d76a073dfeb498d7e97564268495d206030cc";s:42:"seo/release/department_released_management";s:40:"2dfb0a229d68bd779d079db54165598c7f1d233a";s:22:"seo/release/csv_upload";s:40:"fba258fc052858a8e03c5862b8faac47dbb3182d";s:26:"seo/release/search_content";s:40:"6211f08620249a0145ef3b2e8b6325327c0b6cb6";s:25:"seo/release/integral_info";s:40:"1c3c0fb7e5ff8056db53747585e96c5f4fde8c75";s:31:"seo/release/integral_statistics";s:40:"a17f6427de67828f28df3f6141284301ec6a0ebc";s:26:"seo/service_company/manage";s:40:"2d5250ad8c65ae69c7cf7fe3e1b16c2e00c6b1f3";s:40:"seo/permission_copy/permission_copy_show";s:40:"aa9d1a710ad15c754c9877238d9ef55a82fd18c9";s:28:"seo/seo_rank/seo_rank_search";s:40:"2036ca9f5369c028ddf7b4fae9460cc1c0c59865";s:42:"seo/email_marketing/email_marketing_manage";s:40:"7da42ef180aaf00a5efb26cba52d2c5903abff42";s:38:"seo/email_search/email_advanced_search";s:40:"d29e50593aff3b5e2d95a1633dd2920725b61251";s:32:"purchase/finance/finance_pending";s:40:"1206883aea20d971b9d05a331fa3b8371c130585";s:35:"finance/finance_order/confirm_order";s:40:"447e7bbfd04f85b49b3ba404372e858b74f95b69";s:25:"finance/rate/rate_setting";s:40:"73ea773edd7c76f1fac6127f4771988bc0b8937b";s:36:"finance/receipt_way/receipt_way_list";s:40:"3aa753849d7ccfd1e7bb299b494f9ed52d772f4e";s:30:"finance/accounting_cost/manage";s:40:"30ded3cfc6a7c6bd614526c014ab7991d137021a";s:33:"finance/accounting_cost/view_list";s:40:"a24891561fe8a674572a8ef046e1a7384a47d405";s:38:"finance/statistics/product_stock_count";s:40:"0d4247ac40f53bc93c4f035b6c0d0c5f6311f727";s:40:"finance/statistics/order_cost_statistics";s:40:"83f0f9ebeec1138a975bfd60a27fbb171a703744";s:21:"sale/netname/add_edit";s:40:"eeaf69d2814871388fbf7031897e040ec6d9f73c";s:19:"sale/netname/manage";s:40:"9e83cbf6896259b02294e997c0869747641101d4";s:30:"sale/netname/manage_wait_goods";s:40:"d8b3ff5b4db38813d53ff9750483e69fd169cac9";s:24:"sale/makeup_sku/add_edit";s:40:"55a78c59013321cb670119d5776a34c2b97caf53";s:22:"sale/makeup_sku/manage";s:40:"18a74907fea3c1f37021c82cdf1d10b905d97623";s:21:"sale/recommend/manage";s:40:"c992c8ab7be31938c7c50a4041ebbef8d69517a0";s:26:"sale/price/calculate_price";s:40:"7ead6d41a96d4c44757d49498d7b3592d9f2eb23";s:27:"sale/setting/paypal_setting";s:40:"59698a74ebb8285e9dba259424c0d11c6e2caff3";s:37:"sale/setting/product_category_setting";s:40:"b2609baa455c9d067d7cf2e6d6d9a903c7bf8733";s:26:"sale/setting/eshop_setting";s:40:"c866aa0a63269f8bd90e7cc7bc5924ae98e20335";s:26:"sale/setting/trade_setting";s:40:"ae98dbcca2afb00db504863bf8b8ba2ddb1c73d5";s:34:"sale/setting/ebay_platform_setting";s:40:"8938bcf1c2268fc74d53ba00bb8401c4f6bf6627";s:28:"sale/sale_order/sale_setting";s:40:"c38f15e426d18029ebcb8f2511a6e0a24a586e28";s:31:"sale/sale_order/sale_order_view";s:40:"f2ee4368d50803805dd3c8a839fa2b1fc47f6514";s:30:"sale/ebay_manage/myebay_manage";s:40:"882ada5f41a4d2f946ab48e90a88f1e6addcbe31";s:38:"sale/ebay_manage/saler_ebay_id_setting";s:40:"8df1eb25a930fe5bc8d54056ba3f07e5f3c931af";s:20:"sale/taobao/comments";s:40:"036dc2552bba88bf4690cccf4e6a1b52f5dd16f7";s:37:"sale/mytaobao_list/taobao_manage_view";s:40:"d3ad8cf1485e7548a8b8b8842a73803906d5d440";s:38:"sale/catalog_sale_statistics/statistic";s:40:"28e16dc86000d4bce0f3e635adeebde21788fec0";s:49:"sale/view_glance_rate/customer_second_glance_rate";s:40:"0ec86a188128ad4df8129dbf0810a9c447bc0014";s:55:"sale/waiting_for_sale_goods/waiting_for_sale_goods_list";s:40:"0ac12d6d589d5a8c5cc94f05486387f3dd724373";s:31:"order/order_pi_manage/pi_manage";s:40:"1011c43e4ccf879b51c5c9b920635fdaf1c94ede";s:33:"order/regular_order/confirm_order";s:40:"a804f1437e43e8d375aaaf6c36dbc9ab9da9dd02";s:30:"order/regular_order/view_order";s:40:"8910b1fbe47006c1db49883657bc11b4dc40ef6c";s:35:"order/regular_order/file_order_view";s:40:"9bf94db57fbfa345829de35d6304123b6b3db6c3";s:34:"order/regular_order/all_order_view";s:40:"997eb8a982826d1d1d64e1bf88d7b8142dccba27";s:37:"order/regular_order/abroad_order_view";s:40:"b8183fcf1ee87235753ebc8c585465c38f5c0706";s:23:"order/regular_order/add";s:40:"6496324546c4849951f6d8174bcf5a1e51f23d84";s:26:"order/regular_order/search";s:40:"be14272f3e3e2ad3527ea0cc5531653fcb3351e6";s:44:"order/regular_order/not_shipping_orders_view";s:40:"b88567680c0b68fdf0a2e3f5da22cc178efd74f9";s:50:"order/confirm_order_condition/wait_for_confirm_sku";s:40:"7bcab16e27bdc4569a576007c364c786e77fafff";s:40:"order/special_order/view_list_import_log";s:40:"7a5f7be0e61907d7e1c4f912686085b01ebe46e0";s:40:"order/special_order/view_list_ack_failed";s:40:"2a31e145a12dea12495eac4251b27159188063ec";s:24:"order/order_check/search";s:40:"ec4d9f1ea023d0124523d9705a652e7b1dc066fb";s:41:"order/order_check/sale_order_check_manage";s:40:"ea45a8e00994739f071eae00a1f5e9082202ee5f";s:33:"order/paypalapi/search_paypal_api";s:40:"58b8053e6493bc046a009b7ba3ef536f472410b6";s:42:"order/special_order/view_list_return_order";s:40:"0bfbd7ffaebd96b052372207e08649abfbe12b1c";s:47:"order/return_order_auditing/auditing_all_orders";s:40:"ae4f0dfefb283cf3369f6bca091968a8ffd3a49f";s:53:"order/return_order_auditing/auditing_big_money_orders";s:40:"a45db59414b3328bdf6fc7585ecb35798e9b7681";s:55:"order/return_order_auditing/auditing_small_money_orders";s:40:"ccd83311e38f57536c16fd72d747c67b90c0e3d9";s:48:"order/return_order_auditing/management_for_order";s:40:"ac0e607e0f848bd77ec877164ab5044d6ce880fb";s:33:"order/paypalapi/refundtransaction";s:40:"1d094dc0914b97f96d1f5bc2fb80ca03c396c937";s:35:"order/regular_order/give_order_back";s:40:"ce576019fa91a2d1591ba2a6693be44331db628f";s:24:"order/blacklist/add_edit";s:40:"39133243d2ce4a9e4fa089a28466d91231eaa74e";s:22:"order/blacklist/manage";s:40:"a149a8a1937b07b85d01c9fc1d0795187ab0c0f5";s:25:"order/blacklist/view_list";s:40:"e5478e4d5edc871b38b9ca3070eb5be14f1f988a";s:26:"order/setting/paypal_email";s:40:"86e8296b22401db2d2987c1c7e372e132a7bdc52";s:23:"order/setting/stmp_host";s:40:"b07e58ed0933de56ffe319cd6255a7e8329eac17";s:28:"order/order_email/view_order";s:40:"86b20901e55f7dc35448ea68260db77dc68d3446";s:43:"order/power_manage/power_management_setting";s:40:"c6de62a3be021c1a00567918d4a42814844d583d";s:35:"order/setting/order_view_permission";s:40:"ecf014d2a88cf17d10f64e9e54991da9d87ea8c4";s:42:"order/profit_rate/profit_rate_view_setting";s:40:"f7b1c82be53dfd6c754e72cfc6573aaf02506e6e";s:20:"order/setting/search";s:40:"377a0503c79a12a84f92ea0067ed92ec3589f5f6";s:44:"order/setting/order_bad_comment_type_setting";s:40:"97e99ca0d45b40bd546cfd3f04e716ac64887caa";s:19:"qt/recommend/manage";s:40:"dc8ec2230e89900c912d7ebc0f0c226f3ad97f07";s:38:"order/order_statistics/sale_statistics";s:40:"a6bfa1c29e45f99f8e74d340450e739bdf76067d";s:41:"order/order_statistics/my_sale_statistics";s:40:"1ba983b80a3416f0788b053db4b984a7a376467b";s:36:"order/order_statistics/customer_rank";s:40:"4c9f2e11929fb69541fae491f3e0c10bf9ec3507";s:31:"order/order_statistics/sku_rank";s:40:"a2592b90b118de65b11bc6553de9fd67b7ec15d7";s:45:"order/statistics_graph/order_count_statistics";s:40:"fad38f3ea25a0e61c2d7bd7b57c8769badd45988";s:35:"order/ebay_comments/comments_manage";s:40:"c03ef0b2c70cf45c9c753b59465893076d67673a";s:30:"order/regular_order/aliexpress";s:40:"cd96dd640495a5a92d122d91fd4fca3e8c81771e";s:26:"order/ebay_message/catalog";s:40:"5e8d41fbb452f1d42b6fb90a1bb8a83e2cc1def4";s:27:"order/ebay_message/template";s:40:"9fe984bfec43a850174541c67790549557c00d56";s:25:"order/ebay_message/manage";s:40:"28608930aea94af97163d328cc9b28a023fe5a08";s:27:"admin/system/enable_disable";s:40:"c6a1648644b6fc48e1ac10da6742882198aab71c";s:27:"admin/system/system_setting";s:40:"0870c9b66d911792d5f1d76fb92e1b6c5068eeef";s:37:"admin/aliexpress/get_aliexpress_token";s:40:"4088043e0ac88396593d87c1ca5b21cd5edaa265";s:18:"admin/group/manage";s:40:"15ce6a75356f5ecf033ef8d6181598fd744f6b8f";s:22:"admin/group/permission";s:40:"af0c0bf384072c3d75242d401bfa5b0afd05a84b";s:17:"admin/user/manage";s:40:"0c71bb5efbd7d945a59c4a55a9b339abfea29907";s:23:"admin/role_level/manage";s:40:"60047bce35694d63014f19fc98727ef4edf69138";s:20:"admin/message/manage";s:40:"f720c1cb2123b846b3eb8ae9da4760c38069e9f6";s:35:"admin/template/shipped_notification";s:40:"2be11d377d0b1a707015dc52cc2ff7d484296ac0";s:27:"admin/crontab/admin_crontab";s:40:"02b76c041fe917645210360fee2664d91e98f8d1";s:15:"edu/catalog/add";s:40:"b247591a329316d5057e2d815f0944d56a2b5199";s:18:"edu/catalog/manage";s:40:"1ea71e49e089508709241321969342cba7118f88";s:29:"myinfo/myaccount/staff_manage";s:40:"f16ab6e65173be6c68332d6d49302c2b2be4886b";s:31:"myinfo/myaccount/add_expire_day";s:40:"ae49b5d55349fddbe467d80995d45e9e26bc261f";s:34:"myinfo/myaccount/expire_day_manage";s:40:"96449191e5d4ed565df12be7c2eac842d5e2401e";s:26:"purchase/order/product_how";s:40:"3298b82ea730fff2ab96c4fbfd4244f13c0425f0";s:16:"qt/recommend/add";s:40:"325ece01a0ac1da392d824cb816dc836d55e6fa8";s:19:"qt/recommend/search";s:40:"0af756cebf5bee65f5588627294ffb78a51cfc53";s:40:"qt/wait_for_product_list/view_list_pages";s:40:"aa2c2dd6362d4f5a7e9b555c96b11a94879e7148";s:28:"stock/statistics/stock_check";s:40:"e90272c066844041237c75c7436a59500469013b";s:33:"stock/statistics/pick_up_products";s:40:"230b553efb6acaef5a6c5c8ca1ee7a1f6d252a6f";s:36:"stock/statistics/personal_statistics";s:40:"c6921fdab7f430c7f63f302d20388d02421d3e5e";s:38:"stock/statistics/department_statistics";s:40:"b97d092728699f52b4785156e27a71ec4a54e1a6";s:20:"stock/inout/outstock";s:40:"8cee4e2078f0db6f28bc795451ed9a24f0776182";s:32:"stock/inout/outstock_type_manage";s:40:"8e341207b29e40f2b4c5170ed0e503a91c8ce7fd";s:25:"stock/inout/instock_apply";s:40:"70932ee1a09e4b59408a79d38d33a6e9f14c7d17";s:26:"stock/inout/instock_verify";s:40:"ddc60289dadded2ce1c241665943a54b068200d4";s:27:"stock/inout/outstock_record";s:40:"81ea6fcbc4a1fa847bc32544a624fa942ad05444";s:26:"stock/inout/instock_record";s:40:"bd6d5b97de309bb32b1a02e19165ebfa02b7519f";s:30:"stock/inout/inout_stock_record";s:40:"1c8e023c1902f4229ba844fc29f24e08242d58b5";s:28:"stock/inout/instock_by_label";s:40:"4e8879b53081bbc8a309a5f6c9fd513e3e512607";s:26:"stock/inout/quick_in_stock";s:40:"0b39128b40a9fddb1dfe7173285ad7212be157d3";s:27:"stock/inout/quick_out_stock";s:40:"be21f56d915e6cd045b9b23a3b8c23470b20f980";s:25:"stock/inout/print_sku_bar";s:40:"7a36539c71432e4c4c33d1abe14d43307946396d";s:51:"shipping/deliver_management/wait_for_shipping_label";s:40:"50690772416d6c08e69692daa7f6624f909bea40";s:51:"shipping/deliver_management/before_late_print_label";s:40:"e853acd16645252a8e677da283c85cee457f4f88";s:49:"shipping/deliver_management/shipping_confirmation";s:40:"0f84f06a74e67f1e8bbfc51f1f0849199b2b6fa8";s:56:"shipping/deliver_management/wait_for_purchase_order_list";s:40:"da6d5be5b12e47d3c54bc4ef64f5113e7ea0d11b";s:44:"shipping/deliver_management/print_or_deliver";s:40:"f99692c179aaa5dd8bb50f15d2e867b9c295cac1";s:40:"stock/stock_check/waiting_check_or_count";s:40:"f4a3ad39ea54473d4942c7b41d5ec714214a8aa5";s:41:"stock/stock_check/check_or_count_recorder";s:40:"7193176d9c92341534376e2cceb3ae91a9dd3421";s:42:"stock/stock_check/stock_differences_review";s:40:"123e9c8ea32d1f3ddabb1e5372fb445728e8a371";s:35:"stock/stock_check/update_shelf_code";s:40:"c988d6f2f23dbb4a256e33ff093733dcc3f2f603";s:36:"stock/stock_check/import_stock_count";s:40:"ea4bd5e88d720ad8b1c87d1accf0c98e60cd6fe5";s:35:"stock/stock_code/stock_code_setting";s:40:"82d98182266f6890dc8e7eccd8df0bc75680978a";s:38:"stock/abroad_stock/in_store_apply_page";s:40:"66d42cbb2b582368e4bd91ca575fa6b54dc5dcb9";s:58:"shipping/deliver_management/before_late_print_label_abroad";s:40:"f3f7ef220b980c4e71d727bd9d7c9eaca86be0ec";s:63:"shipping/deliver_management/wait_for_purchase_order_list_abroad";s:40:"6424723f78793b123900154e65453b00e2480435";s:26:"stock/move_stock/move_form";s:40:"c1b193f6aaaa90e8ab2e6e17d82733ec53377a9a";s:32:"stock/move_stock/confirm_arrival";s:40:"25b4b62c286df3a77a710d562e3fec391e3d212d";s:58:"stock/waiting_for_stock_goods/waiting_for_stock_goods_list";s:40:"02c3b8c8a3333d478bc4f9c378f5d090f4837860";s:38:"order/return_order_auditing/management";s:40:"17da8561d51413bf15f550ab52c10567783de09f";s:36:"stock/ebay_product/ebay_comment_list";s:40:"27096b63d8dcf645b2c0d50473ca620485ec93d4";s:21:"purchase/provider/add";s:40:"219076e07b1ae6f5dd48e0901cee11cbc3b1f4fb";s:28:"purchase/provider/management";s:40:"d27286fa8ae59bb06f9f3159cb6b7422929154b4";s:27:"purchase/provider/view_list";s:40:"8652661e7bc7930706192cd2599975ecdaec6d2b";s:32:"purchase/purchase_list/view_list";s:40:"f2b0b14c085c6d5cc8d834acc068b1e62ab8512f";s:21:"purchase/order/manage";s:40:"f159821c137e7c7e64537ea7460b97f8007106fc";s:24:"purchase/order/view_list";s:40:"59c931d251c8d0849401b8f7c2b24a4a390998a3";s:28:"purchase/order/pending_order";s:40:"c4a4ba298c8b557e729a8e612d1149882811c12b";s:38:"purchase/order/for_the_purchase_orders";s:40:"eb7c8f82294e94db5cd5f2028b879f26f40857cc";s:41:"purchase/order/my_for_the_purchase_orders";s:40:"c3867e867f41e3c745fea88ae825fe38ba177dcb";s:30:"purchase/purchase_apply/manage";s:40:"a4eba2fa08172d4e57b7251bb93b5826a4006780";s:39:"purchase/statistics/purchase_statistics";s:40:"e1f12482f56d1fe1a4422dcb545867fe5ac98e01";s:50:"purchase/statistics/department_purchase_statistics";s:40:"c0f0e85836c79b35e6223d3baaf5769045f94e75";s:44:"purchase/statistics/purchase_sale_statistics";s:40:"57a87645b59a66bab723793c465199fc5f5e4d1a";s:55:"purchase/statistics/department_purchase_sale_statistics";s:40:"5b07791617b0837d31078773b2db26e18fc59849";s:51:"purchase/statistics/personal_development_statistics";s:40:"1f08b6be7fea9048e10c6337fd899b7aae9ccd33";s:49:"purchase/statistics/develop_department_statistics";s:40:"750cebfd258dd86f6ba345f95fb6fe4636a3fc92";s:50:"purchase/statistics/purchase_department_statistics";s:40:"b8dce7ce66bf382fbd42f5208511d7bf348fa7ae";s:45:"purchase/statistics/department_ito_statistics";s:40:"e25ed39692ed0467d53bcbadce31d434c5860f2b";s:83:"purchase/waiting_for_perfect_purchase_goods/waiting_for_perfect_purchase_goods_list";s:40:"86b01b9abd8fe5c7067da681457a0f47a5c0ae56";s:14:"pi/catalog/add";s:40:"2fda27114882e74da6c55387bbd69358482d93fb";s:17:"pi/catalog/manage";s:40:"4236b5e876f1a45bd4816b7bc1c565b9312e3631";s:38:"pi/catalog/set_catalog_sale_permission";s:40:"3f3220efd4c10bdf5edc5d6b45218c4e6dd5c609";s:19:"pi/product/add_edit";s:40:"7e6acf96984c4ffe482f580545ef1e69cbffe99a";s:17:"pi/product/manage";s:40:"c2807212712a321d574ed46d9fa36bd389e33ecf";s:20:"pi/product/view_list";s:40:"87faaf174474dc4eda7f2acde892150ed58121b3";s:25:"pi/product/import_product";s:40:"8eddef4ceaa3f48fa7fa4bb140c3e03064889a9d";s:32:"pi/product/set_product_purchaser";s:40:"0dad4b296eaf252b1db048830c824517e74c6584";s:31:"pi/product/import_product_stock";s:40:"8be7a3f6b509bd5a5d1e40d97878865ee827c080";s:18:"pi/permission/edit";s:40:"ef432ad48eb2941239ccf97b15f7cf37c4cd45a5";s:24:"pi/purchase_apply/manage";s:40:"1f0290117de7f5673ebd8feb7a106b9e9b048570";s:28:"pi/setting/delete_permission";s:40:"4fd691ef04b64ec8d02d56b021920ea9fb233374";s:48:"pi/shelf_code_setting/product_shelf_code_setting";s:40:"3c7aa1ed5ffbe939860ed78d49dbe7fa8c1609c7";s:33:"pi/home_setting/show_home_setting";s:40:"d941bd671474fb50691b93c741e0c2fb9bd060dc";s:54:"pi/waiting_for_perfect_goods/waiting_for_pi_goods_list";s:40:"fbf2c733d1cea51085386c25f8e135a0b4a8402c";s:53:"pi/waiting_for_update_pic/waiting_for_update_pic_list";s:40:"112c9655bdfb80de9f9866b64f339c6b9545a4f7";s:34:"shipping/shipping_company/add_edit";s:40:"7df7506bae0e6d57eb4e3e3186bbe52a16e79e00";s:32:"shipping/shipping_company/manage";s:40:"76784a38b09a194b70286543eb59404a941509f8";s:35:"shipping/shipping_company/view_list";s:40:"c094ba35c856e44cc15f6c897d25b056cde63282";s:31:"shipping/shipping_type/add_edit";s:40:"8ac89497f376477ad1ba195c7e0b632ca9aed28b";s:29:"shipping/shipping_type/manage";s:40:"9150854ccda361ceab5d2041d57e2081b3b809af";s:32:"shipping/shipping_type/view_list";s:40:"54764bbfd1508e963a17bb5155347b1840f7ce60";s:40:"shipping/shipping_subarea_group/add_edit";s:40:"c99700be69d99cbc771474a588429e639a901922";s:38:"shipping/shipping_subarea_group/manage";s:40:"e1e697a3070a0c14eb5234a3cd36176abd9f3b62";s:41:"shipping/shipping_subarea_group/view_list";s:40:"05d25297b3daaffde6871270536d86a3a61b803e";s:34:"shipping/shipping_subarea/add_edit";s:40:"4c8d1e13d09ab4cdf78c0c2e2cc23b91ef868200";s:32:"shipping/shipping_subarea/manage";s:40:"ee42dd429ee2851cd42be795a652bd45a860a541";s:35:"shipping/shipping_subarea/view_list";s:40:"0a8c2a5bff9e874cec168db1bebfcb85429397cc";s:50:"shipping/shipping_company/calculate_shipping_price";s:40:"8548c6761acf8d512d920e51eb84a8b415898284";s:14:"pi/packing/add";s:40:"0b8a367ae7cf243a5303a23d310c4f653154a378";s:17:"pi/packing/manage";s:40:"0b5f69ea30522ba362eb9884391ff50689db1777";s:47:"shipping/deliver_management/import_track_number";s:40:"a2649deee6cc47d64da91be5b268e885c0658991";s:48:"shipping/deliver_management/import_shipping_cost";s:40:"a6b04fe396109e51fde422c04ac71e6f408bb905";s:35:"shipping/deliver_management/epacket";s:40:"96447941f4fd091347818db57272c628500a4796";s:47:"shipping/deliver_management/for_the_wish_orders";s:40:"00a0e63bf5ceec63801954ae96c1e960b2353d69";s:55:"shipping/deliver_management/update_shipping_information";s:40:"3e625642b23cdb5896834c0ac9aa9f901e6abfbd";s:29:"shipping/shipping_code/search";s:40:"148a8ebb64c0a9537615bcb78a620929b93d5bc8";s:46:"shipping/deliver_statistics/package_statistics";s:40:"2966fa6f15abcf3b227d9222f1464e64e77a0a5f";s:57:"shipping/deliver_statistics/department_package_statistics";s:40:"d05be8dc77694376c0872ed86869fcd4b11bef42";s:49:"shipping/deliver_statistics/shipping_all_download";s:40:"7ef92e358a519c8c8c572ec75437e7cccc115c1d";s:42:"shipping/epacket_config/add_epacket_config";s:40:"fcd8611ad709ed3cf618593a90dbb30d6b171528";s:30:"shipping/epacket_config/manage";s:40:"9fa14488f2c0807c4d2dacb68ff6d6804a337783";s:45:"order/order_check/shipping_order_check_manage";s:40:"b19f711a355811992b4922d5a5a9188e4f8926ff";s:47:"shipping/shipping_code/fetch_all_shipping_codes";s:40:"357f27cba34f3717b9df05ed9083454c457dbd06";s:83:"shipping/waiting_for_perfect_shipping_goods/waiting_for_perfect_shipping_goods_list";s:40:"db3d6c0b0bea3776c80e5557892bd6ca3f3034c7";s:25:"order/country_list/manage";s:40:"2a8a422b6e8ed77da8807469565244e8d541e808";s:29:"myinfo/myaccount/view_account";s:40:"afb6dc09483ccdbb4848d75fd35686e9217b0d69";s:45:"myinfo/myaccount/important_message_management";s:40:"0786b6d28ddbdf825f14d480f8b033da39c46cce";s:38:"myinfo/myaccount/return_cost_statistic";s:40:"ae12a0fea7f4722aa5560ed9041d03f50e5101cb";s:24:"myinfo/myaccount/by_type";s:40:"1cbbbb4146f9cbe4e252d0af61e19b8739bcabdb";s:35:"myinfo/myaccount/work_rewards_error";s:40:"8037e03d666c2a74419b4c41eff1fd79b6fd784c";s:40:"myinfo/myaccount/work_rewards_error_edit";s:40:"32f8b1aa764dec638a807161b9fefe7c60c5841e";s:27:"myinfo/myaccount/csv_upload";s:40:"f8bb582b98a1cafc59bc74a7f2c6ea2061c5f927";s:24:"order/purchase_apply/add";s:40:"23a38abfc0b2da908a4c166be6276da51ecdcbd2";s:27:"order/purchase_apply/manage";s:40:"2195e14085b1d39cb540e470db2e4a9150b6aa1f";s:33:"order/purchase_apply/all_reviewed";s:40:"e5586f1267c382340992308a31effa2fc255250b";s:15:"edu/content/add";s:40:"a00f087cb99266192efd34785961973e1e7e40c8";s:18:"edu/content/manage";s:40:"37180bd66a3d5aaee234d4174124078825ff02f6";s:21:"edu/content/view_list";s:40:"01ba796fcbc072813c3f867aae6ed7e8c46db94a";s:19:"authenticate/logout";s:40:"9b6ba4c25e457e299dbb9d3791beff9216d2492c";s:49:"filter_offset_orderorder/regular_order/view_order";i:0;s:48:"filter_total_orderorder/regular_order/view_order";s:1:"0";}');

-- --------------------------------------------------------

--
-- 表的结构 `cky_in_store_case`
--

CREATE TABLE IF NOT EXISTS `cky_in_store_case` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `list_id` int(11) NOT NULL,
  `case_no` int(11) NOT NULL,
  `weight` double NOT NULL,
  `packing` varchar(35) NOT NULL,
  `creator` int(11) NOT NULL,
  `created_date` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;

--
-- 转存表中的数据 `cky_in_store_case`
--


-- --------------------------------------------------------

--
-- 表的结构 `cky_in_store_list`
--

CREATE TABLE IF NOT EXISTS `cky_in_store_list` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `sign` varchar(20) NOT NULL,
  `log_type` varchar(20) NOT NULL,
  `storage_code` varchar(20) NOT NULL,
  `arrive_time` varchar(50) NOT NULL,
  `locale` varchar(100) NOT NULL,
  `remark` varchar(200) NOT NULL,
  `is_collect` tinyint(1) NOT NULL,
  `collect_time` varchar(50) NOT NULL,
  `collect_address` varchar(100) NOT NULL,
  `collect_contact` varchar(50) NOT NULL,
  `collect_phone` varchar(50) NOT NULL,
  `status` int(11) NOT NULL,
  `creator` int(11) NOT NULL,
  `created_date` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;

--
-- 转存表中的数据 `cky_in_store_list`
--


-- --------------------------------------------------------

--
-- 表的结构 `cky_in_store_product`
--

CREATE TABLE IF NOT EXISTS `cky_in_store_product` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `case_id` int(11) NOT NULL,
  `case_no` int(11) NOT NULL,
  `title` varchar(100) NOT NULL,
  `quantity` varchar(100) NOT NULL,
  `declared_name` varchar(100) NOT NULL,
  `declared_price` varchar(50) NOT NULL,
  `creator` int(11) NOT NULL,
  `created_date` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;

--
-- 转存表中的数据 `cky_in_store_product`
--


-- --------------------------------------------------------

--
-- 表的结构 `cky_outstock`
--

CREATE TABLE IF NOT EXISTS `cky_outstock` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `order_sign` varchar(30) NOT NULL,
  `status` tinyint(4) NOT NULL DEFAULT '0',
  `created_date` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;

--
-- 转存表中的数据 `cky_outstock`
--


-- --------------------------------------------------------

--
-- 表的结构 `core_config`
--

CREATE TABLE IF NOT EXISTS `core_config` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `core_key` varchar(50) NOT NULL,
  `value` varchar(100) NOT NULL,
  `updated_date` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 AUTO_INCREMENT=4 ;

--
-- 转存表中的数据 `core_config`
--

INSERT INTO `core_config` (`id`, `core_key`, `value`, `updated_date`) VALUES
(1, 'debug_mode', '0', '2013-10-04 20:39:46'),
(2, 'customer_notification_mode', '0', '2013-08-10 22:03:37'),
(3, 'customer_notification_dev_mode_email', '7410992@qq.com', '2011-11-13 08:40:28');

-- --------------------------------------------------------

--
-- 表的结构 `country_code`
--

CREATE TABLE IF NOT EXISTS `country_code` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `code` varchar(10) NOT NULL DEFAULT '',
  `code3` varchar(10) NOT NULL,
  `name_en` varchar(50) NOT NULL DEFAULT '',
  `name_cn` varchar(50) NOT NULL DEFAULT '',
  `continent_id` int(11) NOT NULL,
  `regular_check_url` varchar(200) NOT NULL,
  `regular_carrier` varchar(50) NOT NULL,
  PRIMARY KEY (`id`),
  KEY `code` (`code`),
  KEY `name_en` (`name_en`),
  KEY `name_cn` (`name_cn`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 COMMENT='国家代码表' AUTO_INCREMENT=227 ;

--
-- 转存表中的数据 `country_code`
--

INSERT INTO `country_code` (`id`, `code`, `code3`, `name_en`, `name_cn`, `continent_id`, `regular_check_url`, `regular_carrier`) VALUES
(1, 'OT', 'OTH', ' OTHERS', '其它', 0, '', ''),
(2, 'AD', 'AND', 'ANDORRA', '安道尔共和国', 0, '', ''),
(3, 'AE', 'ARE', 'UNITED ARAB EMIRATES', '阿拉伯联合酋长国', 0, '', ''),
(4, 'AF', 'AFG', 'AFGHANISTAN', '阿富汗', 0, '', ''),
(5, 'AG', 'ATG', 'ANTIGUA AND BARBUDA', '安提瓜和巴布达', 0, '', ''),
(6, 'AI', 'AIA', 'ANGUILLA', '安圭拉岛', 0, '', ''),
(7, 'AL', 'ALB', 'ALBANIA', '阿尔巴尼亚', 0, '', ''),
(8, 'AM', 'ARM', 'ARMENIA', '亚美尼亚', 0, '', ''),
(9, 'AO', 'AGO', 'ANGOLA', '安哥拉', 0, '', ''),
(10, 'AR', 'ARG', 'ARGENTINA', '阿根廷', 0, '', ''),
(11, 'AT', 'AUT', 'AUSTRIA', '奥地利', 0, '', ''),
(12, 'AU', 'AUS', 'AUSTRALIA', '澳大利亚', 0, '', ''),
(13, 'AZ', 'AZE', 'AZERBAIJAN', '阿塞拜疆', 0, '', ''),
(14, 'BA', 'BIH', 'BOSNIA AND HERZEGOVINA', '波黑共和国', 0, '', ''),
(15, 'BB', 'BRB', 'BARBADOS', '巴巴多斯', 0, '', ''),
(16, 'BD', 'BGD', 'BANGLADESH', '孟加拉国', 0, '', ''),
(17, 'BE', 'BEL', 'BELGIUM', '比利时', 0, '', ''),
(18, 'BF', 'BUR', 'BURKINA-FASO', '布基纳法索', 0, '', ''),
(19, 'BG', 'BGR', 'BULGARIA', '保加利亚', 0, '', ''),
(20, 'BH', 'BHR', 'BAHRAIN', '巴林', 0, '', ''),
(21, 'BI', 'BDI', 'BURUNDI', '布隆迪', 0, '', ''),
(22, 'BJ', 'BEN', 'BENIN', '贝宁', 0, '', ''),
(23, 'BL', 'PLE', 'PALESTINE', '巴勒斯坦', 0, '', ''),
(24, 'BM', 'BMU', 'BERMUDA IS.', '百慕大群岛', 0, '', ''),
(25, 'BN', 'BRN', 'BRUNEI DARUSSALAM', '文莱', 0, '', ''),
(26, 'BO', 'BOL', 'BOLIVIA', '玻利维亚', 0, '', ''),
(27, 'BR', 'BRA', 'BRAZIL', '巴西', 0, '', ''),
(28, 'BS', 'BHS', 'BAHAMAS', '巴哈马', 0, '', ''),
(29, 'BT', 'BHS', 'BHUTAN', '不丹', 0, '', ''),
(30, 'BW', 'BWA', 'BOTSWANA', '博茨瓦纳', 0, '', ''),
(31, 'BV', 'BVT', 'BOUVET ISLAND', '布维特岛', 0, '', ''),
(32, 'BY', 'BLR', 'BELARUS', '白俄罗斯', 0, '', ''),
(33, 'BZ', 'BLZ', 'BELIZE', '伯利兹', 0, '', ''),
(34, 'CA', 'CAN', 'CANADA', '加拿大', 0, '', ''),
(35, 'CF', 'CAF', 'CENTRAL AFRICAN REPUBLIC', '中非共和国', 0, '', ''),
(36, 'CG', 'COD', 'CONGO, Kinshasa', '刚果（金）', 0, '', ''),
(37, 'CH', 'CHE', 'SWITZERLAND', '瑞士', 0, '', ''),
(38, 'CI', 'CIV', 'Côte d''Ivoire', '科特迪瓦共和国', 0, '', ''),
(39, 'CK', 'CCK', 'COCOS ISLANDS', '库克群岛', 0, '', ''),
(40, 'CL', 'CHL', 'CHILE', '智利', 0, '', ''),
(41, 'CM', 'CMR', 'CAMEROON', '喀麦隆', 0, '', ''),
(42, 'CN', 'CHN', 'CHINA', '中国', 0, '', ''),
(43, 'CO', 'COL', 'COLOMBIA', '哥伦比亚', 0, '', ''),
(44, 'CR', 'CRI', 'COSTA RICA', '哥斯达黎加', 0, '', ''),
(45, 'CS', 'CZE', 'CZECH', '捷克', 0, '', ''),
(46, 'CU', 'CUB', 'CUBA', '古巴', 0, '', ''),
(47, 'CV', 'CPV', 'CAPE VERDE', '佛得角共和国', 0, '', ''),
(48, 'CX', 'CXR', 'CHRISTMAS ISLAND', '圣诞岛', 0, '', ''),
(49, 'CY', 'CYP', 'CYPRUS', '塞浦路斯', 0, '', ''),
(50, 'CZ', 'CZE', 'CZECH REPUBLIC', '捷克共和国', 0, '', ''),
(51, 'DE', 'DEU', 'GERMANY', '德国', 0, '', ''),
(52, 'DJ', 'DJI', 'DJIBOUTI', '吉布提', 0, '', ''),
(53, 'DK', 'DNK', 'DENMARK', '丹麦', 0, '', ''),
(54, 'DM', 'DMA', 'DOMINICA', '多米尼克', 0, '', ''),
(55, 'DO', 'DOM', 'DOMINICA REP.', '多米尼加共和国', 0, '', ''),
(56, 'DZ', 'DZA', 'ALGERIA', '阿尔及利亚', 0, '', ''),
(57, 'EC', 'ECU', 'ECUADOR', '厄瓜多尔', 0, '', ''),
(58, 'EE', 'EST', 'ESTONIA', '爱沙尼亚', 0, '', ''),
(59, 'EG', 'EGY', 'EGYPT', '埃及', 0, '', ''),
(60, 'EH', 'ESH', 'WESTERN SAHARA', '西撒哈拉', 0, '', ''),
(61, 'ES', 'ESP', 'SPAIN', '西班牙', 0, '', ''),
(62, 'ET', 'ETH', 'ETHIOPIA', '埃塞俄比亚', 0, '', ''),
(63, 'FI', 'FIN', 'FINLAND', '芬兰', 0, '', ''),
(64, 'FJ', 'FIJ', 'FIJI', '斐济', 0, '', ''),
(65, 'FK', 'FLK', 'FALKLAND ISLANDS', '福克兰群岛', 0, '', ''),
(66, 'FO', 'FRO', 'FAROE ISLANDS', '法罗群岛', 0, '', ''),
(67, 'FR', 'FRA', 'FRANCE', '法国', 0, '', ''),
(68, 'GA', 'GAB', 'GABON', '加蓬', 0, '', ''),
(69, 'GB', 'GBR', 'UNITED  KINGDOM', '英国', 0, '', ''),
(70, 'GD', 'GRD', 'GRENADA', '格林纳达', 0, '', ''),
(71, 'GE', 'GEO', 'GEORGIA', '格鲁吉亚', 0, '', ''),
(72, 'GF', 'GUF', 'FRENCH GUIANA', '法属圭亚那', 0, '', ''),
(73, 'GH', 'GHA', 'GHANA', '加纳', 0, '', ''),
(74, 'GI', 'GIB', 'GIBRALTAR', '直布罗陀', 0, '', ''),
(75, 'GM', 'GMB', 'GAMBIA', '冈比亚', 0, '', ''),
(76, 'GN', 'GIN', 'GUINEA', '几内亚', 0, '', ''),
(77, 'GP', 'GLP', 'GUADELOUPE', '瓜德卢普岛', 0, '', ''),
(78, 'GQ', 'GNQ', 'Equatorial Guinea', '赤道几内亚', 0, '', ''),
(79, 'GR', 'GRC', 'GREECE', '希腊', 0, '', ''),
(80, 'GT', 'GTM', 'GUATEMALA', '危地马拉', 0, '', ''),
(81, 'GU', 'GUM ', 'GUAM', '关岛', 0, '', ''),
(82, 'GW', 'GNB', 'GUINEA-BISSAU', '几内亚比绍', 0, '', ''),
(83, 'GY', 'GUY', 'GUYANA', '圭亚那', 0, '', ''),
(84, 'HK', 'HKG', 'HONGKONG', '香港特别行政区', 0, '', ''),
(85, 'HM', 'HMD', 'Heard Island and McDonald Islands', '赫德岛和麦当劳群岛', 0, '', ''),
(86, 'HN', 'HND', 'HONDURAS', '洪都拉斯', 0, '', ''),
(87, 'HR', 'HRV', 'Croatia', '克罗地亚共和国', 0, '', ''),
(88, 'HT', 'HTI', 'HAITI', '海地', 0, '', ''),
(89, 'HU', 'HUN', 'HUNGARY', '匈牙利', 0, '', ''),
(90, 'ID', 'IDN', 'INDONESIA', '印度尼西亚', 0, '', ''),
(91, 'IE', 'IRL', 'IRELAND', '爱尔兰', 0, '', ''),
(92, 'IL', 'ISR', 'ISRAEL', '以色列', 0, '', ''),
(93, 'IN', 'IND', 'INDIA', '印度', 0, '', ''),
(94, 'IO', 'IOT', 'British Indian Ocean Territory', '英属印度洋领地', 0, '', ''),
(95, 'IQ', 'IRQ', 'IRAQ', '伊拉克', 0, '', ''),
(96, 'IR', 'IRI', 'IRAN', '伊朗', 0, '', ''),
(97, 'IS', 'ISL', 'ICELAND', '冰岛', 0, '', ''),
(98, 'IT', 'ITA', 'ITALY', '意大利', 0, '', ''),
(99, 'JM', 'JAM', 'JAMAICA', '牙买加', 0, '', ''),
(100, 'JO', 'JOR', 'JORDAN', '约旦', 0, '', ''),
(101, 'JP', 'JPN', 'JAPAN', '日本', 0, '', ''),
(102, 'KE', 'KEN', 'KENYA', '肯尼亚', 0, '', ''),
(103, 'KG', 'KGZ', 'KYRGYZSTAN', '吉尔吉斯坦', 0, '', ''),
(104, 'KH', 'KHM', 'KAMPUCHEA (CAMBODIA)', '柬埔寨', 0, '', ''),
(105, 'KI', 'KIR', 'KIRIBATI', '基里巴斯', 0, '', ''),
(106, 'KN', 'KNA', 'Saint Kitts and Nevis ', '圣基茨和尼维斯联邦', 0, '', ''),
(107, 'KP', 'PRK', 'NORTH KOREA', '朝鲜', 0, '', ''),
(108, 'KR', 'KOR', 'KOREA', '韩国', 0, '', ''),
(109, 'KT', 'CIV', 'REPUBLIC OF IVORY COAST', '科特迪瓦,象牙海岸', 0, '', ''),
(110, 'KW', 'KWT', 'KUWAIT', '科威特', 0, '', ''),
(111, 'KZ', 'KAZ', 'KAZAKSTAN', '哈萨克斯坦', 0, '', ''),
(112, 'LA', 'LAO', 'LAOS', '老挝', 0, '', ''),
(113, 'LB', 'LBN', 'LEBANON', '黎巴嫩', 0, '', ''),
(114, 'LC', 'LCA', 'ST.LUCIA', '圣卢西亚', 0, '', ''),
(115, 'LI', 'LIE', 'LIECHTENSTEIN', '列支敦士登', 0, '', ''),
(116, 'LK', 'LKA', 'SRI LANKA', '斯里兰卡', 0, '', ''),
(117, 'LR', 'LBR', 'LIBERIA', '利比里亚', 0, '', ''),
(118, 'LS', 'LSO', 'LESOTHO', '莱索托', 0, '', ''),
(119, 'LT', 'LTU', 'LITHUANIA', '立陶宛', 0, '', ''),
(120, 'LU', 'LUX', 'LUXEMBOURG', '卢森堡', 0, '', ''),
(121, 'LV', 'LVA', 'LATVIA', '拉脱维亚', 0, '', ''),
(122, 'LY', 'LBY', 'LIBYA', '利比亚', 0, '', ''),
(123, 'MA', 'MAR', 'MOROCCO', '摩洛哥', 0, '', ''),
(124, 'MC', 'MCO', 'MONACO', '摩纳哥', 0, '', ''),
(125, 'MD', 'MDA', 'MOLDOVA REPUBLIC OF ', '摩尔多瓦', 0, '', ''),
(126, 'MG', 'MDG', 'MADAGASCAR', '马达加斯加', 0, '', ''),
(127, 'MK', 'MKD', 'Macedonia', '马其顿', 0, '', ''),
(128, 'ML', 'MLI', 'MALI', '马里', 0, '', ''),
(129, 'MM', 'MMR', 'BURMA', '缅甸', 0, '', ''),
(130, 'MN', 'MNG', 'MONGOLIA', '蒙古', 0, '', ''),
(131, 'MO', 'MAC', 'MACAO', '澳门', 0, '', ''),
(132, 'MR', 'MRT', 'MAURITANIA', '毛里塔尼亚', 0, '', ''),
(133, 'MS', 'MSR', 'MONTSERRAT IS', '蒙特塞拉特岛', 0, '', ''),
(134, 'MT', 'MALTA', 'MALTA', '马耳他', 0, '', ''),
(135, 'MU', 'MUS', 'MAURITIUS', '毛里求斯', 0, '', ''),
(136, 'MV', 'MDV', 'MALDIVES', '马尔代夫', 0, '', ''),
(137, 'MW', 'MWI', 'MALAWI', '马拉维', 0, '', ''),
(138, 'MX', 'MEX', 'MEXICO', '墨西哥', 0, '', ''),
(139, 'MY', 'MYS', 'MALAYSIA', '马来西亚', 0, '', ''),
(140, 'MZ', 'MOZ', 'MOZAMBIQUE', '莫桑比克', 0, '', ''),
(141, 'NA', 'NAM', 'NAMIBIA', '纳米比亚', 0, '', ''),
(142, 'NC', 'NCL', 'New Caledonia', '新喀里多尼亚', 0, '', ''),
(143, 'NE', 'NER', 'NIGER', '尼日尔', 0, '', ''),
(144, 'NG', 'NGA', 'NIGERIA', '尼日利亚', 0, '', ''),
(145, 'NI', 'NIC', 'NICARAGUA', '尼加拉瓜', 0, '', ''),
(146, 'NL', 'NLD', 'NETHERLANDS', '荷兰', 0, '', ''),
(147, 'NO', 'NOR', 'NORWAY', '挪威', 0, '', ''),
(148, 'NP', 'NPL', 'NEPAL', '尼泊尔', 0, '', ''),
(149, 'NR', 'NRU', 'NAURU', '瑙鲁', 0, '', ''),
(150, 'NZ', 'NZL', 'NEW ZEALAND', '新西兰', 0, '', ''),
(151, 'OM', 'OMN', 'OMAN', '阿曼', 0, '', ''),
(152, 'PA', 'PAN', 'PANAMA', '巴拿马', 0, '', ''),
(153, 'PE', 'PER', 'PERU', '秘鲁', 0, '', ''),
(154, 'PF', 'PYF', 'FRENCH POLYNESIA', '法属玻利尼西亚', 0, '', ''),
(155, 'PG', 'PNG', 'PAPUA NEW CUINEA', '巴布亚新几内亚', 0, '', ''),
(156, 'PH', 'PHL', 'PHILIPPINES', '菲律宾', 0, '', ''),
(157, 'PK', 'PAK', 'PAKISTAN', '巴基斯坦', 0, '', ''),
(158, 'PL', 'POL', 'POLAND', '波兰', 0, '', ''),
(159, 'PM', 'SPM', 'Saint Pierre and Miquelon', '圣皮埃尔和密克隆', 0, '', ''),
(160, 'PN', 'PCN', 'Pitcairn Islands Group', '皮竺凯恩群岛', 0, '', ''),
(161, 'PR', 'PRI', 'PUERTO RICO', '波多黎各', 0, '', ''),
(162, 'PT', 'PRT', 'PORTUGAL', '葡萄牙', 0, '', ''),
(163, 'PY', 'PRY', 'PARAGUAY', '巴拉圭', 0, '', ''),
(164, 'QA', 'QAT', 'QATAR', '卡塔尔', 0, '', ''),
(165, 'RE', 'REU', 'Reunion', '留尼汪', 0, '', ''),
(166, 'RO', 'ROU', 'ROMANIA', '罗马尼亚', 0, '', ''),
(167, 'RU', 'RUS', 'RUSSIA', '俄罗斯', 0, '', ''),
(168, 'RW', 'RWA', 'RWANDA', '卢旺达共和国', 0, '', ''),
(169, 'SA', 'SAU', 'SAUDI ARABIA', '沙特阿拉伯', 0, '', ''),
(170, 'SB', 'SLB', 'SOLOMON IS', '所罗门群岛', 0, '', ''),
(171, 'SC', 'SYC', 'SEYCHELLES', '塞舌尔', 0, '', ''),
(172, 'SD', 'SDN', 'SUDAN', '苏丹', 0, '', ''),
(173, 'SE', 'SWE', 'SWEDEN', '瑞典', 0, '', ''),
(174, 'SG', 'SGP', 'SINGAPORE', '新加坡', 0, '', ''),
(175, 'SH', 'SHN', 'Saint Helena', '圣赫勒拿', 0, '', ''),
(176, 'SI', 'SVN', 'SLOVENIA', '斯洛文尼亚', 0, '', ''),
(177, 'SJ', 'SJM', 'Svalbard and jan Mayen Islands', '斯瓦尔巴群岛', 0, '', ''),
(178, 'SK', 'SVK', 'SLOVAKIA', '斯洛伐克', 0, '', ''),
(179, 'SL', 'SLE', 'SIERRA LEONE', '塞拉利昂', 0, '', ''),
(180, 'SM', 'SMR', 'SAN MARINO', '圣马力诺', 0, '', ''),
(181, 'SN', 'SEN', 'SENEGAL', '塞内加尔', 0, '', ''),
(182, 'SO', 'SOM', 'SOMALI', '索马里', 0, '', ''),
(183, 'SR', 'SUR', 'SURINAME', '苏里南', 0, '', ''),
(184, 'ST', 'STP', 'SAO TOME AND PRINCIPE', '圣多美和普林西比', 0, '', ''),
(185, 'SV', 'SLV', 'EI SALVADOR', '萨尔瓦多', 0, '', ''),
(186, 'SY', 'SYR', 'SYRIA', '叙利亚', 0, '', ''),
(187, 'SZ', 'SWZ', 'SWAZILAND', '斯威士兰', 0, '', ''),
(188, 'TC', 'TCA', 'TURKS AND CAICOS ISLANDS', '特克斯科斯群岛', 0, '', ''),
(189, 'TD', 'TCD', 'CHAD', '乍得', 0, '', ''),
(190, 'TG', 'TGO', 'TOGO', '多哥', 0, '', ''),
(191, 'TH', 'THA', 'THAILAND', '泰国', 0, '', ''),
(192, 'TJ', 'TJK', 'TAJIKSTAN', '塔吉克斯坦', 0, '', ''),
(193, 'TK', 'TKL', 'TOKELAU', '托克劳', 0, '', ''),
(194, 'TM', 'TKM', 'TURKMENISTAN', '土库曼斯坦', 0, '', ''),
(195, 'TN', 'TUN', 'TUNISIA', '突尼斯', 0, '', ''),
(196, 'TO', 'TON', 'TONGA', '汤加', 0, '', ''),
(197, 'TR', 'TUR', 'TURKEY', '土耳其', 0, '', ''),
(198, 'TT', 'TTO', 'TRINIDAD AND TOBAGO', '特立尼达和多巴哥', 0, '', ''),
(199, 'TV', 'TUV', 'TUVALU', '图瓦卢', 0, '', ''),
(200, 'TW', 'TWN', 'TAIWAN', '台湾省', 0, '', ''),
(201, 'TZ', 'TZA', 'TANZANIA', '坦桑尼亚', 0, '', ''),
(202, 'UA', 'UKR', 'UKRAINE', '乌克兰', 0, '', ''),
(203, 'UG', 'UGA', 'UGANDA', '乌干达', 0, '', ''),
(204, 'US', 'USA', 'United States', '美国', 0, '', ''),
(205, 'UY', 'URY', 'URUGUAY', '乌拉圭', 0, '', ''),
(206, 'UZ', 'UZB', 'UZBEKISTAN', '乌兹别克斯坦', 0, '', ''),
(207, 'VA', 'VAT', 'VATICAN CITY', '梵蒂冈', 0, '', ''),
(208, 'VC', 'VCT', 'SAINT VINCENT', '圣文森特岛', 0, '', ''),
(209, 'VE', 'VEN', 'VENEZUELA', '委内瑞拉', 0, '', ''),
(210, 'VG', 'VGB', 'British Virgin Islands', '英属维尔京群岛', 0, '', ''),
(211, 'VN', 'VNM', 'VIETNAM', '越南', 0, '', ''),
(212, 'VU', 'VUT', 'VANUATU', '瓦努阿图共和国', 0, '', ''),
(213, 'WS', 'WSM', 'Western Samoa', '西萨摩亚', 0, '', ''),
(214, 'YE', 'YEM', 'YEMEN', '也门', 0, '', ''),
(215, 'YU', 'YUG', 'YUGOSLAVIA', '南斯拉夫', 0, '', ''),
(216, 'ZA', 'ZAF', 'SOUTH AFRICA', '南非', 0, '', ''),
(217, 'ZM', 'ZMB', 'ZAMBIA', '赞比亚', 0, '', ''),
(218, 'RA', '', 'Russian Federation', '俄罗斯联邦', 0, '', ''),
(219, 'UK', '', 'United Kingdom', '英 国', 0, '', '[edit]'),
(220, 'VI', '', 'United States Virgin Islands', '美属维尔京群岛', 0, '', ''),
(221, 'RB', '', 'SERBIA', '塞尔维亚', 0, '', ''),
(222, 'SRB', '', 'Republic of SERBIA', '塞尔维亚共和国', 0, '', ''),
(223, 'MNE', '', 'Montenegro', '黑山共和国', 0, '', ''),
(224, 'GL', '', 'Greenland', '格陵兰', 0, '', ''),
(225, 'AN', '', 'Netherlands Antilles', '荷属安的列斯群岛', 0, '', ''),
(226, 'MQ', '', 'Martinique', '马提尼克岛', 0, '', '');

-- --------------------------------------------------------

--
-- 表的结构 `country_continent`
--

CREATE TABLE IF NOT EXISTS `country_continent` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `name_en` varchar(50) NOT NULL,
  `name_cn` varchar(50) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 AUTO_INCREMENT=7 ;

--
-- 转存表中的数据 `country_continent`
--

INSERT INTO `country_continent` (`id`, `name_en`, `name_cn`) VALUES
(2, 'Europe', '欧洲'),
(3, 'America', '美洲'),
(4, 'Oceania', '大洋州'),
(5, 'Asia', '亚洲'),
(6, 'Africa', '非洲');

-- --------------------------------------------------------

--
-- 表的结构 `currency_code`
--

CREATE TABLE IF NOT EXISTS `currency_code` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `code` varchar(10) NOT NULL DEFAULT '',
  `name_en` varchar(50) NOT NULL DEFAULT '',
  `name_cn` varchar(50) NOT NULL DEFAULT '',
  `ex_rate` decimal(10,5) NOT NULL DEFAULT '0.00000',
  `update_date` varchar(20) NOT NULL,
  `update_user` varchar(20) NOT NULL,
  PRIMARY KEY (`id`),
  KEY `code` (`code`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COMMENT='货币汇率代码表' AUTO_INCREMENT=1 ;

--
-- 转存表中的数据 `currency_code`
--


-- --------------------------------------------------------

--
-- 表的结构 `customer_black_list`
--

CREATE TABLE IF NOT EXISTS `customer_black_list` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `platform` varchar(20) NOT NULL,
  `buyer_id` varchar(50) NOT NULL,
  `email` varchar(50) NOT NULL,
  `name` varchar(50) NOT NULL,
  `remark` varchar(500) NOT NULL,
  `creator_id` int(11) NOT NULL,
  `created_date` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;

--
-- 转存表中的数据 `customer_black_list`
--


-- --------------------------------------------------------

--
-- 表的结构 `customer_second_glance_rate`
--

CREATE TABLE IF NOT EXISTS `customer_second_glance_rate` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `year` int(11) NOT NULL,
  `month` int(11) NOT NULL,
  `second_glance_amount` double NOT NULL,
  `totable_amount` double NOT NULL,
  `second_glance_rate` double NOT NULL,
  `saler_id` int(20) NOT NULL,
  `created_date` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 AUTO_INCREMENT=15 ;

--
-- 转存表中的数据 `customer_second_glance_rate`
--


-- --------------------------------------------------------

--
-- 表的结构 `document_catalog`
--

CREATE TABLE IF NOT EXISTS `document_catalog` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `name` varchar(100) NOT NULL,
  `parent` int(11) NOT NULL,
  `path` varchar(200) NOT NULL,
  `created_date` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;

--
-- 转存表中的数据 `document_catalog`
--


-- --------------------------------------------------------

--
-- 表的结构 `document_comment`
--

CREATE TABLE IF NOT EXISTS `document_comment` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `content_id` int(11) NOT NULL,
  `comment` text NOT NULL,
  `creator` varchar(100) NOT NULL,
  `created_date` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;

--
-- 转存表中的数据 `document_comment`
--


-- --------------------------------------------------------

--
-- 表的结构 `document_content`
--

CREATE TABLE IF NOT EXISTS `document_content` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `title` varchar(200) NOT NULL,
  `content` text NOT NULL,
  `owner_id` int(11) NOT NULL,
  `catalog_id` int(11) NOT NULL,
  `level` tinyint(4) NOT NULL DEFAULT '1' COMMENT '1:normal,2 important, 3 very important',
  `edited_date` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  `custom_date` varchar(20) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;

--
-- 转存表中的数据 `document_content`
--


-- --------------------------------------------------------

--
-- 表的结构 `document_content_file_map`
--

CREATE TABLE IF NOT EXISTS `document_content_file_map` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `content_id` int(11) NOT NULL,
  `file_url` varchar(200) NOT NULL,
  `file_description` varchar(200) NOT NULL,
  `before_file_name` varchar(200) NOT NULL,
  `created_date` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;

--
-- 转存表中的数据 `document_content_file_map`
--


-- --------------------------------------------------------

--
-- 表的结构 `document_permission`
--

CREATE TABLE IF NOT EXISTS `document_permission` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `catalog_id` int(11) NOT NULL,
  `group_id` int(11) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;

--
-- 转存表中的数据 `document_permission`
--


-- --------------------------------------------------------

--
-- 表的结构 `ebay_message`
--

CREATE TABLE IF NOT EXISTS `ebay_message` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `message_id` varchar(100) DEFAULT NULL,
  `message_type` varchar(100) DEFAULT NULL,
  `question_type` varchar(100) DEFAULT NULL,
  `recipientid` varchar(100) DEFAULT NULL,
  `sendmail` varchar(100) DEFAULT NULL,
  `sendid` varchar(100) DEFAULT NULL,
  `subject` text,
  `body` text,
  `itemid` varchar(100) DEFAULT NULL,
  `itemurl` text,
  `starttime` varchar(100) DEFAULT NULL,
  `endtime` varchar(100) DEFAULT NULL,
  `currentprice` varchar(100) DEFAULT NULL,
  `title` varchar(255) DEFAULT NULL,
  `status` int(11) DEFAULT '0',
  `createtime` varchar(60) DEFAULT NULL,
  `ebay_user` varchar(100) DEFAULT NULL,
  `classid` int(11) DEFAULT '0',
  `ebay_id` varchar(100) DEFAULT NULL,
  `add_time` datetime NOT NULL,
  `replaycontent` text,
  `replyuser` varchar(255) DEFAULT NULL,
  `update_ebay` int(2) NOT NULL DEFAULT '0',
  `reply_time` datetime DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;

--
-- 转存表中的数据 `ebay_message`
--


-- --------------------------------------------------------

--
-- 表的结构 `ebay_message_category`
--

CREATE TABLE IF NOT EXISTS `ebay_message_category` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `category_name` varchar(80) NOT NULL,
  `ebay_note` text,
  `user` varchar(80) NOT NULL,
  `category_keywords` varchar(1500) DEFAULT NULL,
  `created_date` datetime DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;

--
-- 转存表中的数据 `ebay_message_category`
--


-- --------------------------------------------------------

--
-- 表的结构 `ebay_message_log`
--

CREATE TABLE IF NOT EXISTS `ebay_message_log` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `order_id` int(11) NOT NULL,
  `ordernumber` int(11) NOT NULL,
  `message_template` int(11) NOT NULL,
  `time` int(11) NOT NULL,
  `status` varchar(250) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;

--
-- 转存表中的数据 `ebay_message_log`
--


-- --------------------------------------------------------

--
-- 表的结构 `ebay_message_template`
--

CREATE TABLE IF NOT EXISTS `ebay_message_template` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `template_name` varchar(180) NOT NULL,
  `template_content` text NOT NULL,
  `user` varchar(20) NOT NULL,
  `template_subject` varchar(180) NOT NULL,
  `created_date` datetime DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;

--
-- 转存表中的数据 `ebay_message_template`
--


-- --------------------------------------------------------

--
-- 表的结构 `edm_email_template`
--

CREATE TABLE IF NOT EXISTS `edm_email_template` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `code` varchar(50) NOT NULL,
  `title` varchar(200) NOT NULL,
  `content` text NOT NULL,
  `creator` int(11) NOT NULL,
  `remark` text NOT NULL,
  `created_date` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;

--
-- 转存表中的数据 `edm_email_template`
--


-- --------------------------------------------------------

--
-- 表的结构 `email_subscription`
--

CREATE TABLE IF NOT EXISTS `email_subscription` (
  `id` int(10) NOT NULL AUTO_INCREMENT,
  `subscription` varchar(50) NOT NULL,
  `api_date` datetime NOT NULL,
  `create_date` datetime NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;

--
-- 转存表中的数据 `email_subscription`
--


-- --------------------------------------------------------

--
-- 表的结构 `epacket_config`
--

CREATE TABLE IF NOT EXISTS `epacket_config` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `user_id` int(11) NOT NULL,
  `is_register` varchar(10) NOT NULL,
  `stock_code` varchar(20) DEFAULT NULL,
  `pickupaddress_company` varchar(150) NOT NULL,
  `pickupaddress_contact` varchar(150) NOT NULL,
  `pickupaddress_email` varchar(80) NOT NULL,
  `pickupaddress_mobile` varchar(15) NOT NULL,
  `pickupaddress_phone` varchar(20) NOT NULL,
  `pickupaddress_postcode` varchar(10) NOT NULL,
  `pickupaddress_country` varchar(30) NOT NULL,
  `pickupaddress_province` varchar(10) NOT NULL,
  `pickupaddress_city` varchar(10) NOT NULL,
  `pickupaddress_district` varchar(10) NOT NULL,
  `pickupaddress_street` varchar(150) NOT NULL,
  `shipfromaddress_company` varchar(150) NOT NULL,
  `shipfromaddress_contact` varchar(150) NOT NULL,
  `shipfromaddress_email` varchar(80) NOT NULL,
  `shipfromaddress_mobile` varchar(15) NOT NULL,
  `shipfromaddress_postcode` varchar(10) NOT NULL,
  `shipfromaddress_country` varchar(30) NOT NULL,
  `shipfromaddress_province` varchar(30) NOT NULL,
  `shipfromaddress_city` varchar(30) NOT NULL,
  `shipfromaddress_district` varchar(30) NOT NULL,
  `shipfromaddress_street` varchar(150) NOT NULL,
  `returntoaddress_company` varchar(50) NOT NULL,
  `returntoaddress_contact` varchar(50) NOT NULL,
  `returntoaddress_postcode` varchar(50) NOT NULL,
  `returntoaddress_country` varchar(50) NOT NULL,
  `returntoaddress_province` varchar(50) NOT NULL,
  `returntoaddress_city` varchar(50) NOT NULL,
  `returntoaddress_district` varchar(50) NOT NULL,
  `returntoaddress_street` varchar(150) NOT NULL,
  `pagesize` int(1) NOT NULL DEFAULT '0',
  `emspickuptype` int(1) NOT NULL DEFAULT '0',
  `update_date` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;

--
-- 转存表中的数据 `epacket_config`
--


-- --------------------------------------------------------

--
-- 表的结构 `epacket_confirm_list`
--

CREATE TABLE IF NOT EXISTS `epacket_confirm_list` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `order_id` int(11) NOT NULL,
  `transaction_id` varchar(40) NOT NULL,
  `track_number` varchar(40) DEFAULT '',
  `print_label` int(11) NOT NULL DEFAULT '0',
  `confirmed` tinyint(4) NOT NULL DEFAULT '0',
  `label_content` text NOT NULL,
  `input_user` int(11) NOT NULL DEFAULT '0',
  `input_date` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `downloaded` tinyint(4) NOT NULL DEFAULT '0',
  `message` varchar(500) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;

--
-- 转存表中的数据 `epacket_confirm_list`
--


-- --------------------------------------------------------

--
-- 表的结构 `epacket_item_transaction_id_poll`
--

CREATE TABLE IF NOT EXISTS `epacket_item_transaction_id_poll` (
  `id` bigint(20) NOT NULL AUTO_INCREMENT,
  `item_id` varchar(20) NOT NULL,
  `variation_title` varchar(200) NOT NULL,
  `buyer_id` varchar(50) NOT NULL,
  `name` varchar(32) NOT NULL COMMENT '迎合paypal的长度',
  `ebay_transaction_id` varchar(20) NOT NULL,
  `paypal_transaction_id` varchar(20) NOT NULL DEFAULT '',
  `gross` double NOT NULL,
  `paid_time` varchar(50) NOT NULL,
  `created_date` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;

--
-- 转存表中的数据 `epacket_item_transaction_id_poll`
--


-- --------------------------------------------------------

--
-- 表的结构 `epacket_paypal_ebay_map`
--

CREATE TABLE IF NOT EXISTS `epacket_paypal_ebay_map` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `item_id` varchar(20) NOT NULL,
  `variation_title` varchar(200) NOT NULL,
  `paypal_transaction_id` varchar(20) NOT NULL,
  `ebay_transaction_id` varchar(20) NOT NULL,
  `ebay_id` varchar(20) NOT NULL,
  `update_time` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;

--
-- 转存表中的数据 `epacket_paypal_ebay_map`
--


-- --------------------------------------------------------

--
-- 表的结构 `eshop_category`
--

CREATE TABLE IF NOT EXISTS `eshop_category` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `eshop_code` varchar(20) NOT NULL,
  `category` varchar(200) NOT NULL,
  `creator` int(11) NOT NULL,
  `created_date` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;

--
-- 转存表中的数据 `eshop_category`
--


-- --------------------------------------------------------

--
-- 表的结构 `eshop_code`
--

CREATE TABLE IF NOT EXISTS `eshop_code` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `code` varchar(20) NOT NULL,
  `currency` varchar(10) NOT NULL,
  `name` varchar(50) NOT NULL,
  `order` tinyint(4) NOT NULL DEFAULT '1',
  PRIMARY KEY (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;

--
-- 转存表中的数据 `eshop_code`
--


-- --------------------------------------------------------

--
-- 表的结构 `eshop_list_fee`
--

CREATE TABLE IF NOT EXISTS `eshop_list_fee` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `eshop_code` varchar(50) NOT NULL,
  `sale_mode` varchar(20) NOT NULL,
  `category_id` int(11) NOT NULL DEFAULT '0',
  `start_price` double NOT NULL,
  `end_price` double NOT NULL,
  `formula` varchar(200) NOT NULL,
  `remark` varchar(500) NOT NULL,
  `creator` int(11) NOT NULL,
  `created_date` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;

--
-- 转存表中的数据 `eshop_list_fee`
--


-- --------------------------------------------------------

--
-- 表的结构 `eshop_sale_mode`
--

CREATE TABLE IF NOT EXISTS `eshop_sale_mode` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `mode` varchar(20) NOT NULL,
  `name` varchar(50) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 AUTO_INCREMENT=3 ;

--
-- 转存表中的数据 `eshop_sale_mode`
--

INSERT INTO `eshop_sale_mode` (`id`, `mode`, `name`) VALUES
(1, 'buy_now', '一口价'),
(2, 'auction', '拍卖');

-- --------------------------------------------------------

--
-- 表的结构 `eshop_trade_fee`
--

CREATE TABLE IF NOT EXISTS `eshop_trade_fee` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `eshop_code` varchar(50) NOT NULL,
  `sale_mode` varchar(20) NOT NULL,
  `category_id` int(11) NOT NULL DEFAULT '0',
  `start_price` double NOT NULL,
  `end_price` double NOT NULL,
  `formula` varchar(200) NOT NULL,
  `remark` varchar(500) NOT NULL,
  `creator` int(11) NOT NULL,
  `created_date` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;

--
-- 转存表中的数据 `eshop_trade_fee`
--


-- --------------------------------------------------------

--
-- 表的结构 `general_status`
--

CREATE TABLE IF NOT EXISTS `general_status` (
  `key` varchar(80) NOT NULL,
  `value` varchar(40) NOT NULL
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

--
-- 转存表中的数据 `general_status`
--


-- --------------------------------------------------------

--
-- 表的结构 `group`
--

CREATE TABLE IF NOT EXISTS `group` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `code` varchar(20) NOT NULL,
  `name` varchar(20) NOT NULL,
  `bind--` varchar(20) NOT NULL,
  `priority` tinyint(4) NOT NULL DEFAULT '1',
  PRIMARY KEY (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;

--
-- 转存表中的数据 `group`
--


-- --------------------------------------------------------

--
-- 表的结构 `group_permission`
--

CREATE TABLE IF NOT EXISTS `group_permission` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `group_id` int(11) NOT NULL,
  `resource` varchar(200) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;

--
-- 转存表中的数据 `group_permission`
--


-- --------------------------------------------------------

--
-- 表的结构 `group_statistics_map`
--

CREATE TABLE IF NOT EXISTS `group_statistics_map` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `group_id` int(11) NOT NULL,
  `key` varchar(50) NOT NULL,
  `created_date` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;

--
-- 转存表中的数据 `group_statistics_map`
--


-- --------------------------------------------------------

--
-- 表的结构 `group_system_map`
--

CREATE TABLE IF NOT EXISTS `group_system_map` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `group_id` int(11) NOT NULL,
  `bind` varchar(20) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;

--
-- 转存表中的数据 `group_system_map`
--


-- --------------------------------------------------------

--
-- 表的结构 `important_message`
--

CREATE TABLE IF NOT EXISTS `important_message` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `message` text NOT NULL,
  `creator` varchar(10) NOT NULL,
  `created_date` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;

--
-- 转存表中的数据 `important_message`
--


-- --------------------------------------------------------

--
-- 表的结构 `important_message_group`
--

CREATE TABLE IF NOT EXISTS `important_message_group` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `message_id` int(11) NOT NULL,
  `read` tinyint(4) NOT NULL DEFAULT '0',
  `group_name` varchar(20) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;

--
-- 转存表中的数据 `important_message_group`
--


-- --------------------------------------------------------

--
-- 表的结构 `message`
--

CREATE TABLE IF NOT EXISTS `message` (
  `id` bigint(20) NOT NULL AUTO_INCREMENT,
  `type` varchar(200) NOT NULL,
  `content` varchar(50) NOT NULL,
  `click_url` varchar(500) NOT NULL,
  `owner_id` int(11) NOT NULL,
  `number` int(11) NOT NULL,
  `created_time` bigint(20) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;

--
-- 转存表中的数据 `message`
--


-- --------------------------------------------------------

--
-- 表的结构 `message_log`
--

CREATE TABLE IF NOT EXISTS `message_log` (
  `id` bigint(20) NOT NULL AUTO_INCREMENT,
  `message_id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `created_date` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;

--
-- 转存表中的数据 `message_log`
--


-- --------------------------------------------------------

--
-- 表的结构 `message_receiver`
--

CREATE TABLE IF NOT EXISTS `message_receiver` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `message_type` varchar(200) NOT NULL,
  `receiver_id` int(11) NOT NULL,
  `created_date` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;

--
-- 转存表中的数据 `message_receiver`
--


-- --------------------------------------------------------

--
-- 表的结构 `move_stock_list`
--

CREATE TABLE IF NOT EXISTS `move_stock_list` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `ship_order_no` varchar(20) NOT NULL,
  `log_type` varchar(20) NOT NULL,
  `storage_code` varchar(20) NOT NULL,
  `ship_confirm_date` varchar(50) NOT NULL,
  `locale` varchar(100) NOT NULL,
  `remark` varchar(200) NOT NULL,
  `sku_str` varchar(1500) NOT NULL,
  `qty_str` varchar(1500) DEFAULT NULL,
  `received_count` varchar(1500) DEFAULT NULL,
  `collect_address` varchar(100) NOT NULL,
  `ship_confirm_user` varchar(50) NOT NULL,
  `transaction_number` varchar(50) NOT NULL,
  `status` int(11) NOT NULL DEFAULT '0',
  `creator` int(11) NOT NULL,
  `created_date` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;

--
-- 转存表中的数据 `move_stock_list`
--


-- --------------------------------------------------------

--
-- 表的结构 `myebay_feedback`
--

CREATE TABLE IF NOT EXISTS `myebay_feedback` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `buyer_id` varchar(200) NOT NULL,
  `feedback_type` varchar(50) NOT NULL,
  `feedback_content` varchar(500) NOT NULL,
  `feedback_time` varchar(50) NOT NULL,
  `feedback_response` varchar(500) NOT NULL,
  `item_id` varchar(20) NOT NULL,
  `item_title` varchar(200) NOT NULL,
  `transaction_id` varchar(20) NOT NULL,
  `ebay_id` varchar(50) NOT NULL,
  `item_no` varchar(50) NOT NULL,
  `verify_type` int(11) NOT NULL DEFAULT '0',
  `verify_content` varchar(500) NOT NULL,
  `created_date` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `feedback_id` varchar(20) NOT NULL,
  `feedback_duty` varchar(20) NOT NULL,
  `feedback_sku_str` varchar(50) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;

--
-- 转存表中的数据 `myebay_feedback`
--


-- --------------------------------------------------------

--
-- 表的结构 `myebay_list`
--

CREATE TABLE IF NOT EXISTS `myebay_list` (
  `id` bigint(20) NOT NULL AUTO_INCREMENT,
  `sku` varchar(20) NOT NULL,
  `sku_sale_status` tinyint(4) NOT NULL,
  `title` varchar(200) NOT NULL,
  `image_url` varchar(200) NOT NULL,
  `listing_type` varchar(20) NOT NULL,
  `currency` varchar(10) NOT NULL,
  `price` double NOT NULL,
  `shipping_price` double NOT NULL,
  `qty` int(11) NOT NULL,
  `start_time` varchar(50) NOT NULL,
  `listing_duration` varchar(20) NOT NULL,
  `time_left` varchar(20) NOT NULL,
  `item_id` varchar(20) NOT NULL,
  `ebay_id` varchar(20) NOT NULL,
  `alarm` tinyint(4) NOT NULL DEFAULT '0',
  `active_status` tinyint(4) NOT NULL DEFAULT '1',
  `updated_date` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;

--
-- 转存表中的数据 `myebay_list`
--


-- --------------------------------------------------------

--
-- 表的结构 `myebay_listing_fee`
--

CREATE TABLE IF NOT EXISTS `myebay_listing_fee` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `listing_fee` float NOT NULL,
  `listing_fee_currency` varchar(10) NOT NULL,
  `item_id` varchar(20) NOT NULL,
  `ebay_id` varchar(20) NOT NULL,
  `created_date` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;

--
-- 转存表中的数据 `myebay_listing_fee`
--


-- --------------------------------------------------------

--
-- 表的结构 `myebay_list_competitor`
--

CREATE TABLE IF NOT EXISTS `myebay_list_competitor` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `item_id` varchar(20) NOT NULL,
  `seller_id` varchar(50) NOT NULL,
  `url` varchar(200) NOT NULL,
  `keyword` text NOT NULL,
  `allowed_difference` double NOT NULL DEFAULT '0',
  `tp_price` decimal(8,2) NOT NULL,
  `is_track` tinyint(1) NOT NULL DEFAULT '0',
  `upset` varchar(15) NOT NULL,
  `balance` varchar(10) NOT NULL,
  `status` tinyint(4) NOT NULL DEFAULT '0',
  `track_time` varchar(20) NOT NULL,
  `created_date` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;

--
-- 转存表中的数据 `myebay_list_competitor`
--


-- --------------------------------------------------------

--
-- 表的结构 `myebay_order_list`
--

CREATE TABLE IF NOT EXISTS `myebay_order_list` (
  `id` bigint(20) NOT NULL AUTO_INCREMENT,
  `order_id` varchar(20) DEFAULT NULL,
  `orderlineitemid` varchar(90) DEFAULT NULL,
  `ebay_id` varchar(50) NOT NULL,
  `item_id` varchar(20) NOT NULL,
  `item_title` varchar(500) NOT NULL,
  `sku_str` varchar(200) NOT NULL,
  `quantitysold` varchar(55) DEFAULT NULL,
  `salesrecordnumber` varchar(55) DEFAULT NULL,
  `transaction_id` varchar(20) NOT NULL,
  `paypal_transaction_id` varchar(20) NOT NULL,
  `currency` varchar(10) NOT NULL,
  `amount_paid` double NOT NULL,
  `paid_time` varchar(50) NOT NULL,
  `transaction_price` double NOT NULL,
  `final_value_fee` float NOT NULL,
  `fvf_currency` varchar(10) NOT NULL,
  `listing_fee_currency` varchar(10) NOT NULL,
  `listing_fee` float NOT NULL,
  `shippingservice` varchar(150) DEFAULT NULL,
  `buyercheckoutmessage` text,
  `buyer_id` varchar(100) NOT NULL,
  `buyer_email` varchar(150) DEFAULT NULL,
  `buyer_name` varchar(100) NOT NULL,
  `country` varchar(100) NOT NULL,
  `province` varchar(255) NOT NULL,
  `city` varchar(255) NOT NULL,
  `street1` varchar(255) NOT NULL,
  `street2` varchar(255) DEFAULT NULL,
  `phone` varchar(100) NOT NULL,
  `postal_code` varchar(20) NOT NULL,
  `order_status` varchar(20) NOT NULL,
  `shipped_time` varchar(100) DEFAULT NULL,
  `checkoutstatus` varchar(30) DEFAULT NULL,
  `completestatus` varchar(30) DEFAULT NULL,
  `ebaypaymentmismatchdetails` varchar(255) DEFAULT NULL,
  `paymentmethodused` varchar(255) DEFAULT NULL,
  `paymentholdstatus` varchar(30) DEFAULT NULL,
  `lasttimemodified` varchar(40) DEFAULT NULL,
  `integratedmerchantcreditcardenabled` varchar(30) DEFAULT NULL,
  `ebaypaymentstatus` varchar(30) DEFAULT NULL,
  `mismatchtype` varchar(255) DEFAULT NULL,
  `actionrequiredby` varchar(30) DEFAULT NULL,
  `created_date` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `order_created_date` varchar(80) DEFAULT NULL,
  `is_import` int(1) NOT NULL DEFAULT '0',
  `last_update_time` timestamp NOT NULL DEFAULT '0000-00-00 00:00:00',
  PRIMARY KEY (`id`),
  KEY `paypal_transaction_id` (`paypal_transaction_id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;

--
-- 转存表中的数据 `myebay_order_list`
--


-- --------------------------------------------------------

--
-- 表的结构 `myebay_order_wait_complete`
--

CREATE TABLE IF NOT EXISTS `myebay_order_wait_complete` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `order_id` int(11) NOT NULL,
  `auction_site_type` varchar(15) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;

--
-- 转存表中的数据 `myebay_order_wait_complete`
--


-- --------------------------------------------------------

--
-- 表的结构 `mytaobao_list`
--

CREATE TABLE IF NOT EXISTS `mytaobao_list` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `sku_str` varchar(100) NOT NULL,
  `image_url` varchar(200) NOT NULL,
  `title` varchar(200) NOT NULL,
  `item_url` varchar(200) NOT NULL,
  `item_id` varchar(20) NOT NULL,
  `price_str` varchar(20) NOT NULL,
  `shipping_cost` varchar(50) NOT NULL,
  `stock_count_str` varchar(50) NOT NULL,
  `sale_status_str` varchar(50) NOT NULL,
  `seller_name` varchar(20) NOT NULL,
  `created` date NOT NULL,
  `created_date` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;

--
-- 转存表中的数据 `mytaobao_list`
--


-- --------------------------------------------------------

--
-- 表的结构 `orders_12hours_missing`
--

CREATE TABLE IF NOT EXISTS `orders_12hours_missing` (
  `transaction_id` varchar(50) NOT NULL,
  `ebay_id` varchar(50) NOT NULL,
  `buyer_id` varchar(50) NOT NULL,
  `paid_time` datetime NOT NULL
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

--
-- 转存表中的数据 `orders_12hours_missing`
--


-- --------------------------------------------------------

--
-- 表的结构 `order_bad_comment_type`
--

CREATE TABLE IF NOT EXISTS `order_bad_comment_type` (
  `id` tinyint(4) NOT NULL AUTO_INCREMENT,
  `type` varchar(200) NOT NULL,
  `department` varchar(20) NOT NULL,
  `default_refund_type` int(11) NOT NULL,
  `default_refund_duty` varchar(20) NOT NULL,
  `default_refund_show_sku` tinyint(4) NOT NULL DEFAULT '1',
  `confirm_required` tinyint(4) NOT NULL DEFAULT '0',
  `creator` int(11) NOT NULL,
  `created_date` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;

--
-- 转存表中的数据 `order_bad_comment_type`
--


-- --------------------------------------------------------

--
-- 表的结构 `order_check_list`
--

CREATE TABLE IF NOT EXISTS `order_check_list` (
  `id` bigint(20) NOT NULL AUTO_INCREMENT,
  `order_id` bigint(20) NOT NULL,
  `submit_remark` text NOT NULL,
  `submitter_id` int(11) NOT NULL,
  `submit_date` datetime NOT NULL,
  `answer_remark` text NOT NULL,
  `answer_id` int(11) NOT NULL,
  `answer_date` datetime NOT NULL,
  `sku_str` varchar(50) NOT NULL,
  `qty_str` varchar(50) NOT NULL,
  `state` varchar(50) NOT NULL DEFAULT 'not_handled',
  PRIMARY KEY (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;

--
-- 转存表中的数据 `order_check_list`
--


-- --------------------------------------------------------

--
-- 表的结构 `order_duplicated_list`
--

CREATE TABLE IF NOT EXISTS `order_duplicated_list` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `item_id_str` varchar(200) NOT NULL,
  `transaction_id` varchar(20) NOT NULL,
  `buyer_id` varchar(20) NOT NULL,
  `created_date` int(11) NOT NULL,
  `order_id` int(11) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;

--
-- 转存表中的数据 `order_duplicated_list`
--


-- --------------------------------------------------------

--
-- 表的结构 `order_import_log`
--

CREATE TABLE IF NOT EXISTS `order_import_log` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `import_date` varchar(20) NOT NULL DEFAULT '',
  `user_name` varchar(20) NOT NULL DEFAULT '',
  `descript` varchar(1500) NOT NULL DEFAULT '',
  `user_login` varchar(30) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COMMENT='导入操作日志' AUTO_INCREMENT=1 ;

--
-- 转存表中的数据 `order_import_log`
--


-- --------------------------------------------------------

--
-- 表的结构 `order_list`
--

CREATE TABLE IF NOT EXISTS `order_list` (
  `id` bigint(20) NOT NULL AUTO_INCREMENT,
  `list_date` date NOT NULL,
  `list_time` time NOT NULL,
  `created_at` datetime NOT NULL,
  `time_zone` varchar(20) NOT NULL DEFAULT '',
  `name` varchar(100) NOT NULL DEFAULT '',
  `list_type` varchar(50) NOT NULL DEFAULT '',
  `payment_status` varchar(20) NOT NULL DEFAULT '',
  `subject` varchar(255) NOT NULL,
  `currency` varchar(20) NOT NULL DEFAULT '',
  `ex_rate` varchar(15) DEFAULT NULL,
  `gross` decimal(10,2) NOT NULL,
  `fee` varchar(20) NOT NULL DEFAULT '',
  `net` varchar(20) NOT NULL DEFAULT '',
  `shippingamt` decimal(5,2) DEFAULT '0.00',
  `note` varchar(255) DEFAULT NULL,
  `from_email` varchar(50) NOT NULL DEFAULT '',
  `to_email` varchar(50) NOT NULL DEFAULT '',
  `transaction_id` varchar(50) NOT NULL DEFAULT '',
  `payment_type` varchar(255) NOT NULL,
  `counterparty_status` varchar(50) NOT NULL DEFAULT '',
  `shipping_address` text NOT NULL,
  `address_status` varchar(50) NOT NULL DEFAULT '',
  `item_title_str` varchar(1500) DEFAULT NULL,
  `item_id_str` varchar(1000) NOT NULL,
  `item_price_str` varchar(1500) DEFAULT NULL,
  `shipping_handling_amount` varchar(20) NOT NULL DEFAULT '',
  `insurance_amount` varchar(20) NOT NULL DEFAULT '',
  `sales_tax` varchar(20) NOT NULL DEFAULT '',
  `auction_site` varchar(20) NOT NULL DEFAULT '',
  `auction_site_type` varchar(50) DEFAULT NULL,
  `buyer_id` varchar(50) NOT NULL DEFAULT '',
  `item_url` varchar(255) NOT NULL DEFAULT '',
  `closing_date` varchar(20) NOT NULL DEFAULT '',
  `reference_txn_id` varchar(20) NOT NULL DEFAULT '',
  `invoice_number` varchar(30) NOT NULL DEFAULT '',
  `subscription_number` varchar(50) NOT NULL,
  `custom_number` varchar(20) NOT NULL DEFAULT '',
  `quantity` int(11) NOT NULL,
  `receipt_id` varchar(20) NOT NULL DEFAULT '',
  `balance` varchar(20) NOT NULL DEFAULT '',
  `address_line_1` varchar(255) NOT NULL DEFAULT '',
  `address_line_2` varchar(255) DEFAULT NULL,
  `town_city` varchar(255) NOT NULL DEFAULT '',
  `state_province` varchar(255) DEFAULT '',
  `zip_code` varchar(30) NOT NULL DEFAULT '',
  `country` varchar(100) NOT NULL DEFAULT '',
  `contact_phone_number` varchar(50) DEFAULT NULL,
  `balance_impact` varchar(255) NOT NULL,
  `income_type` varchar(200) NOT NULL DEFAULT 'Paypal',
  `qty_str` varchar(1500) NOT NULL,
  `descript` text NOT NULL,
  `sku_str` varchar(1500) NOT NULL,
  `input_date` varchar(20) NOT NULL DEFAULT '',
  `input_user` varchar(20) NOT NULL DEFAULT '',
  `input_from_row` varchar(50) NOT NULL DEFAULT '0',
  `order_status` int(11) NOT NULL,
  `check_date` varchar(20) NOT NULL DEFAULT '',
  `check_user` varchar(20) NOT NULL DEFAULT '',
  `bursary_check_date` varchar(20) NOT NULL,
  `bursary_check_user` varchar(30) NOT NULL,
  `print_label_date` varchar(20) NOT NULL DEFAULT '',
  `label_content` text NOT NULL,
  `item_no` varchar(50) NOT NULL DEFAULT '',
  `print_label_user` varchar(20) NOT NULL DEFAULT '',
  `ship_confirm_date` varchar(20) NOT NULL DEFAULT '',
  `ship_confirm_user` varchar(20) NOT NULL DEFAULT '',
  `ship_weight` varchar(20) NOT NULL,
  `sub_ship_weight_str` varchar(200) NOT NULL,
  `ship_remark` varchar(255) NOT NULL,
  `track_number` varchar(500) NOT NULL DEFAULT '',
  `is_register` varchar(10) NOT NULL,
  `cost` double NOT NULL,
  `insure_cost` decimal(5,2) NOT NULL,
  `cost_user` varchar(50) NOT NULL,
  `cost_date` varchar(20) NOT NULL,
  `product_cost` varchar(1500) NOT NULL,
  `product_cost_all` decimal(11,2) NOT NULL DEFAULT '0.00',
  `shipping_cost` double NOT NULL DEFAULT '0',
  `return_date` varchar(20) NOT NULL,
  `return_remark` varchar(255) NOT NULL,
  `return_user` varchar(30) NOT NULL,
  `paid_time` varchar(50) NOT NULL,
  `sys_remark` text NOT NULL,
  `return_why` varchar(255) NOT NULL,
  `return_order` varchar(255) NOT NULL,
  `return_cost` decimal(8,2) NOT NULL,
  `store_change_date` datetime NOT NULL COMMENT '仓库入库修改状态的最后时间',
  `order_receive_date` varchar(15) NOT NULL,
  `email_status` tinyint(4) NOT NULL DEFAULT '0',
  `stock_user_id` int(11) NOT NULL,
  `saler_id` int(11) NOT NULL,
  `purchaser_id_str` varchar(100) NOT NULL,
  `developer_id` int(11) NOT NULL,
  `tester_id` int(11) NOT NULL,
  `trade_fee` double NOT NULL DEFAULT '0',
  `listing_fee` double NOT NULL DEFAULT '0',
  `profit_rate` double NOT NULL DEFAULT '0',
  `refund_verify_status` tinyint(4) NOT NULL DEFAULT '-1',
  `refund_verify_type` smallint(6) NOT NULL,
  `refund_verify_content` text NOT NULL,
  `refund_duty` varchar(50) NOT NULL,
  `refund_sku_str` varchar(50) NOT NULL,
  `is_merged` tinyint(4) NOT NULL DEFAULT '0',
  `is_splited` tinyint(4) NOT NULL DEFAULT '0',
  `is_duplicated` tinyint(4) NOT NULL,
  `address_incorrect` tinyint(4) NOT NULL DEFAULT '0',
  `updated` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  `ebay_id` varchar(50) DEFAULT NULL,
  `domain` varchar(80) DEFAULT NULL,
  `remote_ip` varchar(50) DEFAULT NULL,
  `wish_id` varchar(50) DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `transaction_id` (`transaction_id`),
  KEY `item_no` (`item_no`),
  KEY `qty_str` (`qty_str`(333)),
  KEY `sku_str` (`sku_str`(333)),
  KEY `track_number` (`track_number`(333))
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COMMENT='订单表' AUTO_INCREMENT=1 ;

--
-- 转存表中的数据 `order_list`
--


-- --------------------------------------------------------

--
-- 表的结构 `order_list_ack_failed`
--

CREATE TABLE IF NOT EXISTS `order_list_ack_failed` (
  `transaction_id` varchar(40) NOT NULL,
  `input_user` varchar(20) DEFAULT NULL,
  `email` varchar(20) NOT NULL,
  `status` int(20) NOT NULL,
  `try_times` int(11) NOT NULL DEFAULT '0',
  `input_date` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  KEY `transaction_id` (`transaction_id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

--
-- 转存表中的数据 `order_list_ack_failed`
--


-- --------------------------------------------------------

--
-- 表的结构 `order_list_completed`
--

CREATE TABLE IF NOT EXISTS `order_list_completed` (
  `id` bigint(20) NOT NULL AUTO_INCREMENT,
  `order_id` bigint(20) NOT NULL,
  `list_date` date NOT NULL,
  `list_time` time NOT NULL,
  `created_at` datetime NOT NULL,
  `time_zone` varchar(20) NOT NULL DEFAULT '',
  `name` varchar(50) NOT NULL DEFAULT '',
  `list_type` varchar(50) NOT NULL DEFAULT '',
  `payment_status` varchar(20) NOT NULL DEFAULT '',
  `subject` varchar(255) NOT NULL,
  `currency` varchar(20) NOT NULL DEFAULT '',
  `ex_rate` varchar(15) DEFAULT NULL,
  `gross` decimal(10,2) NOT NULL,
  `fee` varchar(20) NOT NULL DEFAULT '',
  `net` varchar(20) NOT NULL DEFAULT '',
  `shippingamt` varchar(20) DEFAULT NULL,
  `note` varchar(255) NOT NULL,
  `from_email` varchar(50) NOT NULL DEFAULT '',
  `to_email` varchar(50) NOT NULL DEFAULT '',
  `transaction_id` varchar(50) NOT NULL DEFAULT '',
  `payment_type` varchar(255) NOT NULL,
  `counterparty_status` varchar(50) NOT NULL DEFAULT '',
  `shipping_address` text NOT NULL,
  `address_status` varchar(50) NOT NULL DEFAULT '',
  `item_title_str` varchar(1500) DEFAULT NULL,
  `item_id_str` varchar(255) NOT NULL,
  `item_price_str` varchar(1500) DEFAULT NULL,
  `shipping_handling_amount` varchar(20) NOT NULL DEFAULT '',
  `insurance_amount` varchar(20) NOT NULL DEFAULT '',
  `sales_tax` varchar(20) NOT NULL DEFAULT '',
  `auction_site` varchar(20) NOT NULL DEFAULT '',
  `auction_site_type` varchar(50) DEFAULT NULL,
  `buyer_id` varchar(50) NOT NULL DEFAULT '',
  `item_url` varchar(255) NOT NULL DEFAULT '',
  `closing_date` varchar(20) NOT NULL DEFAULT '',
  `reference_txn_id` varchar(20) NOT NULL DEFAULT '',
  `invoice_number` varchar(30) NOT NULL DEFAULT '',
  `subscription_number` varchar(50) NOT NULL,
  `custom_number` varchar(20) NOT NULL DEFAULT '',
  `quantity` int(11) NOT NULL,
  `receipt_id` varchar(20) NOT NULL DEFAULT '',
  `balance` varchar(20) NOT NULL DEFAULT '',
  `address_line_1` varchar(255) NOT NULL DEFAULT '',
  `address_line_2` varchar(255) NOT NULL DEFAULT '',
  `town_city` varchar(255) NOT NULL DEFAULT '',
  `state_province` varchar(255) NOT NULL DEFAULT '',
  `zip_code` varchar(30) NOT NULL DEFAULT '',
  `country` varchar(20) NOT NULL DEFAULT '',
  `contact_phone_number` varchar(30) NOT NULL DEFAULT '',
  `balance_impact` varchar(255) NOT NULL,
  `income_type` varchar(200) NOT NULL DEFAULT 'Paypal',
  `qty_str` varchar(1500) NOT NULL,
  `descript` varchar(255) NOT NULL DEFAULT '',
  `sku_str` varchar(1500) NOT NULL,
  `input_date` varchar(20) NOT NULL DEFAULT '',
  `input_user` varchar(20) NOT NULL DEFAULT '',
  `input_from_row` int(11) NOT NULL DEFAULT '0',
  `order_status` int(11) NOT NULL,
  `check_date` varchar(20) NOT NULL DEFAULT '',
  `check_user` varchar(20) NOT NULL DEFAULT '',
  `bursary_check_date` varchar(20) NOT NULL,
  `bursary_check_user` varchar(30) NOT NULL,
  `print_label_date` varchar(20) NOT NULL DEFAULT '',
  `label_content` text NOT NULL,
  `item_no` varchar(50) NOT NULL DEFAULT '',
  `print_label_user` varchar(20) NOT NULL DEFAULT '',
  `ship_confirm_date` varchar(20) NOT NULL DEFAULT '',
  `ship_confirm_user` varchar(20) NOT NULL DEFAULT '',
  `ship_weight` decimal(8,2) NOT NULL,
  `sub_ship_weight_str` varchar(200) NOT NULL,
  `ship_remark` varchar(255) NOT NULL,
  `track_number` varchar(500) NOT NULL DEFAULT '',
  `is_register` varchar(10) NOT NULL,
  `cost` decimal(6,2) NOT NULL,
  `insure_cost` decimal(5,2) NOT NULL,
  `cost_user` varchar(50) NOT NULL,
  `cost_date` varchar(20) NOT NULL,
  `product_cost` varchar(1500) NOT NULL,
  `product_cost_all` decimal(11,2) NOT NULL DEFAULT '0.00',
  `shipping_cost` double NOT NULL,
  `return_date` varchar(20) NOT NULL,
  `return_remark` varchar(255) NOT NULL,
  `return_user` varchar(30) NOT NULL,
  `paid_time` varchar(50) NOT NULL,
  `sys_remark` text NOT NULL,
  `return_why` varchar(255) NOT NULL,
  `return_order` varchar(255) NOT NULL,
  `return_cost` decimal(8,2) NOT NULL,
  `store_change_date` datetime NOT NULL COMMENT '仓库入库修改状态的最后时间',
  `order_receive_date` varchar(15) NOT NULL,
  `email_status` int(11) NOT NULL DEFAULT '0',
  `stock_user_id` int(11) NOT NULL,
  `saler_id` int(11) NOT NULL,
  `purchaser_id_str` varchar(100) NOT NULL,
  `developer_id` int(11) NOT NULL,
  `tester_id` int(11) NOT NULL,
  `trade_fee` double NOT NULL DEFAULT '0',
  `listing_fee` double NOT NULL DEFAULT '0',
  `profit_rate` double NOT NULL DEFAULT '0',
  `refund_verify_status` tinyint(4) NOT NULL DEFAULT '0',
  `refund_verify_type` smallint(6) NOT NULL,
  `refund_verify_content` text NOT NULL,
  `refund_duty` varchar(20) NOT NULL,
  `refund_sku_str` varchar(50) NOT NULL,
  `is_merged` tinyint(4) NOT NULL DEFAULT '0',
  `is_splited` tinyint(4) NOT NULL DEFAULT '0',
  `is_duplicated` tinyint(4) NOT NULL,
  `address_incorrect` tinyint(4) NOT NULL DEFAULT '0',
  `updated` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  `ebay_id` varchar(50) DEFAULT NULL,
  `domain` varchar(80) DEFAULT NULL,
  `remote_ip` varchar(50) DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `transaction_id` (`transaction_id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COMMENT='已完成的订单表' AUTO_INCREMENT=1 ;

--
-- 转存表中的数据 `order_list_completed`
--


-- --------------------------------------------------------

--
-- 表的结构 `order_list_pending`
--

CREATE TABLE IF NOT EXISTS `order_list_pending` (
  `transaction_id` varchar(255) NOT NULL,
  `input_user` varchar(255) NOT NULL,
  `input_date` datetime NOT NULL,
  KEY `transaction_id` (`transaction_id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

--
-- 转存表中的数据 `order_list_pending`
--


-- --------------------------------------------------------

--
-- 表的结构 `order_list_taobao`
--

CREATE TABLE IF NOT EXISTS `order_list_taobao` (
  `id` bigint(20) NOT NULL AUTO_INCREMENT,
  `tid` varchar(30) NOT NULL,
  `status` varchar(30) NOT NULL,
  `trade_date` datetime NOT NULL,
  `created_date` datetime NOT NULL,
  `receiver_name` varchar(30) NOT NULL,
  `receiver_state` varchar(30) NOT NULL,
  `receiver_city` varchar(30) NOT NULL,
  `receiver_district` varchar(50) NOT NULL,
  `receiver_address` varchar(100) NOT NULL,
  `receiver_zip` bigint(20) NOT NULL,
  `receiver_mobile` varchar(30) NOT NULL,
  `receiver_phone` varchar(30) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;

--
-- 转存表中的数据 `order_list_taobao`
--


-- --------------------------------------------------------

--
-- 表的结构 `order_list_unauthorized`
--

CREATE TABLE IF NOT EXISTS `order_list_unauthorized` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `transaction_id` varchar(40) NOT NULL,
  `input_user` varchar(20) NOT NULL,
  `input_date` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  KEY `transaction_id` (`transaction_id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;

--
-- 转存表中的数据 `order_list_unauthorized`
--


-- --------------------------------------------------------

--
-- 表的结构 `order_merged_list`
--

CREATE TABLE IF NOT EXISTS `order_merged_list` (
  `id` bigint(20) NOT NULL AUTO_INCREMENT,
  `order_id` bigint(20) NOT NULL,
  `transaction_id` varchar(100) NOT NULL,
  `item_title_str` varchar(500) NOT NULL,
  `item_id_str` varchar(200) NOT NULL,
  `buyer_id` varchar(50) NOT NULL,
  `buyer_name` varchar(32) NOT NULL,
  `created_date` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `is_shiped_ebay` int(1) NOT NULL DEFAULT '0',
  PRIMARY KEY (`id`),
  KEY `transaction_id` (`transaction_id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;

--
-- 转存表中的数据 `order_merged_list`
--


-- --------------------------------------------------------

--
-- 表的结构 `order_pi`
--

CREATE TABLE IF NOT EXISTS `order_pi` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `user_id` int(11) NOT NULL,
  `order_id` int(11) NOT NULL DEFAULT '0',
  `sku_str` varchar(200) NOT NULL,
  `qty_str` varchar(200) NOT NULL,
  `pi_file_name` varchar(100) NOT NULL,
  `created_date` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;

--
-- 转存表中的数据 `order_pi`
--


-- --------------------------------------------------------

--
-- 表的结构 `order_power_management_map`
--

CREATE TABLE IF NOT EXISTS `order_power_management_map` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `superintendent_id` int(11) NOT NULL,
  `login_name_str` varchar(500) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;

--
-- 转存表中的数据 `order_power_management_map`
--


-- --------------------------------------------------------

--
-- 表的结构 `order_profit_rate_rule`
--

CREATE TABLE IF NOT EXISTS `order_profit_rate_rule` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `start_rate` double NOT NULL,
  `end_rate` double NOT NULL,
  `creator` int(11) NOT NULL,
  `created_date` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;

--
-- 转存表中的数据 `order_profit_rate_rule`
--


-- --------------------------------------------------------

--
-- 表的结构 `order_profit_rate_rule_permission`
--

CREATE TABLE IF NOT EXISTS `order_profit_rate_rule_permission` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `rule_id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;

--
-- 转存表中的数据 `order_profit_rate_rule_permission`
--


-- --------------------------------------------------------

--
-- 表的结构 `order_recommend_list`
--

CREATE TABLE IF NOT EXISTS `order_recommend_list` (
  `id` bigint(20) NOT NULL AUTO_INCREMENT,
  `order_id` varchar(50) NOT NULL,
  `sku_str` varchar(500) NOT NULL,
  `qty_str` varchar(500) NOT NULL,
  `recommend_no` varchar(50) NOT NULL,
  `status` varchar(50) NOT NULL,
  `cause` varchar(50) NOT NULL,
  `email_time` varchar(50) NOT NULL,
  `finish_status` varchar(50) NOT NULL DEFAULT 'wait_for_proccess',
  `remark` text NOT NULL,
  `creator` varchar(50) NOT NULL,
  `created_date` datetime NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;

--
-- 转存表中的数据 `order_recommend_list`
--


-- --------------------------------------------------------

--
-- 表的结构 `order_remark`
--

CREATE TABLE IF NOT EXISTS `order_remark` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `order_id` int(11) NOT NULL,
  `add_user` varchar(255) NOT NULL,
  `remark_content` text NOT NULL,
  `add_date` datetime NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;

--
-- 转存表中的数据 `order_remark`
--


-- --------------------------------------------------------

--
-- 表的结构 `order_role_map`
--

CREATE TABLE IF NOT EXISTS `order_role_map` (
  `id` bigint(20) NOT NULL AUTO_INCREMENT,
  `order_id` bigint(20) NOT NULL,
  `stock_user_id` int(11) NOT NULL,
  `sale_id` int(11) NOT NULL,
  `purchaser_id` int(11) NOT NULL,
  `developer_id` int(11) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;

--
-- 转存表中的数据 `order_role_map`
--


-- --------------------------------------------------------

--
-- 表的结构 `order_sendmoney`
--

CREATE TABLE IF NOT EXISTS `order_sendmoney` (
  `transaction_id` varchar(255) NOT NULL,
  `input_user` varchar(255) NOT NULL,
  `tomail` varchar(255) NOT NULL,
  `from_email` varchar(40) NOT NULL,
  `input_date` datetime NOT NULL
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

--
-- 转存表中的数据 `order_sendmoney`
--


-- --------------------------------------------------------

--
-- 表的结构 `order_shipping_record`
--

CREATE TABLE IF NOT EXISTS `order_shipping_record` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `yesterday_order_left_count` int(11) NOT NULL,
  `current_print_label_count` int(11) NOT NULL,
  `current_shipping_count` int(11) NOT NULL,
  `current_order_left_count` int(11) NOT NULL,
  `stock_note` text NOT NULL,
  `shipping_note` text NOT NULL,
  `created_date` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;

--
-- 转存表中的数据 `order_shipping_record`
--


-- --------------------------------------------------------

--
-- 表的结构 `order_shipping_record_note`
--

CREATE TABLE IF NOT EXISTS `order_shipping_record_note` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `date` varchar(20) NOT NULL,
  `stock_note` text NOT NULL,
  `shipping_note` text NOT NULL,
  `created_date` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;

--
-- 转存表中的数据 `order_shipping_record_note`
--


-- --------------------------------------------------------

--
-- 表的结构 `order_splited_list`
--

CREATE TABLE IF NOT EXISTS `order_splited_list` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `item_id_str` varchar(200) NOT NULL,
  `transaction_id` varchar(20) NOT NULL,
  `created_date` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `order_id` bigint(20) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;

--
-- 转存表中的数据 `order_splited_list`
--


-- --------------------------------------------------------

--
-- 表的结构 `order_view_permission`
--

CREATE TABLE IF NOT EXISTS `order_view_permission` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `user_id` int(11) NOT NULL,
  `created_date` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;

--
-- 转存表中的数据 `order_view_permission`
--


-- --------------------------------------------------------

--
-- 表的结构 `order_wait_sku`
--

CREATE TABLE IF NOT EXISTS `order_wait_sku` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `sku` varchar(20) NOT NULL,
  `continent_id` int(11) NOT NULL,
  `created_date` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;

--
-- 转存表中的数据 `order_wait_sku`
--


-- --------------------------------------------------------

--
-- 表的结构 `paypal`
--

CREATE TABLE IF NOT EXISTS `paypal` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `paypal` varchar(255) NOT NULL,
  `apiuser` varchar(255) NOT NULL,
  `apipass` varchar(255) NOT NULL,
  `apisign` text NOT NULL,
  `user` varchar(20) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;

--
-- 转存表中的数据 `paypal`
--


-- --------------------------------------------------------

--
-- 表的结构 `paypal_cost`
--

CREATE TABLE IF NOT EXISTS `paypal_cost` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `name` varchar(200) NOT NULL,
  `formula` varchar(200) NOT NULL,
  `creator` int(11) NOT NULL,
  `created_date` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;

--
-- 转存表中的数据 `paypal_cost`
--


-- --------------------------------------------------------

--
-- 表的结构 `permission_block`
--

CREATE TABLE IF NOT EXISTS `permission_block` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `prefix` varchar(50) NOT NULL,
  `key` varchar(50) NOT NULL,
  `name` varchar(50) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 AUTO_INCREMENT=59 ;

--
-- 转存表中的数据 `permission_block`
--

INSERT INTO `permission_block` (`id`, `prefix`, `key`, `name`) VALUES
(1, 'product', 'sku', 'sku'),
(2, 'product', 'name_cn', 'chinese_name'),
(3, 'product', 'name_en', 'english_name'),
(4, 'product', 'description', 'description'),
(5, 'product', 'short_description', 'short_description'),
(6, 'product', 'pure_weight', 'pure_weight'),
(7, 'product', 'width', 'width'),
(8, 'product', 'length', 'length'),
(9, 'product', 'height', 'height'),
(10, 'product', 'image_url', 'small_image_url'),
(11, 'product', 'video_url', 'video_url'),
(12, 'product', 'market_model', 'market_model'),
(13, 'product', 'box_contain_number', 'box_contain_number'),
(14, 'product', 'box_total_weight', 'box_total_weight'),
(15, 'product', 'box_pure_weight', 'box_pure_weight'),
(16, 'product', 'box_width', 'box_width'),
(17, 'product', 'box_height', 'box_height'),
(18, 'product', 'box_length', 'box_length'),
(19, 'product', 'stock_code', 'stock_code'),
(20, 'product', 'shelf_code', 'shelf_code'),
(21, 'product', 'stock_count', 'stock_count'),
(22, 'product', 'packing_id', 'packing_id'),
(23, 'product', 'bulky_cargo', 'bulky_cargo'),
(24, 'product', 'sale_status', 'sale_status'),
(25, 'product', 'forbidden_level', 'forbidden_level'),
(26, 'product', 'sale_amount_level', 'sale_amount_level'),
(27, 'product', 'sale_quota_level', 'sale_quota_level'),
(28, 'product', 'lowest_profit', 'lowest_profit'),
(29, 'product', 'picture_url', 'picture_url'),
(30, 'product', 'min_stock_number', 'min_stock_number'),
(31, 'product', 'packing_or_not', 'packing_or_not'),
(46, 'product', 'pack_cost', 'pack_cost'),
(33, 'product', 'shipping_cost', 'shipping_cost'),
(34, 'product', 'packing_material', 'packing_material'),
(35, 'product', 'product_catalog', 'product_catalog'),
(36, 'product', 'provider_management', 'provider_management'),
(37, 'product', 'purchaser', 'purchaser'),
(43, 'product', 'sale_in_60_days', '60-days_sales_amounts'),
(42, 'product', 'sale_in_30_days', '30-days_sales_amounts'),
(41, 'product', 'sale_in_7_days', '7-days_sales_amounts'),
(44, 'product', 'price', 'price'),
(45, 'product', 'pack_weight', 'pack_weight'),
(47, 'product', 'fill_material_heavy', 'fill_material_heavy'),
(48, 'product', 'total_weight', 'total_weight'),
(49, 'product', 'product_develper', 'product_develper'),
(50, 'product', 'product_images_management', 'product_images_management'),
(51, 'product', 'product_images_view', 'product_images_view'),
(52, 'product', 'ebay_images_management', 'ebay_images_management'),
(53, 'product', 'ebay_images_view', 'ebay_images_view'),
(54, 'product', 'ito_in_30_days', 'ito_in_30_days'),
(55, 'product', 'description_cn', 'description_cn'),
(56, 'product', 'buy_url', 'buy_url'),
(57, 'product', 'sale_price', 'sale_price'),
(58, 'product', 'sale_in_15_days', '15-days_sales_amounts');

-- --------------------------------------------------------

--
-- 表的结构 `product_ban_levels`
--

CREATE TABLE IF NOT EXISTS `product_ban_levels` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `product_id` int(11) NOT NULL,
  `ban_level` tinyint(4) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;

--
-- 转存表中的数据 `product_ban_levels`
--


-- --------------------------------------------------------

--
-- 表的结构 `product_basic`
--

CREATE TABLE IF NOT EXISTS `product_basic` (
  `id` bigint(20) NOT NULL AUTO_INCREMENT,
  `sku` varchar(30) NOT NULL,
  `sku_other` varchar(20) DEFAULT NULL,
  `name_cn` varchar(100) DEFAULT NULL,
  `name_en` varchar(100) DEFAULT NULL,
  `catalog_id` int(11) NOT NULL DEFAULT '1',
  `description` text,
  `short_description` text,
  `description_cn` text,
  `short_description_cn` text,
  `price` double DEFAULT '0',
  `pure_weight` int(11) DEFAULT NULL,
  `width` double DEFAULT NULL,
  `length` double DEFAULT NULL,
  `height` double DEFAULT NULL,
  `image_url` varchar(200) DEFAULT NULL,
  `video_url` varchar(200) DEFAULT NULL,
  `buy_url` varchar(500) DEFAULT NULL,
  `market_model` varchar(250) DEFAULT NULL,
  `box_contain_number` int(11) DEFAULT NULL,
  `box_total_weight` double DEFAULT NULL,
  `box_pure_weight` double DEFAULT NULL,
  `box_width` double DEFAULT NULL,
  `box_height` double DEFAULT NULL,
  `box_length` double DEFAULT NULL,
  `stock_code` varchar(20) DEFAULT 'GZ',
  `shelf_code` varchar(20) DEFAULT NULL,
  `stock_count` int(11) DEFAULT '0',
  `au_on_way_count` int(11) DEFAULT NULL,
  `de_on_way_count` int(11) DEFAULT NULL,
  `uk_on_way_count` int(11) DEFAULT NULL,
  `au_dueout_count` int(11) DEFAULT NULL,
  `de_dueout_count` int(11) DEFAULT NULL,
  `uk_dueout_count` int(11) DEFAULT NULL,
  `au_stock_count` int(11) DEFAULT NULL,
  `de_stock_count` int(11) DEFAULT NULL,
  `uk_stock_count` int(11) DEFAULT NULL,
  `dueout_count` int(11) DEFAULT '0',
  `on_way_count` int(11) DEFAULT '0',
  `packing_id` int(11) DEFAULT NULL,
  `bulky_cargo` tinyint(4) DEFAULT NULL,
  `min_stock_number` int(11) DEFAULT NULL,
  `au_min_stock_number` int(11) DEFAULT NULL,
  `de_min_stock_number` int(11) DEFAULT NULL,
  `uk_min_stock_number` int(11) DEFAULT NULL,
  `packing_or_not` tinyint(4) DEFAULT NULL,
  `packing_cost` double DEFAULT NULL,
  `shipping_cost` double DEFAULT NULL,
  `packing_material` int(11) DEFAULT NULL,
  `purchaser_id` int(11) DEFAULT NULL,
  `product_develper_id` int(11) DEFAULT NULL,
  `stock_user_id` int(11) DEFAULT NULL,
  `tester_id` int(11) DEFAULT NULL,
  `seo_user_id` int(11) DEFAULT NULL,
  `sale_status` tinyint(4) DEFAULT '3',
  `sale_price` double DEFAULT '0',
  `forbidden_level` tinyint(4) DEFAULT NULL,
  `sale_amount_level` double DEFAULT NULL,
  `sale_quota_level` double DEFAULT NULL,
  `lowest_profit` double DEFAULT NULL,
  `picture_url` varchar(200) DEFAULT NULL,
  `pack_cost` double DEFAULT NULL,
  `total_weight` double DEFAULT NULL,
  `pack_weight` double DEFAULT NULL,
  `fill_material_heavy` double DEFAULT '0',
  `sale_in_7_days` int(11) DEFAULT '0',
  `sale_in_15_days` int(11) DEFAULT '0',
  `sale_in_30_days` int(11) DEFAULT '0',
  `sale_in_90_days` int(11) DEFAULT '0',
  `sale_in_60_days` int(11) NOT NULL DEFAULT '0',
  `ito_in_30_days` double DEFAULT '0',
  `updated_date` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  `stock_check_date` datetime DEFAULT NULL,
  `yb_on_way_count` int(11) DEFAULT NULL,
  `yb_dueout_count` int(11) DEFAULT NULL,
  `yb_stock_count` int(11) DEFAULT '0',
  `yb_min_stock_number` int(11) DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `sku` (`sku`),
  KEY `pure_weight` (`pure_weight`),
  KEY `price` (`price`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;

--
-- 转存表中的数据 `product_basic`
--


-- --------------------------------------------------------

--
-- 表的结构 `product_catalog`
--

CREATE TABLE IF NOT EXISTS `product_catalog` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `name_cn` varchar(50) NOT NULL,
  `name_en` varchar(50) NOT NULL,
  `parent` int(11) NOT NULL,
  `path` varchar(100) NOT NULL,
  `packing_material` int(11) DEFAULT NULL,
  `lowest_profit` double NOT NULL,
  `packing_difficulty_factor` float NOT NULL,
  `third_platform` varchar(20) NOT NULL,
  `purchaser_id` int(11) DEFAULT NULL,
  `stock_user_id` int(11) NOT NULL,
  `saler_id` int(11) DEFAULT NULL,
  `tester_id` int(11) NOT NULL,
  `seo_user_id` int(11) NOT NULL DEFAULT '0',
  `created_date` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;

--
-- 转存表中的数据 `product_catalog`
--


-- --------------------------------------------------------

--
-- 表的结构 `product_catalog_sale_permission`
--

CREATE TABLE IF NOT EXISTS `product_catalog_sale_permission` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `product_catalog_id` int(11) NOT NULL,
  `saler_id` int(11) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;

--
-- 转存表中的数据 `product_catalog_sale_permission`
--


-- --------------------------------------------------------

--
-- 表的结构 `product_delete_permission`
--

CREATE TABLE IF NOT EXISTS `product_delete_permission` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `user_id` int(11) NOT NULL,
  `created_date` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;

--
-- 转存表中的数据 `product_delete_permission`
--


-- --------------------------------------------------------

--
-- 表的结构 `product_inoutstock_report`
--

CREATE TABLE IF NOT EXISTS `product_inoutstock_report` (
  `id` bigint(20) NOT NULL AUTO_INCREMENT,
  `stock_type` varchar(200) DEFAULT NULL,
  `product_id` int(11) NOT NULL,
  `order_sku_id` bigint(20) NOT NULL,
  `user_id` int(11) NOT NULL,
  `change_count` int(11) NOT NULL,
  `before_change_count` int(11) NOT NULL DEFAULT '0',
  `after_change_count` int(11) NOT NULL,
  `type` varchar(200) DEFAULT NULL,
  `type_extra` text,
  `status` tinyint(4) NOT NULL DEFAULT '0',
  `verifyer` int(11) DEFAULT NULL,
  `verify_date` varchar(20) DEFAULT NULL,
  `updated_time` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `stock_code` varchar(20) DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;

--
-- 转存表中的数据 `product_inoutstock_report`
--


-- --------------------------------------------------------

--
-- 表的结构 `product_instock_apply_report`
--

CREATE TABLE IF NOT EXISTS `product_instock_apply_report` (
  `id` bigint(20) NOT NULL AUTO_INCREMENT,
  `product_id` int(11) NOT NULL,
  `order_sku_id` bigint(20) NOT NULL,
  `user_id` int(11) NOT NULL,
  `instock_count` int(11) NOT NULL,
  `before_change_count` int(11) NOT NULL,
  `after_change_count` int(11) NOT NULL,
  `type` varchar(200) DEFAULT NULL,
  `type_extra` text,
  `status` tinyint(4) NOT NULL DEFAULT '0',
  `verifyer` int(11) DEFAULT NULL,
  `verify_date` varchar(20) DEFAULT NULL,
  `instock_time` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;

--
-- 转存表中的数据 `product_instock_apply_report`
--


-- --------------------------------------------------------

--
-- 表的结构 `product_instock_report_more`
--

CREATE TABLE IF NOT EXISTS `product_instock_report_more` (
  `id` bigint(20) NOT NULL AUTO_INCREMENT,
  `report_id` bigint(20) NOT NULL,
  `old_shelf_code` varchar(20) NOT NULL,
  `new_shelf_code` varchar(20) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;

--
-- 转存表中的数据 `product_instock_report_more`
--


-- --------------------------------------------------------

--
-- 表的结构 `product_ito_record`
--

CREATE TABLE IF NOT EXISTS `product_ito_record` (
  `id` bigint(20) NOT NULL AUTO_INCREMENT,
  `product_id` int(11) NOT NULL,
  `year` int(11) NOT NULL,
  `month` int(11) NOT NULL,
  `total_sale_amount` double NOT NULL,
  `total_stock_amount` double NOT NULL,
  `ito` double NOT NULL,
  `purchaser_id` int(11) NOT NULL,
  `created_date` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;

--
-- 转存表中的数据 `product_ito_record`
--


-- --------------------------------------------------------

--
-- 表的结构 `product_makeup_sku`
--

CREATE TABLE IF NOT EXISTS `product_makeup_sku` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `user_id` int(11) DEFAULT NULL,
  `makeup_sku` varchar(255) NOT NULL,
  `sku` varchar(1000) NOT NULL,
  `qty` varchar(1000) NOT NULL,
  `update_date` varchar(20) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;

--
-- 转存表中的数据 `product_makeup_sku`
--


-- --------------------------------------------------------

--
-- 表的结构 `product_net_name`
--

CREATE TABLE IF NOT EXISTS `product_net_name` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `user_id` int(11) DEFAULT NULL,
  `net_name` varchar(255) NOT NULL,
  `sku` varchar(1000) NOT NULL,
  `shipping_code` varchar(10) NOT NULL,
  `item_id` varchar(20) NOT NULL,
  `update_date` varchar(20) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;

--
-- 转存表中的数据 `product_net_name`
--


-- --------------------------------------------------------

--
-- 表的结构 `product_outstock_report`
--

CREATE TABLE IF NOT EXISTS `product_outstock_report` (
  `id` bigint(20) NOT NULL AUTO_INCREMENT,
  `product_id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `outstock_count` int(11) NOT NULL,
  `before_change_count` int(11) NOT NULL,
  `after_change_count` int(11) NOT NULL,
  `type` varchar(200) DEFAULT NULL,
  `type_extra` text,
  `outstock_time` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  KEY `product_outstock_report_product_id` (`product_id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;

--
-- 转存表中的数据 `product_outstock_report`
--


-- --------------------------------------------------------

--
-- 表的结构 `product_outstock_type`
--

CREATE TABLE IF NOT EXISTS `product_outstock_type` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `type` varchar(50) NOT NULL,
  `is_saled` tinyint(4) NOT NULL DEFAULT '0',
  `creator` varchar(20) NOT NULL,
  `created_date` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;

--
-- 转存表中的数据 `product_outstock_type`
--


-- --------------------------------------------------------

--
-- 表的结构 `product_packing`
--

CREATE TABLE IF NOT EXISTS `product_packing` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `name_cn` varchar(20) NOT NULL,
  `name_en` varchar(40) NOT NULL,
  `image_url` varchar(200) NOT NULL,
  `length` float NOT NULL,
  `width` float NOT NULL,
  `height` float NOT NULL,
  `weight` float NOT NULL,
  `content` double NOT NULL,
  `cost` double NOT NULL,
  `created_date` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;

--
-- 转存表中的数据 `product_packing`
--


-- --------------------------------------------------------

--
-- 表的结构 `product_permission`
--

CREATE TABLE IF NOT EXISTS `product_permission` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `block_id` int(11) NOT NULL,
  `group_id` int(11) NOT NULL,
  `permission` tinyint(4) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;

--
-- 转存表中的数据 `product_permission`
--


-- --------------------------------------------------------

--
-- 表的结构 `product_purchase_apply`
--

CREATE TABLE IF NOT EXISTS `product_purchase_apply` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `sku` varchar(20) NOT NULL,
  `apply_user_id` int(11) NOT NULL,
  `product_name` varchar(200) NOT NULL,
  `product_description` text NOT NULL,
  `product_image_url` varchar(500) NOT NULL,
  `reference_links` varchar(1500) NOT NULL,
  `sales_strategy` varchar(1500) NOT NULL,
  `sales_statistics` varchar(1500) NOT NULL,
  `related_specifications` varchar(1500) NOT NULL,
  `provider` varchar(1500) NOT NULL,
  `apply_status` tinyint(4) NOT NULL,
  `review_user_id` int(11) NOT NULL,
  `develper_id` int(11) NOT NULL,
  `purchaser_id` int(11) NOT NULL,
  `edit_user_id` int(11) NOT NULL,
  `created_date` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  KEY `id` (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;

--
-- 转存表中的数据 `product_purchase_apply`
--


-- --------------------------------------------------------

--
-- 表的结构 `product_sale_record`
--

CREATE TABLE IF NOT EXISTS `product_sale_record` (
  `id` bigint(11) NOT NULL AUTO_INCREMENT,
  `sku` varchar(20) NOT NULL,
  `sale_amount` int(11) NOT NULL,
  `price` double NOT NULL,
  `days_ago` int(11) NOT NULL,
  `updated_date` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;

--
-- 转存表中的数据 `product_sale_record`
--


-- --------------------------------------------------------

--
-- 表的结构 `product_shelf_code`
--

CREATE TABLE IF NOT EXISTS `product_shelf_code` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `name` varchar(50) NOT NULL,
  `creator` int(11) NOT NULL,
  `created_date` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;

--
-- 转存表中的数据 `product_shelf_code`
--


-- --------------------------------------------------------

--
-- 表的结构 `product_statistics_history`
--

CREATE TABLE IF NOT EXISTS `product_statistics_history` (
  `id` bigint(20) NOT NULL AUTO_INCREMENT,
  `sku` varchar(20) NOT NULL,
  `qty` int(11) NOT NULL,
  `user` varchar(20) NOT NULL,
  `created_date` varchar(20) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;

--
-- 转存表中的数据 `product_statistics_history`
--


-- --------------------------------------------------------

--
-- 表的结构 `product_statistics_outstock_history`
--

CREATE TABLE IF NOT EXISTS `product_statistics_outstock_history` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `outstock_endtime` varchar(20) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;

--
-- 转存表中的数据 `product_statistics_outstock_history`
--


-- --------------------------------------------------------

--
-- 表的结构 `product_stock_check_or_count`
--

CREATE TABLE IF NOT EXISTS `product_stock_check_or_count` (
  `id` bigint(20) NOT NULL AUTO_INCREMENT,
  `product_id` bigint(20) NOT NULL,
  `user_id` int(11) NOT NULL,
  `change_count` int(11) NOT NULL,
  `before_change_count` int(11) NOT NULL,
  `after_change_count` int(11) NOT NULL,
  `type` varchar(50) NOT NULL,
  `type_extra` varchar(100) NOT NULL,
  `update_date` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;

--
-- 转存表中的数据 `product_stock_check_or_count`
--


-- --------------------------------------------------------

--
-- 表的结构 `provider_product_map`
--

CREATE TABLE IF NOT EXISTS `provider_product_map` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `provider_id` int(11) NOT NULL,
  `product_id` int(11) NOT NULL,
  `price1to9` double NOT NULL,
  `price10to99` double NOT NULL,
  `price100to999` double NOT NULL,
  `price1000` double NOT NULL,
  `provide_level` tinyint(11) NOT NULL DEFAULT '127',
  `separating_shipping_cost` double NOT NULL,
  `date` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;

--
-- 转存表中的数据 `provider_product_map`
--


-- --------------------------------------------------------

--
-- 表的结构 `purchase_how`
--

CREATE TABLE IF NOT EXISTS `purchase_how` (
  `id` bigint(20) NOT NULL AUTO_INCREMENT,
  `order_sku_id` bigint(20) NOT NULL,
  `sku` varchar(50) NOT NULL,
  `how_way` int(11) NOT NULL,
  `qualified_number` int(11) NOT NULL,
  `how_state` tinyint(4) NOT NULL DEFAULT '0',
  `how_note` text NOT NULL,
  `created_date` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;

--
-- 转存表中的数据 `purchase_how`
--


-- --------------------------------------------------------

--
-- 表的结构 `purchase_order`
--

CREATE TABLE IF NOT EXISTS `purchase_order` (
  `id` bigint(20) NOT NULL AUTO_INCREMENT,
  `item_no` varchar(50) NOT NULL,
  `provider_id` int(11) NOT NULL,
  `purchaser_id` int(11) NOT NULL,
  `purchase_note` text NOT NULL,
  `review_state` tinyint(4) NOT NULL,
  `payment_type` tinyint(4) NOT NULL,
  `item_cost` double NOT NULL,
  `payment_state` tinyint(4) NOT NULL DEFAULT '0',
  `reject` tinyint(4) NOT NULL DEFAULT '0',
  `arrival_date` datetime NOT NULL,
  `created_date` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;

--
-- 转存表中的数据 `purchase_order`
--


-- --------------------------------------------------------

--
-- 表的结构 `purchase_order_sku`
--

CREATE TABLE IF NOT EXISTS `purchase_order_sku` (
  `id` bigint(20) NOT NULL AUTO_INCREMENT,
  `purchase_order_id` bigint(20) NOT NULL,
  `sku` varchar(20) NOT NULL,
  `sku_quantity` int(11) NOT NULL,
  `sku_price` double NOT NULL,
  `sku_note` text NOT NULL,
  `sku_arrival_quantity` int(11) NOT NULL,
  `sku_arrival_state` tinyint(4) NOT NULL DEFAULT '0',
  `arrival_date` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;

--
-- 转存表中的数据 `purchase_order_sku`
--


-- --------------------------------------------------------

--
-- 表的结构 `purchase_payment`
--

CREATE TABLE IF NOT EXISTS `purchase_payment` (
  `id` bigint(20) NOT NULL AUTO_INCREMENT,
  `purchase_order_id` bigint(20) NOT NULL,
  `payment_cost` double NOT NULL,
  `created_date` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;

--
-- 转存表中的数据 `purchase_payment`
--


-- --------------------------------------------------------

--
-- 表的结构 `purchase_provider`
--

CREATE TABLE IF NOT EXISTS `purchase_provider` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `name` varchar(255) NOT NULL,
  `boss` varchar(100) NOT NULL,
  `address` varchar(255) NOT NULL,
  `phone` varchar(255) NOT NULL,
  `fax` varchar(100) NOT NULL,
  `email` varchar(255) NOT NULL,
  `qq` varchar(100) NOT NULL,
  `web` varchar(255) NOT NULL,
  `contact_person` varchar(255) NOT NULL,
  `mobile` varchar(255) NOT NULL,
  `open_bank` varchar(255) NOT NULL,
  `bank_account` varchar(255) NOT NULL,
  `bank_title` varchar(255) NOT NULL,
  `remark` text NOT NULL,
  `edit_user` varchar(50) NOT NULL,
  `edit_date` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;

--
-- 转存表中的数据 `purchase_provider`
--


-- --------------------------------------------------------

--
-- 表的结构 `purchase_provider_permission`
--

CREATE TABLE IF NOT EXISTS `purchase_provider_permission` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `provider_id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `permission` tinyint(4) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;

--
-- 转存表中的数据 `purchase_provider_permission`
--


-- --------------------------------------------------------

--
-- 表的结构 `receipt_way_list`
--

CREATE TABLE IF NOT EXISTS `receipt_way_list` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `receipt_name` varchar(200) NOT NULL,
  `creator_id` int(11) NOT NULL,
  `created_date` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;

--
-- 转存表中的数据 `receipt_way_list`
--


-- --------------------------------------------------------

--
-- 表的结构 `saler_ebay_id_map`
--

CREATE TABLE IF NOT EXISTS `saler_ebay_id_map` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `saler_id` int(11) NOT NULL,
  `ebay_id_str` varchar(500) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;

--
-- 转存表中的数据 `saler_ebay_id_map`
--


-- --------------------------------------------------------

--
-- 表的结构 `seo_content`
--

CREATE TABLE IF NOT EXISTS `seo_content` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `title` varchar(200) NOT NULL,
  `type` int(11) NOT NULL,
  `content` text NOT NULL,
  `language` varchar(20) NOT NULL,
  `owner_id` int(11) NOT NULL,
  `integral_state` int(11) NOT NULL DEFAULT '0',
  `update_date` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;

--
-- 转存表中的数据 `seo_content`
--


-- --------------------------------------------------------

--
-- 表的结构 `seo_content_catalog`
--

CREATE TABLE IF NOT EXISTS `seo_content_catalog` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `name` varchar(30) NOT NULL,
  `integral` int(11) NOT NULL,
  `creator` varchar(30) NOT NULL,
  `created_date` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;

--
-- 转存表中的数据 `seo_content_catalog`
--


-- --------------------------------------------------------

--
-- 表的结构 `seo_content_catalog_map`
--

CREATE TABLE IF NOT EXISTS `seo_content_catalog_map` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `content_id` int(11) NOT NULL,
  `catalog_id` int(11) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;

--
-- 转存表中的数据 `seo_content_catalog_map`
--


-- --------------------------------------------------------

--
-- 表的结构 `seo_content_company_map`
--

CREATE TABLE IF NOT EXISTS `seo_content_company_map` (
  `id` bigint(20) NOT NULL AUTO_INCREMENT,
  `content_id` int(11) NOT NULL,
  `company_id` int(11) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;

--
-- 转存表中的数据 `seo_content_company_map`
--


-- --------------------------------------------------------

--
-- 表的结构 `seo_content_permission`
--

CREATE TABLE IF NOT EXISTS `seo_content_permission` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `content_id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;

--
-- 转存表中的数据 `seo_content_permission`
--


-- --------------------------------------------------------

--
-- 表的结构 `seo_content_resource_category_map`
--

CREATE TABLE IF NOT EXISTS `seo_content_resource_category_map` (
  `id` bigint(20) NOT NULL AUTO_INCREMENT,
  `content_catalog_id` int(11) NOT NULL,
  `resource_category_id` int(11) NOT NULL,
  `integral` int(11) NOT NULL DEFAULT '1',
  `created_date` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;

--
-- 转存表中的数据 `seo_content_resource_category_map`
--


-- --------------------------------------------------------

--
-- 表的结构 `seo_content_type`
--

CREATE TABLE IF NOT EXISTS `seo_content_type` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `name` varchar(20) NOT NULL,
  `integral` int(11) NOT NULL DEFAULT '0',
  `creator` varchar(20) NOT NULL,
  `created_date` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;

--
-- 转存表中的数据 `seo_content_type`
--


-- --------------------------------------------------------

--
-- 表的结构 `seo_keyword`
--

CREATE TABLE IF NOT EXISTS `seo_keyword` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `keyword` varchar(200) NOT NULL,
  `link_url` varchar(300) NOT NULL,
  `global_search_monthly` int(11) NOT NULL,
  `usa_search` int(11) NOT NULL,
  `search_result` int(11) NOT NULL,
  `search_intitle` int(11) NOT NULL,
  `compete_index` int(11) NOT NULL,
  `compete_price` varchar(50) NOT NULL,
  `intitle` int(11) NOT NULL,
  `price_per_click` varchar(50) NOT NULL,
  `page_first_ten` int(11) NOT NULL,
  `com_ranking` varchar(11) NOT NULL,
  `level` int(11) NOT NULL,
  `note` text NOT NULL,
  `integral_state` int(11) NOT NULL DEFAULT '0',
  `creator` int(11) NOT NULL,
  `created_date` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;

--
-- 转存表中的数据 `seo_keyword`
--


-- --------------------------------------------------------

--
-- 表的结构 `seo_keyword_catalog_map`
--

CREATE TABLE IF NOT EXISTS `seo_keyword_catalog_map` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `keyword_id` int(11) NOT NULL,
  `catalog_id` int(11) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;

--
-- 转存表中的数据 `seo_keyword_catalog_map`
--


-- --------------------------------------------------------

--
-- 表的结构 `seo_keyword_company_map`
--

CREATE TABLE IF NOT EXISTS `seo_keyword_company_map` (
  `id` bigint(20) NOT NULL AUTO_INCREMENT,
  `keyword_id` int(11) NOT NULL,
  `company_id` int(11) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;

--
-- 转存表中的数据 `seo_keyword_company_map`
--


-- --------------------------------------------------------

--
-- 表的结构 `seo_keyword_permission`
--

CREATE TABLE IF NOT EXISTS `seo_keyword_permission` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `keyword_id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;

--
-- 转存表中的数据 `seo_keyword_permission`
--


-- --------------------------------------------------------

--
-- 表的结构 `seo_release`
--

CREATE TABLE IF NOT EXISTS `seo_release` (
  `id` bigint(20) NOT NULL AUTO_INCREMENT,
  `owner_id` int(11) NOT NULL,
  `content_id` int(11) NOT NULL,
  `resource_id` int(11) NOT NULL,
  `validate_url` varchar(500) NOT NULL,
  `status` tinyint(4) NOT NULL,
  `remark` varchar(200) NOT NULL,
  `created_date` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;

--
-- 转存表中的数据 `seo_release`
--


-- --------------------------------------------------------

--
-- 表的结构 `seo_resource`
--

CREATE TABLE IF NOT EXISTS `seo_resource` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `owner_id` int(11) NOT NULL,
  `url` varchar(200) NOT NULL,
  `root_pr` tinyint(4) NOT NULL,
  `current_pr` tinyint(4) NOT NULL,
  `language` varchar(4) NOT NULL,
  `can_post_message` tinyint(4) NOT NULL,
  `do_follow` tinyint(4) NOT NULL,
  `export_links` int(11) NOT NULL,
  `category` smallint(6) NOT NULL,
  `username` varchar(50) NOT NULL,
  `password` varchar(50) NOT NULL,
  `email` varchar(30) NOT NULL,
  `update_date` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `validate_url` varchar(100) NOT NULL,
  `status` int(11) NOT NULL DEFAULT '0',
  `integral_state` int(4) NOT NULL DEFAULT '0',
  `release_left` int(11) NOT NULL DEFAULT '1000',
  `release_left_wholelife` int(11) NOT NULL DEFAULT '-1',
  `note` text NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;

--
-- 转存表中的数据 `seo_resource`
--


-- --------------------------------------------------------

--
-- 表的结构 `seo_resource_category`
--

CREATE TABLE IF NOT EXISTS `seo_resource_category` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `name` varchar(30) NOT NULL,
  `integral` int(11) NOT NULL,
  `release_limit` int(11) NOT NULL DEFAULT '1000',
  `release_limit_wholelife` int(11) NOT NULL DEFAULT '-1',
  `creator` varchar(30) NOT NULL,
  `created_date` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;

--
-- 转存表中的数据 `seo_resource_category`
--


-- --------------------------------------------------------

--
-- 表的结构 `seo_resource_company_map`
--

CREATE TABLE IF NOT EXISTS `seo_resource_company_map` (
  `id` bigint(20) NOT NULL AUTO_INCREMENT,
  `resource_id` int(11) NOT NULL,
  `company_id` int(11) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;

--
-- 转存表中的数据 `seo_resource_company_map`
--


-- --------------------------------------------------------

--
-- 表的结构 `seo_resource_permission`
--

CREATE TABLE IF NOT EXISTS `seo_resource_permission` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `resource_id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;

--
-- 转存表中的数据 `seo_resource_permission`
--


-- --------------------------------------------------------

--
-- 表的结构 `seo_service_company`
--

CREATE TABLE IF NOT EXISTS `seo_service_company` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `name` varchar(50) NOT NULL,
  `website` varchar(50) NOT NULL,
  `description` varchar(200) NOT NULL,
  `creator_id` int(11) NOT NULL,
  `created_date` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;

--
-- 转存表中的数据 `seo_service_company`
--


-- --------------------------------------------------------

--
-- 表的结构 `shipping_code`
--

CREATE TABLE IF NOT EXISTS `shipping_code` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `code` varchar(8) NOT NULL,
  `name_cn` varchar(50) NOT NULL,
  `name_en` varchar(50) NOT NULL,
  `check_url` varchar(200) NOT NULL,
  `stock_code` varchar(10) NOT NULL,
  `taobao_company_code` varchar(50) NOT NULL,
  `wish_company_code` varchar(15) DEFAULT NULL,
  `ydf_code` varchar(20) DEFAULT NULL,
  `is_tracking` tinyint(4) NOT NULL DEFAULT '0',
  `contact_phone_requred` tinyint(4) NOT NULL,
  `created_date` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;

--
-- 转存表中的数据 `shipping_code`
--


-- --------------------------------------------------------

--
-- 表的结构 `shipping_company`
--

CREATE TABLE IF NOT EXISTS `shipping_company` (
  `id` bigint(20) NOT NULL AUTO_INCREMENT,
  `name` varchar(100) NOT NULL,
  `telephone` varchar(20) NOT NULL,
  `contact_person` varchar(50) NOT NULL,
  `remark` varchar(200) NOT NULL,
  `created_date` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;

--
-- 转存表中的数据 `shipping_company`
--


-- --------------------------------------------------------

--
-- 表的结构 `shipping_company_type`
--

CREATE TABLE IF NOT EXISTS `shipping_company_type` (
  `id` bigint(20) NOT NULL AUTO_INCREMENT,
  `company_id` bigint(20) NOT NULL,
  `type_id` bigint(20) NOT NULL,
  `status` tinyint(4) NOT NULL DEFAULT '1',
  PRIMARY KEY (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;

--
-- 转存表中的数据 `shipping_company_type`
--


-- --------------------------------------------------------

--
-- 表的结构 `shipping_function`
--

CREATE TABLE IF NOT EXISTS `shipping_function` (
  `id` int(20) NOT NULL AUTO_INCREMENT,
  `start_weight` double NOT NULL,
  `end_weight` double NOT NULL,
  `rule` varchar(500) NOT NULL,
  `rule_meaning` varchar(200) DEFAULT NULL,
  `weight_rule` varchar(500) NOT NULL,
  `subarea_id` int(11) NOT NULL,
  `company_type_id` int(20) NOT NULL,
  `created_date` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  KEY `start_weight` (`start_weight`),
  KEY `end_weight` (`end_weight`),
  KEY `rule` (`rule`(333)),
  KEY `weight_rule` (`weight_rule`(333)),
  KEY `subarea_id` (`subarea_id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;

--
-- 转存表中的数据 `shipping_function`
--


-- --------------------------------------------------------

--
-- 表的结构 `shipping_global_function`
--

CREATE TABLE IF NOT EXISTS `shipping_global_function` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `global_rule` varchar(500) NOT NULL,
  `company_type_id` int(11) NOT NULL,
  PRIMARY KEY (`id`),
  KEY `global_rule` (`global_rule`(333)),
  KEY `company_type_id` (`company_type_id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;

--
-- 转存表中的数据 `shipping_global_function`
--


-- --------------------------------------------------------

--
-- 表的结构 `shipping_stock_code`
--

CREATE TABLE IF NOT EXISTS `shipping_stock_code` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `stock_code` varchar(10) NOT NULL,
  `abroad` tinyint(4) NOT NULL DEFAULT '0',
  `status` tinyint(4) NOT NULL DEFAULT '1',
  `created_date` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;

--
-- 转存表中的数据 `shipping_stock_code`
--


-- --------------------------------------------------------

--
-- 表的结构 `shipping_subarea`
--

CREATE TABLE IF NOT EXISTS `shipping_subarea` (
  `id` bigint(20) NOT NULL AUTO_INCREMENT,
  `subarea_name` varchar(100) NOT NULL,
  `created_date` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  `subarea_group_id` bigint(20) NOT NULL,
  PRIMARY KEY (`id`),
  KEY `subarea_group_id` (`subarea_group_id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;

--
-- 转存表中的数据 `shipping_subarea`
--


-- --------------------------------------------------------

--
-- 表的结构 `shipping_subarea_country`
--

CREATE TABLE IF NOT EXISTS `shipping_subarea_country` (
  `id` bigint(20) NOT NULL AUTO_INCREMENT,
  `subarea_id` bigint(20) NOT NULL,
  `country_id` bigint(20) NOT NULL,
  PRIMARY KEY (`id`),
  KEY `subarea_id` (`subarea_id`),
  KEY `country_id` (`country_id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;

--
-- 转存表中的数据 `shipping_subarea_country`
--


-- --------------------------------------------------------

--
-- 表的结构 `shipping_subarea_group`
--

CREATE TABLE IF NOT EXISTS `shipping_subarea_group` (
  `id` bigint(20) NOT NULL AUTO_INCREMENT,
  `subarea_group_name` varchar(100) NOT NULL,
  `created_date` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;

--
-- 转存表中的数据 `shipping_subarea_group`
--


-- --------------------------------------------------------

--
-- 表的结构 `shipping_type`
--

CREATE TABLE IF NOT EXISTS `shipping_type` (
  `id` bigint(20) NOT NULL AUTO_INCREMENT,
  `type_name` varchar(50) NOT NULL,
  `code` varchar(10) NOT NULL,
  `arrival_time` varchar(50) NOT NULL,
  `description` text NOT NULL,
  `created_date` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `group_id` bigint(20) NOT NULL,
  PRIMARY KEY (`id`),
  FULLTEXT KEY `code` (`code`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;

--
-- 转存表中的数据 `shipping_type`
--


-- --------------------------------------------------------

--
-- 表的结构 `specification_epacket_confirm_list`
--

CREATE TABLE IF NOT EXISTS `specification_epacket_confirm_list` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `order_id` int(11) NOT NULL,
  `track_number` varchar(40) DEFAULT '',
  `print_label` int(11) NOT NULL DEFAULT '0',
  `lable_download_url` varchar(255) DEFAULT NULL,
  `confirmed` tinyint(4) NOT NULL DEFAULT '0',
  `label_content` text NOT NULL,
  `input_user` int(11) NOT NULL DEFAULT '0',
  `input_date` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `downloaded` tinyint(4) NOT NULL DEFAULT '0',
  `message` varchar(500) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;

--
-- 转存表中的数据 `specification_epacket_confirm_list`
--


-- --------------------------------------------------------

--
-- 表的结构 `status_map`
--

CREATE TABLE IF NOT EXISTS `status_map` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `type` varchar(50) NOT NULL,
  `status_id` int(11) NOT NULL,
  `status_name` varchar(50) NOT NULL,
  `created_date` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 AUTO_INCREMENT=107 ;

--
-- 转存表中的数据 `status_map`
--

INSERT INTO `status_map` (`id`, `type`, `status_id`, `status_name`, `created_date`) VALUES
(1, 'sale_status', 1, 'out_of_stock', '2011-01-10 09:32:26'),
(2, 'sale_status', 2, 'clear_stock', '2011-01-10 10:01:32'),
(3, 'sale_status', 3, 'in_stock', '2011-01-10 10:01:32'),
(4, 'ban_levels', 1, 'absolutely_ban', '2011-01-10 10:01:32'),
(5, 'ban_levels', 2, 'b2b_ban', '2011-01-10 10:01:32'),
(6, 'ban_levels', 3, 'tomtop_ban', '2011-01-10 10:01:32'),
(7, 'ban_levels', 4, 'ebay_ban', '2011-01-10 10:01:32'),
(8, 'ban_levels', 5, 'free_sale', '2011-01-10 10:01:32'),
(18, 'review_state', 2, 'director_review', '2011-01-20 14:46:37'),
(17, 'payment_type', 3, 'pay_portion_before_arrival', '2011-01-20 14:46:37'),
(16, 'payment_type', 2, 'pay_all_before_arrival', '2011-01-20 14:46:37'),
(15, 'payment_type', 1, 'pay_after_arrival', '2011-01-20 14:46:37'),
(19, 'review_state', 3, 'manager_review', '2011-01-20 14:46:37'),
(20, 'review_state', 5, 'general_manager_review', '2011-01-20 14:46:37'),
(21, 'purchase_apply_status', 1, 'in_proccess', '2011-01-21 09:28:19'),
(22, 'purchase_apply_status', 2, 'approved', '2011-01-21 09:28:19'),
(23, 'purchase_apply_status', 3, 'approved_and_edited', '2011-01-21 09:28:19'),
(24, 'purchase_apply_status', -1, 'rejected', '2011-01-21 09:28:19'),
(38, 'order_status', 5, 'finance_holded', '2011-01-28 16:02:10'),
(37, 'order_status', 4, 'wait_for_finance_confirmation', '2011-01-28 16:02:10'),
(36, 'order_status', 3, 'holded', '2011-01-28 16:02:10'),
(35, 'order_status', 2, 'wait_for_confirmation', '2011-01-28 16:02:10'),
(34, 'order_status', 1, 'wait_for_assignment', '2011-01-28 16:02:10'),
(33, 'order_status', -1, 'closed', '2011-01-28 16:02:10'),
(39, 'order_status', 6, 'wait_for_purchase', '2011-01-28 16:02:10'),
(40, 'order_status', 7, 'wait_for_shipping_label', '2011-01-28 16:02:10'),
(41, 'order_status', 8, 'wait_for_shipping_confirmation', '2011-01-31 11:39:38'),
(60, 'order_status', 9, 'wait_for_feedback', '2011-02-23 15:49:18'),
(61, 'order_status', 10, 'received', '2011-02-23 15:49:18'),
(49, 'purchase_payment_state', 3, 'paid_all', '2011-02-15 11:07:55'),
(47, 'purchase_payment_state', 0, 'not_review', '2011-02-15 11:05:07'),
(48, 'purchase_payment_state', 1, 'paid_zero', '2011-02-15 11:05:07'),
(50, 'purchase_payment_state', 2, 'paid_part', '2011-02-15 11:07:55'),
(53, 'resource_status', -1, 'close', '2011-02-22 18:34:00'),
(54, 'resource_status', 0, 'unrelease', '2011-02-22 18:34:00'),
(85, 'review_state', 8, 'completed', '2011-04-22 11:41:01'),
(57, 'resource_status', 1, 'release', '2011-02-22 18:34:45'),
(83, 'order_status', 26, 'not_shipped_agree_to_refund', '2011-03-31 14:14:47'),
(82, 'order_status', 25, 'not_shipped_apply_for_refund', '2011-03-31 14:14:47'),
(68, 'order_status', 11, 'not_received_apply_for_partial_refund', '2011-03-08 09:23:17'),
(69, 'order_status', 12, 'not_received_partial_refunded', '2011-03-08 09:23:17'),
(70, 'order_status', 13, 'not_received_apply_for_full_refund', '2011-03-08 09:23:17'),
(71, 'order_status', 14, 'not_received_full_refunded', '2011-03-08 09:23:17'),
(72, 'order_status', 15, 'not_received_apply_for_resending', '2011-03-08 09:23:17'),
(73, 'order_status', 16, 'not_received_approved_resending', '2011-03-08 09:23:17'),
(74, 'order_status', 17, 'not_received_resended', '2011-03-08 09:23:17'),
(75, 'order_status', 18, 'received_apply_for_partial_refund', '2011-03-08 09:23:17'),
(76, 'order_status', 19, 'received_partial_refunded', '2011-03-08 09:23:17'),
(77, 'order_status', 20, 'received_apply_for_full_refund', '2011-03-08 09:23:17'),
(78, 'order_status', 21, 'received_full_refunded', '2011-03-08 09:23:17'),
(79, 'order_status', 22, 'received_apply_for_resending', '2011-03-08 09:23:17'),
(80, 'order_status', 23, 'received_approved_resending', '2011-03-08 09:23:17'),
(81, 'order_status', 24, 'received_resended', '2011-03-08 09:23:17'),
(87, 'ban_levels', 6, 'ali_ban', '2011-06-08 09:31:07'),
(88, 'ban_levels', 7, 'dh_ban', '2011-06-08 09:31:07'),
(89, 'ban_levels', 8, 'taobao_ban', '2011-06-08 09:31:07'),
(90, 'refund_verify_status', 2, 'waiting_for_verification', '2011-06-14 17:21:07'),
(91, 'refund_verify_status', 8, 'operation_verified', '2011-06-14 17:21:25'),
(92, 'refund_verify_status', 7, 'verified', '2011-06-14 17:26:07'),
(93, 'refund_verify_status', -1, 'wait_for_cs', '2011-06-25 12:05:17'),
(94, 'out_packing', 0, 'no_packing', '2011-07-01 17:43:10'),
(95, 'out_packing', 1, 'neutral_color_packing', '2011-07-01 17:43:10'),
(96, 'out_packing', 2, 'band_color_packing', '2011-07-01 17:43:10'),
(97, 'out_packing', 3, 'white_packing', '2011-07-01 17:43:10'),
(98, 'out_packing', 4, 'bubble_packing', '2011-07-01 17:43:10'),
(99, 'out_packing', 5, 'tape_packing', '2011-07-01 17:43:10'),
(100, 'order_status', 0, 'not_handled', '2011-07-05 09:29:55'),
(101, 'message_status', 0, 'message_status_not', '2011-11-20 01:32:26'),
(102, 'message_status', 1, 'message_status_readed', '2011-11-20 02:01:32'),
(103, 'message_status', 2, 'message_status_replyied', '2011-11-10 12:01:32'),
(104, 'message_status', 3, 'message_status_hold', '2011-11-20 01:32:26'),
(105, 'message_status', 4, 'message_status_needless', '2011-11-20 02:01:32'),
(106, 'message_status', 5, 'message_status_updated_ebay', '2011-11-20 10:01:32');

-- --------------------------------------------------------

--
-- 表的结构 `stmp_account`
--

CREATE TABLE IF NOT EXISTS `stmp_account` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `account_name` varchar(200) NOT NULL,
  `account_password` varchar(50) NOT NULL,
  `stmp_host_id` int(11) NOT NULL,
  `status` tinyint(4) NOT NULL DEFAULT '0',
  `created_date` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;

--
-- 转存表中的数据 `stmp_account`
--


-- --------------------------------------------------------

--
-- 表的结构 `stmp_host`
--

CREATE TABLE IF NOT EXISTS `stmp_host` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `host` varchar(50) NOT NULL,
  `port` int(11) NOT NULL,
  `is_ssl` tinyint(4) NOT NULL DEFAULT '1',
  `created_date` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 AUTO_INCREMENT=3 ;

--
-- 转存表中的数据 `stmp_host`
--

INSERT INTO `stmp_host` (`id`, `host`, `port`, `is_ssl`, `created_date`) VALUES
(1, 'smtp.gmail.com', 465, 1, '2011-02-11 10:39:30'),
(2, 'smtp.exmail.qq.com', 465, 1, '2011-02-11 11:18:57');

-- --------------------------------------------------------

--
-- 表的结构 `stmp_paypal_sender`
--

CREATE TABLE IF NOT EXISTS `stmp_paypal_sender` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `paypal_email` varchar(100) NOT NULL,
  `sender_name` varchar(50) NOT NULL,
  `created_date` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;

--
-- 转存表中的数据 `stmp_paypal_sender`
--


-- --------------------------------------------------------

--
-- 表的结构 `stmp_sender_account_map`
--

CREATE TABLE IF NOT EXISTS `stmp_sender_account_map` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `paypal_sender_id` int(11) NOT NULL,
  `stmp_account_id` int(11) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;

--
-- 转存表中的数据 `stmp_sender_account_map`
--


-- --------------------------------------------------------

--
-- 表的结构 `stock_check_duty`
--

CREATE TABLE IF NOT EXISTS `stock_check_duty` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `sku` varchar(50) NOT NULL,
  `stock_checker` int(11) NOT NULL,
  `differences_remark` text NOT NULL,
  `remark` text NOT NULL,
  `before_change_count` varchar(11) NOT NULL,
  `change_count` varchar(11) NOT NULL,
  `after_change_count` varchar(11) NOT NULL,
  `duty` varchar(11) NOT NULL,
  `update_time` datetime NOT NULL,
  `review_status` int(11) NOT NULL DEFAULT '0',
  PRIMARY KEY (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;

--
-- 转存表中的数据 `stock_check_duty`
--


-- --------------------------------------------------------

--
-- 表的结构 `system`
--

CREATE TABLE IF NOT EXISTS `system` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `code` varchar(20) NOT NULL,
  `name` varchar(40) NOT NULL,
  `description` varchar(50) NOT NULL,
  `version` float NOT NULL,
  `status` tinyint(1) NOT NULL,
  `status_label` varchar(10) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 AUTO_INCREMENT=18 ;

--
-- 转存表中的数据 `system`
--

INSERT INTO `system` (`id`, `code`, `name`, `description`, `version`, `status`, `status_label`) VALUES
(1, 'it', 'it_department', 'Everything for  IT department.', 1, 1, 'enable'),
(2, 'purchase', 'purchase', 'Everything for Purchase department.', 1, 1, '启用'),
(3, 'finance', 'finance', 'Everything for finance department.', 1, 1, 'enable'),
(4, 'shipping', 'shipping', 'Everything for  Shipping department.', 1, 1, 'enable'),
(5, 'sale', 'sale', 'Everything for Sale department.', 1, 1, '启用'),
(6, 'seo', 'seo', 'Everything for Search engine optimization departme', 1, 1, 'enable'),
(7, 'void', 'void', 'Does not belong to any department.', 1, 1, '启用'),
(8, 'cs', 'customer_service', 'Everything for customer service department.', 1, 1, '启用'),
(9, 'stock', 'stock', 'Everything for Stock department.', 1, 1, 'enable'),
(10, 'qt', 'quality_test', 'Everything for  Quality testing department.', 1, 1, 'enable'),
(11, 'pi', 'product_information', 'Everything for Product information department.', 1, 1, 'enable'),
(12, 'admin', 'administrator', 'Everything for Administrator.', 1, 1, 'enable'),
(15, 'order', 'order', 'Everything for customer service department.', 1, 1, '启用'),
(16, 'edu', 'edu_doc', 'Everything for customer edu doc', 1, 1, '启用'),
(17, 'executive', 'executive', 'Everything for executive department', 1, 1, '启用');

-- --------------------------------------------------------

--
-- 表的结构 `system_crontab`
--

CREATE TABLE IF NOT EXISTS `system_crontab` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `on` varchar(200) NOT NULL,
  `job` varchar(500) NOT NULL,
  `description` varchar(500) NOT NULL,
  `creator` int(11) NOT NULL,
  `created_date` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;

--
-- 转存表中的数据 `system_crontab`
--


-- --------------------------------------------------------

--
-- 表的结构 `taobao_trade_rate`
--

CREATE TABLE IF NOT EXISTS `taobao_trade_rate` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `valid_score` tinyint(4) NOT NULL,
  `tid` varchar(20) NOT NULL,
  `oid` varchar(20) NOT NULL,
  `role` varchar(10) NOT NULL,
  `nick` varchar(20) NOT NULL,
  `result` varchar(50) NOT NULL,
  `created` datetime NOT NULL,
  `rated_nick` varchar(20) NOT NULL,
  `item_title` varchar(200) NOT NULL,
  `sku` varchar(50) NOT NULL,
  `item_price` double NOT NULL,
  `content` text NOT NULL,
  `reply` text NOT NULL,
  `bad_type` int(11) NOT NULL,
  `review` varchar(500) NOT NULL,
  `created_datetime` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;

--
-- 转存表中的数据 `taobao_trade_rate`
--


-- --------------------------------------------------------

--
-- 表的结构 `user`
--

CREATE TABLE IF NOT EXISTS `user` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `login_name` varchar(50) NOT NULL,
  `password` varchar(50) NOT NULL,
  `name` varchar(50) NOT NULL,
  `name_en` varchar(50) NOT NULL,
  `phone` varchar(50) NOT NULL,
  `platform1` varchar(50) NOT NULL,
  `platform2` varchar(50) NOT NULL,
  `email` varchar(50) NOT NULL,
  `email_pwd` varchar(20) NOT NULL,
  `msn` varchar(20) NOT NULL,
  `msn_pwd` varchar(20) NOT NULL,
  `QQ` varchar(20) NOT NULL,
  `QQ_pwd` varchar(20) NOT NULL,
  `skype` varchar(20) NOT NULL,
  `skype_pwd` varchar(20) NOT NULL,
  `RTX` varchar(20) NOT NULL,
  `RTX_pwd` varchar(20) NOT NULL,
  `taobao_username` varchar(20) NOT NULL,
  `taobao_pwd` varchar(20) NOT NULL,
  `fileserv_username` varchar(20) NOT NULL,
  `fileserv_pwd` varchar(20) NOT NULL,
  `contrct_time` varchar(20) NOT NULL,
  `birthday` date NOT NULL,
  `trial_end_time` date NOT NULL,
  `role` int(11) NOT NULL,
  `level` int(11) NOT NULL,
  `updated_date` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 AUTO_INCREMENT=2 ;

--
-- 转存表中的数据 `user`
--

INSERT INTO `user` (`id`, `login_name`, `password`, `name`, `name_en`, `phone`, `platform1`, `platform2`, `email`, `email_pwd`, `msn`, `msn_pwd`, `QQ`, `QQ_pwd`, `skype`, `skype_pwd`, `RTX`, `RTX_pwd`, `taobao_username`, `taobao_pwd`, `fileserv_username`, `fileserv_pwd`, `contrct_time`, `birthday`, `trial_end_time`, `role`, `level`, `updated_date`) VALUES
(1, 'admin', '7fef6171469e80d32c0559f88b377245', '赵森林', 'john', '+86-0755-83998886-8888', 'http://www.mallerp.com', 'http://demo5.mallerp.com', 'john@mallerp.com', '', 'john@mallerp.com', '', '7410992', '', 'mallerp', '', '101', '', '赵森林', '2008', 'administrator', '赵森林', '', '1900-07-20', '0000-00-00', 85, 63, '2011-11-15 02:18:11');

-- --------------------------------------------------------

--
-- 表的结构 `user_expire_date_info`
--

CREATE TABLE IF NOT EXISTS `user_expire_date_info` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `contract_time` varchar(10) NOT NULL,
  `probation_time` varchar(10) NOT NULL,
  `birthday` varchar(10) NOT NULL,
  `create_date` datetime NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;

--
-- 转存表中的数据 `user_expire_date_info`
--


-- --------------------------------------------------------

--
-- 表的结构 `user_group`
--

CREATE TABLE IF NOT EXISTS `user_group` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `user_id` int(11) NOT NULL,
  `group_id` int(11) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;

--
-- 转存表中的数据 `user_group`
--


-- --------------------------------------------------------

--
-- 表的结构 `user_integral`
--

CREATE TABLE IF NOT EXISTS `user_integral` (
  `id` bigint(20) NOT NULL AUTO_INCREMENT,
  `user_id` int(11) NOT NULL,
  `content_id` bigint(20) NOT NULL,
  `integral` int(11) NOT NULL,
  `reviewer_id` int(11) NOT NULL,
  `type` varchar(30) NOT NULL,
  `created_date` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;

--
-- 转存表中的数据 `user_integral`
--


-- --------------------------------------------------------

--
-- 表的结构 `user_level`
--

CREATE TABLE IF NOT EXISTS `user_level` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `name` varchar(20) NOT NULL,
  `description` varchar(50) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;

--
-- 转存表中的数据 `user_level`
--


-- --------------------------------------------------------

--
-- 表的结构 `user_login_log`
--

CREATE TABLE IF NOT EXISTS `user_login_log` (
  `id` bigint(20) NOT NULL AUTO_INCREMENT,
  `user_id` int(11) NOT NULL,
  `ip_address` varchar(64) NOT NULL,
  `user_agent` varchar(200) NOT NULL,
  `created_date` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;


--
-- 表的结构 `user_order`
--

CREATE TABLE IF NOT EXISTS `user_order` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `user_id` int(11) NOT NULL,
  `paypal_email_str` varchar(500) NOT NULL,
  `ebay_id_str` varchar(500) NOT NULL,
  `created_date` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;

--
-- 转存表中的数据 `user_order`
--


-- --------------------------------------------------------

--
-- 表的结构 `user_role`
--

CREATE TABLE IF NOT EXISTS `user_role` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `name` varchar(20) NOT NULL,
  `description` varchar(50) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;

--
-- 转存表中的数据 `user_role`
--


-- --------------------------------------------------------

--
-- 表的结构 `user_saler_input_user_map`
--

CREATE TABLE IF NOT EXISTS `user_saler_input_user_map` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `saler_id` int(11) NOT NULL,
  `paypal_email` varchar(50) NOT NULL,
  `in_operation` tinyint(4) NOT NULL DEFAULT '1',
  PRIMARY KEY (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;

--
-- 转存表中的数据 `user_saler_input_user_map`
--


-- --------------------------------------------------------

--
-- 表的结构 `wait_create_amazon_pdf`
--

CREATE TABLE IF NOT EXISTS `wait_create_amazon_pdf` (
  `amazonorderid` varchar(30) NOT NULL,
  `sellerid` varchar(100) NOT NULL,
  `invoice_id` varchar(30) NOT NULL,
  `status` int(1) NOT NULL DEFAULT '0',
  KEY `amazonorderid` (`amazonorderid`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

--
-- 转存表中的数据 `wait_create_amazon_pdf`
--


-- --------------------------------------------------------

--
-- 表的结构 `work_rewards_error`
--

CREATE TABLE IF NOT EXISTS `work_rewards_error` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `content_item` text NOT NULL,
  `status` varchar(50) NOT NULL,
  `department` varchar(50) NOT NULL,
  `result` varchar(50) NOT NULL,
  `author` varchar(50) NOT NULL,
  `created_time` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `order_no` varchar(50) NOT NULL,
  `type` varchar(50) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;

--
-- 转存表中的数据 `work_rewards_error`
--


-- --------------------------------------------------------

--
-- 表的结构 `work_rewards_error_person`
--

CREATE TABLE IF NOT EXISTS `work_rewards_error_person` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `error_item_id` varchar(11) NOT NULL,
  `worker_id` varchar(11) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;

--
-- 转存表中的数据 `work_rewards_error_person`
--

