<!DOCTYPE html>
<html lang="ja">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width,initial-scale=1.0">
    <title>Title</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Noto+Sans+JP:wght@100..900&display=swap" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet"
          integrity="sha384-9ndCyUaIbzAi2FUVXJi0CjmCapSmO7SnpJef0486qhLnuZ2cdeRhO02iuK6FUUVM" crossorigin="anonymous">
    <link rel="stylesheet" href="css/stylemainpage.css">
</head>
<!--ヘッダー-->
<?php include 'common/header.html'; ?>

<body class="bg-light">
<div class="container-fluid">
    <div class="row">

        <!-- サイドバー（PCのみ表示） -->
        <?php include 'common/sidebar.php'; ?>

        <!-- メインコンテンツ -->
        <main class="col-12 col-md-9 col-lg-10 px-md-4 d-flex flex-column align-items-center justify-content-center text-center mx-auto main-content-styles">
            <div class="mx-auto" style="max-width: 800px; padding-top: 80px;">

                <!-- タイトルとボタン（横並び） -->
                <div class="d-flex justify-content-between align-items-center mb-4">
                    <h2 class="mb-0">よくある質問一覧</h2>
                    <a href="#" class="btn btn-primary">
                        <i class="fas fa-plus"></i> 質問をする
                    </a>
                </div>

                <!-- FAQアコーディオン -->
                <div class="accordion shadow" id="faqAccordion">
                    <div class="accordion-item">
                        <h2 class="accordion-header" id="headingOne">
                            <button class="accordion-button" type="button" data-bs-toggle="collapse"
                                    data-bs-target="#collapseOne" aria-expanded="true" aria-controls="collapseOne">
                                行事の予定がわかりません
                            </button>
                        </h2>
                        <div id="collapseOne" class="accordion-collapse collapse show" aria-labelledby="headingOne"
                             data-bs-parent="#faqAccordion">
                            <div class="accordion-body">
                                行事の予定は「マイページ > 行事カレンダー」からご確認いただけます。
                            </div>
                        </div>
                    </div>

                    <div class="accordion-item">
                        <h2 class="accordion-header" id="headingTwo">
                            <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse"
                                    data-bs-target="#collapseTwo" aria-expanded="false" aria-controls="collapseTwo">
                                配布物がどこにあるか分からない
                            </button>
                        </h2>
                        <div id="collapseTwo" class="accordion-collapse collapse" aria-labelledby="headingTwo"
                             data-bs-parent="#faqAccordion">
                            <div class="accordion-body">
                                配布物は「お知らせ」タブにあるPDF一覧からダウンロードできます。
                            </div>
                        </div>
                    </div>

                    <div class="accordion-item">
                        <h2 class="accordion-header" id="headingThree">
                            <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse"
                                    data-bs-target="#collapseThree" aria-expanded="false" aria-controls="collapseThree">
                                チャットの通知が届かない
                            </button>
                        </h2>
                        <div id="collapseThree" class="accordion-collapse collapse" aria-labelledby="headingThree"
                             data-bs-parent="#faqAccordion">
                            <div class="accordion-body">
                                通知設定がOFFになっている可能性があります。アプリの設定をご確認ください。
                            </div>
                        </div>
                    </div>

                    <div class="accordion-item">
                        <h2 class="accordion-header" id="headingFour">
                            <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse"
                                    data-bs-target="#collapseFour" aria-expanded="false" aria-controls="collapseFour">
                                コミュニティの作成方法が知りたい
                            </button>
                        </h2>
                        <div id="collapseFour" class="accordion-collapse collapse" aria-labelledby="headingFour"
                             data-bs-parent="#faqAccordion">
                            <div class="accordion-body">
                                「コミュニティを作成」ボタンを押し、必要事項を入力して作成できます。
                            </div>
                        </div>
                    </div>
                </div>

            </div> <!-- .mx-auto -->
        </main>
    </div>
</div>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"
        integrity="sha384-geWF76RCwLtnZ8qwWowPQNguL3RmwHVBC9FhGdlKrxdiJJigb/j/68SIy3Te4Bkz" crossorigin="anonymous"></script>
<script src="../../index.js"></script>
</body>

</html>