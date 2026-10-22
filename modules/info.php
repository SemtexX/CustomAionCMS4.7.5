<?php
// Am Anfang jeder Seite
include('countdown_timer.php');
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Server Information</title>
    <style>
        body {
            <!--font-family: Arial, sans-serif;-->
            background-color: #f0f0f0;
            color: #000;
            text-align: center;
            margin: 0;
            padding: 0;
        }
        .container {
            width: 90%;
            max-width: 500px;
            margin: 20px auto;
            background-color: #1a1a1a;
            padding: 2px;
            border-radius: 15px;
            overflow-y: auto; /* Hier wird der vertikale Scrollbalken hinzugefügt */
            max-height: 750px; /* Festlegung der maximalen Höhe des Containers */
        }
        .box {
            background-color: #FFFFFF;
            color: #000;
            margin: 5px 0;
            padding: 7px;
            border-radius: 5px;
        }
        .section-title {
            background-color: #FFFFFF;
            color: #000;
            padding: 10px;
            font-size: 1.2em;
            font-weight: bold;
        }
    </style>
</head>
<body>
    <div class="container">
        <div class="box section-title">Server</div>
        <div class="box">Low-Midrate [WEST Siel]</div>
        <div class="box">All Classes Available</div>
        <div class="box">Starter Pakets for Lvl 25,35,45,55 and 65</div>
        <div class="box">Item & Glorypoints Online Bonus</div>
        <div class="box">Daily GloryPoints Reduction implemented</div>
        <div class="box">Housing completely implemented</div>
        <div class="box">All Instances 60 min Cooldown</div>
        <div class="box">Unique System Custom</div>
        <div class="box">No Instance lvl Requirements</div>
        <div class="box">Feather Enchanting Available</div>
        <div class="box">Accessory Enchanting Available</div>
        <div class="box">Item Evolution Available [NEW]</div>
        <div class="box">Item Identification Available</div>
        <div class="box">Decomposable System Available</div>
        <div class="box">New Dropsystem on all Maps</div>
        <div class="box">Enchanting Animation times reduced</div>
        <div class="box">Many Event NPCs Spawned</div>
        <div class="box">Geodata available on all maps</div>
        <div class="box">Navmesh Pathfinding[BETA] available</div>
        <div class="box">.help show all Ingame Commands</div>
        <div class="box">Webshop System</div>
        <div class="box">Website Ticket System</div>
        <div class="box">Website Userpanel</div>
        <div class="box">Website Online Time Exchange</div>
        <div class="box">Website Referral System</div>

        <div class="box section-title">-Server-Rates-</div>
        <div class="box">Exp: Progressive x1 - x5</div>
        <div class="box">Grp.Exp: Progressive x2 - x6</div>
        <div class="box">Kinah: x5</div>
        <div class="box">Drop: x5</div>
        <div class="box">Quest: Progressive x1 - x5</div>
        <div class="box">Manastones: 70%</div>
        <div class="box">Enchant: 50%</div>
        <div class="box">Tempering: 80%</div>

        <div class="box section-title">-Weekend Server-Rates-</div>
        <div class="box">Exp: Progressive x2 - x6</div>
        <div class="box">Grp.Exp: Progressive x3 - x7</div>
        <div class="box">Kinah: x6</div>
        <div class="box">Drop: x6</div>
        <div class="box">Quest: Progressive x2 - x6</div>
        <div class="box">Manastones: 70%</div>
        <div class="box">Enchant: 50%</div>
        <div class="box">Tempering: 80%</div>

        <div class="box section-title">Rates for Mobs [MaxHP]</div>
        <div class="box">Junk Mobs Health: x1.1</div>
        <div class="box">Normal Mobs Health: x1.2</div>
        <div class="box">Elite Mobs Health: x1.3</div>
        <div class="box">Hero Mobs Health: x1.4</div>
        <div class="box">Legendary Mobs Health: x1.5</div>

        <div class="box section-title">Rates for Mobs [Power]</div>
        <div class="box">Junk Mobs Power: x1.1</div>
        <div class="box">Normal Mobs Power: x1.2</div>
        <div class="box">Elite Mobs Power: x1.3</div>
        <div class="box">Hero Mobs Power: x1.4</div>
        <div class="box">Legendary Mobs Power: x1.5</div>
    </div>
</body>
</html>
