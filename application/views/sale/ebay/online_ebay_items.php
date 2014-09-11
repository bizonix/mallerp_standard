<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
    <link href="/static/style/base.css" rel="stylesheet" type="text/css" media="screen"/>
    <script type="text/javascript" src="/js/prototype.js"></script>
    <script type="text/javascript" src="/js/addLoadEvent.js"></script>
    <script type="text/javascript" src="/js/stripeTables.js"></script>
    <script type="text/javascript" src="/js/highlightRows.js"></script>
    <script type="text/javascript" src="/js/ebay_erp.js"></script>
    <link rel="icon" type="image/vnd.microsoft.icon" href="http://www.b2c-china.com/favicon.ico"/>
    <title>Myebay Management</title>
</head>
<script type="text/javascript">
    document.observe("dom:loaded", function() {
        //$('getStoreItems').observe('click', displayMyebayList);
    });
</script>


<body>
    <div class="headerBar" style="text-align:right;background-color: rgb(251, 250, 246); border: 1px solid rgb(187, 175, 160);">
        <a href="/ci.php/ebay/upload" target="rightFrame"><strong>Add a new item</strong></a>&nbsp;&nbsp;
    </div>
    <div class="v2content" style="margin: 10px;margin-top:40px;">
        <form id="myebayItemsform" name="myebayItemsform" method="post" action="">
          <label>Ebay id:
            <select name="EbayID" id="EbayID">
            <?php if (isset($ebay_id_list)): ?>
                <?php foreach ($ebay_id_list as $id) : ?>
                    <option value="<?php echo $id; ?>"><?php echo $id; ?></option>
                <?php endforeach; ?>
            <?php endif; ?>

            </select>
           </label>&nbsp;&nbsp;
          <label>Market:
            <select name="Site" id="Site">

            <option value="US" selected="selected">eBay.com</option>
            <option value="Australia">eBay.au</option>
            <option value="UK">ebay.co.uk</option>
            <option value="France">ebay.fr</option>

            </select>
          </label>
           &nbsp;&nbsp;
          <label>Item type:
            <select name="itemType" id="itemType">
            <option value="ActiveAuctionList">Active Auction List</option>
            <option value="ActiveFixedPriceList">Active FixedPrice List</option>
            <option value="BidList">Bid List</option>
            <option value="ScheduledList">Scheduled List</option>
            <option value="SoldList">Sold List</option>
            <option value="UnsoldList">Unsold List</option>
            </select>
          </label>
           &nbsp;&nbsp;
          <label>Show
            <select name="itemsPerPage" id="itemsPerPage">
            <option value="20" selected="selected">20</option>
            <option value="50">50</option>
            <option value="100">100</option>
            <option value="200">200</option>
            </select>
            per page
          </label>
          &nbsp;&nbsp;
          <label>
            <input type="button" name="getStoreItems" id="getStoreItems" value="get store items..." onClick="displayMyebayList(1);" />
          </label>
        </form>
    </div>
    <div class="v2content" style="margin: 10px; display: none" id="ebayItemList">
    </div>
</body>
</html>