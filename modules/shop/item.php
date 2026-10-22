<?php
// Am Anfang jeder Seite
include(__DIR__ . '/../countdown_timer.php');
?>
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

try {
    
    $Shop = new Shop();
    
    if(!check($_GET['id'])) throw new Exception('Your request could not be completed, please try again.');
    
    $ws = false;
    if(check($_GET['ws'])) {
        $Shop->setIsWeekly();
        $ws = true;
    }
    
    $Shop->setItemId($_GET['id']);
    $itemData = $Shop->getItemData();
    
    if(isLoggedIn()) {
        $Shop->setUserId($_SESSION['userid']);
        $accountData = $Shop->getAccountData();
    }
    
    echo '<div class="shop-container">';
        echo '<div class="shop-header">';
            echo '<div class="row text-center">';
                echo '<a href="'.module_url('shop/', true).'"><img src="'.template_img(true).'webshop/shop_header_title.png" /></a>';
            echo '</div>';
            
            if(isLoggedIn()) {
                echo '<div class="row">';
                    echo '<div class="col-md-3 text-center">';
                        echo 'ACCOUNT<br />';
                        echo '<a href="'.module_url('usercp/', true).'">usercp</a>';
                    echo '</div>';
                    echo '<div class="col-md-3 text-center">';
                        echo 'CREDITS<br />';
                        echo '<a href="'.module_url('donate/', true).'">'.number_format($accountData['toll']).' (add)</a>';
                    echo '</div>';
                    echo '<div class="col-md-3 text-center">';
                        echo 'HISTORY<br />';
                        echo '<a href="'.module_url('shop/history/', true).'">list</a>';
                    echo '</div>';
                    echo '<div class="col-md-3 text-center">';
                        echo 'HELP<br />';
                        echo '<a href="'.config('forum_url', true).'" target="_blank">forum</a>';
                    echo '</div>';
                echo '</div>';
            }
            
        echo '</div>';
            
        echo '<div class="shop-content">';
            echo '<div class="row">';
                echo '<div class="col-md-12" style="padding-top: 20px;">';
                    echo '<ol class="breadcrumb">';
                        echo '<li><a href="'.$Shop->getShopHome().'"><span class="glyphicon glyphicon-chevron-left" aria-hidden="true"></span> Shop Home</a></li>';
                    echo '</ol>';
                echo '</div>';
            echo '</div>';
            
            if(check($_POST['submit_purchase'])) {
                try {
                    if($_POST['submit_purchase'] != 'ok') throw new Exception('Could not purchase item.');
                    
                    if(check($_POST['gift_mode']) && $_POST['gift_mode'] == '1') {
                        // Gift purchase flow
                        if(!check($_POST['receiver_name'])) throw new Exception('Please enter the recipient\'s character name.');
                        if(!check($_POST['receiver_server'])) throw new Exception('Please select the recipient\'s server.');
                        
                        $Shop->setIsGift(true);
                        $Shop->setReceiverCharacter($_POST['receiver_name']);
                        $Shop->setReceiverServer($_POST['receiver_server']);
                        
                        if($ws == true) {
                            $Shop->buyItemWS();
                        } else {
                            $Shop->buyItem();
                        }
                        
                        message('The gift has been successfully sent to '.$_POST['receiver_name'].'!', 'success');
                    } else {
                        // Normal purchase flow
                        if(!check($_POST['character'])) throw new Exception('The selected character is not valid [1].');
                        
                        $requestData = explode("-", $_POST['character']);
                        if(!is_array($requestData)) throw new Exception('The selected character is not valid [2].');
                        if(!check($requestData[0])) throw new Exception('The selected character is not valid [3].');
                        if(!check($requestData[1])) throw new Exception('The selected character is not valid [4].');
                        
                        $serverName = $requestData[0];
                        $characterName = $requestData[1];
                        
                        $Shop->setServer($serverName);
                        $Shop->setCharacter($characterName);
                        if($ws == true){
                            $Shop->buyItemWS();
                        } else {
                            $Shop->buyItem();
                        }
                    }
                    
                } catch(Exception $ex) {
                    message($ex->getMessage(), 'error');
                }
            }
            
            echo '<div class="row">';
                echo '<div class="col-md-6 items-content">';
                    $Shop->displayItemDetail();
                echo '</div>';
                
                echo '<div class="col-md-6 items-content">';
                    
                    echo '<div class="row">';
                        echo '<div class="row shop-purchase-box">';
                            echo '<div class="col-md-6">';
                                echo '<span style="color:#aaa;font-size:16px;">Quantity: '.$itemData['count'].'x</span><br />';
                                echo '<span style="color:#aaa;font-size:16px;">Enchantment: '.$itemData['enchantment'].'</span><br />';
                                echo '<span style="color:#aaa;font-size:16px;">Temperance: '.$itemData['temperance'].'</span><br /><br />';
                                echo '<span style="color:#C99B4B;font-size:24px;font-weight:bold;">'.number_format($itemData['cost']).' Credits</span><br />';
                            echo '</div>';
                            echo '<div class="col-md-6 text-right">';
                                echo '<br /><br />';
                                echo '<a href="#" class="btn btn-primary" data-toggle="modal" data-target="#giftItem">Gift</a> ';
                                echo '<a href="#" class="btn btn-primary" data-toggle="modal" data-target="#purchaseItem">Purchase</a>';
                            echo '</div>';
                            echo '<div class="col-md-12" style="font-size: 12px;padding-top: 20px;">';
                                echo '<p>* Your item will be delivered by express mail in-game.</p>';
                                echo '<p>* Before adding items make sure you have free space.</p>';
                                echo '<p>* Make sure you are purchasing the correct item.</p>';
                                echo '<p>* We do not replace items purchased by mistake.</p>';
                                echo '<p>* Gifts cannot be refunded once sent.</p>';
                            echo '</div>';
                        echo '</div>';
                    echo '</div>';
                    
                echo '</div>';
            echo '</div>';
            
            echo '<br />';
            
        echo '</div>';
    echo '</div>';
    
} catch(Exception $ex) {
    # ERROR
    echo '<div class="shop-container">';
        echo '<div class="shop-header">';
            echo '<div class="row text-center">';
                echo '<a href="'.module_url('shop/', true).'"><img src="'.template_img(true).'webshop/shop_header_title.png" /></a>';
            echo '</div>';
        echo '</div>';
            
        echo '<div class="shop-content">';
            echo '<div class="row">';
                echo '<div class="col-md-12 items-content">';
                    message($ex->getMessage(), 'error');
                echo '</div>';
            echo '</div>';
        echo '</div>';
    echo '</div>';
}
?>

<div class="modal fade" id="underDevelopment" tabindex="-1" role="dialog" aria-labelledby="underDevelopmentMsg">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                <h4 class="modal-title" id="underDevelopmentMsg">Under Development</h4>
            </div>
            <div class="modal-body">
                This feature is currently being developed.
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-primary" data-dismiss="modal">Close</button>
            </div>
        </div>
    </div>
</div>

<div class="modal fade" id="purchaseItem" tabindex="-1" role="dialog" aria-labelledby="purchaseItemBox">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                <h4 class="modal-title" id="purchaseItemBox">Select your character</h4>
            </div>
            <div class="modal-body">
                <?php
                    try {
                        if(!isLoggedIn()) throw new Exception('You need to log-in to purchase items from the shop.');
                        
                        $sdb = Handler::loadDB('siel');
                        
                        $sielCharacters = $sdb->queryFetch("SELECT * FROM `players` WHERE `account_id` = ?", array($accountData['id']));
                        
                        if(!is_array($sielCharacters)) {
                            throw new Exception('Looks like you don\'t have any characters yet.');
                        }
                        
                        echo '<form action="" method="post">';
                            echo '<input type="hidden" name="gift_mode" value="0">';
                            echo '<div class="form-group">';
                                echo '<select class="form-control" name="character">';
                                    if(is_array($sielCharacters)) {
                                        foreach($sielCharacters as $sielCharacter) {
                                            echo '<option value="siel-'.$sielCharacter['name'].'">'.$sielCharacter['name'].' (GlobalXcentra)</option>';
                                        }
                                    }
                                echo '</select>';
                            echo '</div>';
                        
                    } catch(Exception $ex) {
                        message($ex->getMessage(), 'error');
                        $hideConfirmButton = true;
                    }
                ?>
            </div>
            <div class="modal-footer">
                <?php if(!$hideConfirmButton) { ?>
                <button type="submit" name="submit_purchase" value="ok" class="btn btn-success">Confirm Purchase</button>
                <?php } ?>
                </form>
                <button type="button" class="btn btn-primary" data-dismiss="modal">Cancel</button>
            </div>
        </div>
    </div>
</div>

<div class="modal fade" id="giftItem" tabindex="-1" role="dialog" aria-labelledby="giftItemBox">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                <h4 class="modal-title" id="giftItemBox">Send as gift</h4>
            </div>
            <div class="modal-body">
                <?php
                    try {
                        if(!isLoggedIn()) throw new Exception('You need to log-in to send gifts from the shop.');
                        
                        echo '<form action="" method="post">';
                            echo '<input type="hidden" name="gift_mode" value="1">';
                            
                            // Sender Character Name
                            echo '<div class="form-group">';
                                echo '<label for="sender_name">Your Character Name</label>';
                                echo '<input type="text" class="form-control" id="sender_name" name="sender_name" placeholder="Enter your character name" required value="'.(isset($_POST['sender_name']) ? htmlspecialchars($_POST['sender_name']) : '').'">';
                            echo '</div>';
                            
                            // Receiver Character Name
                            echo '<div class="form-group">';
                                echo '<label for="receiver_name">Recipient Character Name</label>';
                                echo '<input type="text" class="form-control" id="receiver_name" name="receiver_name" placeholder="Enter recipient character name" required value="'.(isset($_POST['receiver_name']) ? htmlspecialchars($_POST['receiver_name']) : '').'">';
                            echo '</div>';
                            
                            // Server Selection
                            echo '<div class="form-group">';
                                echo '<label for="receiver_server">Recipient Server</label>';
                                echo '<select class="form-control" id="receiver_server" name="receiver_server" required>';
                                    echo '<option value="">-- Select Server --</option>';
                                    echo '<option value="siel"'.(isset($_POST['receiver_server']) && $_POST['receiver_server'] == 'siel' ? ' selected' : '').'>GlobalXcentra</option>';
                                echo '</select>';
                            echo '</div>';
                            
                            echo '<div class="alert alert-info">';
                                echo '<strong>Note:</strong> The gift will be sent directly to the recipient via in-game mail.';
                                echo '<br>Please make sure all character names and server are correct.';
                            echo '</div>';
                        
                    } catch(Exception $ex) {
                        message($ex->getMessage(), 'error');
                        $hideGiftConfirmButton = true;
                    }
                ?>
            </div>
            <div class="modal-footer">
                <?php if(!$hideGiftConfirmButton) { ?>
                <button type="submit" name="submit_purchase" value="ok" class="btn btn-success">Send Gift</button>
                <?php } ?>
                </form>
                <button type="button" class="btn btn-primary" data-dismiss="modal">Cancel</button>
            </div>
        </div>
    </div>
</div>