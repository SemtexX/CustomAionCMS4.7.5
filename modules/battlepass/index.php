<?php
/**
 * BattlePass main page
 */
try {

    $BP = new BattlePass();

    if (!isLoggedIn()) {
        throw new Exception('You must be logged in to view the BattlePass.');
    }

    $BP->setUserId($_SESSION['userid']);
    $BP->setServer('siel');
    $accountData = $BP->getAccountData();

    if (!$BP->hasActiveSeason()) {
        throw new Exception('There is currently no active season.');
    }

    // Grant daily-login points lazily on page load
    $BP->grantDailyLoginIfDue();

    $season  = $BP->getCurrentSeason();
    $tiers   = $BP->getTiers();
    $points  = $BP->getPoints();
    $curTier = $BP->getCurrentTierNumber();
    $claimed = $BP->getClaimedTierIds();

    // Character list for claim dropdown
    $sdb = Handler::loadDB('siel');
    $characters = $sdb->queryFetch(
        "SELECT * FROM `players` WHERE `account_id` = ?",
        array($accountData['id'])
    );

    echo '<div class="shop-container">';

    // Header (mirrors shop header style)
    echo '<div class="shop-header">';
        echo '<div class="row text-center">';
            echo '<h2>BattlePass &ndash; '.htmlspecialchars($season['name']).'</h2>';
            echo '<p style="color:#aaa;">'.htmlspecialchars($season['description']).'</p>';
            echo '<p>Season ends on '.date('d.m.Y H:i', strtotime($season['end_date'])).'</p>';
        echo '</div>';
        echo '<div class="row text-center">';
            echo '<div class="col-md-4">';
                echo '<strong>Your Points</strong><br>';
                echo '<span style="color:#C99B4B;font-size:24px;">'.number_format($points).'</span>';
            echo '</div>';
            echo '<div class="col-md-4">';
                echo '<strong>Current Tier</strong><br>';
                echo '<span style="color:#C99B4B;font-size:24px;">'.$curTier.' / '.count($tiers).'</span>';
            echo '</div>';
            echo '<div class="col-md-4">';
                echo '<strong>Unlocked</strong><br>';
                echo '<span style="color:#C99B4B;font-size:24px;">'.count($claimed).'</span>';
            echo '</div>';
        echo '</div>';
    echo '</div>';

    // Tier grid
    echo '<div class="shop-content">';
    echo '<div class="row">';

    if (!is_array($characters) || count($characters) === 0) {
        echo '<div class="col-md-12">';
            message('You do not have any characters on this server yet. Create one first.', 'warning');
        echo '</div>';
    }

    foreach ($tiers as $t) {
        $tierNum   = (int)$t['tier_number'];
        $required  = (int)$t['required_points'];
        $isReached = $points >= $required;
        $isClaimed = in_array((int)$t['id'], array_map('intval', $claimed), true);

        $rewardIcon  = '';
        $rewardLabel = '';
        if ($t['reward_type'] === 'item') {
            $rewardIcon  = $BP->getItemIconUrl((int)$t['reward_item_id']);
            $rewardLabel = (int)$t['reward_item_count'].'x '.$BP->getItemName((int)$t['reward_item_id']);
        } elseif ($t['reward_type'] === 'credits') {
            $rewardLabel = number_format((int)$t['reward_credits']).' Credits';
        } else {
            $rewardLabel = $t['reward_label'];
        }

        $border = $isClaimed ? '#3c3' : ($isReached ? '#C99B4B' : '#555');

        echo '<div class="col-md-3 col-sm-4 col-xs-6" style="margin-bottom:20px;">';
        echo '<div style="border:2px solid '.$border.';border-radius:6px;padding:10px;text-align:center;background:#1e1e1e;">';

        echo '<div style="font-size:11px;color:#aaa;">Tier</div>';
        echo '<div style="font-size:22px;font-weight:bold;color:#C99B4B;">'.$tierNum.'</div>';

        if ($rewardIcon !== '') {
            echo '<img src="'.htmlspecialchars($rewardIcon).'" style="max-width:48px;max-height:48px;margin:6px 0;" />';
        }
        echo '<div style="font-size:12px;color:#ddd;min-height:32px;">'.htmlspecialchars($rewardLabel).'</div>';
        echo '<div style="font-size:10px;color:#888;">'.$required.' points</div>';

        if ($isClaimed) {
            echo '<div style="color:#3c3;font-weight:bold;margin-top:6px;">&#10003; Claimed</div>';
        } elseif ($isReached) {
            echo '<button class="btn btn-success btn-sm bp-claim-btn" '
               . 'data-tier-id="'.(int)$t['id'].'" '
               . 'style="margin-top:6px;">Claim</button>';
        } else {
            echo '<div style="color:#777;margin-top:6px;">&#128274;</div>';
        }

        echo '</div>';
        echo '</div>';
    }

    echo '</div>';
    echo '</div>';
    echo '</div>';

    // Character selection modal
    echo '<div class="modal fade" id="claimModal" tabindex="-1" role="dialog">';
    echo '<div class="modal-dialog" role="document"><div class="modal-content">';
    echo '<div class="modal-header">';
    echo '<button type="button" class="close" data-dismiss="modal"><span>&times;</span></button>';
    echo '<h4 class="modal-title">Claim reward</h4>';
    echo '</div>';
    echo '<div class="modal-body">';
    echo '<form id="claimForm">';
    echo '<input type="hidden" name="tier_id" id="claimTierId" value="">';
    echo '<input type="hidden" name="csrf_token" value="'.htmlspecialchars($_SESSION['csrf_token']).'">';
    echo '<div class="form-group">';
    echo '<label>Select character</label>';
    echo '<select name="character" class="form-control" required>';
    if (is_array($characters)) {
        foreach ($characters as $c) {
            echo '<option value="'.htmlspecialchars($c['name']).'">'.htmlspecialchars($c['name']).'</option>';
        }
    }
    echo '</select>';
    echo '</div>';
    echo '</form>';
    echo '</div>';
    echo '<div class="modal-footer">';
    echo '<button type="button" class="btn btn-default" data-dismiss="modal">Cancel</button>';
    echo '<button type="button" class="btn btn-success" id="confirmClaim">Claim</button>';
    echo '</div>';
    echo '</div></div></div>';

    // Inline JS (vanilla + jQuery, jQuery is already in your admin layout)
    echo '<script>
    document.addEventListener("DOMContentLoaded", function() {
        document.querySelectorAll(".bp-claim-btn").forEach(function(btn) {
            btn.addEventListener("click", function() {
                document.getElementById("claimTierId").value = this.getAttribute("data-tier-id");
                jQuery("#claimModal").modal("show");
            });
        });
        document.getElementById("confirmClaim").addEventListener("click", function() {
            var form = document.getElementById("claimForm");
            var fd = new FormData(form);
            fetch("'.__BASE_URL__.'battlepass/claim/", {method:"POST", body:fd, credentials:"same-origin"})
                .then(function(r){ return r.json(); })
                .then(function(data){
                    if (data.ok) {
                        alert(data.message || "Reward claimed!");
                        location.reload();
                    } else {
                        alert("Error: " + (data.message || "Unknown error"));
                    }
                })
                .catch(function(){ alert("Network error."); });
        });
    });
    </script>';

} catch (Exception $ex) {
    echo '<div class="shop-container"><div class="shop-content">';
    message($ex->getMessage(), 'error');
    echo '</div></div>';
}