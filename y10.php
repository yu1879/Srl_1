<?php
require_once('./database.php');
session_start();
header('Cache-Control: Private');

if (!$_SESSION['account']) {
    header("Location: ./login.php");
    exit();
}

if (isset($_SESSION['message'])) {
    echo "<script>alert('{$_SESSION['message']}')</script>";
    unset($_SESSION['message']);
}

$school_id = $_SESSION['school_id'] ?? null;
$school_type = $_SESSION['school_type'] ?? null;

$project = $_POST['project'] ?? '112-113年5G新科技學習示範學校計畫';
$year = $_POST['year'] ?? '112';
$month = $_POST['month'] ?? strval(intval(date('n')));
$status = $_POST['status'] ?? '0';
if ((int)$month + 1 > 12) {
    $deadline = date((int)$year + 1 . '-1-5');
} else {
    $deadline = date($year . '-' . (int)$month + 1 . '-5');
}
$connect = connect_sql();
$conditions = "where project='$project' and year='$year' and month='$month' and unit='$school_id' order by version desc";
$demo_teachers = select($connect, 'demo_teachers', '*', $conditions);
$demo_result = select($connect, 'demo_result', '*', $conditions);
$demo_public = select($connect, 'demo_public', '*', $conditions);
$demo_school = select($connect, 'demo_school', '*', $conditions);
$demo_evaluate = select($connect, 'demo_evaluate', '*', $conditions);
$demo_counseling = select($connect, 'demo_counseling', '*', $conditions);

$display_demo_teachers = !($status === '0' xor count($demo_teachers) === 0);
$display_demo_result = !($status === '0' xor count($demo_result) === 0);
$display_demo_public = !($status === '0' xor count($demo_public) === 0);
$display_demo_school = !($status === '0' xor count($demo_school) === 0);
$display_demo_evaluate = !($status === '0' xor count($demo_evaluate) === 0);
$display_demo_counseling = !($status === '0' xor count($demo_counseling) === 0);
?>

<!DOCTYPE html>
<html lang="en" data-theme="light">

<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link href="https://fonts.googleapis.com/css2?family=Noto+Sans+TC&family=Roboto&display=swap" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/daisyui@2.51.5/dist/full.css" rel="stylesheet" type="text/css" />
    <link href="https://cdn.jsdelivr.net/npm/tailwindcss@2.2/dist/tailwind.min.css" rel="stylesheet" type="text/css" />
    <title>計畫管考系統</title>
    <style>
        html,
        body {
            padding: 0;
            margin: 0;
            font-family: -apple-system, BlinkMacSystemFont, Segoe UI, Roboto, Oxygen,
                Ubuntu, Cantarell, Fira Sans, Droid Sans, Helvetica Neue, Noto Sans TC,
                sans-serif;
        }
    </style>
</head>

<body>
    <div class="max-w-5xl mx-auto lg:pb-4">
        <?php require('./navbar.php') ?>

        <div id="top" class="p-4 max-w-3xl mx-auto bg-base-200 lg:rounded-xl">
            <h1 class="text-2xl">填報資料</h1>

            <form class="form-control" name="form" method="post" action="./y10.php#top">
                <div class="grid grid-cols-1 sm:grid-cols-4 gap-2 mt-4">
                    <div class="flex flex-col gap-2 bg-base-100 rounded-lg p-4 sm:col-span-2">
                        <p class="text-base">計畫名稱：</p>
                        <select class="select select-bordered select-sm w-full mt-1" name="project" onChange="auto_submit()">
                            <option value='112-113年5G新科技學習示範學校計畫' <?php if ($project === '112-113年5G新科技學習示範學校計畫') echo 'selected' ?>>112-113年5G新科技學習示範學校計畫</option>
                            <!-- <option value='112-113年數位學習推動計畫' <?php if ($project === '112-113年數位學習推動計畫') echo 'selected' ?>>112-113年數位學習推動計畫</option> -->
                        </select>
                    </div>
                    <div class="flex flex-col gap-2 bg-base-100 rounded-lg p-4">
                        <p class="text-bae">年度：</p>
                        <select class="select select-bordered select-sm w-full mt-1" name="year" onChange="auto_submit()">
                            <option value='112' <?php if ($year === '112') echo 'selected' ?>>112</option>
                            <option value='113' <?php if ($year === '113') echo 'selected' ?>>113</option>
                        </select>
                    </div>
                    <div class="flex flex-col gap-2 bg-base-100 rounded-lg p-4">
                        <p class="text-base">填報月份：</p>
                        <select class="select select-bordered select-sm mt-1" name="month" onChange="auto_submit()">
                            <?php
                            for ($m = 1; $m <= 12; $m++) {
                                if ($m == $month) {
                                    echo "<option value='$m' selected='selected'>{$m}月</option>";
                                } else {
                                    echo "<option value='$m'>{$m}月</option>";
                                }
                            }
                            ?>
                        </select>
                    </div>
                </div>
                <div class="grid grid-cols-1 md:grid-cols-4 gap-2 mt-2">
                    <div class="flex flex-col gap-2 bg-base-100 rounded-lg p-4">
                        <p class="text-base">填報狀態：</p>
                        <select class="select select-bordered select-sm w-full mt-1" name="status" onChange="auto_submit()">
                            <option value='0' <?php if ($status === '0') echo 'selected' ?>>未填報</option>
                            <option value='1' <?php if ($status === '1') echo 'selected' ?>>已填報</option>
                        </select>
                    </div>
                </div>
            </form>
            <?php if (!$display_demo_teachers && !$display_demo_result && !$display_demo_public && !$display_demo_school && !$display_demo_counseling) { ?>
                <div class="p-8">
                    <p class="text-2xl text-error">查無資料</p>
                </div>
            <?php } else { ?>
                <div class="overflow-x-auto w-full mt-8">
                    <table class="table w-full">
                        <thead>
                            <tr>
                                <th class="bg-base-100">#</th>
                                <th class="bg-base-100">名稱</th>
                                <th class="bg-base-100">年度</th>
                                <th class="bg-base-100">月份</th>
                                <th class="bg-base-100">截止時間</th>
                                <th class="bg-base-100"></th>
                            </tr>
                        </thead>
                        <?php if ($display_demo_teachers) { ?>
                            <tr>
                                <th>#</th>
                                <td>教師培訓人數</td>
                                <td><?= $year ?></td>
                                <td><?= $month ?>月</td>
                                <td><?= $deadline ?></td>
                                <td>
                                    <form action="./y05.php" method="post">
                                        <input type="hidden" name="project" value="<?= $project ?>">
                                        <input type="hidden" name="year" value="<?= $year ?>">
                                        <input type="hidden" name="month" value="<?= $month ?>">
                                        <div class="flex gap-2">
                                            <button class="btn btn-sm"><?= $status === '1' && ($demo_teachers[0]['state'] === '-1' || $demo_teachers[0]['state'] === '-2') ? '未辦理' : ($status === '1' ? '修改' : '填報') ?></button>
                                            <?php if ($status === '0') { ?>
                                                <button class="btn btn-sm" name="state" value="-1">未辦理</button>
                                            <?php } ?>
                                        </div>
                                    </form>
                                </td>
                            </tr>
                        <?php } ?>
                        <?php if ($display_demo_result) { ?>
                            <tr>
                                <th>#</th>
                                <td>實施成果</td>
                                <td><?= $year ?></td>
                                <td><?= $month ?>月</td>
                                <td><?= $deadline ?></td>
                                <td>
                                    <form action="./y04.php" method="post">
                                        <input type="hidden" name="project" value="<?= $project ?>">
                                        <input type="hidden" name="year" value="<?= $year ?>">
                                        <input type="hidden" name="month" value="<?= $month ?>">
                                        <div class="flex gap-2">
                                            <button class="btn btn-sm"><?= $status === '1' && ($demo_result[0]['state'] === '-1' || $demo_result[0]['state'] === '-2') ? '未辦理' : ($status === '1' ? '修改' : '填報') ?></button>
                                            <?php if ($status === '0') { ?>
                                                <button class="btn btn-sm" name="state" value="-1">未辦理</button>
                                            <?php } ?>
                                        </div>
                                    </form>
                                </td>
                            </tr>
                        <?php } ?>
                        <?php if ($display_demo_public) { ?>
                            <tr>
                                <th>#</th>
                                <td>當月公開授課紀錄</td>
                                <td><?= $year ?></td>
                                <td><?= $month ?>月</td>
                                <td><?= $deadline ?></td>
                                <td>
                                    <form action="./y03.php" method="post">
                                        <input type="hidden" name="project" value="<?= $project ?>">
                                        <input type="hidden" name="year" value="<?= $year ?>">
                                        <input type="hidden" name="month" value="<?= $month ?>">
                                        <div class="flex gap-2">
                                            <button class="btn btn-sm"><?= $status === '1' && ($demo_public[0]['state'] === '-1' || $demo_public[0]['state'] === '-2') ? '未辦理' : ($status === '1' ? '修改' : '填報') ?></button>
                                            <?php if ($status === '0') { ?>
                                                <button class="btn btn-sm" name="state" value="-1">未辦理</button>
                                            <?php } ?>
                                        </div>
                                    </form>
                                </td>
                            </tr>
                        <?php } ?>
                        <?php if ($display_demo_school) { ?>
                            <?php if ($school_type == '1') { ?>
                                <tr>
                                    <th>#</th>
                                    <td>當月開放教室紀錄</td>
                                    <td><?= $year ?></td>
                                    <td><?= $month ?>月</td>
                                    <td><?= $deadline ?></td>
                                    <td>
                                        <form action="./y14.php" method="post">
                                            <input type="hidden" name="project" value="<?= $project ?>">
                                            <input type="hidden" name="year" value="<?= $year ?>">
                                            <input type="hidden" name="month" value="<?= $month ?>">
                                            <div class="flex gap-2">
                                                <button class="btn btn-sm"><?= $status === '1' && ($demo_school[0]['state'] === '-1' || $demo_school[0]['state'] === '-2') ? '未辦理' : ($status === '1' ? '修改' : '填報') ?></button>
                                                <?php if ($status === '0') { ?>
                                                    <button class="btn btn-sm" name="state" value="-1">未辦理</button>
                                                <?php } ?>
                                            </div>
                                        </form>
                                    </td>
                                </tr>
                            <?php } ?>
                        <?php } ?>
                        <?php if ($display_demo_evaluate) { ?>
                            <tr>
                                <th>#</th>
                                <td>教學成效評估</td>
                                <td><?= $year ?></td>
                                <td><?= $month ?>月</td>
                                <td><?= $deadline ?></td>
                                <td>
                                    <form action="./y13.php" method="post">
                                        <input type="hidden" name="project" value="<?= $project ?>">
                                        <input type="hidden" name="year" value="<?= $year ?>">
                                        <input type="hidden" name="month" value="<?= $month ?>">
                                        <div class="flex gap-2">
                                            <button class="btn btn-sm"><?= $status === '1' && ($demo_evaluate[0]['state'] === '-1' || $demo_evaluate[0]['state'] === '-2') ? '未辦理' : ($status === '1' ? '修改' : '填報') ?></button>
                                            <?php if ($status === '0') { ?>
                                                <button class="btn btn-sm" name="state" value="-1">未辦理</button>
                                            <?php } ?>
                                        </div>
                                    </form>
                                </td>
                            </tr>
                        <?php } ?>
                        <?php if ($display_demo_counseling) { ?>
                            <tr>
                                <th>#</th>
                                <td>當月入校輔導紀錄</td>
                                <td><?= $year ?></td>
                                <td><?= $month ?>月</td>
                                <td><?= $deadline ?></td>
                                <td>
                                    <form action="./y02.php" method="post">
                                        <input type="hidden" name="project" value="<?= $project ?>">
                                        <input type="hidden" name="year" value="<?= $year ?>">
                                        <input type="hidden" name="month" value="<?= $month ?>">
                                        <div class="flex gap-2">
                                            <button class="btn btn-sm"><?= $status === '1' && ($demo_counseling[0]['state'] === '-1' || $demo_counseling[0]['state'] === '-2') ? '未辦理' : ($status === '1' ? '修改' : '填報') ?></button>
                                            <?php if ($status === '0') { ?>
                                                <button class="btn btn-sm" name="state" value="-1">未辦理</button>
                                            <?php } ?>
                                        </div>
                                    </form>
                                </td>
                            </tr>
                        <?php } ?>
                        <tfoot>
                            <tr>
                                <th class="bg-base-100">#</th>
                                <th class="bg-base-100">名稱</th>
                                <th class="bg-base-100">年度</th>
                                <th class="bg-base-100">月份</th>
                                <th class="bg-base-100">截止時間</th>
                                <th class="bg-base-100"></th>
                            </tr>
                        </tfoot>
                    </table>
                </div>
            <?php } ?>
        </div>
    </div>

    <script>
        function auto_submit() {
            form = document.forms['form'];
            form.submit();
        }
    </script>
</body>

</html>