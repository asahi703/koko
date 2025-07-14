<head>
    <link rel="stylesheet" href="../main/css/class_sidebar.css">
</head>
<!-- クラスサイドバー -->
<nav id="class-sidebar" class="d-none d-md-block flex-column px-0" style="border:none;">
    <div class="d-flex flex-row justify-content-start align-items-center ms-2 py-md-2 text-nowrap" style="border:none;">
        <!-- ← だけ残す（枠線なし） -->
        <a class="nav-link text-dark d-flex align-items-center" href="javascript:history.back();">
            <img src="../main/img/sidebarImg/arrow_back_25dp_999999_FILL0_wght400_GRAD0_opsz24.svg" alt=""
                 class="me-2" style="width: 24px; height: 24px;">
        </a>
    </div>
    <!-- 以下の項目は全て非表示・削除 -->
    <!--
    <div class="d-flex flex-row justify-content-between align-items-center ms-2 py-4 border-bottom">
        <a class="text-dark text-decoration-none" href="#">
            <span class="fs-5 fw-bold small">クラス名を表示</span>
        </a>
        <div class="dropdown">
            <a class="cu-pt" id="dropdownMenuButton1" data-bs-toggle="dropdown" aria-expanded="false">
                <img src="../main/img/sidebarImg/more_vert_25dp_999999_FILL0_wght400_GRAD0_opsz24.svg" alt="">
            </a>
            <ul class="dropdown-menu" aria-labelledby="dropdownMenuButton1">
                <li><a class="dropdown-item" href="#">メンバー一覧</a></li>
                <li><a class="dropdown-item" href="#">メンバー追加</a></li>
            </ul>
        </div>
    </div>
    <div class="d-flex flex-column justify-content-start ms-4 align-items-center">
        <ul class="list-unstyled w-100">
            <li>
                <a class="nav-link text-dark py-3 text-nowrap custom-hover d-block px-3" href="#">
                    行事予定表
                </a>
            </li>
            <li>
                <a class="nav-link text-dark py-3 text-nowrap custom-hover d-block px-3" href="#">
                    出席確認
                </a>
            </li>
        </ul>
    </div>
    -->
</nav>

<!-- スマホ用(ヘッダーの直下に置く) -->
<div class="d-flex d-md-none bg-white py-2 px-3 justify-content-between align-items-center sticky-top"
     style="top: 80px; z-index: 1020; border:none;">
    <!-- 左：戻る（←だけ残す、枠線なし） -->
    <a href="javascript:history.back();" class="text-dark text-decoration-none d-flex align-items-center">
        <img src="../main/img/sidebarImg/arrow_back_25dp_999999_FILL0_wght400_GRAD0_opsz24.svg" alt="戻る"
             style="width: 24px; height: 24px;">
    </a>
    <!-- 中央・右は非表示 -->
</div>