<?php
include '../config/db.php';
include '../config/session.php';
include 'header.php';
include 'navbar.php';
?>
<html>
<head>
    <title>Surat Jalan</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <style>
        body {
            font-family: Arial, sans-serif;
        }
        .border-table, .border-table th, .border-table td {
            border: 1px solid black;
            border-collapse: collapse;
        }
        .border-table th, .border-table td {
            padding: 5px;
            text-align: center;
        }
    </style>
</head>
<body class="p-8">
    <div class="text-center mb-4">
        <p class="text-lg">BANDUNG, 05 OKTOBER 2024</p>
        <p class="text-xl font-bold">SURAT JALAN</p>
    </div>
    <div class="mb-4">
        <p>SAHARA TEXTILE</p>
        <p>JL. ADIPATI AGUNG NO.31</p>
    </div>
    <div class="mb-4">
        <p>IBU USWATUN</p>
        <p>CIREBON</p>
    </div>
    <div class="mb-4">
        <p>NO SJ: SHR/2024/10/001</p>
        <p>NO kendaraan:</p>
    </div>
    <table class="border-table w-full text-sm">
        <thead>
            <tr>
                <th rowspan="2">NO</th>
                <th rowspan="2">NAMA ITEM</th>
                <th colspan="10">QTY</th>
                <th rowspan="2">TOTAL MTR</th>
                <th rowspan="2">ROLL/BAL</th>
            </tr>
            <tr>
                <th>1</th>
                <th>2</th>
                <th>3</th>
                <th>4</th>
                <th>5</th>
                <th>6</th>
                <th>7</th>
                <th>8</th>
                <th>9</th>
                <th>10</th>
            </tr>
        </thead>
        <tbody>
            <tr>
                <td>1</td>
                <td>DISPERSE FLOWER GRADATION</td>
                <td>120</td>
                <td>120</td>
                <td>120</td>
                <td>120</td>
                <td>120</td>
                <td>120</td>
                <td>120</td>
                <td>120</td>
                <td>120</td>
                <td>120</td>
                <td>600</td>
                <td>5</td>
            </tr>
            <tr>
                <td>2</td>
                <td>DISPERSE CINAMOROL</td>
                <td>120</td>
                <td>120</td>
                <td>120</td>
                <td>120</td>
                <td>120</td>
                <td>120</td>
                <td>120</td>
                <td>120</td>
                <td>120</td>
                <td>120</td>
                <td>600</td>
                <td>5</td>
            </tr>
            <tr>
                <td>3</td>
                <td>DISPERSE FLAMINGO UNGU</td>
                <td>146</td>
                <td>120</td>
                <td>120</td>
                <td>120</td>
                <td>120</td>
                <td>120</td>
                <td>120</td>
                <td>120</td>
                <td>120</td>
                <td>120</td>
                <td>1226</td>
                <td>10</td>
            </tr>
            <tr>
                <td>4</td>
                <td>DISPERSE DORA KIMONO</td>
                <td>120</td>
                <td>120</td>
                <td>120</td>
                <td>120</td>
                <td>120</td>
                <td>120</td>
                <td>120</td>
                <td>120</td>
                <td>120</td>
                <td>120</td>
                <td>600</td>
                <td>5</td>
            </tr>
            <tr>
                <td>5</td>
                <td>DISPERSE PANDA MOON</td>
                <td>120</td>
                <td>120</td>
                <td>120</td>
                <td>120</td>
                <td>120</td>
                <td>120</td>
                <td>120</td>
                <td>120</td>
                <td>120</td>
                <td>120</td>
                <td>600</td>
                <td>5</td>
            </tr>
            <tr>
                <td>6</td>
                <td>DISPERSE GREY ORCHID</td>
                <td>120</td>
                <td>120</td>
                <td>120</td>
                <td>120</td>
                <td>120</td>
                <td>120</td>
                <td>120</td>
                <td>120</td>
                <td>120</td>
                <td>120</td>
                <td>600</td>
                <td>5</td>
            </tr>
            <tr>
                <td>7</td>
                <td>DISPERSE BROWN MINNIE MOUSE</td>
                <td>120</td>
                <td>120</td>
                <td>120</td>
                <td>120</td>
                <td>120</td>
                <td>120</td>
                <td>120</td>
                <td>120</td>
                <td>120</td>
                <td>120</td>
                <td>600</td>
                <td>5</td>
            </tr>
            <tr>
                <td>8</td>
                <td>DISPERSE MINNIE MOUSE</td>
                <td>120</td>
                <td>120</td>
                <td>120</td>
                <td>120</td>
                <td>120</td>
                <td>120</td>
                <td>120</td>
                <td>120</td>
                <td>120</td>
                <td>120</td>
                <td>600</td>
                <td>5</td>
            </tr>
            <tr>
                <td>9</td>
                <td>DISPERSE TULIP UNGU</td>
                <td>120</td>
                <td>120</td>
                <td>120</td>
                <td>120</td>
                <td>120</td>
                <td>120</td>
                <td>120</td>
                <td>120</td>
                <td>120</td>
                <td>120</td>
                <td>600</td>
                <td>5</td>
            </tr>
            <tr>
                <td>10</td>
                <td>DISPERSE GREEN ROSE</td>
                <td>120</td>
                <td>120</td>
                <td>120</td>
                <td>120</td>
                <td>120</td>
                <td>120</td>
                <td>120</td>
                <td>120</td>
                <td>120</td>
                <td>120</td>
                <td>600</td>
                <td>5</td>
            </tr>
            <tr>
                <td>11</td>
                <td>DISPERSE NOVITA</td>
                <td>120</td>
                <td>120</td>
                <td>120</td>
                <td>120</td>
                <td>120</td>
                <td>120</td>
                <td>120</td>
                <td>120</td>
                <td>120</td>
                <td>120</td>
                <td>600</td>
                <td>5</td>
            </tr>
            <tr>
                <td>12</td>
                <td>DISPERSE KOTAK HITAM</td>
                <td>120</td>
                <td>120</td>
                <td>120</td>
                <td>120</td>
                <td>120</td>
                <td>120</td>
                <td>120</td>
                <td>120</td>
                <td>120</td>
                <td>120</td>
                <td>600</td>
                <td>5</td>
            </tr>
            <tr>
                <td>13</td>
                <td>DISPERSE PINK SYIFA</td>
                <td>167</td>
                <td>120</td>
                <td>120</td>
                <td>120</td>
                <td>120</td>
                <td>120</td>
                <td>120</td>
                <td>120</td>
                <td>120</td>
                <td>120</td>
                <td>967</td>
                <td>8</td>
            </tr>
            <tr>
                <td>14</td>
                <td>DISPERSE RANIA</td>
                <td>120</td>
                <td>120</td>
                <td>120</td>
                <td>120</td>
                <td>120</td>
                <td>120</td>
                <td>120</td>
                <td>120</td>
                <td>120</td>
                <td>120</td>
                <td>600</td>
                <td>5</td>
            </tr>
            <tr>
                <td>15</td>
                <td>EMBOSS ABU TUA</td>
                <td>120</td>
                <td>120</td>
                <td>120</td>
                <td>120</td>
                <td>120</td>
                <td>120</td>
                <td>120</td>
                <td>120</td>
                <td>120</td>
                <td>120</td>
                <td>600</td>
                <td>5</td>
            </tr>
            <tr>
                <td>16</td>
                <td>EMBOSS TANGERIN</td>
                <td>120</td>
                <td>120</td>
                <td>120</td>
                <td>120</td>
                <td>120</td>
                <td>120</td>
                <td>120</td>
                <td>120</td>
                <td>120</td>
                <td>120</td>
                <td>600</td>
                <td>5</td>
            </tr>
            <tr>
                <td colspan="11" class="text-right font-bold">GRAND TOTAL</td>
                <td>12073</td>
                <td></td>
            </tr>
        </tbody>
    </table>
    <div class="flex justify-between mt-8">
        <div>
            <p>YANG MENERIMA,</p>
        </div>
        <div>
            <p>YANG MENGELUARKAN,</p>
        </div>
    </div>
</body>
</html>


<?php 
include 'footer.php';
$conn->close();
?>