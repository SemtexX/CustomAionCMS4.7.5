<?php
// Am Anfang jeder Seite
include('countdown_timer.php');
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>TimeTable</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            background-color: #f4f4f4;
            color: #333;
            margin: 0;
            padding: 0;
        }
        .main-section {
            width: 80%;
            margin: 20px auto;
            background-color: #1a1a1a;
            padding: 20px;
            border-radius: 10px;
            box-shadow: 0 0 10px rgba(0, 0, 0, 0.1);
            overflow-y: auto;
            max-height: 80vh;
			color: white;
        }
        h1, h2, h3 {
            text-align: center;
        }
        table {
            width: 100%;
            border-collapse: collapse;
            margin: 20px 0;
        }
        th, td {
            padding: 10px;
            border: 1px solid #ddd;
            text-align: left;
        }
        th {
            background-color: #1a1a1a;
        }
        .highlight {
            background-color: #006400;
            color: white;
        }
    </style>
    <script>
        function AutoFarbe() {
            setTimeout(FarbeWechseln, 2000);
        }

        function FarbeWechseln() {
            const DatumAktuell = new Date();
            const TagAktuell = DatumAktuell.getDay();
            const ElementID = "Tag" + TagAktuell;

            const TauschElement = document.getElementById(ElementID);
            if (TauschElement) {
                TauschElement.classList.toggle("highlight");
            } else {
                console.warn("CSS nicht unterstützt oder Fehler in der Deklaration!");
            }
        }

        document.addEventListener("DOMContentLoaded", AutoFarbe);
    </script>
</head>
<body>

<section class="main-section">
    <h3><u>TimeTable</u></h3>
    <div id="login-form">
        <table>
            <thead>
                <tr>
                    <th>Day</th>
                    <th>Events</th>
                </tr>
            </thead>
            <tbody>
                <?php
                $days = [
                    'Sunday', 'Monday', 'Tuesday', 'Wednesday', 'Thursday', 'Friday', 'Saturday'
                ];
                $schedules = [
                    'Monday' => ['17:00 - Sources', '21:00 - Bassen', '22:00 - Agentfight'],
                    'Tuesday' => ['17:00 - Sources', '21:00 - Miren/Kysis', '22:00 - Agentfight'],
                    'Wednesday' => ['17:00 - Sources', '19:00 - Sulphur/Siels Eastern/Western fortress', '21:00 - Pradeth'],
                    'Thursday' => ['17:00 - Sources', '19:00 - Gelkmaros/Inggison', '22:00 - Agentfight'],
                    'Friday' => ['17:00 - Sources', '21:00 - Sillus', '22:00 - Agentfight'],
                    'Saturday' => ['17:00 - Sources', '19:00 - Divine Fortress', '21:00 - Kaldor Raid'],
                    'Sunday' => ['17:00 - Sources', '19:00 - Gelkmaros/Inggison', '21:00 - Roah/Asteria/Krotan']
                ];

                foreach ($days as $index => $day) {
                    echo "<tr id='Tag$index'>";
                    echo "<td><strong>$day</strong></td>";
                    echo "<td>";
                    foreach ($schedules[$day] as $event) {
                        echo "$event<br>";
                    }
                    echo "</td>";
                    echo "</tr>";
                }
                ?>
            </tbody>
        </table>
    </div>
    <center>
        <?php
        $sections = [
            "Dredgion" => ["lvl 50" => "10.00-12.00/ 18.00-20.00/ 22.00-00.00", "Days" => "Monday-Sunday"],
            "Chantra-Dredgion" => ["lvl 55" => "10.00-12.00/ 18.00-20.00/ 22.00-00.00", "Days" => "Monday-Sunday"],
            "Terath-Dredgion" => ["lvl 55" => "10.00-12.00/ 18.00-20.00/ 22.00-00.00", "Days" => "Monday-Sunday"],
            "Arena of Discipline" => ["lvl 55-65" => "12.00-14.00/ 18.00-02.00", "Days" => "Monday-Sunday"],
            "Arena of Vehemence" => ["lvl 55-65" => "12.00-14.00/ 18.00-02.00", "Days" => "Monday-Sunday"],
            "Arena of Cooperation" => ["lvl 55-65" => "12.00-14.00/ 18.00-02.00", "Days" => "Monday-Sunday"],
            "Arena of Glory" => ["lvl 55-65" => "20.00-22.00", "Days" => "Saturday, Sunday"]
        ];

        $rifts = [
            "Rifts Elyos" => [
                "Eltnen" => [
                    "Monday" => "13-14/15-16/19-20",
                    "Thursday" => "13-14/15-16",
                    "Friday" => "13-14",
                    "Saturday" => "17-18",
                    "Sunday" => "15-16"
                ],
                "Heiron" => [
                    "Monday" => "13-14/15-16/19-20",
                    "Thursday" => "13-14/15-16",
                    "Friday" => "19-20",
                    "Saturday" => "01-02",
                    "Sunday" => "19-20"
                ],
                "Ingisson" => [
                    "Monday" => "02-04/19-21",
                    "Thursday" => "02-04/19-21",
                    "Friday" => "02-04/19-21",
                    "Saturday" => "02-04/19-21",
                    "Sunday" => "02-04/19-21"
                ]
            ],
            "Rifts Asmodians" => [
                "Morheim" => [
                    "Monday" => "13-14/17-18",
                    "Thursday" => "13-14",
                    "Friday" => "17-18",
                    "Saturday" => "15-16",
                    "Sunday" => "17-18"
                ],
                "Beluslan" => [
                    "Monday" => "15-16/19-20/01-02",
                    "Thursday" => "19-20",
                    "Friday" => "15-16/01-02",
                    "Saturday" => "19-20",
                    "Sunday" => "01-02"
                ],
                "Gelkmaros" => [
                    "Monday" => "02-04/19-21",
                    "Thursday" => "02-04/19-21",
                    "Friday" => "02-04/19-21",
                    "Saturday" => "02-04/19-21",
                    "Sunday" => "02-04/19-21"
                ]
            ]
        ];

        foreach ($sections as $title => $info) {
            echo "<h1>$title</h1>";
            foreach ($info as $level => $time) {
                echo "<h2>$level</h2>";
                echo "<p>$time</p>";
            }
            echo "<br>";
        }

        foreach ($rifts as $faction => $areas) {
            echo "<h1>$faction</h1>";
            foreach ($areas as $area => $days) {
                echo "<h2>$area</h2>";
                foreach ($days as $day => $time) {
                    echo "<p>$day</p>";
                    echo "<p>$time</p>";
                }
                echo "<br>";
            }
        }
        ?>
    </center>
</section>

</body>
</html>
