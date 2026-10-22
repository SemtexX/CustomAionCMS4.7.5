<?php
    $IP_denylist = 
    //Hier die entsprechenen IP Adressen eintragen
    array("66.249.66.00", "66.249.66.99");
    if (in_array($_SERVER['REMOTE_ADDR'], $IP_denylist)) {
        header("location: www.google.com/"); //Auf eine beliebige Seite umleiten
        exit();
    }
?>
<?php
/**
 * AionCMS
 * https://aioncms.com
 * 
 * @author Lautaro Angelico <https://lautaroangelico.com/>
 * @copyright (c) 2012-2019 Lautaro Angelico, All Rights Reserved
 */

// Make sure BattlePass class is available for the shop-purchase hook.
// Adjust the path if your lib folder differs.
if (!class_exists('BattlePass')) {
    $__bpLib = __DIR__ . DIRECTORY_SEPARATOR . 'lib.battlepass.php';
    if (file_exists($__bpLib)) {
        require_once $__bpLib;
    }
}

class Shop {
    
    private $_itemsPerPage = 5;
    private $_currentPage = 1;
    
    private $_category;
    private $_subCategory;
    
    private $_shopUrl;
    private $_categories;
    
    private $_itemId;
    private $_itemData;
    
    private $_account;
    private $_userId;
    private $_accountData;
    private $_character;
    private $_characterData;
    private $_server;
    private $_isWeeklyShop = false;
    
    private $_uniqueItemIdIncrementValue = 190000000;
    
    // Gift feature properties
    private $_isGift = false;
    private $_senderCharacter = '';
    private $_receiverCharacter = '';
    private $_receiverServer = '';
    private $_receiverAccountId = 0;
    
    function __construct() {
        
        # paths and urls
        $this->_shopUrl = __PAGE_URL__ . 'shop/';
        $this->_itemDetailUrl = $this->_shopUrl . 'item/id/';
        
        # database
        $this->db = Handler::loadDB();
        
        # load categories
        $this->_loadCategories();
        
    }
    
    /**
     * setUserId
     * 
     */
    public function setUserId($userId) {
        if(!Validator::UnsignedNumber($userId)) throw new Exception('The user id provided is not valid.');
        
        $this->_userId = $userId;
        $this->_loadAccountData();
    }
    
    /**
     * _loadAccountData
     * 
     */
    private function _loadAccountData() {
        if(!check($this->_userId)) throw new Exception('User id needs to be provided to load account data.');
        
        $this->_account = new Account();
        $this->_account->setId($this->_userId);
        $this->_accountData = $this->_account->getAccountData();
    }
    
    /**
     * getAccountData
     * 
     */
    public function getAccountData() {
        if(!is_array($this->_accountData)) return;
        return $this->_accountData;
    }
    
    /**
     * setServer
     * 
     */
    public function setServer($server) {
        $this->_server = 'siel';
        $this->sdb = Handler::loadDB('siel');
    }
    
    /**
     * setCharacter
     * 
     */
    public function setCharacter($characterName) {
        if(!is_array($this->_accountData)) throw new Exception('User id needs to be set first.');
        if(!check($this->_server)) throw new Exception('The server needs to be set first.');
        if(!Validator::AlphaNumeric($characterName)) throw new Exception('The selected character is not valid.');
        
        $playerInfo = $this->sdb->queryFetchSingle("SELECT * FROM `players` WHERE `name` = ? AND `account_id` = ?", array($characterName, $this->_accountData['id']));
        if(!is_array($playerInfo)) throw new Exception('The selected character is not valid.');
        
        $this->_character = $characterName;
        $this->_characterData = $playerInfo;
    }
    
    /**
     * Set gift mode
     */
    public function setIsGift($isGift = true) {
        $this->_isGift = (bool)$isGift;
    }
    
    /**
     * Set sender character
     */
    public function setSenderCharacter($characterName) {
        if(!Validator::AlphaNumeric($characterName)) throw new Exception('Invalid sender character name.');
        $this->_senderCharacter = $characterName;
    }
    
    /**
     * Set receiver character
     */
    public function setReceiverCharacter($characterName) {
        if(!Validator::AlphaNumeric($characterName)) throw new Exception('Invalid character name.');
        $this->_receiverCharacter = $characterName;
    }
    
    /**
     * Set receiver server
     */
    public function setReceiverServer($serverName) {
        $this->_receiverServer = $serverName;
        $this->sdb = Handler::loadDB($serverName);
    }
    
    /**
     * Check if receiver character exists
     */
private function _validateReceiverCharacter() {
    if($this->sdb === null) {
        throw new Exception('Server database connection is not initialized.');
    }
    
    try {
        $characterData = $this->sdb->queryFetchSingle("SELECT * FROM `players` WHERE `name` = ?", array($this->_receiverCharacter));
        if(!is_array($characterData)) {
            throw new Exception('The recipient character does not exist on this server.');
        }
        
        $this->_receiverAccountId = $characterData['account_id'];
        return $characterData;
    } catch(Exception $e) {
        throw new Exception('Database error while validating recipient: ' . $e->getMessage());
    }
}
    
    /**
     * _loadCategories
     * 
     */
    private function _loadCategories() {
        $categories = $this->db->queryFetch("SELECT * FROM `aioncms`.`website_shop_categories` WHERE `parent` IS NULL AND `status` = 1 ORDER BY `order` ASC, `id` ASC");
        if(!is_array($categories)) throw new Exception('There are no categories.');
        
        foreach($categories as $category) {
            $this->_categories[$category['id']] = $category;
            
            $childs = $this->db->queryFetch("SELECT * FROM `aioncms`.`website_shop_categories` WHERE `parent` = ? AND `status` = 1 ORDER BY `order` ASC, `id` ASC", array($category['id']));
            if(is_array($childs)) {
                foreach($childs as $child) {
                    $this->_categories[$child['parent']]['childs'][$child['id']] = $child;
                }
            }
            
        }
    }
    
    /**
     * _categoryLink
     * 
     */
    private function _categoryLink($id=0, $sub=0, $page=1) {
        $return = $this->_shopUrl;
        if($id != 0) {
            $return .= 'list/c/' . $id;
            
            if($sub != 0) {
                $return .= '/s/' . $sub;
            }
        }
        if($page > 1) {
            $return .= '/p/' . $page;
        }
        
        return $return;
    }
    
    /**
     * displayMenu
     * 
     */
    public function displayMenu() {
        echo '<ul class="nav nav-sidebar">';
        foreach($this->_categories as $category) {
            if(check($category['childs'])) {
                # has sub-categories
                if($this->_category == $category['id']) {
                    echo '<li data-toggle="collapse" data-target="#'.ucfirst($category['title']).'" class="collapsed active">';
                    echo '<a style="color:#ff7200;">';
                        echo '<span class="glyphicon glyphicon-chevron-down" aria-hidden="false"></span> '.ucfirst($category['title']).'';
                    echo '</a>';
                    echo '<ul class="nav collapse in" id="'.ucfirst($category['title']).'" aria-expanded="true">';
                } else {
                    echo '<li data-toggle="collapse" data-target="#'.ucfirst($category['title']).'">';
                    echo '<a >';
                        echo '<span class="glyphicon glyphicon-chevron-right" aria-hidden="false"></span> '.ucfirst($category['title']).'';
                    echo '</a>';
                    echo '<ul class="nav collapse" id="'.ucfirst($category['title']).'">';
                }
                
                foreach($category['childs'] as $child) {
                    echo '<li class="sub-category"><a href="'.$this->_categoryLink($category['id'], $child['id']).'" '.(check($this->_subCategory) ? ($this->_subCategory == $child['id'] ? 'style="color:#ff7200;"' : null) : null).'>'.ucfirst($child['title']).'</a></li>';
                }
                echo '</ul></li>';
            } else {
                # no sub-categories
                echo '<li><a href="'.$this->_categoryLink($category['id']).'" '.($this->_category == $category['id'] ? 'style="color:#ff7200;"' : null).'><span class="glyphicon glyphicon-chevron-right" aria-hidden="true"></span> '.ucfirst($category['title']).'</a></li>';
            }
        }
        echo '</ul>';
    }
    
    /**
     * setCategory
     * 
     */
    public function setCategory($id) {
        if(!Validator::UnsignedNumber($id)) throw new Exception('Invalid category id.');
        if(!array_key_exists($id, $this->_categories)) throw new Exception('Category does not exist.');
        
        $this->_category = $id;
    }
    
    /**
     * setSubCategory
     * 
     */
    public function setSubCategory($id) {
        if(!Validator::UnsignedNumber($id)) throw new Exception('Invalid sub-category id.');
        if(!check($this->_category)) throw new Exception('Category has not been set.');
        if(!array_key_exists($id, $this->_categories[$this->_category]['childs'])) throw new Exception('Sub-category does not exist.');
        
        $this->_subCategory = $id;
    }
    
    /**
     * setPage
     * 
     */
    public function setPage($page) {
        if(!Validator::UnsignedNumber($page)) throw new Exception('Invalid page number.');
        if($page > 100) throw new Exception('Invalid page number.');
        
        $this->_currentPage = $page;
    }
    
    /**
     * _resultLimitStart
     * 
     */
    private function _resultLimitStart() {
        if($this->_currentPage == 1) {
            return 0;
        } else {
            return ($this->_currentPage-1)*$this->_itemsPerPage;
        }
    }
    
    /**
     * _categoryItemCount
     * 
     */
    private function _categoryItemCount($categoryId) {
        
        $count = $this->db->queryFetchSingle("SELECT COUNT(*) as `total` FROM `aioncms`.`website_shop_items` WHERE `category` IN (".$categoryId.") AND `status` = ?", array(1));
        if(!is_array($count)) return 0;
        
        return $count['total'];
    }
    
    /**
     * _itemDetailLink
     * 
     */
    private function _itemDetailLink($id, $isWeeklyShop=false) {
        if($isWeeklyShop) return $this->_itemDetailUrl . $id . '/ws/1';
        return $this->_itemDetailUrl . $id;
    }
    
    /**
     * setIsWeekly
     * 
     */
    public function setIsWeekly() {
        $this->_isWeeklyShop = true;
    }
    
    /**
     * setItemId
     * 
     */
    public function setItemId($id) {
        if(!Validator::UnsignedNumber($id)) throw new Exception('Invalid item id.');
        
        if($this->_isWeeklyShop) {
            # item is from weekly shop
            $itemData = $this->db->queryFetchSingle("SELECT * FROM `aioncms`.`weeklyspecial_activeitems` WHERE `quantity` >= 1 AND `item_id` = ?", array($id));
            if(!is_array($itemData)) throw new Exception('The requested item is not currently in the web shop.');
        } else {
            # regular webshop
            $itemData = $this->db->queryFetchSingle("SELECT * FROM `aioncms`.`website_shop_items` WHERE `item_id` = ? AND `status` = ?", array($id, 1));
            if(!is_array($itemData)) throw new Exception('The requested item is not currently in the web shop.');
            
            # check if category is enabled
            $checkCategory = $this->db->queryFetchSingle("SELECT * FROM `aioncms`.`website_shop_categories` WHERE `id` = ? AND `status` = 1", array($itemData['category']));
            if(!is_array($checkCategory)) throw new Exception('The requested item is not currently in the web shop (2).');
            
            # category has parent, check if parent is enabled
            if(check($checkCategory['parent'])) {
                $checkCategoryParent = $this->db->queryFetchSingle("SELECT * FROM `aioncms`.`website_shop_categories` WHERE `id` = ? AND `status` = 1", array($checkCategory['parent']));
                if(!is_array($checkCategoryParent)) throw new Exception('The requested item is not currently in the web shop (3).');
            }
        }
        
        $this->_itemId = $id;
        $this->_itemData = $itemData;
    }
    
    /**
     * getItemData
     * 
     */
	public function getItemData() {
		if(!is_array($this->_itemData)) throw new Exception('Item id has to be set first.');
		
		return $this->_itemData;
	}
    
    /**
     * getShopHome
     * 
     */
    public function getShopHome() {
        return $this->_shopUrl;
    }
    
    /**
     * listItems
     * 
     */
    public function listItems() {
        if(check($this->_subCategory)) {
            # items from single sub-category
            $categoryId = $this->_subCategory;
            $itemList = $this->db->queryFetch("SELECT * FROM `aioncms`.`website_shop_items` WHERE `category` = ? AND `status` = ? ORDER BY `order` ASC, `id` ASC LIMIT ".$this->_resultLimitStart().",".$this->_itemsPerPage."", array($categoryId , 1));
        } else {
            # items from whole category and sub-categories
            if(!check($this->_category)) throw new Exception('The selected category is not valid.');
            $categoriesArray = array();
            $categoriesArray[] = $this->_category;
            
            if(is_array($this->_categories[$this->_category]['childs'])) {
                foreach($this->_categories[$this->_category]['childs'] as $categoryChild) {
                    $categoriesArray[] = $categoryChild['id'];
                }
            }
            
            $categoryId = implode(",", $categoriesArray);
            
            $itemList = $this->db->queryFetch("SELECT * FROM `aioncms`.`website_shop_items` WHERE `category` IN (".$categoryId.") AND `status` = ? ORDER BY `order` ASC, `id` ASC LIMIT ".$this->_resultLimitStart().",".$this->_itemsPerPage."", array(1));
            
        }
        
        echo '<ol class="breadcrumb">';
            echo '<li><a href="'.$this->_shopUrl.'">Shop</a></li>';
            if(check($this->_category)) echo '<li><a href="'.$this->_categoryLink($this->_category).'">'.ucfirst($this->_categories[$this->_category]['title']).'</a></li>';
            if(check($this->_subCategory)) echo '<li><a href="'.$this->_categoryLink($this->_category, $this->_subCategory).'">'.ucfirst($this->_categories[$this->_category]['childs'][$this->_subCategory]['title']).'</a></li>';
            //echo '<li class="active">Sub Category</li>';
        echo '</ol>';
        
        echo '<div class="row">';
        if(is_array($itemList)) {
            
            echo '<table class="table table-striped shop-items-table">';
            foreach($itemList as $item) {
                
                # item id
                $itemId = $item['item_id'];
                
                # item information (our database)
                $itemInfo = getItemInfo($itemId);
                if(!is_array($itemInfo)) continue;
                
                # item name
                if(!check($item['name'])) {
                    $itemName = $itemInfo['item_name'];
                } else {
                    $itemName = $item['name'];
                }
                
                # item info (api)
                $itemQuality = '';
                if(check($itemInfo['item_quality'])) {
                    $itemQuality = 'shop-quality-' . $itemInfo['item_quality'];
                }
                
                # item icon
                $itemIcon = 'https://aioncodex.com/items/'.$itemInfo['item_icon'].'.png';
                
                # item enchantment
                $itemEnchantment = $item['enchantment'] >= 1 ? ' <span style="font-weight:bold;color:#ff0000;">+' . $item['enchantment'] . '</span>' : '';
                
                # item temperance
                $itemTemperance = $item['temperance'] >= 1 ? ' <span style="font-weight:bold;color:#ffcc00;">+' . $item['temperance'] . '</span>' : '';
                
                echo '<tr>';
                    //echo '<td style="width:80px;" class="vert-align text-center"><span id="'.$itemId.'" class="shop-item-icon"></span></td>';
                    echo '<td style="width:80px;" class="vert-align text-center"><img src="'.$itemIcon.'" /></td>';
                    echo '<td>';
                        echo '<span style="color:#aaa;font-size:11px;">'.number_format($item['count']).'x</span><br />';
                        echo '<span id="'.$itemId.'"><a href="'.$this->_itemDetailLink($itemId).'" class="shop-item-text '.$itemQuality.'">' . $itemName . '</a>'.$itemEnchantment.$itemTemperance.'</span><br />';
                        echo '<span style="color:#C99B4B;font-size:12px;">'.number_format($item['cost']).' credits</span>';
                    echo '</td>';
                    echo '<td class="vert-align text-center">';
                        echo '<a href="'.$this->_itemDetailLink($itemId).'" class="btn btn-primary btn-xs">Purchase</a>';
                    echo '</td>';
                echo '</tr>';
            }
            echo '</table>';
            
        } else {
            message('There are no items to display.', 'error');
        }
        
        echo '</div>';
        
        # PAGINATION
        $paginationPages = ceil($this->_categoryItemCount($categoryId)/$this->_itemsPerPage);
        $paginationPrevious = ($this->_currentPage > 1 ? ($this->_currentPage-1) : 1);
        $paginationNext = ($this->_currentPage < $paginationPages ? ($this->_currentPage+1) : $paginationPages);
        
        if($paginationPages > 1) {
            echo '<div class="row text-center">';
                echo '<nav aria-label="Page navigation">';
                    echo '<ul class="pagination">';
                        echo '<li>';
                            echo '<a href="'.$this->_categoryLink($this->_category, $this->_subCategory, $paginationPrevious).'" aria-label="Previous">';
                                echo '<span aria-hidden="true">&laquo;</span>';
                            echo '</a>';
                        echo '</li>';
                        
                        # pages
                        for($i=1; $i<=$paginationPages; $i++) {
                            if($i == $this->_currentPage) {
                                echo '<li class="active"><a href="'.$this->_categoryLink($this->_category, $this->_subCategory, $i).'">'.$i.'</a></li>';
                            } else {
                                echo '<li><a href="'.$this->_categoryLink($this->_category, $this->_subCategory, $i).'">'.$i.'</a></li>';
                            }
                        }
                        
                        echo '<li>';
                            echo '<a href="'.$this->_categoryLink($this->_category, $this->_subCategory, $paginationNext).'" aria-label="Next">';
                                echo '<span aria-hidden="true">&raquo;</span>';
                            echo '</a>';
                        echo '</li>';
                    echo '</ul>';
                echo '</nav>';
            echo '</div>';
        }
        # <--- PAGINATION END
        
    }
    
    public function listWeeklySpecialItems() {
        
        $itemList = $this->db->queryFetch("SELECT * FROM `aioncms`.`weeklyspecial_activeitems` WHERE `quantity` >= 1");
        
        echo '<div class="row">';
        if(is_array($itemList)) {
            
            echo '<table class="table table-striped shop-items-table">';
            foreach($itemList as $item) {
                
                # item id
                $itemId = $item['item_id'];
                
                # item information (our database)
                $itemInfo = getItemInfo($itemId);
                if(!is_array($itemInfo)) continue;
                
                # item name
                if(!check($item['name'])) {
                    $itemName = $itemInfo['item_name'];
                } else {
                    $itemName = $item['name'];
                }
                
                # item info (api)
                $itemQuality = '';
                if(check($itemInfo['item_quality'])) {
                    $itemQuality = 'shop-quality-' . $itemInfo['item_quality'];
                }
                
                # item detail link
                if($item['quantity'] >= 1) {
                    $itemDetailLink = $this->_itemDetailLink($itemId, true);
                } else {
                    $itemDetailLink = '#';
                }
                
                echo '<tr>';
                    echo '<td style="width:80px;" class="vert-align text-center"><span id="'.$itemId.'" class="shop-item-icon"></span></td>';
                    echo '<td style="width:80px;" class="vert-align text-center"><img src="https://aioncodex.com/items/'.$itemInfo['item_icon'].'.png" /></td>';
                    echo '<td>';
                        echo '<span style="color:#aaa;font-size:11px;">'.number_format($item['item_qty']).'x</span><br />';
                        echo '<span id="'.$itemId.'"><a href="'.$itemDetailLink.'" class="shop-item-text '.$itemQuality.'">' . $itemName . '</a></span><br />';
                        echo '<span style="color:#C99B4B;font-size:12px;">'.number_format($item['cost']).' credits</span>';
                    echo '</td>';
                    echo '<td class="vert-align text-center">';
                        echo '<a href="'.$itemDetailLink.'" class="btn btn-primary btn-xs">Purchase</a>';
                    echo '</td>';
                echo '</tr>';
            }
            echo '</table>';
            
        } else {
            message('There are no items to display.', 'error');
        }
        
        echo '</div>';
        
    }
    
    public function displayItemDetail() {
        if(!check($this->_itemId)) return;
        
        $aiondatabaseLink = 'https://aioncodex.com/us/item/' . $this->_itemId . '/';
        #$aiondatabase = file_get_contents('https://aioncodex.com/tip.php?nf=on&l=us&id=item--' . $this->_itemId);
        $aiondatabase = preg_replace("/img src=\"/", "img src=\"https://aioncodex.com/", $aiondatabase);
        $aiondatabase = preg_replace("/a href=\"/", "a target=\"_blank\" href=\"https://aioncodex.com/", $aiondatabase);

        echo '<div class="shop-item-detail">';
            echo $aiondatabase;
        echo '</div>';
        echo '<a href="'.$aiondatabaseLink.'" target="_blank" class="btn btn-primary btn-xs btn-block">View on AionCodex</a>';
        
    }
    
    public function displayPurchaseLogs() {
        
        # SIEL
        $purchaseLogsSIEL = $this->db->queryFetch("SELECT * FROM `aioncms`.`website_shop_logs` WHERE `acc` = ?", array($this->_accountData['name']));
        if(is_array($purchaseLogsSIEL)) {
            echo '<h4>Siel:</h4>';
            echo '<table class="table table-condensed table-striped">';
            echo '<tr>';
                echo '<th>Player</th>';
                echo '<th>Item Purchased</th>';
                echo '<th>Date</th>';
                echo '<th>Type</th>';
            echo '</tr>';
            foreach($purchaseLogsSIEL as $log) {
                $itemName = getItemName($log['item']);
                if(!check($itemName)) continue;
                
                $type = ($log['description'] == 'GIFT') ? 'Gift' : 'Purchase';
                
                echo '<tr>';
                    echo '<td>'.$log['name'].'</td>';
                    echo '<td>'.$itemName.'</td>';
                    echo '<td>'.date("Y-m-d h:i A", $log['time']).'</td>';
                    echo '<td>'.$type.'</td>';
                echo '</tr>';
            }
            echo '</table>';
            
            echo '<br /><hr><br />';
        }
        
        # WEEKLY SPECIAL SHOP
        $purchaseLogsWS = $this->db->queryFetch("SELECT * FROM `aioncms`.`weeklyspecial_logs` WHERE `account_name` = ? ORDER BY `id` DESC", array($this->_accountData['name']));
        if(is_array($purchaseLogsWS)) {
            echo '<h4>Weekly Special:</h4>';
            echo '<table class="table table-condensed table-striped">';
            echo '<tr>';
                echo '<th>Player</th>';
                echo '<th>Item Purchased</th>';
                echo '<th>Date</th>';
            echo '</tr>';
            foreach($purchaseLogsWS as $log) {
                $itemName = getItemName($log['item_id']);
                if(!check($itemName)) continue;
                
                echo '<tr>';
                    echo '<td>'.$log['player_name'].'</td>';
                    echo '<td>'.$itemName.'</td>';
                    echo '<td>'.date("Y-m-d h:i A", strtotime($log['purchase_date'])).'</td>';
                echo '</tr>';
            }
            echo '</table>';
        }
        
    }
    
    private function _itemInfoAPI($itemId) {
        
        $apiUrl = __BASE_URL__ . 'api/item.php?id=';
        $request = file_get_contents($apiUrl . $itemId);
        if(!$request) return;
        
        return JSON_DECODE($request, true);
    }
    
    /**
     * buyItem
     * 
     */
	public function buyItem() {
		if($this->_isGift) {
			// Gift purchase flow
			if(!check($_POST['sender_name'])) throw new Exception('Please enter your character name.');
			if(!check($_POST['receiver_name'])) throw new Exception('Please enter the recipient\'s character name.');
			if(!check($_POST['receiver_server'])) throw new Exception('Please select the recipient\'s server.');
			
			$this->setSenderCharacter($_POST['sender_name']);
			$this->setReceiverCharacter($_POST['receiver_name']);
			$this->setReceiverServer($_POST['receiver_server']);
			
			$characterData = $this->_validateReceiverCharacter();
			
			// Validate sender character belongs to account
			$senderCheck = $this->sdb->queryFetchSingle("SELECT * FROM `aioncms`.`players` WHERE `name` = ? AND `account_id` = ?", 
				array($this->_senderCharacter, $this->_accountData['id']));
			if(!is_array($senderCheck)) throw new Exception('The sender character does not belong to your account.');
		} else {
			// Normal purchase flow
			if(!check($this->_character)) throw new Exception('You must select a character before buying an item.');
			if(!is_array($this->_characterData)) throw new Exception('You must select a character before buying an item.');
			$characterData = $this->_characterData;
		}
			
			if(!is_array($this->_itemData)) throw new Exception('There was a problem with the system, please contact support.');
			if(!is_array($this->_accountData)) throw new Exception('There was a problem with the system, please contact support [2].');
			
			# item info
			$itemInfo = getItemInfo($this->_itemData['item_id']);
			if(!is_array($itemInfo)) throw new Exception('There was an error getting the item information, please contact support.');
			
			# check credits
			if($this->_accountData['toll'] < $this->_itemData['cost']) throw new Exception('You don\'t have enough credits to purchase this item.');
			
			# take credits
			$subtractCredits = $this->_account->subtractCredits($this->_itemData['cost']);
			if(!$subtractCredits) throw new Exception('Your purchase could not be processed, please contact support.');
			
			// GET LAST INVENTORY ID
			$inventoryLastId = $this->sdb->queryFetchSingle("SELECT MAX(item_unique_id) as result FROM `inventory`");
			if(!is_array($inventoryLastId)) throw new Exception('There was an error, contact support (E3)');
			
			if($inventoryLastId['result'] > $this->_uniqueItemIdIncrementValue) {
				$uniqueItemId = $inventoryLastId['result']+1;
			} else {
				$uniqueItemId = $inventoryLastId['result']+$this->_uniqueItemIdIncrementValue;
			}
			
			// INSERT INVENTORY ITEM
			$inventoryItemData = array(
				'item_unique_id' => $uniqueItemId,
				'item_id' => $this->_itemData['item_id'],
				'item_skin' => $this->_itemData['item_id'],
				'item_count' => $this->_itemData['count'],
				'item_owner' => $characterData['id'],
				'item_creator' => 'GlobalXcentra Webshop',
				'activation_count' => in_array($this->_itemData['item_id'], [164005001, 164005002, 164005003, 164005004, 164005005]) ? 10000000 : 1,
				'item_location' => 127,
				'rnd_bonus' => 0,
				'enchant' => $this->_itemData['enchantment'],
				'authorize' => $this->_itemData['temperance']
			);
			$addInventoryItem = $this->sdb->query("INSERT INTO `aioncms`.`inventory` (item_unique_id, item_id, item_skin, item_count, item_owner, item_creator, activation_count, rnd_bonus, item_location, enchant, authorize) VALUES (:item_unique_id, :item_id, :item_skin, :item_count, :item_owner, :item_creator, :activation_count, :rnd_bonus, :item_location, :enchant, :authorize)", $inventoryItemData);
			if(!$addInventoryItem) throw new Exception('There was an error, contact support (E4)');
			
			// GET MAIL LAST ID
			$mailLastId = $this->sdb->queryFetchSingle("SELECT MAX(mail_unique_id) as result FROM `mail`");
			if(!is_array($mailLastId)) {
				$mailLastId['result'] = 0;
			}
			
			// INSERT MAIL
			$mailMessage = $this->_isGift 
				? 'A gift from '.$this->_senderCharacter.'. Thank you for playing!'
				: 'Thank you for purchasing.';
				
			// In the buyItem() and buyItemWS() method:
			$mailData = array(
				'mail_unique_id'        =>    $mailLastId['result'] + 1,
				'mail_recipient_id'    =>    (int)$characterData['id'], // Ensure it is an integer
				'sender_name'            =>    $this->_isGift ? substr($this->_senderCharacter, 0, 64) : 'Webshop', // Limit length
				'mail_title'            =>    $this->_isGift ? substr('Gift from '.$this->_senderCharacter, 0, 64) : 'Webshop Delivery',
				'mail_message'            =>    substr($mailMessage, 0, 500), // Limit length
				'unread'                =>    1,
				'attached_item_id'		=>	$uniqueItemId,
				'attached_kinah_count'    =>    0,
				'express'                =>    1
			);
			$addMail = $this->sdb->query("INSERT INTO `aioncms`.`mail` (mail_unique_id, mail_recipient_id, sender_name, mail_title, mail_message, unread, attached_item_id, attached_kinah_count, express) VALUES (:mail_unique_id, :mail_recipient_id, :sender_name, :mail_title, :mail_message, :unread, :attached_item_id, :attached_kinah_count, :express)", $mailData);
			if(!$addMail) throw new Exception('There was an error, contact support (E5)');
				
			// INSERT LOG
			$logData = array(
				$this->_accountData['name'],
				Handler::userIP(),
				date('l jS \of F Y h:i:s A'),
				$characterData['name'],
				$this->_itemData['item_id'],
				$this->_isGift ? $this->_receiverAccountId : 0,
				time(),
				$this->_isGift ? 'GIFT' : ''
			);
			$sendLog = $this->db->query("INSERT INTO `aioncms`.`website_shop_logs` (`acc`, `ip`, `date`, `name`, `item`, `uid`, `time`, `description`) VALUES (?, ?, ?, ?, ?, ?, ?, ?)", $logData);
			if(!$sendLog) throw new Exception("There has been an error, please contact support. [Q2]");
			
			# web log
			$logMessage = $this->_isGift 
				? 'gifted webshop item ['.$this->_itemData['count'].'] ('.$this->_itemData['item_id'].') to '.$characterData['name']
				: 'purchased webshop item ['.$this->_itemData['count'].'] ('.$this->_itemData['item_id'].')';
			logSystem::add($logMessage);
			
			$successMessage = $this->_isGift
				? '<strong>'.$this->_itemData['count'].'x '.$itemInfo['item_name'].'</strong> has been sent as gift to <strong>'.$characterData['name'].'</strong> ('.ucfirst($this->_receiverServer).')'
				: '<strong>'.$this->_itemData['count'].'x '.$itemInfo['item_name'].'</strong> has been mailed to <strong>'.$characterData['name'].'</strong> ('.ucfirst($this->_server).')';
				
			message($successMessage, 'success');

			// --------------------------------------------------
			// BattlePass: grant progress points for this purchase
			// --------------------------------------------------
			try {
				$bpSeason = $this->db->queryFetchSingle(
					"SELECT `id`, `points_per_credit` FROM `aioncms`.`battlepass_seasons`
					 WHERE `status` = 1 AND `start_date` <= NOW() AND `end_date` >= NOW()
					 ORDER BY `id` DESC LIMIT 1"
				);
				if (is_array($bpSeason)) {
					$bpPoints = (int)$this->_itemData['cost'] * (int)$bpSeason['points_per_credit'];
					if ($bpPoints > 0 && class_exists('BattlePass')) {
						BattlePass::addPoints(
							(int)$this->_accountData['id'],
							$bpPoints,
							'shop_purchase',
							'Item '.$this->_itemData['item_id'].' x'.$this->_itemData['count'].' ('.$this->_itemData['cost'].' credits)',
							(int)$bpSeason['id']
						);
					}
				}
			} catch (Exception $e) {
				logSystem::add('BattlePass points error: ' . $e->getMessage());
			}
		}
    
    /**
     * buyItemWS
     * weekly special shop buy item process
     */
    public function buyItemWS() {
        if($this->_isGift) {
        // Gift purchase flow
        if(!check($_POST['sender_name'])) throw new Exception('Please enter your character name.');
        if(!check($_POST['receiver_name'])) throw new Exception('Please enter the recipient\'s character name.');
        if(!check($_POST['receiver_server'])) throw new Exception('Please select the recipient\'s server.');
        
        $this->setSenderCharacter($_POST['sender_name']);
        $this->setReceiverCharacter($_POST['receiver_name']);
        $this->setReceiverServer($_POST['receiver_server']);
        
        $characterData = $this->_validateReceiverCharacter();
        
        // Validate sender character belongs to account
        $senderCheck = $this->sdb->queryFetchSingle("SELECT * FROM `aioncms`.`players` WHERE `name` = ? AND `account_id` = ?", 
            array($this->_senderCharacter, $this->_accountData['id']));
        if(!is_array($senderCheck)) throw new Exception('The sender character does not belong to your account.');
    } else {
        // Normal purchase flow
        if(!check($this->_character)) throw new Exception('You must select a character before buying an item.');
        if(!is_array($this->_characterData)) throw new Exception('You must select a character before buying an item.');
        $characterData = $this->_characterData;
    }
        
        if(!is_array($this->_itemData)) throw new Exception('There was a problem with the system, please contact support.');
        if(!is_array($this->_accountData)) throw new Exception('There was a problem with the system, please contact support [2].');
        
        # item info
        $itemInfo = getItemInfo($this->_itemData['item_id']);
        if(!is_array($itemInfo)) throw new Exception('There was an error getting the item information, please contact support.');
        
        # check credits
        if($this->_accountData['toll'] < $this->_itemData['cost']) throw new Exception('You don\'t have enough credits to purchase this item.');
        
        # take credits
        $subtractCredits = $this->_account->subtractCredits($this->_itemData['cost']);
        if(!$subtractCredits) throw new Exception('Your purchase could not be processed, please contact support.');
        
        // GET LAST INVENTORY ID
        $inventoryLastId = $this->sdb->queryFetchSingle("SELECT MAX(item_unique_id) as result FROM `inventory`");
        if(!is_array($inventoryLastId)) throw new Exception('There was an error, contact support (E3)');
        
        if($inventoryLastId['result'] > $this->_uniqueItemIdIncrementValue) {
            $uniqueItemId = $inventoryLastId['result']+1;
        } else {
            $uniqueItemId = $inventoryLastId['result']+$this->_uniqueItemIdIncrementValue;
        }
        
        // INSERT INVENTORY ITEM
        $inventoryItemData = array(
            'item_unique_id' => $uniqueItemId,
            'item_id' => $this->_itemData['item_id'],
            'item_skin' => $this->_itemData['item_id'],
            'item_count' => $this->_itemData['item_qty'],
            'item_owner' => $characterData['id'],
            'item_creator' => 'GlobalXcentra Webshop',
            'activation_count' => in_array($this->_itemData['item_id'], [188710162, 188710163, 188710164]) ? 10000000 : 1,
            'item_location' => 127,
            'rnd_bonus' => 0,
            'enchant' => 0,
            'authorize' => 0
        );
        $addInventoryItem = $this->sdb->query("INSERT INTO `aioncms`.`inventory` (item_unique_id, item_id, item_skin, item_count, item_owner, item_creator, activation_count, item_location, rnd_bonus, enchant, authorize) VALUES (:item_unique_id, :item_id, :item_skin, :item_count, :item_owner, :item_creator, :activation_count, :item_location, :rnd_bonus, :enchant, :authorize)", $inventoryItemData);
        if(!$addInventoryItem) throw new Exception('There was an error, contact support (E4)');
        
        // GET MAIL LAST ID
        $mailLastId = $this->sdb->queryFetchSingle("SELECT MAX(mail_unique_id) as result FROM `mail`");
        if(!is_array($mailLastId)) {
            $mailLastId['result'] = 0;
        }
        
        // INSERT MAIL
        $mailMessage = $this->_isGift 
            ? 'A gift from '.$this->_senderCharacter.'. Thank you for playing!'
            : 'Thank you for purchasing.';
            
		// In the buyItem() and buyItemWS() method:
		$mailData = array(
			'mail_unique_id'        =>    $mailLastId['result'] + 1,
			'mail_recipient_id'    =>    (int)$characterData['id'], // Ensure it is an integer
			'sender_name'            =>    $this->_isGift ? substr($this->_senderCharacter, 0, 64) : 'Webshop', // Limit length
			'mail_title'            =>    $this->_isGift ? substr('Gift from '.$this->_senderCharacter, 0, 64) : 'Webshop Delivery',
			'mail_message'            =>    substr($mailMessage, 0, 500), // Limit length
			'unread'                =>    1,
			'attached_item_id'        =>    (int)$uniqueItemId,
			'attached_kinah_count'    =>    0,
			'express'                =>    1
		);
        $addMail = $this->sdb->query("INSERT INTO `aioncms`.`mail` (mail_unique_id, mail_recipient_id, sender_name, mail_title, mail_message, unread, attached_item_id, attached_kinah_count, express) VALUES (:mail_unique_id, :mail_recipient_id, :sender_name, :mail_title, :mail_message, :unread, :attached_item_id, :attached_kinah_count, :express)", $mailData);
        if(!$addMail) throw new Exception('There was an error, contact support (E5)');
        
        # subtract from weeklyspecial quantity
        $takeOne = $this->db->query("UPDATE `aioncms`.`weeklyspecial_activeitems` SET `quantity` = `quantity` - 1 WHERE `item_id` = ?", array($this->_itemData['item_id']));
        if(!$takeOne) throw new Exception("There has been an error, please contact support. [Q3]");
        
        // INSERT LOG
        $logData = array(
            $this->_accountData['name'],
            $characterData['name'],
            $this->_itemData['item_id'],
            $this->_itemData['cost'],
            Handler::userIP(),
            $this->_isGift ? $this->_receiverAccountId : 0,
            $this->_isGift ? 'GIFT' : ''
        );
        $sendLog = $this->db->query("INSERT INTO `aioncms`.`weeklyspecial_logs` (`account_name`, `player_name`, `item_id`, `item_cost`, `ip_address`, `recipient_id`, `gift_status`, `purchase_date`) VALUES (?, ?, ?, ?, ?, ?, ?, now())", $logData);
        if(!$sendLog) throw new Exception("There has been an error, please contact support. [Q2]");
        
        # web log
        $logMessage = $this->_isGift 
            ? 'gifted weekly webshop item ['.$this->_itemData['item_qty'].'] ('.$this->_itemData['item_id'].') to '.$characterData['name']
            : 'purchased weekly webshop item ['.$this->_itemData['item_qty'].'] ('.$this->_itemData['item_id'].')';
        logSystem::add($logMessage);
        
        $successMessage = $this->_isGift
            ? '<strong>'.$this->_itemData['item_qty'].'x '.$itemInfo['item_name'].'</strong> has been sent as gift to <strong>'.$characterData['name'].'</strong> ('.ucfirst($this->_receiverServer).')'
            : '<strong>'.$this->_itemData['item_qty'].'x '.$itemInfo['item_name'].'</strong> has been mailed to <strong>'.$characterData['name'].'</strong> ('.ucfirst($this->_server).')';
            
        message($successMessage, 'success');

        // --------------------------------------------------
        // BattlePass: grant progress points for this purchase
        // --------------------------------------------------
        try {
            $bpSeason = $this->db->queryFetchSingle(
                "SELECT `id`, `points_per_credit` FROM `aioncms`.`battlepass_seasons`
                 WHERE `status` = 1 AND `start_date` <= NOW() AND `end_date` >= NOW()
                 ORDER BY `id` DESC LIMIT 1"
            );
            if (is_array($bpSeason)) {
                $bpPoints = (int)$this->_itemData['cost'] * (int)$bpSeason['points_per_credit'];
                if ($bpPoints > 0 && class_exists('BattlePass')) {
                    BattlePass::addPoints(
                        (int)$this->_accountData['id'],
                        $bpPoints,
                        'shop_purchase',
                        'Weekly item '.$this->_itemData['item_id'].' x'.$this->_itemData['item_qty'].' ('.$this->_itemData['cost'].' credits)',
                        (int)$bpSeason['id']
                    );
                }
            }
        } catch (Exception $e) {
            logSystem::add('BattlePass points error: ' . $e->getMessage());
        }
    }
    
    private function _getAionPowerbookItemName($itemId) {
        $url = "https://aionpowerbook.com/powerbook/Item/" . $itemId;
        $result = file_get_contents($url);
        if(!$result) return;
        
        preg_match('!<h1 id="firstHeading" class="firstHeading" lang="en-GB">(.*)</h1>!s', $result, $matches);
        if($matches[1] == "Item") return;
        if(!check($matches[1])) return;
        return $matches[1];
    }
    
    private function _getAionPowerbookItemIcon($itemId) {
        $url = "https://aioncodex.com/items/" . $itemId;
        //$result = file_get_contents($url);
        if(!$result) return;
        
        preg_match('!\[img\](.*)\[/img\]!s', $result, $matches);
        if(!check($matches[1])) return;
        return $matches[1];
    }
    
}