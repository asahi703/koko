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
        <div class="team-option d-flex flex-row flex-column flex-md-row fs-5 justify-content-center mb-3">
          <ul class="navbar-nav flex-column flex-md-row gap-3 gap-md-5 w-100 align-items-center">
            <li class="nav-item">
              <a class="nav-link hover-text" href="#" data-bs-toggle="modal" data-bs-target="#joinCommunityModal">コミュニティに参加</a>
            </li>
            <li class="nav-item">
              <a class="nav-link hover-text" href="#" data-bs-toggle="modal" data-bs-target="#createCommunityModal">コミュニティを作成</a>
            </li>
          </ul>
        </div>

        <!-- コミュニティ参加モーダル -->
        <div class="modal fade" id="joinCommunityModal" tabindex="-1" aria-labelledby="joinCommunityModalLabel" aria-hidden="true">
          <div class="modal-dialog">
            <form class="modal-content">
              <div class="modal-header">
                <h5 class="modal-title" id="joinCommunityModalLabel">コミュニティに参加</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
              </div>
              <div class="modal-body">
                <div class="mb-3">
                  <label for="communityJoinCode" class="form-label">参加コード</label>
                  <input type="text" class="form-control" id="communityJoinCode" placeholder="" required>
                </div>
              </div>
              <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">閉じる</button>
                <button type="submit" class="btn btn-primary">参加</button>
              </div>
            </form>
          </div>
        </div>

        <!-- コミュニティ作成モーダル -->
        <div class="modal fade" id="createCommunityModal" tabindex="-1" aria-labelledby="createCommunityModalLabel" aria-hidden="true">
          <div class="modal-dialog">
            <form class="modal-content">
              <div class="modal-header">
                <h5 class="modal-title" id="createCommunityModalLabel">コミュニティを作成</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
              </div>
              <div class="modal-body">
                <div class="mb-3">
                  <label for="communityName" class="form-label">コミュニティ名</label>
                  <input type="text" class="form-control" id="communityName" required>
                </div>
                <div class="mb-3">
                  <label for="communityDesc" class="form-label">説明（任意）</label>
                  <textarea class="form-control" id="communityDesc" rows="2"></textarea>
                </div>
              </div>
              <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">閉じる</button>
                <button type="submit" class="btn btn-primary">作成</button>
              </div>
            </form>
          </div>
        </div>

        <!-- 検索バー -->
        <div class="row justify-content-center w-100">
          <div class="col-12 col-md-6 px-3">
            <form class="mt-4">
              <div class="input-group bg-white rounded-pill border px-3 py-1 w-100 search-bar-styles">
                <div class="d-flex justify-content-center align-items-center">🔍</div>
                <input type="text" name="" class="form-control rounded-pill" placeholder="検索">
              </div>
            </form>
          </div>
        </div>

        <!-- コミュニティカード -->
        <div class="container mt-4">
          <div class="row community-card-row">
            <div class="container mt-4">
              <div class="row justify-content-start">
                <div class="col-12 col-md-4 mb-4 px-3">
                  <a class="nav-link" href="">
                    <div class="class-card border rounded p-3">
                      <h5 class="mb-3 d-flex justify-content-start ms-2">コミュニティタイトル1</h5>
                      <div class="d-flex align-items-start gap-3">
                        <div class="img-fluid rounded bg-primary community-image-placeholder"></div>
                        <p class="mb-0 flex-grow-1">説明文1</p>
                      </div>
                    </div>
                  </a>
                </div>
                <!-- 他のコミュニティカードもここに追加可能 -->
              </div>
            </div>
          </div>
        </div>
      </main>
    </div>
  </div>
  <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"
    integrity="sha384-geWF76RCwLtnZ8qwWowPQNguL3RmwHVBC9FhGdlKrxdiJJigb/j/68SIy3Te4Bkz" crossorigin="anonymous"></script>
  <script src="../../index.js"></script>
</body>

</html>