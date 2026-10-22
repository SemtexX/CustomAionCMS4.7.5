<?php
// countdown_timer.php
function displayGlobalCountdown() {
    $target_date = '2025-12-01 00:00:00';
    $target_timestamp = strtotime($target_date);
    $current_timestamp = time();
    $time_left = $target_timestamp - $current_timestamp;
    
    if ($time_left <= 0) {
        return '
        <div class="countdown-timer-top" style="position: fixed; top: 20px; right: 20px; z-index: 9999;">
            <div class="countdown-timer-expired">
                🎉 Event Started! 🎉
            </div>
        </div>
        <script>
            setTimeout(function(){
                if(document.querySelector(".countdown-timer-top")) {
                    document.querySelector(".countdown-timer-top").style.display = "none";
                }
            }, 5000);
        </script>
        ';
    }
    
    $days = floor($time_left / (60 * 60 * 24));
    $hours = floor(($time_left % (60 * 60 * 24)) / (60 * 60));
    $minutes = floor(($time_left % (60 * 60)) / 60);
    $seconds = $time_left % 60;
    
    return '
    <style>
    .countdown-timer-top {
        position: fixed !important;
        top: 20px !important;
        right: 20px !important;
        background: linear-gradient(135deg, #000000 0%, #6a2c91 50%, #000000 100%);
        color: white !important;
        padding: 15px !important;
        border-radius: 10px !important;
        text-align: center !important;
        box-shadow: 0 4px 15px rgba(0,0,0,0.4) !important;
        border: 1px solid #1e3799 !important;
        z-index: 9999 !important;
        min-width: 180px !important;
        font-family: Arial, sans-serif !important;
    }
    .countdown-timer-title {
        font-size: 14px !important;
        margin-bottom: 10px !important;
        font-weight: bold !important;
        color: #d6be93 !important;
        text-shadow: 1px 1px 2px rgba(0,0,0,0.5) !important;
    }
    .countdown-timer-grid {
        display: grid !important;
        grid-template-columns: repeat(4, 1fr) !important;
        gap: 8px !important;
        margin-bottom: 8px !important;
    }
    .countdown-timer-item {
        background: rgba(255,255,255,0.15) !important;
        padding: 8px 5px !important;
        border-radius: 6px !important;
        border: 1px solid rgba(255,255,255,0.2) !important;
    }
    .countdown-timer-number {
        font-size: 16px !important;
        font-weight: bold !important;
        display: block !important;
        color: #4bcffa !important;
        text-shadow: 1px 1px 2px rgba(0,0,0,0.3) !important;
    }
    .countdown-timer-label {
        font-size: 10px !important;
        opacity: 0.9 !important;
        margin-top: 2px !important;
        color: #dff9fb !important;
    }
    .countdown-timer-expired {
        background: linear-gradient(135deg, #00b894 0%, #00a085 100%) !important;
        padding: 15px !important;
        border-radius: 8px !important;
        font-weight: bold !important;
        font-size: 14px !important;
    }
    .countdown-timer-target {
        font-size: 10px !important;
        color: #c7ecee !important;
        margin-top: 5px !important;
        opacity: 0.8 !important;
    }
    
    /* Responsive */
    @media (max-width: 768px) {
        .countdown-timer-top {
            position: relative !important;
            top: auto !important;
            right: auto !important;
            margin: 10px auto !important;
            max-width: 300px !important;
        }
    }
    </style>
    
    <div class="countdown-timer-top">
        <div class="countdown-timer-title">⏳ Next Event</div>
        <div class="countdown-timer-grid">
            <div class="countdown-timer-item">
                <span class="countdown-timer-number" id="timer-days">'.$days.'</span>
                <span class="countdown-timer-label">Days</span>
            </div>
            <div class="countdown-timer-item">
                <span class="countdown-timer-number" id="timer-hours">'.str_pad($hours, 2, '0', STR_PAD_LEFT).'</span>
                <span class="countdown-timer-label">Hours</span>
            </div>
            <div class="countdown-timer-item">
                <span class="countdown-timer-number" id="timer-minutes">'.str_pad($minutes, 2, '0', STR_PAD_LEFT).'</span>
                <span class="countdown-timer-label">Mins</span>
            </div>
            <div class="countdown-timer-item">
                <span class="countdown-timer-number" id="timer-seconds">'.str_pad($seconds, 2, '0', STR_PAD_LEFT).'</span>
                <span class="countdown-timer-label">Secs</span>
            </div>
        </div>
        <div class="countdown-timer-target">01.12.2025</div>
    </div>
    <script>
        function updateGlobalTimer() {
            const target = new Date("2025-12-01T00:00:00").getTime();
            const now = new Date().getTime();
            const distance = target - now;
            
            if (distance <= 0) {
                window.location.reload();
                return;
            }
            
            const days = Math.floor(distance / (1000 * 60 * 60 * 24));
            const hours = Math.floor((distance % (1000 * 60 * 60 * 24)) / (1000 * 60 * 60));
            const minutes = Math.floor((distance % (1000 * 60 * 60)) / (1000 * 60));
            const seconds = Math.floor((distance % (1000 * 60)) / 1000);
            
            const daysElement = document.getElementById("timer-days");
            const hoursElement = document.getElementById("timer-hours");
            const minutesElement = document.getElementById("timer-minutes");
            const secondsElement = document.getElementById("timer-seconds");
            
            if(daysElement && hoursElement && minutesElement && secondsElement) {
                daysElement.textContent = days;
                hoursElement.textContent = hours.toString().padStart(2, "0");
                minutesElement.textContent = minutes.toString().padStart(2, "0");
                secondsElement.textContent = seconds.toString().padStart(2, "0");
            }
        }
        
        // Timer sofort starten und alle Sekunde aktualisieren
        updateGlobalTimer();
        setInterval(updateGlobalTimer, 1000);
    </script>
    ';
}

// Timer anzeigen
echo displayGlobalCountdown();
?>